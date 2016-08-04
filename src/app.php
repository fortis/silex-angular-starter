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

class App extends Application {
  use \Silex\Application\MonologTrait;

  private $settings;

  function __construct(array $values = []) {
    parent::__construct($values);

    // Load settings.
    $this->settings = Yaml::parse(
      file_get_contents(__DIR__ . '/../app/config/settings.yml')
    );

    // Register service providers.
    $this->register(new PimpleDumpProvider());
    $this->register(new SwiftmailerServiceProvider());

    // Whoops provider.
    $this->register(new WhoopsServiceProvider());

    // Monolog provider.
    $this->register(new MonologServiceProvider(), [
        'monolog.logfile' => __DIR__ . '/../app/storage/log/development.log',
      ]
    );

    // Assets provider.
    $this->register(new \Silex\Provider\AssetServiceProvider(), array(
      'assets.version' => 'v1',
      'assets.version_format' => '%s?version=%s',
      'assets.base_path' => '/public'
    ));

    // Twig provider.
    $this->register(new TwigServiceProvider(), [
      'twig.path' => [
        __DIR__ . '/../resources/views',
      ],
    ]);

    // Web profiler provider.
    $this->register(new HttpFragmentServiceProvider());
    $this->register(new ServiceControllerServiceProvider());
    $this->register(new WebProfilerServiceProvider(), [
        'profiler.cache_dir'    => __DIR__ . '/../app/storage/cache/profiler',
        'profiler.mount_prefix' => '/_profiler', // this is the default
      ]
    );

    // Doctrine DBAL Profiler.
    $this->register(new DoctrineProfilerServiceProvider());

    // Bugsnag Provider.
    $this->register(new BugsnagServiceProvider(), [
        'bugsnag.options' => [
          'apiKey' => $this->settings['bugsnag.api_key'],
        ],
      ]
    );

    // Cache provider.
    $this->register(new CacheServiceProvider(), [
        'cache.options' => [
          'driver'    => 'file',
          'cache_dir' => __DIR__ . '/../app/storage/cache',
        ],
      ]
    );

    // Configure database connection.
    $this->register(new DoctrineServiceProvider(), [
        'db.options' => $this->settings['db.options'],
      ]
    );
  }

}
