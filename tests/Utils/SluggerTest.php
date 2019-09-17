<?php

namespace App\Tests\Util;

use App\Utils\Slugger;
use PHPUnit\Framework\TestCase;

class SluggerTest extends TestCase
{
    public function testSlug(){
        $slug = new Slugger();
        $result = $slug->slugify("un deux");
        $this->assertSame("un-deux", $result);
    }
}
