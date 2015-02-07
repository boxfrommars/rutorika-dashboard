<?php

namespace Rutorika\Dashboard\Uploader;

class UploadController extends \Controller
{
    public function handle()
    {
        $config = \Config::get('dashboard::uploader.types');

        $typeName = \Input::has('type') ? \Input::get('type') : 'default';
        $typeConfig = app_array_find_where($config, 'name', $typeName);

        if ($typeConfig === null) {
            \App::abort(422, "type {$typeName} not found");
        }

        $file = \Input::file('file');

        $validator = \Validator::make(['file' => $file], ['file' => $typeConfig['rules']]);

        if ($validator->fails()) {
            return \Response::json(['success' => false, 'errors' => $validator->errors()]);
        } else {

            switch (array_get($typeConfig, 'location.type')) {
                case 'file':
                    $path = array_get($typeConfig, 'location.path');

                    $filename = static::generateFilename($file);
                    $fileDestinationInfo = static::getDestinationInfo($filename, $path);

                    $file->move($fileDestinationInfo['destination'], $fileDestinationInfo['filename']);

                    return \Response::json(['success' => true, 'path' => $fileDestinationInfo['public_destination'], 'filename' => $fileDestinationInfo['assets_destination']]);
                    break;
                default:
                    return \Response::json(['success' => false, 'errors' => ['location type required']]);
                    break;
            }
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|array $file
     * @return string
     */
    public static function generateFilename($file)
    {
        $filename = md5(uniqid() . '_' . $file->getClientOriginalName());
        $extension = $file->getClientOriginalExtension();
        $filename = $extension ? $filename . '.' . $extension : $filename;

        return $filename;
    }

    public static function getDestinationInfo($filename, $path)
    {

        $splittedFilename = str_split($filename, 2);

        $subpath = implode('/', array_slice($splittedFilename, 0, 2));
        $filename = implode('', array_slice($splittedFilename, 2));

        return [
            'destination' => public_path() . $path . $subpath,
            'filename' => $filename,
            'public_destination' => $path . $subpath . '/' . $filename,
            'assets_destination' => $subpath . '/' . $filename
        ];
    }
}