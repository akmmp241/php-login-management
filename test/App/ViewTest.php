<?php

namespace Akmalmp\BelajarPhpMvc\App;

use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    public function testRender()
    {
        View::render('Home/index', [
            'title' => 'PHP LOGIN MANAGEMENT'
        ]);

        $this->expectOutputRegex('[PHP LOGIN MANAGEMENT]');
//        $this->expectOutputRegex('[html]');
//        $this->expectOutputRegex('[body]');
//        $this->expectOutputRegex('[Login Management]');
//        $this->expectOutputRegex('[Login]');
//        $this->expectOutputRegex('[Register]');
    }

}
