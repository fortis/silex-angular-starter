<?php

namespace App;

use Silex\Api\ControllerProviderInterface;

class CommonControllerProvider implements ControllerProviderInterface
{
    public function connect(\Silex\Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function (Application $app) {
            return $app['twig']->render('layout.twig');
        });

        return $controllers;
    }
}
