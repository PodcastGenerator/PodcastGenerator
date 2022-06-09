<?php

############################################################
# PODCAST GENERATOR
#
# Created by the Podcast Generator Development Team
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

namespace PodcastGenerator;

/**
 * Represents a user and their profile data.
 */
class User implements \JsonSerializable
{
    /**
     * The name of the user for logging into the site.
     *
     * @var string
     */
    private readonly string $username;

    /**
     * The hash value of the user's password.
     *
     * @var string
     */
    private string $passwordHash = '';

    /**
     * An indicator that the user has unsaved changes.
     *
     * @var boolean
     */
    private bool $dirty;

    /**
     * Creates a new User object from the provided parameters.
     *
     * @param string $username   The name of the user.
     * @param mixed $properties  An associative array of user properties, or a
     *                           string of the user's password hash. Optional.
     */
    public function __construct($username, $properties = null)
    {
        $this->username = $username;

        if (is_string($properties)) {
            // If $properties is a string instead of an associative array or
            // object, then it's the hashed password.
            $this->passwordHash = $properties;
        } elseif (is_array($properties) || is_object($properties)) {
            // Array or object, assume each key exists
            foreach ($properties as $key => $value) {
                if ($key === 'username' || $key === 'dirty') {
                    continue; // don't set these properties here!
                } elseif ($key === 'password') {
                    $this->passwordHash = $value;
                    continue;
                }
                $this->{$key} = $value;
            }
        }

        $this->dirty = false;
    }

    /**
     * Gets if the User object has unsaved changes.
     *
     * @return boolean
     */
    public function isDirty(): bool
    {
        return $this->dirty;
    }

    /**
     * Gets the name of the user.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Gets the hash value of the user's password.
     *
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * Sets the hash value of the user's password.
     *
     * @param string $hashedPassword  The pre-hashed password for the user.
     * @return void
     */
    public function setPasswordHash($hashedPassword): void
    {
        $this->passwordHash = $hashedPassword;
        $this->dirty = true;
    }

    /**
     * Sets the hash value of the user's password from the provided plain text password.
     *
     * @param string $plainPassword  The plain text password for the user.
     * @return void
     *
     * This produces the password hash using the password_hash() function and
     * the default password hashing algorithm.
     */
    public function setPassword($plainPassword): void
    {
        $this->setPasswordHash(password_hash($plainPassword, PASSWORD_DEFAULT));
    }

    public function jsonSerialize(): mixed
    {
        $this->dirty = false;
        return [
            'username' => $this->username,
            'password' => $this->passwordHash
        ];
    }
}
