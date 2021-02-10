<?php

namespace Gendiff;

class Engine
{
    public static function diff($firstFile, $secondFile)
    {
        [$firstFileData, $secondFileData] = self::getDataFromFiles($firstFile, $secondFile);
        $diff = self::calculateDiff($firstFileData, $secondFileData);
        self::printResult($diff);
    }

    private static function getDataFromFiles($firstFile, $secondFile): array
    {
        $firstFileData = json_decode(file_get_contents($firstFile), true);
        $secondFileData = json_decode(file_get_contents($secondFile), true);
        return [$firstFileData, $secondFileData];
    }

    private static function calculateDiff($firstFileData, $secondFileData): array
    {
        $keys = array_merge(array_keys($firstFileData), array_keys($secondFileData));
        sort($keys);
        return array_reduce($keys, function ($carry, $key) use ($firstFileData, $secondFileData) {
            if (!isset($firstFileData[$key])) {
                $carry["+ $key"] = $secondFileData[$key];
            } elseif (!isset($secondFileData[$key])) {
                $carry["- $key"] = $firstFileData[$key];
            } elseif ($firstFileData[$key] === $secondFileData[$key]) {
                $carry["  $key"] = $firstFileData[$key];
            } else {
                $carry["- $key"] = $secondFileData[$key];
                $carry["+ $key"] = $firstFileData[$key];
            }
            return $carry;
        }, []);
    }

    private static function printResult($diff)
    {
        foreach ($diff as $key => $value) {
            if ($value === false) {
                $str = 'false';
            } elseif ($value === true) {
                $str = 'true';
            } else {
                $str = $value;
            }
            echo ("$key: $str\n");
        }
    }
}
