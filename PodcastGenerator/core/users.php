<?php

############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

/**
 * Adds a new user to the users collection.
 *
 * @param string $username        The username of the new user.
 * @param string $password_plain  The unencrypted password of the new user.
 * @return bool  Whether the user was successfully saved.
 * 
 * @deprecated 3.3      Use `$userManager->addUser()` directly
 */
function addUser($username, $password_plain)
{
    global $userManager;
    $user = new PodcastGenerator\User($username);
    $user->setPassword($password_plain);
    return $userManager->addUser($user);
}

/**
 * Deletes an existing user from the users collection.
 *
 * @param string $username  The username of the user to delete.
 * @return bool  Whether the user was successfully deleted.
 * 
 * @deprecated 3.3      Use `$userManager->deleteUser()` directly
 */
function deleteUser($username)
{
    global $userManager;
    return $userManager->deleteUser($username);
}

/**
 * Changes an existing user's password.
 *
 * @param string $username            The username of the user to update.
 * @param string $new_password_plain  The unencrypted new password of the user.
 * @return bool  Whether the user was successfully updated.
 * 
 * @deprecated 3.3      Use `$userManager->changeUserPassword()` directly
 */
function changeUserPassword($username, $new_password_plain)
{
    global $userManager;
    return $userManager->changeUserPassword($username, $new_password_plain);
}

/**
 * Gets an array of all registered users.
 *
 * @return array  An array of users.
 * 
 * @deprecated 3.3      Use `$userManager->getUsers()` directly
 */
function getUsers()
{
    // not mapped to $userManager->getUsers() because this returns a different
    // data type (associate array of string to string|object)
    global $userManager;
    $users = [];
    foreach ($userManager->getUsers() as $user) {
        $users[$user->getUsername()] = $user->getPasswordHash();
    }
    return $users;
}
