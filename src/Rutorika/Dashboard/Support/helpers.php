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

if (!function_exists('plural_ru')) {
    /**
     * @param int $n
     * @param str $form1 форма использующаяся в словосочетании с числительным 1 (1 яблоко, 1 квартира)
     * @param str $form2 форма использующаяся в словосочетании с числительным 2 (2 яблока, 2 квартиры)
     * @param str $form5 форма использующаяся в словосочетании с числительным 5 (5 яблок, 5 квартир)
     * @return str
     */
    function plural_ru($n, $form1, $form2, $form5) {
        $n = abs($n) % 100;
        $n1 = $n % 10;

        if ($n > 10 && $n < 20) {
            return $form5;
        } elseif ($n1 > 1 && $n1 < 5) {
            return $form2;
        } elseif ($n1 == 1) {
            return $form1;
        } else {
            return $form5;
        }
    }
}

if (!function_exists('image_src')) {
    function image_src($filename, $type) {
        return "/assets/image/{$type}/{$filename}";
    }
}
if (!function_exists('file_src')) {
    function file_src($filename, $type) {
        return "/assets/file/{$type}/{$filename}";
    }
}
if (!function_exists('human_filesize')) {
    function human_filesize($bytes, $decimals = 2) {
        $size = ['B','kB','MB','GB','TB','PB','EB','ZB','YB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
    }
}
if (!function_exists('mb_ucfirst')) {
    function mb_ucfirst($string, $encoding) {
        $strlen = mb_strlen($string, $encoding);
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, $strlen - 1, $encoding);
        return mb_strtoupper($firstChar, $encoding) . $then;
    }
}


if (!function_exists('generate_crud_routes')) {
    function generate_crud_routes($routes, $entityDefaultNameSpace = '\\')
    {
        foreach ($routes as $route) {
            $name = $route['name'];
            $entity = camel_case($name);
            $controller = studly_case($name) . 'Controller';
            $prefix = array_key_exists('prefix', $route) ? $route['prefix'] : '';
            $entityClassName = studly_case($name);

            $entityNameSpace = isset($route['entityNameSpace']) ? $route['entityNameSpace'] : $entityDefaultNameSpace;
            Route::model($entity, $entityNameSpace . "{$entityClassName}");

            Route::get( "{$name}/{id}",                         ["as" => ".{$name}.view",      "uses" => "{$controller}@view"]);
            Route::get( "{$prefix}{$name}",                     ["as" => ".{$name}.index",     "uses" => "{$controller}@index"]);
            Route::get( "{$prefix}{$name}/create",              ["as" => ".{$name}.create",    "uses" => "{$controller}@create"]);
            Route::post("{$name}/store",                        ["as" => ".{$name}.store",     "uses" => "{$controller}@store"]);
            Route::post("{$name}/{id}/update",                  ["as" => ".{$name}.update",    "uses" => "{$controller}@store"]);
            Route::get( '{$name}/{' . $entity . '}/destroy',    ["as" => ".{$name}.destroy",   "uses" => "{$controller}@destroy"]);
        }
    }
}
