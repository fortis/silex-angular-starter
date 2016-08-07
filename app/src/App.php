<?php

namespace App;

use Bugsnag\Silex\Provider\BugsnagServiceProvider;
use Moust\Silex\Provider\CacheServiceProvider;
use Silex\Application;
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

class App extends Application
{
    use \Silex\Application\MonologTrait;

    private $settings;

    const CONFIG_PATH = SILEX_ROOT.'/app/config';
    const STORAGE_PATH = SILEX_ROOT.'/app/storage';
    const RESOURCES_PATH = SILEX_ROOT.'/resources';

    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this['debug'] = false;

        // Load settings.
        $this->settings = file_exists(self::CONFIG_PATH.'/settings.yml')
          ? Yaml::parse(file_get_contents(self::CONFIG_PATH.'/settings.yml'))
          : array();

        // Swift mailer.
        $this->register(new SwiftmailerServiceProvider());
        // Assets.
        $this->register(new \Silex\Provider\AssetServiceProvider(), [
          'assets.version'        => 'v1',
          'assets.version_format' => '%s?version=%s',
          'assets.base_path'      => '/public',
        ]);
        // Twig.
        $this->register(new TwigServiceProvider(), [
          'twig.path' => [self::RESOURCES_PATH.'/views'],
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
            $this->register(new DoctrineProfilerServiceProvider());
        }

        // Load and bind routes.
        $routes = new Routes($this);
        $routes->bindRoutes();
    }

}
