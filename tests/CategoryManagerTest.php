<?php declare(strict_types=1);

namespace PodcastGenerator\Tests;

// phpcs:disable
require('../PodcastGenerator/vendor/autoload.php');
// phpcs:enable

use PHPUnit\Framework\TestCase;
use PodcastGenerator\Models\Category;
use PodcastGenerator\CategoryManager;

/**
 * @covers PodcastGenerator\CategoryManager
 */
class CategoryManagerTest extends TestCase
{
    private $filename;

    protected function setUp(): void
    {
        $this->filename = tempnam(sys_get_temp_dir(), "PGT");
        file_put_contents($this->filename, <<<ENDXML
<?xml version="1.0" encoding="utf-8"?>
<PodcastGenerator>
    <category>
        <id>uncategorized</id>
        <description>Uncategorized</description>
    </category>
    <category>
        <id>test_1</id>
        <description>Test 1</description>
    </category>
    <category>
        <id>test_2</id>
        <description>Test 2</description>
    </category>
</PodcastGenerator>
ENDXML);
    }

    protected function tearDown(): void
    {
        unlink($this->filename);
    }

    public function testCanBeCreatedFromFilePath()
    {
        $this->assertInstanceOf(
            CategoryManager::class,
            new CategoryManager($this->filename)
        );
    }

    public function testCanGetAllCategories()
    {
        $sut = new CategoryManager($this->filename);

        $categories = $sut->getCategories();

        $this->assertIsIterable($categories);
        foreach ($categories as $category) {
            $this->assertInstanceOf(Category::class, $category);
        }
    }

    public function testCanGetCategoryBySlug()
    {
        $slug = 'test_1';
        $sut = new CategoryManager($this->filename);

        $category = $sut->getCategoryBySlug($slug);

        $this->assertNotNull($category);
        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals($category->slug, $slug);
    }

    public function testCannotGetNonexistentCategory()
    {
        $sut = new CategoryManager($this->filename);

        $category = $sut->getCategoryBySlug('nonexistent');

        $this->assertNull($category);
    }

    public function testCanAddCategory()
    {
        $slug = 'added_category';
        $sut = new CategoryManager($this->filename);
        $new = new Category($slug, 'Added Category');

        $result = $sut->addCategory($new);

        $this->assertTrue($result);

        $category = $sut->getCategoryBySlug($slug);

        $this->assertNotNull($category);
        $this->assertEquals($category->name, $new->name);
    }

    public function testCannotAddCategoryForExistingSlug()
    {

        $slug = 'test_1';
        $sut = new CategoryManager($this->filename);
        $new = new Category($slug, 'Added Category');

        $this->expectExceptionMessage('Category already exists');

        $sut->addCategory($new);
    }

    public function testCannotAddInvalidCategory()
    {
        $slug = 'invalid_category%%%';
        $sut = new CategoryManager($this->filename);
        $new = new Category($slug, 'Invalid Category');

        $this->assertFalse($new->isValid());
        $this->expectExceptionMessage('Invalid category data');

        $sut->addCategory($new);
    }

    public function testCanSaveChangesOnAdd()
    {
        $slug = 'added_category';
        $sut = new CategoryManager($this->filename);
        $category = new Category($slug, 'Added Category');

        $result = $sut->addCategory($category, true);

        $this->assertTrue($result);

        $newManager = new CategoryManager($this->filename);
        $newCategory = $newManager->getCategoryBySlug($slug);

        $this->assertEquals($newCategory->name, $category->name);
    }

    public function testCanUpdateCategory()
    {
        $slug = 'test_1';
        $name = 'Updated Category';
        $sut = new CategoryManager($this->filename);

        $category = $sut->getCategoryBySlug($slug);
        $category->name = $name;

        $result = $sut->updateCategory($category);

        $this->assertTrue($result);

        $updated = $sut->getCategoryBySlug($slug);

        $this->assertNotSame($updated, $category);
        $this->assertEquals($updated->name, $category->name);
    }

    public function testCannotUpdateNonexistentCategory()
    {
        $sut = new CategoryManager($this->filename);
        $category = new Category('nonexistent', 'No Such Category');

        $result = $sut->updateCategory($category);

        $this->assertFalse($result);

        $updated = $sut->getCategoryBySlug($category->slug);

        $this->assertNull($updated);
    }

