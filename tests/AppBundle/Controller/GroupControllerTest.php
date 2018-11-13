<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GroupControllerTest extends WebTestCase
{
    const GROUP_NAME = 'Group One';
    const GROUP_NAME_NEW = 'Group New';

    public function testEmptyGroups()
    {
        $client = static::createClient();

        $client->request('GET', '/groups/');

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(0, $responseData);
    }

    public function testCreateGroupWithWrongName()
    {
        $client = static::createClient();

        $client->request('POST', '/groups/', [
            'name' => '',
        ]);

        $response = $client->getResponse();

        $this->assertSame(400, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(1, $responseData);

        $this->assertArrayHasKey('property_path', $responseData[0]);
        $this->assertArrayHasKey('message', $responseData[0]);

        $this->assertSame('name', $responseData[0]['property_path']);
        $this->assertSame('This value should not be blank.', $responseData[0]['message']);
    }

    public function testCreateGroupCorrectly()
    {
        $client = static::createClient();

        $client->request('POST', '/groups/', [
            'name' => self::GROUP_NAME,
        ]);

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(2, $responseData);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertArrayHasKey('group_id', $responseData);
        $this->assertSame('Group has been added successfully.', $responseData['message']);
        $this->assertSame(1, $responseData['group_id']);

        return $responseData['group_id'];
    }

    public function testGroups()
    {
        $client = static::createClient();

        $client->request('GET', '/groups/');

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(1, $responseData);

        $this->assertSame(self::GROUP_NAME, $responseData[0]['name']);
    }

    /**
     * @depends testCreateGroupCorrectly
     *
     * @param int $groupId
     */
    public function testModifyGroupIncorrectly($groupId)
    {
        $client = static::createClient();

        $client->request('PUT', '/groups/' . $groupId . '/', [
            'name'  => '',
        ]);

        $response = $client->getResponse();

        $this->assertSame(400, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(1, $responseData);
    }

    /**
     * @depends testCreateGroupCorrectly
     *
     * @param int $groupId
     * @return int
     */
    public function testModifyGroupCorrectly($groupId)
    {
        $client = static::createClient();

        $client->request('PUT', '/groups/' . $groupId . '/', [
            'name'  => self::GROUP_NAME_NEW,
        ]);

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(2, $responseData);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertArrayHasKey('group_id', $responseData);
        $this->assertSame('Group has been updated successfully.', $responseData['message']);
        $this->assertSame(1, $responseData['group_id']);

        return $responseData['group_id'];
    }

    public function testGroupsAfterModification()
    {
        $client = static::createClient();

        $client->request('GET', '/groups/');

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(1, $responseData);

        $this->assertSame(self::GROUP_NAME_NEW, $responseData[0]['name']);
    }
}
