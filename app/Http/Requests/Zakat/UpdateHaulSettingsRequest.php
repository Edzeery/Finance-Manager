<?php

namespace App\Http\Requests\Zakat;

use App\Services\HijriDateService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHaulSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $calendarType = $this->input('calendar_type', 'hijri');

        if ($calendarType === 'hijri') {
            return [
                'zakat_start_date_hijri_year' => ['required', 'integer', 'min:1000', 'max:1500'],
                'zakat_start_date_hijri_month' => ['required', 'integer', 'min:1', 'max:12'],
                'zakat_start_date_hijri_day' => ['required', 'integer', 'min:1', 'max:30'],
                'calendar_type' => ['required', 'string', 'in:hijri,gregorian'],
            ];
        }

        return [
            'zakat_start_date' => ['required', 'date', 'before_or_equal:today', 'after:2000-01-01'],
            'calendar_type' => ['required', 'string', 'in:hijri,gregorian'],
        ];
    }

    public function messages(): array
    {
        return [
            'zakat_start_date_hijri_year.required' => __('zakat.hijri_date_required'),
            'zakat_start_date_hijri_month.required' => __('zakat.hijri_date_required'),
            'zakat_start_date_hijri_day.required' => __('zakat.hijri_date_required'),
            'zakat_start_date_hijri_day.max' => __('zakat.invalid_hijri_day'),
            'zakat_start_date_hijri_month.max' => __('zakat.invalid_hijri_month'),
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $calendarType = $this->input('calendar_type');

            if ($calendarType === 'hijri') {
                $year = (int) $this->input('zakat_start_date_hijri_year');
                $month = (int) $this->input('zakat_start_date_hijri_month');
                $day = (int) $this->input('zakat_start_date_hijri_day');

                if ($year && $month && $day) {
                    $maxDay = HijriDateService::hijriMonthDays($year, $month);
                    $day = min($day, $maxDay);
                    $gregorian = HijriDateService::hijriToGregorian($year, $month, $day);
                    $today = new \DateTimeImmutable('today');

                    if ($gregorian > $today) {
                        $validator->errors()->add(
                            'zakat_start_date_hijri_year',
                            __('zakat.haul_date_future')
                        );
                    }

                    if ($gregorian->format('Y') < 2000) {
                        $validator->errors()->add(
                            'zakat_start_date_hijri_year',
                            __('zakat.haul_date_too_old')
                        );
                    }
                }
            }
        });
    }

    public function getResolvedStartDate(): string
    {
        $calendarType = $this->input('calendar_type');

        if ($calendarType === 'hijri') {
            $year = (int) $this->input('zakat_start_date_hijri_year');
            $month = (int) $this->input('zakat_start_date_hijri_month');
            $day = (int) $this->input('zakat_start_date_hijri_day');

            $maxDay = HijriDateService::hijriMonthDays($year, $month);
            $day = min($day, $maxDay);

            $gregorian = HijriDateService::hijriToGregorian($year, $month, $day);

            return $gregorian->format('Y-m-d');
        }

        return $this->input('zakat_start_date');
    }
}
