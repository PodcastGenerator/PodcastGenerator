<?php declare(strict_types=1);

############################################################
# PODCAST GENERATOR
#
# Created by the Podcast Generator Development Team
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

namespace PodcastGenerator;

// phpcs:disable
require_once(__DIR__ . '/../vendor/autoload.php'); // @codeCoverageIgnore
// phpcs:enable

use PodcastGenerator\Configuration;
use PodcastGenerator\User;

class UserManager
{
    private Configuration $config;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    private function getUsersJson(): mixed
    {
        return json_decode($this->config['users_json'], true);
    }

    private function setUsersJson(array $users): bool
    {
        return $this->config->set('users_json', json_encode($users), true);
    }

    /**
     * Adds a new user to Podcast Generator.
     *
     * @param string $user  An object representing the new user.
     * @return boolean      `true` if the user was successfully added;
     *                       otherwise, `false`.
     */
    public function addUser(User $user): bool
    {
        $username = $user->getUsername();
        $users = $this->getUsersJson();
        if (array_key_exists($username, $users)) {
            return false;
        }
        $users[$username] = $user;
        return $this->setUsersJson($users);
    }

    /**
     * Deletes an existing user from Podcast Generator.
     *
     * @param string|User $user  The name of the user to delete, or an object
     *                           representing the user.
     * @return boolean           `true` if the user was successfully deleted;
     *                           otherwise, `false`.
     */
    public function deleteUser(string|User $user): bool
    {
        if (is_a($user, User::class)) {
            $username = $user->getUsername();
        } else {
            $username = $user;
        }

        $users = $this->getUsersJson();
        if (!array_key_exists($username, $users)) {
            return true; // pretend we deleted a user that doesn't exist
        }

        unset($users[$username]);
        return $this->setUsersJson($users);
    }

    /**
     * Updates details of a user in Podcast Generator.
     *
     * @param User $user  An object representing the user being modified.
     * @return boolean    `true` if the user was successfully updated;
     *                    otherwise, `false`.
     */
    public function updateUser(User $user): bool
    {
        $username = $user->getUsername();
        $users = $this->getUsersJson();
        if (!array_key_exists($username, $users)) {
            return false; // don't update users that don't exist!
        }

        $users[$username] = $user;
        return $this->setUsersJson($users);
    }

    /**
     * Gets an array of all users in Podcast Generator.
     *
     * @return User[]
     */
    public function getUsers(): array
    {
        $users = [];
        $usersJson = $this->getUsersJson();
        foreach ($usersJson as $username => $properties) {
            $users[$username] = new User($username, $properties);
        }
        return $users;
    }

    /**
     * Gets an object representing the named user.
     *
     * @param string $username  The name of the user to retrieve.
     * @return User|null        An object representing the user, or `null` if
     *                          the user does not exist.
     */
    public function getUserByName(string $username): ?User
    {
        $users = $this->getUsersJson();
        if (array_key_exists($username, $users)) {
            return new User($username, $users[$username]);
        }
        return null;
    }

    /**
     * Updates the password of the named user.
     *
     * @param string $username     The name of the user to update.
     * @param string $newPassword  The unencrypted new password of the user.
     * @return boolean             `true` if the new password was successfully
     *                             set; otherwise, `false`.
     */
    public function changeUserPassword(string $username, string $newPassword): bool
    {
        $user = $this->getUserByName($username);
        if ($user == null) {
            return false;
        }
        $user->setPassword($newPassword);
        return $this->updateUser($user);
    }

    /**
     * Checks if the named user exists on the website.
     *
     * @param string $username  The name of the user to look for.
     * @return boolean          `true` if the named user has an account;
     *                          otherwise, `false`.
     */
    public function userExists(string $username): bool
    {
        return $this->getUserByName($username) != null;
    }
}
