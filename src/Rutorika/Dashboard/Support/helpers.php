<?php
if (!function_exists('app_array_find_where')) {

    function app_array_find_where($name, $parameters = [])
    {
        return app('url')->action($name, $parameters);
    }
}