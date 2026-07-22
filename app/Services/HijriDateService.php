<?php

namespace App\Services;

class HijriDateService
{
    private const HIJRI_MONTHS_AR = [
        'محرم', 'صفر', 'ربيع الأول', 'ربيع الثاني',
        'جمادى الأولى', 'جمادى الثانية', 'رجب', 'شعبان',
        'رمضان', 'شوال', 'ذو القعدة', 'ذو الحجة',
    ];

    private const HIJRI_MONTHS_EN = [
        'Muharram', 'Safar', 'Rabi al-Awwal', 'Rabi al-Thani',
        'Jumada al-Ula', 'Jumada al-Thani', 'Rajab', 'Shaban',
        'Ramadan', 'Shawwal', 'Dhul Qadah', 'Dhul Hijjah',
    ];

    private const HIJRI_MONTHS_FR = [
        'Mouharram', 'Safar', 'Rabia al-Aoual', 'Rabia al-Thani',
        'Joumada al-Oula', 'Joumada al-Thani', 'Rajab', 'Cha\'ban',
        'Ramadan', 'Chawwal', 'Dhou al-Qa\'da', 'Dhou al-Hijja',
    ];

    public static function gregorianToHijri(\DateTimeInterface $date): array
    {
        $gy = (int) $date->format('Y');
        $gm = (int) $date->format('n');
        $gd = (int) $date->format('j');

        $a = (int) floor((14 - $gm) / 12);
        $y = $gy + 4800 - $a;
        $m = $gm + 12 * $a - 3;

        $jdn = $gd + (int) floor((153 * $m + 2) / 5) + 365 * $y
            + (int) floor($y / 4) - (int) floor($y / 100)
            + (int) floor($y / 400) - 32045;

        $l = $jdn - 1948440 + 10632;
        $n = (int) (($l - 1) / 10631);
        $l = $l - 10631 * $n + 354;

        $j = (int) ((10985 - $l) / 5316) * (int) ((50 * $l) / 17719)
            + (int) ($l / 5670) * (int) ((43 * $l) / 15238);

        $l = $l - (int) ((30 - $j) / 15) * (int) ((17719 * $j) / 50)
            - (int) ($j / 16) * (int) ((15238 * $j) / 43) + 29;

        $hijriMonth = (int) ((24 * $l) / 709);
        $hijriDay = $l - (int) ((709 * $hijriMonth) / 24);
        $hijriYear = 30 * $n + $j - 30;

        return [
            'year' => $hijriYear,
            'month' => $hijriMonth,
            'day' => $hijriDay,
        ];
    }

    public static function hijriToGregorian(int $year, int $month, int $day): \DateTimeImmutable
    {
        $jdn = (int) floor((11 * $year + 3) / 30)
            + 354 * $year
            + 30 * $month
            - (int) floor(($month - 1) / 2)
            + $day
            + 1948440
            - 385;

        $a = $jdn + 32044;
        $b = (int) floor((4 * $a + 3) / 146097);
        $c = $a - (int) floor(146097 * $b / 4);
        $d = (int) floor((4 * $c + 3) / 1461);
        $e = $c - (int) floor(1461 * $d / 4);
        $m = (int) floor((5 * $e + 2) / 153);

        $gDay = $e - (int) floor((153 * $m + 2) / 5) + 1;
        $gMonth = $m + 3 - 12 * (int) floor($m / 10);
        $gYear = 100 * $b + $d - 4800 + (int) floor($m / 10);

        return new \DateTimeImmutable("{$gYear}-{$gMonth}-{$gDay}");
    }

    public static function formatHijriDate(array $hijri, string $locale = 'ar'): string
    {
        $months = match ($locale) {
            'fr' => self::HIJRI_MONTHS_FR,
            'en' => self::HIJRI_MONTHS_EN,
            default => self::HIJRI_MONTHS_AR,
        };

        $monthName = $months[$hijri['month'] - 1] ?? '';

        return "{$hijri['day']} {$monthName} {$hijri['year']}";
    }

    public static function formatHijriShort(array $hijri): string
    {
        return sprintf('%04d-%02d-%02d', $hijri['year'], $hijri['month'], $hijri['day']);
    }

    public static function getHijriMonthName(int $month, string $locale = 'ar'): string
    {
        $months = match ($locale) {
            'fr' => self::HIJRI_MONTHS_FR,
            'en' => self::HIJRI_MONTHS_EN,
            default => self::HIJRI_MONTHS_AR,
        };

        return $months[$month - 1] ?? '';
    }

    public static function hijriMonthDays(int $year, int $month): int
    {
        if ($month < 1 || $month > 12) {
            return 29;
        }

        if ($month % 2 === 1) {
            return 30;
        }

        if ($month === 12 && self::isHijriLeapYear($year)) {
            return 30;
        }

        return 29;
    }

    public static function isHijriLeapYear(int $year): bool
    {
        $leapYears = [2, 5, 7, 10, 13, 16, 18, 21, 24, 26, 29];
        $cycle = (($year % 30) + 30) % 30 ?: 30;

        return in_array($cycle, $leapYears);
    }
}
