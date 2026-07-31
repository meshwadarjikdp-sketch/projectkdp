<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Faculty;
use App\Models\Subject;
use App\Models\Timetable;

class TimetableGenerator
{
    protected $days = [];
    protected $totalSlots = 6;
    protected $lunchSlot = 4;
    protected $lectureSlots = [];
    protected $labSlots = [];
    
    protected $maxDailyLoad = 4; // Max hours a faculty can teach per day
    protected $maxStudentDailyLoad = 6; // Max hours a student can have per day

    // Global trackers to prevent cross-division clashes
    protected $facultySchedule = [];
    protected $roomSchedule = [];
    protected $facultyDailyLoad = [];

    // Local trackers for the current generation
    protected $subjectDayTracker = [];
    protected $studentDailyLoad = [];
    protected $lastScheduledSubject = []; // Track last subject for consecutive prevention
    
    public function generate($departmentId, $semester, $division, $academicYear, $workingDays, $totalSlots, $lunchSlot, $lectureSlots = [], $labSlots = [], $strictWorkload = true, $continuousLabs = true, $noConsecutive = true, $subjectClassrooms = [])
    {
        $this->days = $workingDays;
        $this->totalSlots = $totalSlots;
        $this->lunchSlot = $lunchSlot;
        $this->lectureSlots = array_values(array_unique(array_filter($lectureSlots, fn ($slot) => is_numeric($slot))));
        $this->labSlots = array_values(array_unique(array_filter($labSlots, fn ($slot) => is_numeric($slot))));

        // 1. Delete previous timetable for this specific division
        Timetable::where('department_id', $departmentId)
            ->where('semester', $semester)
            ->where('division', $division)
            ->where('academic_year', $academicYear)
            ->delete();

        // 2. Fetch Subjects and verify faculty department
        $subjects = Subject::with('faculty')->where('department_id', $departmentId)
            ->where('semester', $semester)
            ->where('status', 'Active')
            ->orderBy('subject_type', 'desc') // Labs first
            ->get();

        if ($subjects->isEmpty()) {
            return ['success' => false, 'message' => 'Missing Subject Mapping: No subjects found for this department and semester.'];
        }

        foreach ($subjects as $subject) {
            if (! $subject->faculty_id) {
                return ['success' => false, 'message' => "Missing Subject Mapping: Subject {$subject->subject_name} must have an assigned faculty member."];
            }
        }

        $allFaculties = Faculty::where('department_id', $departmentId)->get();
        if ($allFaculties->isEmpty()) {
            return ['success' => false, 'message' => 'Missing Faculty: No faculty members found in this department.'];
        }

        // 3. Fetch Classrooms
$theoryRooms = Classroom::where(function ($query) {
    $query->where('room_type', 'like', '%Classroom%')
          ->orWhere('room_type', 'like', '%Theory%')
          ->orWhere('room_type', 'like', '%Lecture%');
})
->where(function ($query) {
    $query->where('availability', 'YES')
          ->orWhere('availability', 'Available');
})
->get();

$labRooms = Classroom::where('room_type', 'like', '%Lab%')
    ->where(function ($query) {
        $query->where('availability', 'YES')
              ->orWhere('availability', 'Available');
    })
    ->get();

        if ($theoryRooms->isEmpty()) {
            return ['success' => false, 'message' => 'Missing Classroom: No available theory classrooms found.'];
        }
        
        $hasLabSubjects = $subjects->contains(fn($s) => stripos($s->subject_type, 'Lab') !== false);
        if ($hasLabSubjects && $labRooms->isEmpty()) {
            return ['success' => false, 'message' => 'Missing Lab: No available lab rooms found.'];
        }

        // Retry logic for heuristic scheduling
        $maxRetries = 200; // Increased retries since we are not failing fast
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $success = $this->attemptGeneration($subjects, $allFaculties, $theoryRooms, $labRooms, $departmentId, $semester, $division, $academicYear, $subjectClassrooms);
            if ($success) {
                return ['success' => true, 'message' => 'Timetable Generated Successfully!'];
            }
        }