    public function testCannotUpdateInvalidCategory()
    {
        $slug = 'test_1';
        $sut = new CategoryManager($this->filename);
        $category = $sut->getCategoryBySlug($slug);

        $origName = $category->name;
        $category->name = '';

        $this->assertFalse($category->isValid());
        $this->expectExceptionMessage('Invalid category data');

        $sut->updateCategory($category);

        $nonUpdated = $sut->getCategoryBySlug($slug);

        $this->assertNotNull($nonUpdated);
        $this->assertEquals($nonUpdated->name, $origName);
    }

    public function testCanSaveChangesOnUpdate()
    {
        $slug = 'test_1';
        $newName = 'Updated Category';
        $sut = new CategoryManager($this->filename);

        $category = $sut->getCategoryBySlug($slug);
        $oldCategoryName = $category->name;
        $category->name = $newName;

        $result = $sut->updateCategory($category, true);

        $this->assertTrue($result);

        $newManager = new CategoryManager($this->filename);
        $updatedCategory = $newManager->getCategoryBySlug($slug);

        $this->assertEquals($updatedCategory->name, $newName);
    }

    public function testCanDeleteCategory()
    {
        $sut = new CategoryManager($this->filename);
        $category = $sut->getCategoryBySlug('test_1');

        $result = $sut->deleteCategory($category);

        $this->assertTrue($result);

        $deleted = $sut->getCategoryBySlug($category->slug);

        $this->assertNull($deleted);
    }

    public function testCanDeleteCategoryBySlug()
    {
        $sut = new CategoryManager($this->filename);
        $category = $sut->getCategoryBySlug('test_1');

        $result = $sut->deleteCategoryBySlug($category->slug);

        $this->assertTrue($result);

        $deleted = $sut->getCategoryBySlug($category->slug);

        $this->assertNull($deleted);
    }

    public function testIgnoresDeleteForNonexistentCategory()
    {
        $slug = 'nonexistent';
        $sut = new CategoryManager($this->filename);

        $result = $sut->deleteCategoryBySlug($slug);

        $this->assertTrue($result);
    }

    public function testCanSaveChangesOnDelete()
    {
        $slug = 'test_2';
        $sut = new CategoryManager($this->filename);

        $category = $sut->getCategoryBySlug($slug);

        $result = $sut->deleteCategory($category, true);

        $this->assertTrue($result);

        $newManager = new CategoryManager($this->filename); // load new contents of file
        $this->assertNull($newManager->getCategoryBySlug($slug));
    }

    public function testCanSaveChangesOnDeleteBySlug()
    {
        $slug = 'test_2';
        $sut = new CategoryManager($this->filename);

        $result = $sut->deleteCategoryBySlug($slug, true);

        $this->assertTrue($result);

        $newManager = new CategoryManager($this->filename); // load new contents of file
        $this->assertNull($newManager->getCategoryBySlug($slug));
    }

    public function testCanExplicitlySaveChangesToDisk()
    {
        $sut = new CategoryManager($this->filename);
        $originalCategories = [];
        foreach ($sut->getCategories() as $cat) {
            $originalCategories[$cat->slug] = $cat;
        }

        $added = new Category('added_category', 'Added Category');
        $sut->addCategory($added);

        $updated = new Category('test_1', 'Updated Category');
        $sut->updateCategory($updated);

        $deletedSlug = 'test_2';
        $sut->deleteCategory($originalCategories[$deletedSlug]);

        $result = $sut->saveChanges();

        $this->assertTrue($result);

        $newManager = new CategoryManager($this->filename); // load new contents of file
        $newCategories = [];
        foreach ($newManager->getCategories() as $cat) {
            $newCategories[$cat->slug] = $cat;
        }

        $this->assertTrue(isset($newCategories[$added->slug]));
        $this->assertEquals($newCategories[$added->slug]->name, $added->name);

        $this->assertEquals($newCategories[$updated->slug]->name, $updated->name);

        $this->assertFalse(isset($newCategories[$deletedSlug]));
    }
}
