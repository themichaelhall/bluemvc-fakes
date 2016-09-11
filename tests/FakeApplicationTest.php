<?php

use BlueMvc\Fakes\FakeApplication;
use DataTypes\FilePath;

/**
 * Test FakeApplication class.
 */
class FakeApplicationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test default constructor of FakeApplication.
     */
    public function testDefaultConstructor()
    {
        $fakeApplication = new FakeApplication();

        $this->assertSame(FilePath::parse(getcwd() . DIRECTORY_SEPARATOR)->__toString(), $fakeApplication->getDocumentRoot()->__toString());
    }
}
