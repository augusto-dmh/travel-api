<?php

namespace App\Http\Requests;

use App\Models\Travel;
use App\Rules\DateDifference;
use Illuminate\Foundation\Http\FormRequest;

class StoreTourRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->roles()->where('name', 'admin')->exists();
    }

    public function prepareForValidation(): void {
        $this->merge([
            'travel_id' => $this->route('travel')->id,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'travel_id' => ['required', 'exists:travels,id'],
            'name' => ['required', 'string', 'max:255'],
            'starting_date' => ['required', 'date'],
            'ending_date' => [
                'required',
                'date',
                new DateDifference(
                    $this->starting_date,
                    $this->ending_date,
                    Travel::find(request('travel_id'))->first()->number_of_days
                )
            ],
            'price' => ['required', 'integer'],
        ];
    }

    public function messages() {
        return [
            'ending_date.date_difference' => `the "starting_date" and "ending_date" difference has to be consistent with travel's "number_of_days"`
        ];
    }
}
