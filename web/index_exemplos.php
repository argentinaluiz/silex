<?php
require_once __DIR__.'/../vendor/autoload.php';

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$app['debug'] = true;

$data = [
    'nome' => 'Davi',
    'empresa' => 'Overalt'
];

$app->get('/blog/{id}', function(Silex\Application $app, $id) use($data) {
    if(!isset($data['idade']))
        $app->abort(404, 'Idade não existe!!');
    return $id;
});

$app->get('/artigo/{id}/{nome}', function($id, $nome) {
    return new Response("Olá Mundo {$id} - {$nome}<br>", 200);
})
    //->convert('id', function($id) { return (int) $id; }) // Converte valores
    ->assert('id', '\d+') // Valida o tipo de dados
    ->value('nome', 'Davi') // Coloca valor default
    ->bind('articles'); // Nomeia uma rota!

$app->get('/json', function() use($app) {
    return $app->json(['nome' => 'Davi Gomes'], 200);
});

$app->before(function(Request $request) {
   echo "Middleware Application BEFORE<br>";
}, Silex\Application::EARLY_EVENT); // Roda o middleware antes de qualquer coisa, é possível mudar

$app->after(function (Request $request, Response $response){ // Roda logo antes do Response para o browser
    echo "Middleware Application AFTER<br>";
});

$app->finish(function() {
    echo "Middleware Application FINISH<br>";
}); // Roda logo após do Response

$app->get('/middleware', function() {
    return "Executou a Rota<br>";
})
    ->before(function() { echo "Middleware Route BEFORE<br>"; }) // Middleware de rota, posso ter vários before
    ->after(function(){ echo "Middleware Route AFTER<br>"; }); // Middleware de rota, posso ter vários
$app->run();