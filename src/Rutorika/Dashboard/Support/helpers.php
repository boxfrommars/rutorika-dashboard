<?php

if (!function_exists('app_array_find')) {

    function app_array_find($array, $key, $value)
    {
        $results = array_where($array,
        function ($i, $item) use ($key, $value) {
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

if (!function_exists('grid_link')) {

    function grid_link($name, $action, $routeParams)
    {
        switch ($action) {
            case 'view': // no break
            case 'edit':
                $icon = 'pencil';
                break;
            case 'destroy':
                $icon = 'remove';
                break;
            default:
                $icon = null;
        }

        $linkHtml = $icon ? '<span class="glyphicon glyphicon-' . $icon . '"></span>' : $action;

        return '<a data-action="' . $action . '" href="' . route(".{$name}.{$action}",
            $routeParams) . '">' . $linkHtml . '</a>';
    }
}

if (!function_exists('add_link')) {
    function add_link($name, $text = 'Добавить')
    {
        return '<a href="' . route(".{$name}.create") . '" class="btn btn-primary" role="button">
                <span class="glyphicon glyphicon-plus"></span> ' . $text . '
            </a>';
    }
}

