<?php

declare(strict_types=1);

require('../PodcastGenerator/vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use PodcastGenerator\Configuration;

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

\$intvar = 10; // This is an integer var

\$floatvar = 1.2; // This is a float var

\$boolvar = true; // This is a bool var

\$anyvar = 'foo';
");
    }

    protected function tearDown(): void
    {
        unlink($this->filename);
    }

    public function testCanBeCreatedFromFilePath()
    {
        $this->assertInstanceOf(
            Configuration::class,
            Configuration::load($this->filename)
        );
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

    public function testCannotSetNonexistentValue()
    {
        $this->expectError();

        $sut = Configuration::load($this->filename);

        $sut['nonexistent'] = 'test';

        $this->assertArrayNotHasKey('nonexistent', $sut->keys());
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

    public function testCanSetOtherValueAsString()
    {
        $sut = Configuration::load($this->filename);
        $new = new Exception('test');

        $sut['anyvar'] = $new;

        $this->assertEquals($new->__toString(), $sut['anyvar']);
    }

    public function testCanWriteValuesToFileOnExplicitCallToSet()
    {
        $sut = Configuration::load($this->filename);
        $new = 'foobarbaz';

        $this->assertTrue($sut->set('stringvar', $new, true));

        $sut2 = Configuration::load($this->filename);
        $this->assertEquals($new, $sut2['stringvar']);
    }

    public function testCanWriteValuesToFileOnSave()
    {
        $sut = Configuration::load($this->filename);
        $new = 'foobarbaz';

        $sut['stringvar'] = $new;
        $sut->save();

        $sut2 = Configuration::load($this->filename);
        $this->assertEquals($new, $sut2['stringvar']);
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
