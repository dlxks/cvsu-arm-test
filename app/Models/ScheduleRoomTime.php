<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['schedule_id', 'room_id', 'schedule_category_id', 'day', 'time_in', 'time_out'])]
class ScheduleRoomTime extends Model
{
    use HasFactory;

    protected $table = 'schedule_room_time';

    public const DAYS = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'];

    protected function casts(): array
    {
        return [
            'time_in' => 'datetime:H:i:s',
            'time_out' => 'datetime:H:i:s',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function scheduleCategory(): BelongsTo
    {
        return $this->belongsTo(ScheduleCategory::class);
    }
}
