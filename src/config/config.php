<?php
return [
    'uploader' => [
        'types' => [
            [
                'name' => 'default',
                'location' => [
                    'type' => 'file',
                    'path' => '/assets/image/default/',
                ],
                'rules' => 'required|image'
            ],
            [
                'name' => 'default-file',
                'location' => [
                    'type' => 'file',
                    'path' => '/assets/file/default/',
                ],
                'rules' => 'required'
            ]
        ],
    ]
];