<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['department_id', 'semester', 'division', 'academic_year', 'subject_id', 'faculty_id', 'classroom_id', 'day_of_week', 'slot_number'])]
class Timetable extends Model
{
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }
}
