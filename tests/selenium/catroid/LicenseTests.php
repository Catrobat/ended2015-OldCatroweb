<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/* Set TESTS_BASE_PATH in testsBootstrap.php to your catroid www-root */
require_once 'testsBootstrap.php';

class LicenseTests extends PHPUnit_Framework_TestCase
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
  
  public function testImprint() {
    $this->selenium->open(TESTS_BASE_PATH);
    $this->selenium->waitForPageToLoad(10000);
    // Privacy Policy | Terms of Use | Copyright Policy | Imprint | Contact 
 
    // contact us
    $this->selenium->click("xpath=//a[@class='license'][5]");    
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent(("Contact us")));        
    $this->selenium->isElementPresent("xpath=//p[@class='licenseText']/a");
    $this->selenium->goBack();     
    $this->selenium->waitForPageToLoad(10000);
    // copyright policy
    $this->selenium->click("xpath=//a[@class='license'][3]");    
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent(("Copyright Policy")));        
    $this->selenium->isElementPresent("xpath=//p[@class='licenseText']/a");
    $this->selenium->click("xpath=//p[@class='licenseText']/a[2]");
    $this->selenium->selectWindow("_blank");    
    $this->selenium->waitForPageToLoad(10000);    
    $this->assertTrue($this->selenium->isTextPresent(("Directive 2001/29/EC of the European Parliament and of the Council")));
    $this->assertTrue($this->selenium->isTextPresent(("32001L0029")));
    $this->selenium->close();
    $this->selenium->selectWindow(null);
    
    $this->selenium->click("xpath=//p[@class='licenseText']/a[3]");
    $this->selenium->selectWindow("_blank");    
    $this->selenium->waitForPageToLoad(10000);    
    $this->assertTrue($this->selenium->isTextPresent(("Chilling Effects")));
    $this->assertTrue($this->selenium->isTextPresent(("Chilling Effects Clearinghouse - www.chillingeffects.org")));
    $this->selenium->close();
    $this->selenium->selectWindow(null);
    
    $this->selenium->goBack();
    $this->selenium->waitForPageToLoad(10000);
    // imprint
    $this->selenium->click("xpath=//a[@class='license'][4]");    
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent(("Address")));
    $this->assertTrue($this->selenium->isTextPresent(("Institut für Softwaretechnologie")));
    $this->assertTrue($this->selenium->isTextPresent(("Technische Universität Graz")));
    $this->assertTrue($this->selenium->isTextPresent(("Inffeldgasse 16B/II")));
    $this->assertTrue($this->selenium->isTextPresent(("8010 Graz")));
    $this->assertTrue($this->selenium->isTextPresent(("Austria")));
    $this->selenium->click("xpath=//p[@class='licenseText']/a");    
    $this->selenium->selectWindow("_blank");    
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/IST web - Index/", $this->selenium->getTitle());  
    $this->selenium->close();
    $this->selenium->selectWindow(null);   
    $this->selenium->goBack();    
    $this->selenium->waitForPageToLoad(10000);
  
     // privacy policy
    $this->selenium->click("xpath=//a[@class='license']");    
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent(("Privacy Policy")));        
    $this->selenium->isElementPresent("xpath=//p[@class='licenseText']/a");
    $this->selenium->goBack();    
    $this->selenium->waitForPageToLoad(10000);

    
    // test terms of use
    $this->selenium->click("xpath=//a[@class='license'][2]");    
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent(("Welcome to the Catroid community! As part of the Catroid community, you are sharing projects and ideas with people: ")));    
    $this->selenium->click("xpath=//p[@class='licenseText'][3]/a");    
    $this->selenium->selectWindow("_blank");    
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/Creative Commons — Attribution-ShareAlike 2.0 Generic — CC BY-SA 2.0/", $this->selenium->getTitle());  
    $this->selenium->close();
    $this->selenium->selectWindow(null);
    
    $this->selenium->click("xpath=//p[@class='licenseText']/a[2]");    
    $this->selenium->selectWindow("_blank");    
    $this->selenium->waitForPageToLoad(10000);    
    $this->assertTrue($this->selenium->isTextPresent("GNU GENERAL PUBLIC LICENSE"));
    $this->assertTrue($this->selenium->isTextPresent("Version 3, 29 June 2007"));
    $this->selenium->close();
    $this->selenium->selectWindow(null);

    $this->selenium->click("xpath=//p[@class='licenseText']/a[3]");    
    $this->selenium->selectWindow("_blank");    
    $this->selenium->waitForPageToLoad(10000);    
    $this->assertTrue($this->selenium->isTextPresent("GNU AFFERO GENERAL PUBLIC LICENSE"));
    $this->assertTrue($this->selenium->isTextPresent("Version 3, 19 November 2007"));
    $this->selenium->close();
    $this->selenium->selectWindow(null);
    
    $this->selenium->click("xpath=//p[@class='licenseText']/a[4]");
    $this->selenium->selectWindow("_blank");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/catroid \-/", $this->selenium->getTitle());  
    $this->assertRegExp("/An on-device graphical programming language for Android inspired by Scratch/", $this->selenium->getTitle());
    $this->selenium->close();
    $this->selenium->selectWindow(null);
    $this->selenium->goBack();    
            
    
  }
}
?>

