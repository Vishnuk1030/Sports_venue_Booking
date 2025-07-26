<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReserveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'venue_id' => 'required|exists:venues,id',
            'booking_date' => 'required|date|after_or_equal:today|before_or_equal:' . now()->addMonth()->toDateString(),
            'start_time' => 'required|date_format:h:i A',
            'end_time' => 'required|date_format:h:i A|after:start_time',
        ];
    }
}
