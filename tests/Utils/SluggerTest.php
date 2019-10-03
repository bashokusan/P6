<?php

namespace App\Tests\Util;

use App\Utils\Slugger;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for the app utils.
 *
 * execute with vendor/bin/simple-phpunit
 * or bin/phpunit tests/Util/SluggerTest.php
 */
class SluggerTest extends TestCase
{
    /**
    * @dataProvider getSlugs
    */
    public function testSlug(string $string, string $slug){
        $this->assertSame($slug, Slugger::slugify($string));
    }

    public function getSlugs()
    {
        yield ['Lorem Ipsum', 'lorem-ipsum'];
        yield ['  Lorem Ipsum  ', 'lorem-ipsum'];
        yield [' lOrEm  iPsUm  ', 'lorem-ipsum'];
        yield ['!Lorem Ipsum!', '!lorem-ipsum!'];
        yield ['lorem-ipsum', 'lorem-ipsum'];
        yield ['lorem 日本語 ipsum', 'lorem-日本語-ipsum'];
        yield ['lorem русский язык ipsum', 'lorem-русский-язык-ipsum'];
        yield ['lorem العَرَبِيَّة‎‎ ipsum', 'lorem-العَرَبِيَّة‎‎-ipsum'];
    }
}
