<?php

namespace Gendiff;

class Engine
{
    public static function compareJsonFiles($firstFile, $secondFile)
    {
        $firstFileData = json_decode(file_get_contents($firstFile), true);
        $secondFileData = json_decode(file_get_contents($secondFile), true);

        $keys = array_merge(array_keys($firstFileData), array_keys($secondFileData));
        sort($keys);

        $diff = array_reduce($keys, function ($carry, $key) use ($firstFileData, $secondFileData) {
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
