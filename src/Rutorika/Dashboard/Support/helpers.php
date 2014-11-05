<?php

if (!function_exists('app_array_find')) {

    function app_array_find($array, $key, $value)
    {
        $results = array_where($array, function ($i, $item) use ($key, $value) {
            return $item[$key] === $value;
        });

        return $results;
    }
}

if (!function_exists('app_array_find_where')) {

    function app_array_find_where($array, $key, $value)
    {
        $results = app_array_find($array, $key, $value);
        return array_shift($results);
    }
}

