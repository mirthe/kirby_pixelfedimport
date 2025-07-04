<?php
$kirby = kirby();
if (($user = $kirby->user()) && $user->role()->id() === 'admin') {

    Kirby::plugin('mirthe/pixelfed-import', [
        'options' => [
            'token' => option('pixelfed.token'),
            'userid' => option('pixelfed.userid'),
            'limit' => option('pixelfed.limit'),
            'since_id' => option('pixelfed.since_id'),
            'contentsubfolder' => option('pixelfed.contentsubfolder')
        ],

        'routes' => [
            [
                'pattern' => 'pixelfed/getaccount',
                'action'  => function () {
                    include 'getaccount.php';
                }
            ],
            [
                'pattern' => 'pixelfed/getlatest',
                'action'  => function () {
                    $exportdir = __DIR__ . '/temp/'; // TODO move to config, use my fotofeed folder
                    include 'getlatest.php';
                }
            ],
            [
                'pattern' => 'pixelfed/pickone',
                'action'  => function () {
                    include 'pickone.php';
                }
            ],
            [
                'pattern' => 'pixelfed/getone',
                'action'  => function () {
                    include 'getone.php';
                }
            ]
            
        ]
        
    ]);

;}
