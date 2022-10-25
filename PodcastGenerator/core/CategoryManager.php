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

// phpcs:disable
require_once(__DIR__ . '/models/Category.php'); // @codeCoverageIgnore
// phpcs:enable

use Exception;
use SimpleXMLElement;
use PodcastGenerator\Models\Category;

/**
 * Provides category management for Podcast Generator.
 */
class CategoryManager
{
    private string $path;
    private ?\SimpleXMLElement $xml = null;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->xml = simplexml_load_file($path);
    }

    private static function mapFromXml(\SimpleXMLElement $element): Category
    {
        return new Category((string) $element->id, (string) $element->description);
    }

    public function getCategories(): iterable
    {
        foreach ($this->xml as $item) {
            yield self::mapFromXml($item);
        }
    }

    public function getCategoryBySlug(string $slug): ?Category
    {
        foreach ($this->getCategories() as $cat) {
            if ($cat->slug == $slug) {
                return $cat;
            }
        }
        return null;
    }

    public function addCategory(Category $category, bool $saveImmediately = false): bool
    {
        if (!$category->isValid()) {
            throw new Exception(_('Invalid category data'));
        }

        foreach ($this->xml as $item) {
            if ($item->id == $category->slug) {
                throw new Exception(_('Category already exists'));
            }
        }

        $item = $this->xml->addChild('category');
        $item->addChild('id', $category->slug);
        $item->addChild('description', htmlspecialchars($category->name));

        if ($saveImmediately) {
            return $this->saveChanges();
        }
        return true;
    }

    public function updateCategory(Category $category, bool $saveImmediately = false): bool
    {
        if (!$category->isValid()) {
            throw new Exception(_('Invalid category data'));
        }

        $found = false;

        foreach ($this->xml as $item) {
            if ($item->id == $category->slug) {
                $found = true;
                $item->description = $category->name;
            }
        }

        if ($found && $saveImmediately) {
            return $this->saveChanges();
        }

        return $found;
    }

    public function deleteCategory(Category $category, bool $saveImmediately = false): bool
    {
        return $this->deleteCategoryBySlug($category->slug, $saveImmediately);
    }

    public function deleteCategoryBySlug(string $slug, bool $saveImmediately = false): bool
    {
        foreach ($this->xml as $item) {
            if ($item->id == $slug) {
                $dom = dom_import_simplexml($item);
                $dom->parentNode->removeChild($dom);
            }
        }

        if ($saveImmediately) {
            return $this->saveChanges();
        }
        return true;
    }

    public function saveChanges(): bool
    {
        return $this->xml->asXML($this->path);
    }
}
