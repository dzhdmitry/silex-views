<?php

require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();

$app['debug'] = true;

$app['statistics_manager'] = function ($app) {
    return new Service\StatisticsManager($app['db']);
};

$app->register(new Silex\Provider\DoctrineServiceProvider(), [
    'db.options' => [
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__ . '/app.db',
    ],
]);

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));

$app->get('/', 'App\\IndexController::getIndex')->bind('index');

$app->get('/stat', 'App\\IndexController::getStat')->bind('stat');

$app->post('/stat', 'App\\IndexController::postStat');

$app->run();
