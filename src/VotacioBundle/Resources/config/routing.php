<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();


$collection->add('votacio_homepage', new Route('/', array(
    '_controller' => 'VotacioBundle:Default:index',// se modifica el controller para que renderize resultats 30/05/2017
)));
$collection->add('votacio_registre', new Route('/registre/', array(
'_controller'=>'VotacioBundle:Default:registre')
        ));

$collection->add('votacio_registre_confirm', new Route('/registreConfirm/', array(
'_controller'=>'VotacioBundle:Default:registreConfirm'),
    array(),
    array(),
    '',
    array(),
    array('GET','POST')
        ));


$collection->add('votacio_regitre_mesInfo', new Route('{id}/registreMesInfo', array(
    '_controller'=>'VotacioBundle:Default:registreMesInfo'),
        array(),
        array(),
        '',
        array(),
        array('GET','POST')
));

$collection->add('votacio_registre_info', new Route('registreInfo/', array(
'_controller'=>'VotacioBundle:Default:registreInfo'),
    array(),
    array(),
    '',
    array(),
    array('GET','POST')
        ));


$collection->add('votacio_registreTest', new Route('/registreTest/', array(
'_controller'=>'VotacioBundle:Default:registreTest'),
    array(),
    array(),
    '',
    array(),
    array('GET','POST')
        ));

$collection->add('votacio_registreTestSms', new Route('/registreTestSms', array(
'_controller'=>'VotacioBundle:Default:registreTestSms'),
   array(),
   array(),
   '',
   array(),
   array('GET','POST')
       ));

$collection->add('default_questionari_votar', new Route(
    '/{id}/votar',
    array('_controller' => 'VotacioBundle:Default:votar'),
    array(),
    array(),
   '',
    array(),
    array('GET','POST')
));

$collection->add('votacio_adminLogin', new Route('/admin', array(
    '_controller' => 'VotacioBundle:Admin:login',
)));
$collection->add('pressencial', new Route('/admin/pressencial', array(
    '_controller' => 'VotacioBundle:Admin:pressencial',
)));
$collection->add('votacio_adminLogout', new Route('/admin/logout', array(
    '_controller' => 'VotacioBundle:Admin:logout',
)));
$collection->add('votacio_adminHomepage', new Route('/admin/homepage', array(
    '_controller' => 'VotacioBundle:Admin:index',
)));

$collection->add('votacio_adminCreate', new Route('/admin/createAdmin', array(
    '_controller' => 'VotacioBundle:Admin:createAdmin',
)));
$collection->add('votacio_adminShowResults', new Route('/admin/showResults', array(
    '_controller' => 'VotacioBundle:Admin:showResults',
)));
//Cal Executar-ho solament un cop per fer el Admin

return $collection;