        return ['success' => false, 'message' => 'Constraint Conflict: Could not find a valid timetable layout that satisfies all constraints (Zero clashes, max workloads, avoiding consecutive repeats). Please allocate more faculties or relax constraints.'];
    }

    protected function attemptGeneration($subjects, $allFaculties, $theoryRooms, $labRooms, $departmentId, $semester, $division, $academicYear, $subjectClassrooms = [])
    {
        $this->initTrackingArrays($academicYear);
        $this->subjectDayTracker = [];
        $this->studentDailyLoad = [];
        $this->lastScheduledSubject = [];
        
        foreach ($this->days as $day) {
            $this->studentDailyLoad[$day] = 0;
            $this->lastScheduledSubject[$day] = [];
        }

        $tempTimetable = [];

        foreach ($subjects as $subject) {
            $hoursToSchedule = $subject->hours_per_week;
            $isLab = stripos($subject->subject_type, 'Lab') !== false;

            if ($isLab) {
                $slotsNeededPerSession = 2;
                $sessionsNeeded = max(1, (int)ceil($hoursToSchedule / 2));
                $rooms = $labRooms;
            } else {
                $slotsNeededPerSession = 1;
                $sessionsNeeded = $hoursToSchedule;
                if (! empty($subjectClassrooms[$subject->id])) {
                    $manualRoomId = $subjectClassrooms[$subject->id];
                    $rooms = Classroom::where('id', $manualRoomId)->get();
                } else {
                    $rooms = $theoryRooms;
                }
            }

            for ($session = 0; $session < $sessionsNeeded; $session++) {
                $assigned = false;
                $shuffledDays = $this->days;
                shuffle($shuffledDays);

                foreach ($shuffledDays as $day) {
                    if ($assigned) break;

                    // Theory and Lab of same subject on different days
                    // Theory should not repeat on the same day
                    if (isset($this->subjectDayTracker[$subject->id][$day])) {
                        continue;
                    }

                    if ($this->studentDailyLoad[$day] + $slotsNeededPerSession > $this->maxStudentDailyLoad) {
                        continue; // Student max daily load reached
                    }

                    $slots = range(1, $this->totalSlots);
                    // Instead of shuffle, let's distribute evenly across the day (front-loading)
                    // shuffle($slots); 
                    
                    foreach ($slots as $slot) {
                        if ($assigned) break;
                        if ($slot == $this->lunchSlot) continue; // No classes in lunch break
                        if ($slotsNeededPerSession > 1) {
                            if ($slot == $this->totalSlots || $slot == ($this->lunchSlot - 1)) continue; // Can't start a 2-hr block right before day end or lunch
                        }

                        // Check same subject consecutive repeat rule
                        if (isset($this->lastScheduledSubject[$day][$slot - 1]) && $this->lastScheduledSubject[$day][$slot - 1] == $subject->id) {
                            continue;
                        }

                        if ($this->isSlotAllowedForSubject($subject, $slot)) {
                            
                            $facultyId = $subject->faculty_id;
                            
                            if ($this->canScheduleFaculty($facultyId, $day, $slot, $slotsNeededPerSession)) {
                                $roomId = $this->findAvailableRoom($day, $slot, $rooms, $slotsNeededPerSession);
                                if ($roomId) {
                                    for ($i = 0; $i < $slotsNeededPerSession; $i++) {
                                        $currentSlot = $slot + $i;
                                        $tempTimetable[] = [
                                            'department_id' => $departmentId,
                                            'semester' => $semester,
                                            'division' => $division,
                                            'academic_year' => $academicYear,
                                            'subject_id' => $subject->id,
                                            'faculty_id' => $facultyId,
                                            'classroom_id' => $roomId,
                                            'day_of_week' => $day,
                                            'slot_number' => $currentSlot,
                                            'batch' => null
                                        ];

                                        $this->facultySchedule[$day][$currentSlot][] = $facultyId;
                                        $this->roomSchedule[$day][$currentSlot][] = $roomId;
                                        $this->lastScheduledSubject[$day][$currentSlot] = $subject->id;

                                        if (! isset($this->facultyDailyLoad[$facultyId][$day])) {
                                            $this->facultyDailyLoad[$facultyId][$day] = 0;
                                        }
                                        $this->facultyDailyLoad[$facultyId][$day]++;
                                    }
                                    
                                    $this->subjectDayTracker[$subject->id][$day] = true;
                                    $this->studentDailyLoad[$day] += $slotsNeededPerSession;
                                    $assigned = true;
                                }
                            } else {
                                // Intelligent Faculty Reuse: The assigned faculty is busy or reached max load. 
                                // Let's find ANOTHER available faculty in the same department as a backup.
                                $backupFacultyId = $this->findAvailableBackupFaculty($allFaculties, $facultyId, $day, $slot, $slotsNeededPerSession);
                                if ($backupFacultyId) {
                                    $roomId = $this->findAvailableRoom($day, $slot, $rooms, $slotsNeededPerSession);
                                    if ($roomId) {
                                        for ($i = 0; $i < $slotsNeededPerSession; $i++) {
                                            $currentSlot = $slot + $i;
                                            $tempTimetable[] = [
                                                'department_id' => $departmentId,
                                                'semester' => $semester,
                                                'division' => $division,
                                                'academic_year' => $academicYear,
                                                'subject_id' => $subject->id,
                                                'faculty_id' => $backupFacultyId,
                                                'classroom_id' => $roomId,
                                                'day_of_week' => $day,
                                                'slot_number' => $currentSlot,
                                                'batch' => null
                                            ];

                                            $this->facultySchedule[$day][$currentSlot][] = $backupFacultyId;
                                            $this->roomSchedule[$day][$currentSlot][] = $roomId;
                                            $this->lastScheduledSubject[$day][$currentSlot] = $subject->id;

                                            if (! isset($this->facultyDailyLoad[$backupFacultyId][$day])) {
                                                $this->facultyDailyLoad[$backupFacultyId][$day] = 0;
                                            }
                                            $this->facultyDailyLoad[$backupFacultyId][$day]++;
                                        }
                                        
                                        $this->subjectDayTracker[$subject->id][$day] = true;
                                        $this->studentDailyLoad[$day] += $slotsNeededPerSession;
                                        $assigned = true;
                                    }
                                }
                            }
                        }
                    }
                }

                if (! $assigned) {
                    return false; // Failed to schedule a session, trigger retry
                }
            }
        }

        foreach ($tempTimetable as $entry) {
            Timetable::create($entry);
        }

        return true;
    }

    protected function initTrackingArrays($academicYear)
    {
        $this->facultySchedule = [];
        $this->roomSchedule = [];
        $this->facultyDailyLoad = [];

        $existing = Timetable::where('academic_year', $academicYear)->get();

        foreach ($existing as $tt) {
            $this->facultySchedule[$tt->day_of_week][$tt->slot_number][] = $tt->faculty_id;
            $this->roomSchedule[$tt->day_of_week][$tt->slot_number][] = $tt->classroom_id;

            if (! isset($this->facultyDailyLoad[$tt->faculty_id][$tt->day_of_week])) {
                $this->facultyDailyLoad[$tt->faculty_id][$tt->day_of_week] = 0;
            }
            $this->facultyDailyLoad[$tt->faculty_id][$tt->day_of_week]++;
        }
    }

    protected function canScheduleFaculty($facultyId, $day, $slot, $slotsNeeded)
    {
        $currentLoad = $this->facultyDailyLoad[$facultyId][$day] ?? 0;
        if ($currentLoad + $slotsNeeded > $this->maxDailyLoad) return false;

        for ($i = 0; $i < $slotsNeeded; $i++) {
            $currentSlot = $slot + $i;
            if (isset($this->facultySchedule[$day][$currentSlot]) && in_array($facultyId, $this->facultySchedule[$day][$currentSlot])) {
                return false;
            }
        }
        return true;
    }

    protected function findAvailableBackupFaculty($allFaculties, $primaryFacultyId, $day, $slot, $slotsNeeded)
    {
        $shuffled = $allFaculties->shuffle();
        foreach ($shuffled as $faculty) {
            if ($faculty->id == $primaryFacultyId) continue;
            
            if ($this->canScheduleFaculty($faculty->id, $day, $slot, $slotsNeeded)) {
                return $faculty->id;
            }
        }
        return null;
    }

    protected function findAvailableRoom($day, $slot, $rooms, $slotsNeeded)
    {
        $shuffledRooms = $rooms->shuffle();
        foreach ($shuffledRooms as $room) {
            $isAvailable = true;
            for ($i = 0; $i < $slotsNeeded; $i++) {
                $currentSlot = $slot + $i;
                if (isset($this->roomSchedule[$day][$currentSlot]) && in_array($room->id, $this->roomSchedule[$day][$currentSlot])) {
                    $isAvailable = false;
                    break;
                }
            }
            if ($isAvailable) return $room->id;
        }
        return null;
    }
    
    protected function isSlotAllowedForSubject($subject, $slot)
    {
        if (stripos($subject->subject_type, 'Lab') !== false) {
            return empty($this->labSlots) || in_array($slot, $this->labSlots, true);
        }
        return empty($this->lectureSlots) || in_array($slot, $this->lectureSlots, true);
    }
}
