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
 */
function addUser($username, $password_plain)
{
    global $config;
    $users =  json_decode($config['users_json'], true);
    // Check if user exists
    if (array_key_exists($username, $users)) {
        return false;
    }
    $users[$username] = password_hash($password_plain, PASSWORD_DEFAULT);
    return $config->set('users_json', str_replace('"', '\"', json_encode($users)), true);
}

/**
 * Deletes an existing user from the users collection.
 *
 * @param string $username  The username of the user to delete.
 * @return bool  Whether the user was successfully deleted.
 */
function deleteUser($username)
{
    global $config;
    $users =  json_decode($config['users_json'], true);
    unset($users[$username]);
    return $config->set('users_json', str_replace('"', '\"', json_encode($users)), true);
}

/**
 * Changes an existing user's password.
 *
 * @param string $username            The username of the user to update.
 * @param string $new_password_plain  The unencrypted new password of the user.
 * @return bool  Whether the user was successfully updated.
 */
function changeUserPassword($username, $new_password_plain)
{
    global $config;
    $users = json_decode($config['users_json'], true);
    // Check if user exists
    if (!array_key_exists($username, $users)) {
        return false;
    }
    $users[$username] = password_hash($new_password_plain, PASSWORD_DEFAULT);
    return $config->set('users_json', str_replace('"', '\"', json_encode($users)), true);
}

/**
 * Gets an array of all registered users.
 *
 * @return array  An array of users.
 */
function getUsers()
{
    global $config;
    return json_decode($config['users_json'], true);
}
