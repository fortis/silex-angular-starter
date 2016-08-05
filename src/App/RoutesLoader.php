<?php

namespace App;

use Silex\Application;

class RoutesLoader
{

    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function bindRoutes()
    {
        $app = $this->app;

        $app->get('/', function (Application $app) {
            return $app['twig']->render('layout.twig');
        });

        $app->get('/partials/index', function (Application $app) {
            return $app['twig']->render('partials/index.twig');
        });

        $app->get('/partials/{category}/{action}',
          function (Application $app, $category, $action) {
              return $app['twig']->render(implode('/',
                  ['partials', $category, $action]).'.twig');
          })->value('action', 'index');

        $app->get('/partials/{category}/{action}/{id}',
          function (Application $app, $category, $action = 'index', $id) {
              return $app['twig']->render(implode('/',
                  ['partials', $category, $action]).'.twig');
          });

        $app->get('{undefinedRoute}',
          function (Application $app, $undefinedRoute) {
              return $app['twig']->render('layout.twig');
          })->assert('undefinedRoute', '([A-z\d-\/_.]+)?');
    }
}
