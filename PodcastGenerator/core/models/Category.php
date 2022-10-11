<?php

############################################################
# PODCAST GENERATOR
#
# Created by the Podcast Generator Development Team
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

namespace PodcastGenerator\Models;

// phpcs:disable
require_once(__DIR__ . '/../misc/functions.php');
// phpcs:enable

/**
 * Represents a category for organizing episodes of the podcast.
 */
class Category
{
    /** Characters that are not permitted in category slugs. */
    public const BAD_ID_CHARS = [' ', '&', '"', '\'', '<', '>', '%', '*', '#'];

    /**
     * The text slug used for referencing the category in URLs.
     *
     * @var string
     */
    public string $slug;

    /**
     * The display name of the category.
     *
     * @var string
     */
    public string $name;

    /**
     * Create a new instance of Category.
     *
     * @param string $slug  The text slug of the category.
     * @param string $name  The name of the category.
     */
    public function __construct(string $slug, string $name)
    {
        $this->slug = $slug;
        $this->name = $name;
    }

    /**
     * Create a new instance of Category, with an automatically generated slug.
     *
     * @param string $name     The name of the new category.
     * @return Category|false  A Category object, or false if $name is empty or null.
     */
    public static function newFromDescription(string $name): Category|false
    {
        if (empty($name)) {
            return false;
        }
        $slug = strtolower(str_replace(self::BAD_ID_CHARS, '_', $name));
        return new Category($slug, $name);
    }

    /**
     * Ensures that the Category object has valid properties.
     *
     * @return array  A list of validation errors (empty if the object is valid).
     */
    public function validate(): array
    {
        $errors = [];

        if ($this->slug == null || $this->slug == '') {
            $errors[] = _('Missing slug value');
        } else {
            if (str_contains_any($this->slug, self::BAD_ID_CHARS)) {
                $errors[] = _('Slug value contains invalid characters');
            }
            if (strtolower($this->slug) != $this->slug) {
                $errors[] = _('Slug value must be lower case');
            }
        }

        if ($this->name == null || $this->name == '') {
            $errors[] = _('Missing name value');
        }

        return $errors;
    }

    /**
     * Check whether or not the Category object is valid.
     *
     * @return boolean  `true` if the object is valid; otherwise, `false`.
     */
    public function isValid(): bool
    {
        return empty($this->validate());
    }
}
