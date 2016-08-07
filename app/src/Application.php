<?php

namespace App;

use Bugsnag\Silex\Provider\BugsnagServiceProvider;
use Moust\Silex\Provider\CacheServiceProvider;
use Silex\Application as BaseApplication;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use Sorien\Provider\DoctrineProfilerServiceProvider;
use Sorien\Provider\PimpleDumpProvider;
use Symfony\Component\Yaml\Yaml;
use WhoopsSilex\WhoopsServiceProvider;

class Application extends BaseApplication
{
    use \Silex\Application\MonologTrait;

    private $settings;

    const APP_ROOT = __DIR__.'/../../';
    const CONFIG_PATH = self::APP_ROOT.'/app/config';
    const STORAGE_PATH = self::APP_ROOT.'/app/storage';
    const SRC_PATH = self::APP_ROOT.'/app/src';
    const RESOURCES_PATH = self::APP_ROOT.'/resources';

    public function __construct(array $values = [])
    {
        parent::__construct($values);

        // Mount routes.
        $this->mount('/', new CommonControllerProvider());
        $this->mount('/partials', new PartialsControllerProvider());
        $this->get('{undefinedRoute}',
          function (Application $app, $undefinedRoute) {
              return $app['twig']->render('layout.twig');
          })->assert('undefinedRoute', '([A-z\d-\/_.]+)?');

        // Load settings.
        $this->settings = file_exists(self::CONFIG_PATH.'/settings.yml')
          ? Yaml::parse(file_get_contents(self::CONFIG_PATH.'/settings.yml'))
          : [];

        // Swift mailer.
        $this->register(new SwiftmailerServiceProvider());
        // Assets.
        $this->register(new \Silex\Provider\AssetServiceProvider(), [
          'assets.version'        => 'v1',
          'assets.version_format' => '%s?version=%s',
          'assets.base_path'      => '/',
        ]);
        // Twig.
        $this->register(new TwigServiceProvider(), [
          'twig.path'    => [self::SRC_PATH.'/Views'],
          'twig.options' => ['cache' => self::STORAGE_PATH.'/cache/twig'],
        ]);
        // Bugsnag.
        if (!empty($this->settings['bugsnag.api_key'])) {
            $this->register(new BugsnagServiceProvider(), [
                'bugsnag.options' => [
                  'apiKey' => $this->settings['bugsnag.api_key'],
                ],
              ]
            );
        }

        // Cache.
        $this->register(new CacheServiceProvider(), [
            'cache.options' => [
              'driver'    => 'file',
              'cache_dir' => self::STORAGE_PATH.'/cache',
            ],
          ]
        );
        // Configure database connection.
        if (!empty($this->settings['db.options'])) {
            $this->register(new DoctrineServiceProvider(), [
                'db.options' => $this->settings['db.options'],
              ]
            );
        }
        // Debug tools.
        if ($this['debug']) {
            // Pimple dump.
            $this->register(new PimpleDumpProvider());
            // Whoops.
            $this->register(new WhoopsServiceProvider());
            // Monolog.
            $this->register(new MonologServiceProvider(), [
                'monolog.logfile' => self::STORAGE_PATH.'/log/development.log',
              ]
            );
            // Web Profiler.
            $this->register(new HttpFragmentServiceProvider());
            $this->register(new ServiceControllerServiceProvider());
            $this->register(new WebProfilerServiceProvider(), [
                'profiler.cache_dir'    => self::STORAGE_PATH.'/cache/profiler',
                'profiler.mount_prefix' => '/_profiler', // this is the default
              ]
            );
            // Doctrine DBAL Profiler.
            if (!empty($this->settings['db.options'])) {
                $this->register(new DoctrineProfilerServiceProvider());
            }
        }
    }

}
