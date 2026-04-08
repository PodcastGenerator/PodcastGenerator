<?php declare(strict_types=1);

namespace PodcastGenerator\Tests;

// phpcs:disable
require_once(__DIR__ . '/../PodcastGenerator/vendor/autoload.php');
// phpcs:enable

use PHPUnit\Framework\TestCase;
use PodcastGenerator\Configuration;
use PodcastGenerator\User;
use PodcastGenerator\UserExistsException;
use PodcastGenerator\UserManager;
use stdClass;

/**
 * @covers PodcastGenerator\UserManager
 * @uses PodcastGenerator\Configuration::offsetGet
 * @uses PodcastGenerator\User
 */
class UserManagerTest extends TestCase
{
    private const CONFIG_KEY = 'users_json';
    private const INITIAL_JSON = '{"admin": "$2y$10$sOifKaCuf3spO6d5SMsUm.B3YDasw/zLv6koOPhxTY0hisRKkczxW","testuser":"$2y$10$H2FyjbIQo95JcaPspqZKqOnfo6QIV0N4ugumByui6CkqQFqw1KIJO"}';

    private $users_json;

    protected function setUp(): void
    {
        $this->users_json = self::INITIAL_JSON;
    }

    private function mockConfigureGet($key)
    {
        if ($key == self::CONFIG_KEY) {
            return $this->users_json;
        }
        return null;
    }

    private function mockConfigureSet($key, $value, $saveImmediately)
    {
        if ($key == self::CONFIG_KEY) {
            $this->users_json = $value;
        }
        return true;
    }

    private function addMockGet($configMock)
    {
        return $configMock
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with($this->equalTo(self::CONFIG_KEY))
            ->will($this->returnCallback(function ($key) { return $this->mockConfigureGet($key); }));
    }

