<?php declare(strict_types=1);

namespace PodcastGenerator\Tests;

// phpcs:disable
require_once(__DIR__ . '/../PodcastGenerator/vendor/autoload.php');
// phpcs:enable

use PHPUnit\Framework\TestCase;
use PodcastGenerator\Configuration;

/**
 * @covers PodcastGenerator\Configuration
 */
class ConfigurationTest extends TestCase
{
    private $filename;

    protected function setUp(): void
    {
        $this->filename = tempnam(sys_get_temp_dir(), "PGT");

        file_put_contents($this->filename, "<?php
\$podcastgen_version = '1.2.3'; // Version

\$absoluteurl = '/tmp/tests/podcastgen'; // The location on the server

\$stringvar = 'test'; // This is a string var

\$intvar = 1; // This is an integer var

\$floatvar = 1.2; // This is a float var

\$boolvar = true; // This is a bool var

\$falsevar = false; // This is also a bool var

\$anyvar = 'foo';

// C-style comment lines are ignored
# so are hash comment lines

// leading tabs are stripped from config file lines
\t\$tabvar = 'tab';

\$emptyvar = '';

\$nullvar = null;

\$double_quoted = \"there is no \\ backslash in this string\";
\$single_quoted = 'there is a \\ backslash in this string';

\$indexed[0] = 'indexed 0';
\$indexed[1] = 'indexed 1';
");
    }

    protected function tearDown(): void
    {

        if (!($this->hasFailed() && str_contains($this->getName(), 'WriteValuesToFile'))) {
            unlink($this->filename);
        }
    }

    public function testCanBeCreatedFromFilePath()
    {
        $this->assertInstanceOf(
            Configuration::class,
            Configuration::load($this->filename)
        );
    }

    public function testIgnoresLeadingTabsInConfigFile()
    {
        $sut = Configuration::load($this->filename);

        $value = $sut['tabvar'];

        $this->assertEquals('tab', $value);
    }

    public function testCannotSetVersionValue()
    {
        $this->expectError();

        $sut = Configuration::load($this->filename);
        $podcastgen_version = $sut['podcastgen_version'];

        $sut['podcastgen_version'] = 'test';

        $this->assertEquals($podcastgen_version, $sut['podcastgen_version']);
    }

    public function testCannotSetAbsoluteurlValue()
    {
        $this->expectError();

        $sut = Configuration::load($this->filename);
        $absoluteurl = $sut['absoluteurl'];

        $sut['absoluteurl'] = 'test';

        $this->assertEquals($absoluteurl, $sut['absoluteurl']);
    }

    public function testCanGetArrayOfConfigurationKeys()
    {
        $sut = Configuration::load($this->filename);

        $keys = $sut->keys();

        $this->assertIsArray($keys);
        foreach ($keys as $key) {
            $this->assertTrue(isset($sut[$key]));
        }
    }

    public function testCanUseIssetToDetermineValueExists()
    {
        $sut = Configuration::load($this->filename);

        $this->assertTrue(isset($sut['stringvar']));
    }

    public function testCannotGetNonexistentValue()
    {
        $this->expectError();

        $sut = Configuration::load($this->filename);

        $value = $sut['nonexistent'];

        $this->assertArrayNotHasKey('nonexistent', $sut->keys());
        $this->assertFalse(isset($value));
    }

    public function testCannotSetNonexistentValue()
    {
        $this->expectError();

        $sut = Configuration::load($this->filename);

        $sut['nonexistent'] = 'test';

        $this->assertArrayNotHasKey('nonexistent', $sut->keys());
    }

    public function testCanGetDoubleQuotedStringValue()
    {
        $sut = Configuration::load($this->filename);

        $value = $sut['double_quoted'];

        $this->assertEquals('there is no  backslash in this string', $value);
    }

    public function testCanGetSingleQuotedSingleValue()
    {
        $sut = Configuration::load($this->filename);

        $value = $sut['single_quoted'];

        $this->assertEquals('there is a \\ backslash in this string', $value);
    }

    public function testCanGetIntegerValue()
    {
        $sut = Configuration::load($this->filename);

        $value = $sut['intvar'];

        $this->assertIsInt($value);
    }

    public function testCanGetFloatValue()
    {
        $sut = Configuration::load($this->filename);

        $value = $sut['floatvar'];

        $this->assertIsFloat($value);
    }

    public function testCanGetBoolValue()
    {
        $sut = Configuration::load($this->filename);

        $value = $sut['boolvar'];

        $this->assertIsBool($value);
    }

    public function testCanGetNullValue()
    {
        $sut = Configuration::load($this->filename);

        $value = $sut['nullvar'];

        $this->assertNull($value);
    }

    public function testCanSetStringValue()
    {
        $sut = Configuration::load($this->filename);
        $new = 'testest';

        $sut['stringvar'] = $new;

        $this->assertEquals($new, $sut['stringvar']);
    }

    public function testCanSetBooleanValue()
    {
        $sut = Configuration::load($this->filename);
        $new = !($sut['boolvar']);

        $sut['boolvar'] = $new;

        $this->assertEquals($new, $sut['boolvar']);
    }

    public function testCanSetIntegerValue()
    {
        $sut = Configuration::load($this->filename);
        $new = 123;

        $sut['intvar'] = $new;

        $this->assertEquals($new, $sut['intvar']);
    }

    public function testCanSetFloatValue()
    {
        $sut = Configuration::load($this->filename);
        $new = 42.333;

        $sut['floatvar'] = $new;

        $this->assertEquals($new, $sut['floatvar']);
    }

    public function testCanSetNumericValue()
    {
        $sut = Configuration::load($this->filename);
        $new = '42.333';

        $sut['floatvar'] = $new;

        $this->assertEquals($new, $sut['floatvar']);
        $this->assertFalse($new === $sut['floatvar']);
    }

    public function testCanSetOtherValueAsString()
    {
        $sut = Configuration::load($this->filename);
        $new = new \Exception('test');

        $sut['anyvar'] = $new;

        $this->assertEquals($new->__toString(), $sut['anyvar']);
    }

    public function testCanSetValueToNull()
    {
        $sut = Configuration::load($this->filename);

        $sut['anyvar'] = null;

        $this->assertTrue(isset($sut['anyvar']));
        $this->assertNull($sut['anyvar']);
    }

    public function testCanSetValueToNullWithUnset()
    {
        $sut = Configuration::load($this->filename);

        unset($sut['stringvar']);

        $this->assertTrue(isset($sut['stringvar']));
        $this->assertNull($sut['stringvar']);
    }

    public function testCanWriteValuesToFileOnExplicitCallToSet()
    {
        $sut = Configuration::load($this->filename);
        $new = "this string has 'quote' and \"double quote\" characters in it,\na line break, and also a \\ backslash";

        $this->assertTrue($sut->set('stringvar', $new, true));

        $sut2 = Configuration::load($this->filename);
        $this->assertEquals($new, $sut2['stringvar']);
    }

    public function testCanWriteValuesToFileOnSave()
    {
        $sut = Configuration::load($this->filename);
        $new = "this string has 'quote' and \"double quote\" characters in it,\na line break, and also a \\ backslash";

        $sut['stringvar'] = $new;
        $sut->save();

        $sut2 = Configuration::load($this->filename);
        $this->assertEquals($new, $sut2['stringvar']);
    }

    public function testCanGetValuesFromIndexedKeys()
    {
        $sut = Configuration::load($this->filename);

        $this->assertEquals('indexed 0', $sut['indexed[0]']);
    }

    public function testCanSetValuesToIndexedKeys()
    {
        $sut = Configuration::load($this->filename);
        $new = 'test value';

        $sut['indexed[1]'] = $new;

        $this->assertEquals($new, $sut['indexed[1]']);
    }

    public function testCanReloadValuesFromFile()
    {
        $sut = Configuration::load($this->filename);
        $old = $sut['stringvar'];
        $new = 'foobarbaz';

        $sut['stringvar'] = $new;
        $sut->reload();

        $this->assertEquals($old, $sut['stringvar']);
    }
}
