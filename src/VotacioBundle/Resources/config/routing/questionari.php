<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('admin_questionari_index', new Route(
    '/',
    array('_controller' => 'VotacioBundle:Questionari:index'),
    array(),
    array(),
    '',
    array(),
    array('GET')
));

$collection->add('admin_questionari_show', new Route(
    '/{id}/show',
    array('_controller' => 'VotacioBundle:Questionari:show'),
    array(),
    array(),
    '',
    array(),
    array('GET')
));
$collection->add('admin_questionari_savePreguntes', new Route(
    '/{id}/savePreguntes',
     array('_controller' => 'VotacioBundle:Questionari:savePreguntes'),
    array(),
    array(),
    '',
    array(),
    array('GET','POST')
));
$collection->add('admin_questionari_deletePregunta', new Route(
    '/{id}/deletePregunta',
     array('_controller' => 'VotacioBundle:Questionari:deletePregunta'),
    array(),
    array(),
    '',
    array(),
    array('GET','POST')
));
$collection->add('admin_questionari_new', new Route(
    '/new',
    array('_controller' => 'VotacioBundle:Questionari:new'),
    array(),
    array(),
    '',
    array(),
    array('GET', 'POST')
));

$collection->add('admin_questionari_edit', new Route(
    '/{id}/edit',
    array('_controller' => 'VotacioBundle:Questionari:edit'),
    array(),
    array(),
    '',
    array(),
    array('GET', 'POST')
));

$collection->add('admin_questionari_delete', new Route(
    '/{id}/delete',
    array('_controller' => 'VotacioBundle:Questionari:delete'),
    array(),
    array(),
    '',
    array(),
    array('DELETE')
));

return $collection;
