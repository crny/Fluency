<?php

// Define routes
$app->get('/', function () use ($app) {
    // Sample log message
    $app->log->info("Slim-Skeleton '/' route");
    // Render index view
    $app->render('index.phtml');
});

$app->group('/ni', function() use($app){
    $app->get('/hello', function() use($app){
        $app->halt(500, 'Error');
    });
});
