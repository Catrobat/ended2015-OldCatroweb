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

/* Set TESTS_BASE_PATH in testsBootstrap.php to your catroid www-root */
require_once 'testsBootstrap.php';

class IndexTests extends PHPUnit_Framework_TestCase
{
  private $selenium;

  public function setUp()
  {
    $path= 'http://'.str_replace('http://', '', TESTS_BASE_PATH).'catroid/';
    $this->selenium = new Testing_Selenium("*firefox", $path);
    if (TESTS_SLOW_MODE==TRUE) {
      $this->selenium->setSpeed(TESTS_SLOW_MODE_SPEED);
    } else {
      $this->selenium->setSpeed(1);
    }
    $this->selenium->start();
  }

  public function tearDown()
  {
    $this->selenium->stop();
  }
  
  public function testIndexPage() {
    $this->selenium->open(TESTS_BASE_PATH);
    $this->selenium->waitForPageToLoad(10000);
    
    //test page title
    $this->assertRegExp("/Catroid Website/", $this->selenium->getTitle());
    
    //test catroid header text
    $this->assertTrue($this->selenium->isTextPresent("Catroid [beta]"));
    
    //test catroid download link
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@class='webHeadTitleDownload']/a"));
    $this->selenium->click("xpath=//div[@class='webHeadTitleDownload']/a");
    $this->selenium->selectWindow("_blank");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Catroid_0-4-3d.apk"));
    $this->assertTrue($this->selenium->isTextPresent("Paintroid_0-1-5-6b.apk"));
    $this->selenium->close();
    $this->selenium->selectWindow(null);
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@class='webHeadTitleName']/a"));
    $this->selenium->click("xpath=//div[@class='webHeadTitleName']/a");
    $this->selenium->selectWindow("_blank");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Catroid_0-4-3d.apk"));
    $this->assertTrue($this->selenium->isTextPresent("Paintroid_0-1-5-6b.apk"));
    $this->selenium->close();
    $this->selenium->selectWindow(null);
    
    //test home link
    $this->selenium->click("xpath=//a[1]"); //clicks first link on page (should be the home link)
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/catroid\/index/", $this->selenium->getLocation());
    
    //test links to details page
    $this->selenium->click("xpath=//a[@class='projectListDetailsLink']");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/catroid\/details/", $this->selenium->getLocation());
    $this->selenium->goBack();
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->click("xpath=//div[@class='projectListDetails']/a");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/catroid\/details/", $this->selenium->getLocation());
  }
}
?>

