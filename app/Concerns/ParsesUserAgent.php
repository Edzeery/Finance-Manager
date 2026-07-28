<?php

namespace App\Concerns;

trait ParsesUserAgent
{
    private function parseDevice(string $userAgent): string
    {
        if (preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $userAgent)) {
            return preg_match('/iPad|iPod/i', $userAgent) ? 'tablet' : 'phone';
        }

        return 'desktop';
    }

    private function parseBrowser(string $userAgent): string
    {
        $browsers = [
            'Edg/' => 'Edge',
            'OPR' => 'Opera',
            'Chrome' => 'Chrome',
            'Firefox' => 'Firefox',
            'Safari' => 'Safari',
            'MSIE' => 'IE',
            'Trident' => 'IE',
        ];
        foreach ($browsers as $pattern => $name) {
            if (stripos($userAgent, $pattern) !== false) {
                return $name;
            }
        }

        return 'Unknown';
    }

    private function parseOS(string $userAgent): string
    {
        $oses = [
            'Windows NT 10' => 'Windows 10/11',
            'Windows NT 6.3' => 'Windows 8.1',
            'Windows NT 6.2' => 'Windows 8',
            'Windows NT 6.1' => 'Windows 7',
            'Windows' => 'Windows',
            'Mac OS X' => 'macOS',
            'Android' => 'Android',
            'iPhone OS' => 'iOS',
            'iPad' => 'iPadOS',
            'Linux' => 'Linux',
            'CrOS' => 'ChromeOS',
        ];
        foreach ($oses as $pattern => $name) {
            if (stripos($userAgent, $pattern) !== false) {
                return $name;
            }
        }

        return 'Unknown';
    }
}
