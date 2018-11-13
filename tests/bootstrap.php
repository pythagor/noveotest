<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

require_once __DIR__ . '/../vendor/autoload.php';

$kernel = new AppKernel('test', true);
$kernel->boot();

/** @var EntityManager $em */
$em = $kernel->getContainer()->get('doctrine')->getManager();
$metadatas = $em->getMetadataFactory()->getAllMetadata();
if (!empty($metadatas)) {
    $tool = new SchemaTool($em);
    $tool->dropSchema($metadatas);
    try {
        $tool->createSchema($metadatas);
    } catch (\Exception $e) {
        echo $e->getMessage() . PHP_EOL;
        die();
    }
}
