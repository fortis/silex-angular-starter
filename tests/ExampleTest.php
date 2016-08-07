<?php

namespace App\Tests;

use App\App;
use Silex\WebTestCase;

const SILEX_ROOT = __DIR__.'/../';

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
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Todos")'));
    }

}