<?php
use \Symfony\Component\HttpFoundation\Response;

$enquete = $app['controllers_factory'];

$enquete->get('/', function() {
    return new Response('Home enquete');
});

$enquete->get('/show', function() {
    return new Response('Exibir Enquete');
});

return $enquete;