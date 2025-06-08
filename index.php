<?php
$kirby = kirby();
if (($user = $kirby->user()) && $user->role()->id() === 'admin') {

    Kirby::plugin('mirthe/pixelfed-import', [
        'options' => [
            'token' => option('pixelfed.token'),
            'userid' => option('pixelfed.userid'),
            'limit' => option('pixelfed.limit'),
            'since_id' => option('pixelfed.since_id')
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
                    $exportdir = __DIR__ . '/temp/';
                    include 'getlatest.php';
                }
            ]
        ]
        
    ]);

;}
