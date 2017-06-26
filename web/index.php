<?php
require_once __DIR__.'/../bootstrap.php';

$app->get('/ola/{nome}', function($nome) use ($app) {
    return $app['twig']->render('ola.twig', ['nome' => $nome]);
});

$app->get('/link', function() use ($app) {
   return $app['twig']->render('link.twig');
})->bind('link');

$app->get('/link2/{nome}', function($nome) use ($app) {
    return $app['twig']->render('link.twig');
})->bind('link2');

$app->get('/criaAdmin', function() use ($app) {
    $repo = $app['user_repository'];
    $repo->createAdminUser('admin', 'admin');
    return 'Usuário criado';
});

$app->get('/login', function(\Symfony\Component\HttpFoundation\Request $request) use ($app) {
   return $app['twig']->render('login.twig', [
       'error' => $app['security.last_error']($request),
       'last_username' => $app['session']->get('_security.last_username')
   ]);
})->bind('login');

$app->get('/', function() use($app) {
    return $app['twig']->render('index.twig',[
        'username' =>  $app['security.token_storage']->getToken()->getUser()
    ]);
});

$app->run();