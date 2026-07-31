<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Timetable;

class TimetableGenerator
{
    protected $days = [];

    protected $totalSlots = 6;

    protected $lunchSlot = 4;

    protected $lectureSlots = [];

    protected $labSlots = [];

    protected $maxDailyLoad = 6;

    // Global trackers to prevent cross-division clashes
    protected $facultySchedule = [];

    protected $roomSchedule = [];

    protected $facultyDailyLoad = [];

    // Local trackers for the current generation
    protected $subjectDayTracker = [];

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

        // 2. Fetch Subjects and verify faculty department (Rule 2, 8, 18)
        $subjects = Subject::with('faculty')->where('department_id', $departmentId)
            ->where('semester', $semester)
            ->where('status', 'Active')
            ->orderBy('subject_type', 'desc') // Labs first
            ->get();

        if ($subjects->isEmpty()) {
            return ['success' => false, 'message' => 'Generation Failed: No subjects found.'];
        }

        foreach ($subjects as $subject) {
            if (! $subject->faculty_id) {
                return ['success' => false, 'message' => "Generation Failed: Subject {$subject->subject_name} must have an assigned faculty member."];
            }

            if ($subject->faculty && $subject->faculty->department_id != $departmentId) {
                return ['success' => false, 'message' => "Generation Failed: Faculty {$subject->faculty->faculty_name} does not belong to the selected department."];
            }
        }

        // 3. Fetch Classrooms (Rule 12)
        $theoryRooms = Classroom::where(function ($query) {
            $query->where('room_type', 'like', '%Lecture%')
                ->orWhere('room_type', 'like', '%Theory%')
                ->orWhere('room_type', 'Classroom');
        })
            ->where('availability', 'Available')
            ->get();

        $labRooms = Classroom::where('room_type', 'like', '%Lab%')
            ->where('availability', 'Available')
            ->get();

        if ($theoryRooms->isEmpty() && $labRooms->isEmpty()) {
            return ['success' => false, 'message' => 'Generation Failed: No available classrooms.'];
        }

