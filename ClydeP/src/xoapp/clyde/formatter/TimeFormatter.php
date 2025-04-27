<?php

namespace xoapp\clyde\formatter;

class TimeFormatter
{

    private const VALID_FORMATS = ["minutes", "hours", "seconds", "days"];

    public static function isValidFormat(string $format): bool
    {
        if (strlen($format) >= 5) {
            return false;
        }

        $chars = str_split($format);
        if (!is_numeric($chars[0])) {
            return false;
        }

        $int = self::stringToInt($format);
        if (!is_numeric($int)) {
            return false;
        }

        return in_array(
            self::intToString($format), self::VALID_FORMATS
        );
    }

    private static function intToString(string $timeFormat): ?string
    {
        $format = str_split($timeFormat);
        $time = null;

        for ($i = 0; $i < count($format); $i++) {
            switch ($format[$i]) {
                case "m":
                    $time = "minutes";
                    break;
                case "h":
                    $time = "hours";
                    break;
                case "d":
                    $time = "days";
                    break;
                case "s":
                    $time = "seconds";
                    break;
            }
        }

        return $time;
    }

    private static function stringToInt(string $timeFormat): int
    {
        $format = str_split($timeFormat);
        $characters = "";

        for ($i = 0; $i < count($format); $i++) {
            if (is_numeric($format[$i])) {
                $characters .= $format[$i];
            }
        }

        return intval($characters);
    }

    private static function getFormatTime(int $time, string $timeFormat): ?int
    {
        return match (self::intToString($timeFormat)) {
            "minutes" => time() + ($time * 60),
            "hours" => time() + ($time * 3600),
            "days" => time() + ($time * 86400),
            "seconds" => time() + ($time),
            default => null
        };
    }

    public static function getTimeLeft(int $time): string
    {
        $remaining = $time - time();
        $s = $remaining % 60;
        $m = null;
        $h = null;
        $days = null;

        if ($remaining >= 60) {
            $m = floor(($remaining % 3600) / 60);
            if ($remaining >= 3600) {
                $h = floor(($remaining % 86400) / 3600);
                if ($remaining >= 3600 * 24) {
                    $days = floor($remaining / 86400);
                }
            }
        }

        return ($m !== null ? ($h !== null ? ($days !== null ? "$days days " : "") . "$h hours " : "") . "$m minutes " : "") . "$s seconds";
    }

    public static function parseTime(string $data): int
    {
        return self::getFormatTime(
            self::stringToInt($data), $data
        );
    }
}