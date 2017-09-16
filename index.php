<?php

require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();

// Registering Silex components
$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->register(new Silex\Provider\DoctrineServiceProvider(), [
    'db.options' => [
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/app.db',
    ],
]);

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));

// Setting debug mode
$app['debug'] = true;

// Defining services
$app['statistics_manager'] = function ($app) {
    return new Service\StatisticsManager($app['db']);
};

$app['view_record_factory'] = function ($app) {
    return new Service\ViewRecordFactory($app['validator']);
};

// Configuring routes
$app->get('/', 'App\\IndexController::getIndexAction')->bind('index');

$app->get('/stat', 'App\\IndexController::getStatAction')->bind('stat');

$app->post('/stat', 'App\\IndexController::postStatAction');

$app->run();
