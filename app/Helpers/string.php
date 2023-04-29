<?php

namespace App\Helpers;

function replaceExtraSpace (string $str, string $replacement = '') : string
{
    return preg_replace('/\s+/', $replacement, $str);
}

function replaceStringKeys (string $str, array $keyValuePairs) : string
{
    foreach ($keyValuePairs as $key => $value)
    {
        $str = str_replace(":{$key}", $value, $str);
    }

    return $str;
}