    private function addMockSet($configMock)
    {
        return $configMock
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo(self::CONFIG_KEY),
                $this->anything(),
                $this->anything()
            )
            ->will($this->returnCallback(function ($k, $v, $i) { return $this->mockConfigureSet($k, $v, $i); }));
    }

    public function testCanBeCreatedFromConfigurationObject()
    {
        // Arrange
        $configMock = $this->createMock(Configuration::class);

        // Act
        $sut = new UserManager($configMock);

        $this->assertInstanceOf(UserManager::class, $sut);
    }

    public function testDoesNotEagerlyLoadUsers()
    {
        // Arrange
        $configMock = $this->createMock(Configuration::class);
        $configMock
            ->expects($this->never())
            ->method('get')
            ->with($this->equalTo(self::CONFIG_KEY));

        // Act
        new UserManager($configMock);
    }

    public function testCanLoadUsersFromConfigurationValue()
    {
        // Arrange
        $configMock = $this->createMock(Configuration::class);
        $this->addMockGet($configMock);

        $sut = new UserManager($configMock);

        // Act
        $users = $sut->getUsers();

        // Assert
        $this->assertContains('admin', array_keys($users));
        $this->assertInstanceOf(User::class, $users['admin']);
    }

    public function testCanGetUserByUserName()
    {
        // Arrange
        $configMock = $this->createMock(Configuration::class);
        $this->addMockGet($configMock);

        $sut = new UserManager($configMock);

        // Act
        $user = $sut->getUserByName('admin');

        // Assert
        $this->assertNotNull($user);
        $this->assertInstanceOf(User::class, $user);
    }

    public function testWillGetNullForNonexistentUserName()
    {
        // Arrange
        $configMock = $this->createMock(Configuration::class);
        $this->addMockGet($configMock);

        $sut = new UserManager($configMock);

        // Act
        $user = $sut->getUserByName('nonexistent');

        // Assert
        $this->assertNull($user);
    }

    public function testCannotAddExistingUser()
    {
        // Arrange
        $configMock = $this->createMock(Configuration::class);
        $this->addMockGet($configMock);

        $sut = new UserManager($configMock);

        // Act
        $result = $sut->addUser(new User('admin', []));

        // Assert
        $this->assertFalse($result);
    }

    public function testCanAddNewUser()
    {
        // Arrange
        $configMock = $this->createMock(Configuration::class);
        $this->addMockGet($configMock);
        $this->addMockSet($configMock);

        $sut = new UserManager($configMock);

        // Act
        $username = 'newuser';
        $user = new User($username, []);
        $result = $sut->addUser($user);

        // Assert
        $this->assertTrue($result);

        $this->assertNotEquals(self::INITIAL_JSON, $this->users_json);

        $jsonObj = json_decode($this->users_json, true);
        $this->assertContains($username, array_keys($jsonObj));
    }

    public function testCanDeleteExistingUserByName()
    {
        // Arrange
        $configMock = $this->createMock(Configuration::class);
        $this->addMockGet($configMock);
        $this->addMockSet($configMock);

        $sut = new UserManager($configMock);

        // Act
        $username = 'testuser';
        $result = $sut->deleteUser($username);

        // Assert
        $this->assertTrue($result);

        $this->assertNotEquals(self::INITIAL_JSON, $this->users_json);

        $jsonObj = json_decode($this->users_json, true);
        $this->assertNotContains($username, array_keys($jsonObj));
    }

    public function testCanDeleteExistingUserByUserObject()
    {
        // Arrange
        $configMock = $this->createMock(Configuration::class);
        $this->addMockGet($configMock);
        $this->addMockSet($configMock);

        $sut = new UserManager($configMock);

        // Act
        $username = 'testuser';
        $user = new User($username, []);
        $result = $sut->deleteUser($user);

        // Assert
        $this->assertTrue($result);

        $this->assertNotEquals(self::INITIAL_JSON, $this->users_json);

        $jsonObj = json_decode($this->users_json, true);
        $this->assertNotContains($username, array_keys($jsonObj));
    }

    public function testCanDeleteNonexistingUserWithoutError()
    {
        // Arrange
        $configMock = $this->createMock(Configuration::class);
        $this->addMockGet($configMock);
        $configMock
            ->expects($this->never())
            ->method('set')
            ->with(
                $this->equalTo(self::CONFIG_KEY),
                $this->anything(),
                $this->anything()
            );

        $sut = new UserManager($configMock);

        // Act
        $username = 'nonexistent';
        $result = $sut->deleteUser($username);

        // Assert
        $this->assertTrue($result);

        $this->assertEquals(self::INITIAL_JSON, $this->users_json);
    }

    public function testCanUpdateExistingUser()
    {
        // Arrange
        $configMock = $this->createMock(Configuration::class);
        $this->addMockGet($configMock);
        $this->addMockSet($configMock);

        $sut = new UserManager($configMock);

        // Act
        $username = 'testuser';
        $password = 'newpassword';
        $user = new User($username, []);
        $user->setPassword($password);

        $result = $sut->updateUser($user);

        // Assert
        $this->assertTrue($result);

        $this->assertNotEquals(self::INITIAL_JSON, $this->users_json);

        $jsonObj = json_decode($this->users_json, true);
        $jsonUser = $jsonObj[$username];
        if (is_string($jsonUser)) {
            $this->assertTrue(password_verify($password, $jsonUser));
        } else {
            $this->assertTrue(password_verify($password, $jsonUser['password']));
        }
    }

    public function testWillNotUpdateNewUser()
    {
        // Arrange
        $configMock = $this->createMock(Configuration::class);
        $this->addMockGet($configMock);
        $configMock
            ->expects($this->never())
            ->method('set')
            ->with(
                $this->equalTo(self::CONFIG_KEY),
                $this->anything(),
                $this->anything()
            );

        $sut = new UserManager($configMock);

        // Act
        $username = 'nonexistent';
        $user = new User($username, []);
        $result = $sut->updateUser($user);

        // Assert
        $this->assertFalse($result);

        $this->assertEquals(self::INITIAL_JSON, $this->users_json);
    }

    public function testCanChangeUserPasswordForExistingUser()
    {
        // Arrange
        $configMock = $this->createMock(Configuration::class);
        $this->addMockGet($configMock);
        $this->addMockSet($configMock);

        $sut = new UserManager($configMock);

        // Act
        $username = 'testuser';
        $password = 'testCanChangeUserPassword()';

        $result = $sut->changeUserPassword($username, $password);

        // Assert
        $this->assertTrue($result);

        $this->assertNotEquals(self::INITIAL_JSON, $this->users_json);

        $jsonObj = json_decode($this->users_json, true);
        $jsonUser = $jsonObj[$username];
        if (is_string($jsonUser)) {
            $this->assertTrue(password_verify($password, $jsonUser));
        } else {
            $this->assertTrue(password_verify($password, $jsonUser['password']));
        }
    }

    public function testWillNotChangeUserPasswordForUnknownUser()
    {
        // Arrange
        $configMock = $this->createMock(Configuration::class);
        $this->addMockGet($configMock);
        $configMock
            ->expects($this->never())
            ->method('set')
            ->with(
                $this->equalTo(self::CONFIG_KEY),
                $this->anything(),
                $this->anything()
            );

        $sut = new UserManager($configMock);

        // Act
        $username = 'nonexistent';
        $password = 'newpassword';

        $result = $sut->changeUserPassword($username, $password);

        // Assert
        $this->assertFalse($result);

        $this->assertEquals(self::INITIAL_JSON, $this->users_json);
    }
}
