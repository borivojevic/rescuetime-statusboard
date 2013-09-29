<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();
$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__ . "/../config.json"));

$app->register(new \Silex\Provider\ServiceControllerServiceProvider());

$app['controllers.statusboards'] = $app->share(function ($app) {
    return new RescueTimeStatusboard\RescueTimeBoards($app['rescuetime-api-key']);
});

$app->get('/summary', 'controllers.statusboards:summary');
$app->get('/productivity_by_activity', 'controllers.statusboards:productivity_by_activity');
$app->get('/productivity_by_category', 'controllers.statusboards:productivity_by_category');
$app->get('/productivity_by_day', 'controllers.statusboards:productivity_by_day');

$app->run();
