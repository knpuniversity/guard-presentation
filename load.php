<?php

use AppBundle\Entity\User;

$loader = require __DIR__.'/app/autoload.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();

$em = $container->get('doctrine.orm.entity_manager');

$em->createQuery('DELETE FROM AppBundle:User')->execute();
$user = new User('weaverryan', 'ryan@knpuniversity.com');

$em->persist($user);
$em->flush();

