<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    const USER_EMAIL            = 'user@example.com';
    const USER_GROUP_EMAIL      = 'user.group@example.com';
    const USER_LAST_NAME        = 'User1Last';
    const USER_FIRST_NAME       = 'User1First';
    const USER_STATE_ACTIVE     = 1;
    const USER_STATE_NON_ACTIVE = 0;

    public function testEmptyUsers()
    {
        $client = static::createClient();

        $client->request('GET', '/users/');

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(0, $responseData);
    }

    public function testCreateUserWithWrongEmail()
    {
        $client = static::createClient();

        $client->request('POST', '/users/', [
            'email'     => 'wrong',
            'lastName'  => self::USER_LAST_NAME,
            'firstName' => self::USER_FIRST_NAME,
            'state'     => self::USER_STATE_ACTIVE,
        ]);

        $response = $client->getResponse();

        $this->assertSame(400, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(1, $responseData);

        $this->assertArrayHasKey('property_path', $responseData[0]);
        $this->assertArrayHasKey('message', $responseData[0]);

        $this->assertSame('email', $responseData[0]['property_path']);
        $this->assertSame('This value is not a valid email address.', $responseData[0]['message']);
    }

    public function testCreateUserWithWrongLastName()
    {
        $client = static::createClient();

        $client->request('POST', '/users/', [
            'email'     => self::USER_EMAIL,
            'lastName'  => '',
            'firstName' => self::USER_FIRST_NAME,
            'state'     => self::USER_STATE_ACTIVE,
        ]);

        $response = $client->getResponse();

        $this->assertSame(400, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(1, $responseData);

        $this->assertArrayHasKey('property_path', $responseData[0]);
        $this->assertArrayHasKey('message', $responseData[0]);

        $this->assertSame('lastName', $responseData[0]['property_path']);
        $this->assertSame('This value should not be blank.', $responseData[0]['message']);
    }

    public function testCreateUserWithWrongFirstName()
    {
        $client = static::createClient();

        $client->request('POST', '/users/', [
            'email'     => self::USER_EMAIL,
            'lastName'  => self::USER_LAST_NAME,
            'firstName' => '',
            'state'     => self::USER_STATE_ACTIVE,
        ]);

        $response = $client->getResponse();

        $this->assertSame(400, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(1, $responseData);

        $this->assertArrayHasKey('property_path', $responseData[0]);
        $this->assertArrayHasKey('message', $responseData[0]);

        $this->assertSame('firstName', $responseData[0]['property_path']);
        $this->assertSame('This value should not be blank.', $responseData[0]['message']);
    }

    public function testCreateUserWithWrongState()
    {
        $client = static::createClient();

        $client->request('POST', '/users/', [
            'email'     => self::USER_EMAIL,
            'lastName'  => self::USER_LAST_NAME,
            'firstName' => self::USER_FIRST_NAME,
            'state'     => null,
        ]);

        $response = $client->getResponse();

        $this->assertSame(400, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(1, $responseData);

        $this->assertArrayHasKey('property_path', $responseData[0]);
        $this->assertArrayHasKey('message', $responseData[0]);

        $this->assertSame('state', $responseData[0]['property_path']);
        $this->assertSame('This value should not be blank.', $responseData[0]['message']);
    }

    public function testCreateUserCorrectly()
    {
        $client = static::createClient();

        $client->request('POST', '/users/', [
            'email'     => self::USER_EMAIL,
            'lastName'  => self::USER_LAST_NAME,
            'firstName' => self::USER_FIRST_NAME,
            'state'     => self::USER_STATE_ACTIVE,
        ]);

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(2, $responseData);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertArrayHasKey('user_id', $responseData);
        $this->assertSame('User has been added successfully.', $responseData['message']);
        $this->assertSame(1, $responseData['user_id']);

        return $responseData['user_id'];
    }

    public function testUsers()
    {
        $client = static::createClient();

        $client->request('GET', '/users/');

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(1, $responseData);
    }

    /**
     * @depends testCreateUserCorrectly
     *
     * @param int $userId
     */
    public function testUsersId($userId)
    {
        $client = static::createClient();

        $client->request('GET', '/users/' . $userId . '/');

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertArrayHasKey('email', $responseData);
        $this->assertArrayHasKey('last_name', $responseData);
        $this->assertArrayHasKey('first_name', $responseData);
        $this->assertArrayHasKey('state', $responseData);

        $this->assertSame(self::USER_EMAIL, $responseData['email']);
        $this->assertSame(self::USER_LAST_NAME, $responseData['last_name']);
        $this->assertSame(self::USER_FIRST_NAME, $responseData['first_name']);
        $this->assertSame((bool)self::USER_STATE_ACTIVE, $responseData['state']);
    }

    /**
     * @depends testCreateUserCorrectly
     *
     * @param int $userId
     */
    public function testModifyUserIncorrectly($userId)
    {
        $client = static::createClient();

        $client->request('PUT', '/users/' . $userId . '/', [
            'email'     => 'wrong email',
            'lastName'  => '',
            'firstName' => '',
            'state'     => '',
        ]);

        $response = $client->getResponse();

        $this->assertSame(400, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(4, $responseData);
    }

    /**
     * @depends testCreateUserCorrectly
     *
     * @param int $userId
     * @return int
     */
    public function testModifyUserCorrectly($userId)
    {
        $client = static::createClient();

        $client->request('PUT', '/users/' . $userId . '/', [
            'email'     => self::USER_EMAIL,
            'lastName'  => self::USER_LAST_NAME,
            'firstName' => self::USER_FIRST_NAME,
            'state'     => self::USER_STATE_NON_ACTIVE,
        ]);

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(2, $responseData);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertArrayHasKey('user_id', $responseData);
        $this->assertSame('User has been updated successfully.', $responseData['message']);
        $this->assertSame(1, $responseData['user_id']);

        return $responseData['user_id'];
    }

    /**
     * @depends testModifyUserCorrectly
     *
     * @param int $userId
     */
    public function testUsersIdAfterModification($userId)
    {
        $client = static::createClient();

        $client->request('GET', '/users/' . $userId . '/');

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertArrayHasKey('email', $responseData);
        $this->assertArrayHasKey('last_name', $responseData);
        $this->assertArrayHasKey('first_name', $responseData);
        $this->assertArrayHasKey('state', $responseData);

        $this->assertSame(self::USER_EMAIL, $responseData['email']);
        $this->assertSame(self::USER_LAST_NAME, $responseData['last_name']);
        $this->assertSame(self::USER_FIRST_NAME, $responseData['first_name']);
        $this->assertSame((bool)self::USER_STATE_NON_ACTIVE, $responseData['state']);
    }

    public function testCreateUserWithGroupNotExisted()
    {
        $client = static::createClient();

        $client->request('POST', '/users/', [
            'email'     => self::USER_GROUP_EMAIL,
            'lastName'  => self::USER_LAST_NAME,
            'firstName' => self::USER_FIRST_NAME,
            'state'     => self::USER_STATE_ACTIVE,
            'group_id'     => 10,
        ]);

        $response = $client->getResponse();

        $this->assertSame(400, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(1, $responseData);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertSame('Group does not exist.', $responseData['message']);
    }

    public function testCreateUserWithGroup()
    {
        $client = static::createClient();

        $client->request('POST', '/users/', [
            'email'     => self::USER_GROUP_EMAIL,
            'lastName'  => self::USER_LAST_NAME,
            'firstName' => self::USER_FIRST_NAME,
            'state'     => self::USER_STATE_ACTIVE,
            'group_id'     => 1,
        ]);

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertCount(2, $responseData);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertArrayHasKey('user_id', $responseData);
        $this->assertSame('User has been added successfully.', $responseData['message']);
        $this->assertSame(2, $responseData['user_id']);

        return $responseData['user_id'];
    }

    /**
     * @depends testCreateUserWithGroup
     *
     * @param int $userId
     */
    public function testUsersIdWithGroup($userId)
    {
        $client = static::createClient();

        $client->request('GET', '/users/' . $userId . '/');

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertInternalType('array', $responseData);
        $this->assertArrayHasKey('email', $responseData);
        $this->assertArrayHasKey('last_name', $responseData);
        $this->assertArrayHasKey('first_name', $responseData);
        $this->assertArrayHasKey('state', $responseData);
        $this->assertArrayHasKey('group', $responseData);

        $this->assertSame(self::USER_GROUP_EMAIL, $responseData['email']);
        $this->assertSame(self::USER_LAST_NAME, $responseData['last_name']);
        $this->assertSame(self::USER_FIRST_NAME, $responseData['first_name']);
        $this->assertSame((bool)self::USER_STATE_ACTIVE, $responseData['state']);

        $this->assertInternalType('array', $responseData['group']);
        $this->assertArrayHasKey('name', $responseData['group']);
        $this->assertInternalType('string', $responseData['group']['name']);
    }
}
