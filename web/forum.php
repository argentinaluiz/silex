<?php
use \Symfony\Component\HttpFoundation\Response;

$forum = $app['controllers_factory'];

$forum->get('/', function() {
    return new Response('Home Fórum');
});

return $forum;