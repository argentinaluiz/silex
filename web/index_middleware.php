<?php
require_once __DIR__.'/../vendor/autoload.php';

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$app['debug'] = true;

$app['res'] = function() { // São iguais porque compartilham o objeto
    return new Response('OI');
};

//$app['res'] = $app->factory(function() { // São diferentes porque a cada chamada do serviço um novo objeto é criado, sendo eles independentes
//    return new Response('OI');
//});

$res1 = $app['res'];
$res2 = $app['res'];

if ($res1 === $res2) {
    echo "São Iguais";
} else {
    echo "São Diferentes";
}



// Declaração de Serviços
$app['pdo'] = function () {
   return new PDO('dsn', 'user', 'pass');
};

$app['pessoa'] = function() use ($app) { // Serviços com Pimple
    $pdo = $app['pdo'];
    return new Pessoa($pdo); // Injeção de Dependências
};

// Instância de Serviços
$pessoa = $app['pessoa']; // Gera o objeto pessoa

$app->mount('/enquete', include 'enquete.php');
$app->mount('/forum', include 'forum.php');

$app->run();