<?php

namespace App\Tests;

use App\App;
use Silex\WebTestCase;

class ExampleTest extends WebTestCase
{

    public function createApplication()
    {
        $app = new App();
        $app['debug'] = true;
        unset($app['exception_handler']);

        return $app;
    }

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $response = $this->call('GET', '/');

        $this->assertEquals(200, $response->getStatusCode());
    }

}