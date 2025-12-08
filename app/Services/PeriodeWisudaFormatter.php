<?php

namespace App\Services;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PeriodeWisudaFormatter
{
    private const MONTH_MAP = [
        'jan' => 1,
        'januari' => 1,
        'january' => 1,
        'feb' => 2,
        'februari' => 2,
        'february' => 2,
        'mar' => 3,
        'maret' => 3,
        'march' => 3,
        'apr' => 4,
        'april' => 4,
        'mei' => 5,
        'may' => 5,
        'jun' => 6,
        'juni' => 6,
        'june' => 6,
        'jul' => 7,
        'juli' => 7,
        'july' => 7,
        'aug' => 8,
        'agus' => 8,
        'agustus' => 8,
        'august' => 8,
        'sep' => 9,
        'sept' => 9,
        'september' => 9,
        'okt' => 10,
        'oktober' => 10,
        'oct' => 10,
        'october' => 10,
        'nov' => 11,
        'november' => 11,
        'des' => 12,
        'desember' => 12,
        'dec' => 12,
        'december' => 12,
    ];

    public static function normalize($periode, bool $keepOriginalOnFail = false): ?string
    {
        if (is_null($periode)) {
            return null;
        }

        if (is_numeric($periode)) {
            $numericString = trim((string) $periode);

            if (preg_match('/^(\\d{6})$/', $numericString)) {
                $year = substr($numericString, 0, 4);
                $month = substr($numericString, 4, 2);
                return sprintf('%04d-%02d', $year, $month);
            }

            if ((int) $periode > 30000) {
                try {
                    $date = Date::excelToDateTimeObject($periode);
                    return $date->format('Y-m');
                } catch (\Throwable $e) {
                    // fall through to generic handling
                }
            }
        }

        $periode = trim((string) $periode);

        if ($periode === '') {
            return null;
        }

        $periode = preg_replace('/\\s+/', ' ', $periode);

        if (preg_match('/^(\\d{4})[-\\/](\\d{1,2})$/', $periode, $matches)) {
            return sprintf('%04d-%02d', $matches[1], $matches[2]);
        }

        if (preg_match('/^(\\d{6})$/', $periode, $matches)) {
            $year = substr($matches[1], 0, 4);
            $month = substr($matches[1], 4, 2);
            return sprintf('%04d-%02d', $year, $month);
        }

        $monthYear = self::parseMonthYearString($periode);
        if ($monthYear) {
            [$year, $month] = $monthYear;
            return sprintf('%04d-%02d', $year, $month);
        }

        try {
            return Carbon::parse($periode)->format('Y-m');
        } catch (\Throwable $e) {
            return $keepOriginalOnFail ? $periode : null;
        }
    }

    public static function formatForDisplay(?string $periode): string
    {
        if (!$periode) {
            return '';
        }

        $normalized = self::normalize($periode);

        if (!$normalized) {
            return (string) $periode;
        }

        try {
            [$year, $month] = explode('-', $normalized);
            return Carbon::createFromDate((int) $year, (int) $month, 1)->translatedFormat('F Y');
        } catch (\Throwable $e) {
            return (string) $periode;
        }
    }

    public static function extractYear(?string $periode): ?string
    {
        $normalized = self::normalize($periode);
        if ($normalized) {
            return substr($normalized, 0, 4);
        }

        if (!$periode) {
            return null;
        }

        if (preg_match('/(\\d{4})/', (string) $periode, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private static function parseMonthYearString(string $value): ?array
    {
        $patternMonthFirst = '/^(?<month>[A-Za-z\\.]+)[\\s\\-\\/]+(?<year>\\d{4})$/u';
        if (preg_match($patternMonthFirst, $value, $matches)) {
            $month = self::mapMonthName($matches['month']);
            if ($month) {
                return [$matches['year'], $month];
            }
        }

        $patternYearFirst = '/^(?<year>\\d{4})[\\s\\-\\/]+(?<month>[A-Za-z\\.]+)$/u';
        if (preg_match($patternYearFirst, $value, $matches)) {
            $month = self::mapMonthName($matches['month']);
            if ($month) {
                return [$matches['year'], $month];
            }
        }

        return null;
    }

    private static function mapMonthName(string $month): ?int
    {
        $key = strtolower(str_replace('.', '', $month));
        return self::MONTH_MAP[$key] ?? null;
    }
}
