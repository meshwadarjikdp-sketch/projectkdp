        <div class="timetable-wrapper" id="generated-timetable-grid">
            <div class="timetable-header">
                <h3>K. D. Polytechnic, Patan</h3>
                <h4>Time Table (Term: {{ $academicYear }})</h4>
                <h5>Department of {{ App\Models\Department::find($departmentId)->department_name ?? 'Computer Engineering' }}</h5>
            </div>
            <div class="timetable-sub-header">
                <div>
                    Class: {{ $semester }}{{ $division }}
                </div>
                <div class="right-info">
                    Term Dates: 24-JUL-2026 to 31-DEC-2026 (SEM-1)<br>
                    13-JUL-2026 to 03-DEC-2026 (SEM-3)<br>
                    15-JUN-2026 to 30-OCT-2026 (SEM-5)<br>
                    WEF: 29-JUN-2026
                </div>
            </div>
            <table class="timetable-grid">
                <thead>
                    <tr>
                        <th style="width: 12%;">TIME</th>
                        @foreach($config->working_days as $day)
                            <th>{{ strtoupper($day) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        $timeSlots = [
                            1 => '08:30 - 09:30',
                            2 => '09:30 - 10:30',
                            3 => '10:30 - 11:30',
                            4 => '11:30 - 12:30',
                            5 => '01:00 - 02:00',
                            6 => '02:00 - 03:00',
                            7 => '03:10 - 04:10',
                            8 => '04:10 - 05:10',
                            9 => '05:10 - 06:10',
                            10 => '06:10 - 07:10',
                        ];
                    @endphp
                    @for($slot = 1; $slot <= $config->total_slots; $slot++)
                        @if($slot == $config->lunch_slot)
                            <tr>
                                <th>{{ $timeSlots[$slot] ?? '12:30 - 01:00' }}</th>
                                <td colspan="{{ count($config->working_days) }}" class="slot-lunch">RECESS</td>
                            </tr>
                        @else
                            <tr>
                                <th>{{ $timeSlots[$slot] ?? "Slot $slot" }}</th>
                                @foreach($config->working_days as $day)
                                    <td>
                                        @php
                                            $entries = collect();
                                            if (isset($timetables[$day])) {
                                                $entries = $timetables[$day]->where('slot_number', $slot);
                                            }
                                        @endphp
                                        
                                        @if($entries->isNotEmpty())
                                            @foreach($entries as $index => $entry)
                                                <div class="entry-box">
                                                    {{ $entry->subject->subject_code ?? $entry->subject->subject_name }}-{{ $entry->faculty->faculty_name ?? 'N/A' }}-{{ $entry->classroom->room_number ?? 'N/A' }}
                                                </div>
                                            @endforeach
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endif
                    @endfor
                </tbody>
            </table>
            <div class="timetable-footer">
                <div style="text-align: center; font-weight: normal;">^ = Tutorial</div>
                <div style="margin-top: 10px;">Recess-1: 12:30 PM - 01:00 PM</div>
                <div style="margin-bottom: 20px;">Recess-2: 03:00 PM - 03:10 PM</div>
                
                <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                    <div style="border: 1px solid #000; padding: 5px; display: inline-block;">
                        Skill Based Training: 15-JUN-2026 to 28-JUN-2026 (SEM-5)
                    </div>
                    <div class="timetable-footer-signature">
                        HOD<br>
                        Department of {{ App\Models\Department::find($departmentId)->department_name ?? 'Computer Engineering' }}
                    </div>
                </div>
            </div>
        </div>
