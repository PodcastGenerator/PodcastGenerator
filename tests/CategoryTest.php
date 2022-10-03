<?php declare(strict_types=1);

namespace PodcastGenerator\Tests;

// phpcs:disable
require('../PodcastGenerator/vendor/autoload.php');
// phpcs:enable

use PHPUnit\Framework\TestCase;
use PodcastGenerator\Models\Category;

/**
 * @covers PodcastGenerator\Models\Category
 */
class CategoryTest extends TestCase
{
    public function testCanCreateWithSlugAndName()
    {
        $slug = 'slug';
        $name = 'Name';

        $sut = new Category($slug, $name);

        $this->assertEquals($slug, $sut->slug);
        $this->assertEquals($name, $sut->name);
    }

    public function testCanCreateFromDescriptionOnly()
    {
        $name = 'Test Category Name';
        $slug = 'test_category_name'; // lowercase, bad characters replaced

        $sut = Category::newFromDescription($name);

        $this->assertInstanceOf(Category::class, $sut);

        $this->assertEquals($slug, $sut->slug);
        $this->assertEquals($name, $sut->name);
    }

    public function testCannotCreateFromEmptyStringDescription()
    {
        $sut = Category::newFromDescription('');

        $this->assertFalse($sut);
    }

    public function testTreatsEmptyStringSlugAsInvalid()
    {
        $sut = new Category('', 'Invalid');

        $results = $sut->validate();

        $this->assertContains('Missing slug value', $results);
        $this->assertFalse($sut->isValid());
    }

    public function testTreatsSlugWithNonAlphanumericCharactersAsInvalid()
    {
        $sut = new Category('#$%^&*"\'', 'Invalid');

        $results = $sut->validate();

        $this->assertContains('Slug value contains invalid characters', $results);
        $this->assertFalse($sut->isValid());
    }

    public function testTreatsSlugWithUppercaseLettersAsInvalid()
    {
        $sut = new Category('INVALID', 'Invalid');

        $results = $sut->validate();

        $this->assertContains('Slug value must be lower case', $results);
        $this->assertFalse($sut->isValid());
    }

    public function testTreatsEmptyStringNameAsInvalid()
    {
        $sut = new Category('invalid', '');

        $results = $sut->validate();

        $this->assertContains('Missing name value', $results);
        $this->assertFalse($sut->isValid());
    }

    public function testTreatsValidCategoryValuesAsValid()
    {
        $sut = new Category('valid', 'Valid');

        $results = $sut->validate();

        $this->assertEmpty($results);
        $this->assertTrue($sut->isValid());
    }
}
