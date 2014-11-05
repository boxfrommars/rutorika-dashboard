<?php

if (!function_exists('app_array_find')) {

    function app_array_find($array, $key, $value)
    {
        \Log::debug($array);
        \Log::debug($key);
        \Log::debug($value);
        $results = array_where($array, function ($i, $item) use ($key, $value) {
            return $item[$key] === $value;
        });
        \Log::debug($results);
        return $results;
    }
}

if (!function_exists('app_array_find_where')) {

    function app_array_find_where($array, $key, $value)
    {
        \Log::debug($array);
        \Log::debug($key);
        \Log::debug($value);
        $results = app_array_find($array, $key, $value);
        \Log::debug($results);
        return array_shift($results);
    }
}

