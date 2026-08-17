<?php

namespace App\Http\Requests;

use App\Enums\FlightDirection;
use App\Enums\FlightType;
use App\Models\Airport;
use App\Services\FlightService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreFlightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return $this->baseRules();
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('departure_flight_number')) {
            $this->merge([
                'departure_flight_number' => strtoupper(preg_replace('/\s+/', '', (string) $this->input('departure_flight_number')) ?? ''),
            ]);
        }

        if ($this->filled('via_departure_flight_number')) {
            $this->merge([
                'via_departure_flight_number' => strtoupper(preg_replace('/\s+/', '', (string) $this->input('via_departure_flight_number')) ?? ''),
            ]);
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->validateAirportBelongsToCity($validator);
            $this->validateViaStay($validator);
        });
    }

    protected function validateViaStay(Validator $validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        if ($this->input('flight_type') !== FlightType::Indirect->value) {
            return;
        }

        try {
            app(FlightService::class)->calculateStayMinutes(
                (string) $this->input('via_arrival_date'),
                (string) $this->input('via_arrival_time'),
                (string) $this->input('via_departure_date'),
                (string) $this->input('via_departure_time'),
            );
        } catch (\InvalidArgumentException $exception) {
            $validator->errors()->add('via_departure_time', $exception->getMessage());
        }
    }

    protected function validateAirportBelongsToCity(Validator $validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $pairs = [
            ['departure_city_id', 'departure_airport_id', 'departure'],
            ['arrival_city_id', 'arrival_airport_id', 'arrival'],
        ];

        if ($this->input('flight_type') === FlightType::Indirect->value) {
            $pairs[] = ['via_city_id', 'via_airport_id', 'via'];
        }

        foreach ($pairs as [$cityKey, $airportKey, $label]) {
            $cityId = (int) $this->input($cityKey);
            $airportId = (int) $this->input($airportKey);

            if ($cityId === 0 || $airportId === 0) {
                continue;
            }

            $airport = Airport::query()->find($airportId);

            if ($airport !== null && (int) $airport->city_id !== $cityId) {
                $validator->errors()->add($airportKey, ucfirst($label).' airport must belong to the selected city.');
            }
        }
    }

    /** @return array<string, mixed> */
    protected function baseRules(): array
    {
        $isIndirect = $this->input('flight_type') === FlightType::Indirect->value;

        return [
            'flight_type' => ['required', Rule::enum(FlightType::class)],
            'direction' => ['required', Rule::enum(FlightDirection::class)],

            'departure_city_id' => ['required', Rule::exists('cities', 'id')],
            'departure_airport_id' => ['required', Rule::exists('airports', 'id')],
            'departure_airline_id' => ['required', Rule::exists('airlines', 'id')],
            'departure_flight_number' => ['required', 'string', 'max:10', 'regex:/^[A-Z0-9]+$/'],
            'departure_date' => ['required', 'date'],
            'departure_time' => ['required', 'date_format:H:i'],

            'via_city_id' => [$isIndirect ? 'required' : 'nullable', Rule::exists('cities', 'id')],
            'via_airport_id' => [$isIndirect ? 'required' : 'nullable', Rule::exists('airports', 'id')],
            'via_arrival_date' => [$isIndirect ? 'required' : 'nullable', 'date'],
            'via_arrival_time' => [$isIndirect ? 'required' : 'nullable', 'date_format:H:i'],
            'via_airline_id' => [$isIndirect ? 'required' : 'nullable', Rule::exists('airlines', 'id')],
            'via_departure_flight_number' => [$isIndirect ? 'required' : 'nullable', 'string', 'max:10', 'regex:/^[A-Z0-9]+$/'],
            'via_departure_date' => [$isIndirect ? 'required' : 'nullable', 'date', 'after_or_equal:via_arrival_date'],
            'via_departure_time' => [$isIndirect ? 'required' : 'nullable', 'date_format:H:i'],

            'arrival_city_id' => ['required', Rule::exists('cities', 'id')],
            'arrival_airport_id' => ['required', Rule::exists('airports', 'id')],
            'arrival_date' => ['required', 'date', 'after_or_equal:departure_date'],
            'arrival_time' => ['required', 'date_format:H:i'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'departure_flight_number.regex' => 'Enter the flight number without the airline code.',
            'via_departure_flight_number.regex' => 'Enter the flight number without the airline code.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function flightPayload(FlightService $flightService): array
    {
        $data = $flightService->prepareFlightData($this->validated());
        $data['created_by'] = auth()->id();

        return $data;
    }
}
