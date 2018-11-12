<?php

namespace Tests\AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $kernel = self::bootKernel();
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
    }

    public function testGet()
    {
        $client = static::createClient();

        $client->request('GET', '/users/');

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(0, $responseData);
    }
}
