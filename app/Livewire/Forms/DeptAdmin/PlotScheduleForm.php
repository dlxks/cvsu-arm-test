<?php

declare(strict_types=1);

namespace App\Livewire\Forms\DeptAdmin;

use App\Models\ScheduleRoomTime;
use Illuminate\Validation\Rule;
use Livewire\Form;

class PlotScheduleForm extends Form
{
    public ?int $schedule_id = null;

    public ?int $schedule_category_id = null;

    public ?string $day = null;

    public ?string $time_in = null;

    public ?string $time_out = null;

    public ?int $faculty_id = null;

    public ?int $room_id = null;

    public function resetAfterSubmit(): void
    {
        $this->schedule_id = null;
        $this->schedule_category_id = null;
        $this->day = null;
        $this->time_in = null;
        $this->time_out = null;
        $this->faculty_id = null;
        $this->room_id = null;
    }

    /**
     * @return array<string, mixed>
     */
    public function validateForm(): array
    {
        return $this->validate($this->rules());
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    public function payload(array $validated): array
    {
        return array_filter(
            [
                'schedule_category_id' => $validated['schedule_category_id'] ?? null,
                'day' => $validated['day'] ?? null,
                'time_in' => $validated['time_in'] ?? null,
                'time_out' => $validated['time_out'] ?? null,
                'user_id' => $validated['faculty_id'] ?? null,
                'room_id' => $validated['room_id'] ?? null,
            ],
            static fn (mixed $value): bool => $value !== null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'schedule_id' => ['required', 'integer', 'exists:schedules,id'],
            'schedule_category_id' => [
                'required',
                'integer',
                Rule::exists('schedule_categories', 'id')->where(fn ($query) => $query->where('is_active', true)->orWhere('id', $this->schedule_category_id)),
            ],
            'day' => ['nullable', Rule::in(ScheduleRoomTime::DAYS)],
            'time_in' => ['nullable', 'date_format:H:i'],
            'time_out' => ['nullable', 'date_format:H:i', 'after:time_in'],
            'faculty_id' => ['nullable', 'integer', 'exists:users,id'],
            'room_id' => ['nullable', 'integer', 'exists:rooms,id'],
        ];
    }
}