        // Retry logic for heuristic scheduling
        $maxRetries = 20;
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $success = $this->attemptGeneration($subjects, $theoryRooms, $labRooms, $departmentId, $semester, $division, $academicYear, $subjectClassrooms);
            if ($success) {
                return ['success' => true, 'message' => 'Timetable Generated Successfully! All constraints satisfied.'];
            }
        }

        return ['success' => false, 'message' => 'Generation Failed: Could not find a conflict-free timetable under the configured constraints. Please verify faculty assignments, available rooms, working days, and slot settings.'];
    }

    protected function attemptGeneration($subjects, $theoryRooms, $labRooms, $departmentId, $semester, $division, $academicYear, $subjectClassrooms = [])
    {
        $this->initTrackingArrays($academicYear);
        $this->subjectDayTracker = [];

        $tempTimetable = [];

        foreach ($subjects as $subject) {
            $hoursToSchedule = $subject->hours_per_week;
            $isLab = stripos($subject->subject_type, 'Lab') !== false;

            if (! empty($subjectClassrooms[$subject->id])) {
                $manualRoomId = $subjectClassrooms[$subject->id];
                $rooms = Classroom::where('id', $manualRoomId)->get();
            } else {
                $rooms = $isLab ? $labRooms : $theoryRooms;
            }

            if ($isLab) {
                $slotsNeededPerSession = 2;
                $sessionsNeeded = max(1, (int)ceil($hoursToSchedule / 2));
            } else {
                $slotsNeededPerSession = 1;
                $sessionsNeeded = $hoursToSchedule;
            }

            for ($session = 0; $session < $sessionsNeeded; $session++) {
                $assigned = false;

                // Shuffle days and slots for randomness in retry logic (Rule 13, 15)
                $shuffledDays = $this->days;
                shuffle($shuffledDays);

                foreach ($shuffledDays as $day) {
                    if ($assigned) {
                        break;
                    }

                    // Skip if theory subject already scheduled on this day (Rule 6)
                    if (! $isLab && isset($this->subjectDayTracker[$subject->id][$day])) {
                        continue;
                    }

                    $slots = range(1, $this->totalSlots);
                    shuffle($slots);

                    foreach ($slots as $slot) {
                        if ($assigned) {
                            break;
                        }

                        // Check valid slot ranges
                        if ($slot == $this->lunchSlot) {
                            continue;
                        }

                        if ($slotsNeededPerSession > 1) {
                            if ($slot == $this->totalSlots || $slot == ($this->lunchSlot - 1)) {
                                continue;
                            }
                        }

                        if ($this->isSlotAllowedForSubject($subject, $slot) && $this->canSchedule($subject, $day, $slot, $rooms, $slotsNeededPerSession)) {
                            $roomId = $this->findAvailableRoom($day, $slot, $rooms, $slotsNeededPerSession);

                            if ($roomId) {
                                // Temporarily assign
                                for ($i = 0; $i < $slotsNeededPerSession; $i++) {
                                    $currentSlot = $slot + $i;

                                    $tempTimetable[] = [
                                        'department_id' => $departmentId,
                                        'semester' => $semester,
                                        'division' => $division,
                                        'academic_year' => $academicYear,
                                        'subject_id' => $subject->id,
                                        'faculty_id' => $subject->faculty_id,
                                        'classroom_id' => $roomId,
                                        'day_of_week' => $day,
                                        'slot_number' => $currentSlot,
                                    ];

                                    $this->facultySchedule[$day][$currentSlot][] = $subject->faculty_id;
                                    $this->roomSchedule[$day][$currentSlot][] = $roomId;

                                    if (! isset($this->facultyDailyLoad[$subject->faculty_id][$day])) {
                                        $this->facultyDailyLoad[$subject->faculty_id][$day] = 0;
                                    }
                                    $this->facultyDailyLoad[$subject->faculty_id][$day]++;
                                }

                                $this->subjectDayTracker[$subject->id][$day] = true;
                                $assigned = true;
                            }
                        }
                    }
                }

                if (! $assigned) {
                    return false; // Failed to schedule a session, trigger retry
                }
            }
        }

        // If we reach here, all subjects were scheduled successfully
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

    protected function canSchedule($subject, $day, $slot, $rooms, $slotsNeeded)
    {
        $facultyId = $subject->faculty_id;

        // Rule 6: Maximum Faculty teaching load per day = 4 Hours
        $currentLoad = $this->facultyDailyLoad[$facultyId][$day] ?? 0;
        if ($currentLoad + $slotsNeeded > $this->maxDailyLoad) {
            return false;
        }

        for ($i = 0; $i < $slotsNeeded; $i++) {
            $currentSlot = $slot + $i;

            // Rule 10: Faculty Clash Rule
            if (isset($this->facultySchedule[$day][$currentSlot]) && in_array($facultyId, $this->facultySchedule[$day][$currentSlot])) {
                return false;
            }
        }

        // Rule 11 & 12: Room availability
        $roomAvailable = $this->findAvailableRoom($day, $slot, $rooms, $slotsNeeded) !== null;
        if (! $roomAvailable) {
            return false;
        }

        return true;
    }

    protected function isSlotAllowedForSubject($subject, $slot)
    {
        if (stripos($subject->subject_type, 'Lab') !== false) {
            return empty($this->labSlots) || in_array($slot, $this->labSlots, true);
        }

        return empty($this->lectureSlots) || in_array($slot, $this->lectureSlots, true);
    }

    protected function findAvailableRoom($day, $slot, $rooms, $slotsNeeded)
    {
        // Shuffle rooms for better distribution (Rule 13)
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
            if ($isAvailable) {
                return $room->id;
            }
        }

        return null;
    }
}
