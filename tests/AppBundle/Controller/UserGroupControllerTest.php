<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserGroupControllerTest extends WebTestCase
{
    public function testModifyGroupAddUsers($groupId = 1)
    {
        $client = static::createClient();

        $client->request('PUT', '/groups/' . $groupId . '/', [
            'users_list' => [
                1,
                2,
            ],
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

    public function testGroupsAfterUsersAdd()
    {
        $client = static::createClient();

        $client->request('GET', '/groups/');

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(1, $responseData);

        $this->assertArrayHasKey('users', $responseData[0]);
        $this->assertCount(2, $responseData[0]['users']);
    }

    public function testModifyGroupRemoveUsers($groupId = 1)
    {
        $client = static::createClient();

        $client->request('PUT', '/groups/' . $groupId . '/', [
            'users_list' => [
                1,
            ],
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

    public function testGroupsAfterUsersRemove()
    {
        $client = static::createClient();

        $client->request('GET', '/groups/');

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(1, $responseData);

        $this->assertArrayHasKey('users', $responseData[0]);
        $this->assertCount(1, $responseData[0]['users']);
    }
}
