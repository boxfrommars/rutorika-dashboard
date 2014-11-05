<?php

namespace Rutorika\Dashboard\Uploader;

class UploadController extends \Controller
{
    public function handle()
    {
        $config = \Config::get('dashboard::uploader.types');
        \Log::debug($config);

        $typeName = \Input::has('type') ? \Input::get('type') : 'default';
        $typeConfig = app_array_find_where($config, 'name', $typeName);

        if ($typeConfig === null) {
            \App::abort(422, "type {$typeName} not found");
        }

        $file = \Input::file("file");

        $validator = \Validator::make(['file' => $file], ['file' => $typeConfig['rules']]);

        if ($validator->fails()) {
            return \Response::json(['success' => false, 'errors' => $validator->errors()]);
        } else {

            switch (array_get($typeConfig, 'location.type')) {
                case 'file':
                    $path = array_get($typeConfig, 'location.path');
                    $destinationPath = public_path() . $path;
                    $filename = uniqid() . '_' . $file->getClientOriginalName();
                    $file->move($destinationPath, $filename);

                    return \Response::json(['success' => true, 'path' => asset($path . $filename), 'filename' => $filename]);
                    break;
                default:
                    return \Response::json(['success' => false, 'errors' => ['location type required']]);
                    break;
            }
        }
    }
}