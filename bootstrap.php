<?php
require_once 'vendor/autoload.php';

use Doctrine\ORM\Tools\Setup,
    Doctrine\ORM\EntityManager,
    Doctrine\Common\EventManager as EventManager,
    Doctrine\ORM\Events,
    Doctrine\ORM\Configuration,
    Doctrine\Common\Cache\ArrayCache as Cache,
    Doctrine\Common\Annotations\AnnotationRegistry,
    Doctrine\Common\Annotations\AnnotationReader,
    Doctrine\Common\ClassLoader;

use Silex\Provider\SessionServiceProvider;
use Silex\Provider\SecurityServiceProvider;


$cache = new Doctrine\Common\Cache\ArrayCache;
$annotationReader = new Doctrine\Common\Annotations\AnnotationReader;

$cachedAnnotationReader = new Doctrine\Common\Annotations\CachedReader(
    $annotationReader, // use reader
    $cache // and a cache driver
);

$annotationDriver = new Doctrine\ORM\Mapping\Driver\AnnotationDriver(
    $cachedAnnotationReader, // our cached annotation reader
    array(__DIR__ . DIRECTORY_SEPARATOR . 'src')
);

$driverChain = new Doctrine\ORM\Mapping\Driver\DriverChain();
$driverChain->addDriver($annotationDriver,'SON');

$config = new Doctrine\ORM\Configuration;
$config->setProxyDir('/tmp');
$config->setProxyNamespace('Proxy');
$config->setAutoGenerateProxyClasses(true); // this can be based on production config.
// register metadata driver
$config->setMetadataDriverImpl($driverChain);
// use our allready initialized cache driver
$config->setMetadataCacheImpl($cache);
$config->setQueryCacheImpl($cache);

AnnotationRegistry::registerFile(__DIR__. DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'doctrine' . DIRECTORY_SEPARATOR . 'orm' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Doctrine' . DIRECTORY_SEPARATOR . 'ORM' . DIRECTORY_SEPARATOR . 'Mapping' . DIRECTORY_SEPARATOR . 'Driver' . DIRECTORY_SEPARATOR . 'DoctrineAnnotations.php');

$evm = new Doctrine\Common\EventManager();
$em = EntityManager::create(
    array(
        'driver'  => 'pdo_mysql',
        'host'    => '127.0.0.1',
        'port'    => '3306',
        'user'    => 'root',
        'password'  => 'root',
        'dbname'  => 'db_aulas',
    ),
    $config,
    $evm
);

$app = new Silex\Application();
$app['debug'] = true;

$app['user_repository'] = function ($app) use ($em) {
    $user = new \SON\Entity\User;
    $repo = $em->getRepository('SON\Entity\User');
    $repo->setPasswordEncoder($app['security.encoder_factory']->getEncoder($user));

    return $repo;
};

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/views',
]);

$app->register(new SessionServiceProvider());
$app->register(new SecurityServiceProvider(),[
    'security.firewalls' => [
        'admin' => [
            'anonymous' => true,
            'pattern' => '^/',
            'form' => [
                'login_path' => '/login',
                'check_path' => '/admin/login_check'
            ],
            'users' => function() use ($app) {
                return $app['user_repository'];
            },
            'logout' => [
                'logout_path' => '/admin/logout'
            ]
        ]
    ]
]);

$app['security.access_rules'] = [
    [
        '^/admin', 'ROLE_ADMIN'
    ]
];

return $app;