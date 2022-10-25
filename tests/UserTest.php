<?php declare(strict_types=1);

namespace PodcastGenerator\Tests;

// phpcs:disable
require_once(__DIR__ . '/../PodcastGenerator/vendor/autoload.php');
// phpcs:enable

use PHPUnit\Framework\TestCase;
use PodcastGenerator\User;

/**
 * @covers PodcastGenerator\User
 */
class UserTest extends TestCase
{
    public function testCanBeCreatedWithUsername()
    {
        $username = 'username';

        $user = new User($username);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($username, $user->getUsername());
    }

    public function testCanBeCreatedWithUsernameAndPasswordHashString()
    {
        $username = 'username';
        $passwordHash = 'passwordHash'; // doesn't have to be real value

        $user = new User($username, $passwordHash);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($passwordHash, $user->getPasswordHash());
    }

    public function testCanBeCreatedWithUsernameAndPropertiesArray()
    {
        $username = 'username';
        $properties = ['password' => 'passwordHash']; // password is special key

        $user = new User($username, $properties);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($properties['password'], $user->getPasswordHash());
    }

    public function testCanBeCreatedWithUsernameAndPropertiesObject()
    {
        $username = 'username';
        $properties = new \stdClass();
        $properties->password = 'passwordHash';

        $user = new User($username, $properties);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($properties->password, $user->getPasswordHash());
    }

    public function testConstructorIgnoresUsernameKeyInPropertiesArray()
    {
        $username = 'username';
        $properties = ['username' => 'notUserName'];

        $user = new User($username, $properties);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($username, $user->getUsername());
        $this->assertNotEquals($properties['username'], $user->getUsername());
    }

    public function testConstructorIgnoresUsernameKeyInPropertiesObject()
    {
        $username = 'username';
        $properties = new \stdClass();
        $properties->username = 'notUserName';

        $user = new User($username, $properties);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($username, $user->getUsername());
        $this->assertNotEquals($properties->username, $user->getUsername());
    }

    public function testConstructorIgnoresDirtyKeyInPropertiesArray()
    {
        $username = 'username';
        $properties = ['dirty' => true];

        $user = new User($username, $properties);

        $this->assertInstanceOf(User::class, $user);
        $this->assertFalse($user->isDirty());
    }

    public function testConstructorIgnoresDirtyKeyInPropertiesObject()
    {
        $username = 'username';
        $properties = new \stdClass();
        $properties->dirty = true;

        $user = new User($username, $properties);

        $this->assertInstanceOf(User::class, $user);
        $this->assertFalse($user->isDirty());
    }

    public function testConstructorCreatesPropertyForUnrecognizedKeyInPropertiesArray()
    {
        $username = 'username';
        $properties = ['foo' => 123];

        $user = new User($username, $properties);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($properties['foo'], $user->foo);
    }

    public function testConstructorCreatesPropertyForUnrecognizedKeyInPropertiesObject()
    {
        $username = 'username';
        $properties = new \stdClass();
        $properties->foo = 123;

        $user = new User($username, $properties);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($properties->foo, $user->foo);
    }

    public function testCanSetPasswordHash()
    {
        $user = new User('username', array());
        $password = 'password';
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $user->setPasswordHash($passwordHash);

        $this->assertEquals($passwordHash, $user->getPasswordHash());
        $this->assertTrue($user->isDirty());
    }

    public function testCanSetPassword()
    {
        $user = new User('username', array());
        $password = 'password';

        $user->setPassword($password);

        $this->assertTrue(password_verify($password, $user->getPasswordHash()));
        $this->assertTrue($user->isDirty());
    }

    public function testJsonSerializeClearsDirtyFlag()
    {
        $user = new User('username', array());
        $password = 'password';
        $user->setPassword($password);

        $user->jsonSerialize();

        $this->assertFalse($user->isDirty());
    }

    public function testCanSerializeUsernameAndHashedPasswordToJson()
    {
        $user = new User('username', 'passwordHash');

        $result = $user->jsonSerialize();

        $this->assertEquals('username', $result['username']);
        $this->assertEquals('passwordHash', $result['password']);
    }
}
