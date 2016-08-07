<?php

namespace App\Controllers;

use Silex\Api\ControllerProviderInterface;

class PartialsControllerProvider implements ControllerProviderInterface
{
    public function connect(\Silex\Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/index', function (Application $app) {
            return $app['twig']->render('partials/index.twig');
        });

        $controllers->get('/{category}/{action}',
          function (Application $app, $category, $action) {
              return $app['twig']->render(implode('/',
                  ['partials', $category, $action]).'.twig');
          })->value('action', 'index');

        $controllers->get('/{category}/{action}/{id}',
          function (Application $app, $category, $action = 'index', $id) {
              return $app['twig']->render(implode('/',
                  ['partials', $category, $action]).'.twig');
          });

        return $controllers;
    }
}
