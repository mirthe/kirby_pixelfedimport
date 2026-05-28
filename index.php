<?php
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
                $kirby = kirby();
                if (($user = $kirby->user()) && $user->role()->id() === 'admin') {
                    include 'getaccount.php';
                }
            }
        ],
        [
            'pattern' => 'pixelfed/getlatest',
            'action'  => function () {
                $kirby = kirby();
                if (($user = $kirby->user()) && $user->role()->id() === 'admin') {
                    $exportdir = __DIR__ . '/temp/'; // TODO move to config, use my fotofeed folder
                    include 'getlatest.php';
                }
            }
        ],
        [
            'pattern' => 'pixelfed/pickone',
            'action'  => function () {
                $kirby = kirby();
                if (($user = $kirby->user()) && $user->role()->id() === 'admin') {
                    include 'pickone.php';
                }
            }
        ],
        [
            'pattern' => 'pixelfed/getone',
            'action'  => function () {
                $kirby = kirby();
                if (($user = $kirby->user()) && $user->role()->id() === 'admin') {
                    include 'getone.php';
                }
            }
        ]
    ]
]);
