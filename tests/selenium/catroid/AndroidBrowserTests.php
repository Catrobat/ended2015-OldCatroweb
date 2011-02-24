<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010  Catroid development team 
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'testsBootstrap.php';

class AndroidBrowserTests extends PHPUnit_Framework_TestCase
{
    private $selenium;
    private $path;
  
    public function setUp()
    {
        $this->path = TESTS_BASE_PATH.'catroid/';
        $this->selenium = new Testing_Selenium("*custom ./android_browser.sh", $this->path);
        $this->selenium->start();
    }

    public function tearDown()
    {
        $this->selenium->stop();
    }
    
    public function testHeaderLogoClick()
    {     
        $this->selenium->open('thumbnail/thumbnailUploader');
        $this->selenium->waitForPageToLoad(10000);
        $this->assertRegExp("/Catroid Website/", $this->selenium->getTitle());
        $this->assertTrue($this->selenium->isTextPresent("Catroid [alpha]"));
        $this->selenium->runScript("window.location=document.getElementById('link_indexmain').href;");
        $this->selenium->waitForPageToLoad(10000);
        $this->assertEquals($this->path . 'index', $this->selenium->getLocation());
        $this->assertTrue($this->selenium->isTextPresent("Catroid [alpha]"));
    }    

    public function testIndexTextPresent()
    {
        $this->selenium->open($this->path);  
        $this->assertRegExp("/Catroid Website/", $this->selenium->getTitle());
        $this->assertTrue($this->selenium->isTextPresent("Catroid [alpha]"));        
        $this->assertTrue($this->selenium->isTextPresent("Download Catroid"));
    }

}

?>

