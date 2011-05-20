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

class LoginTests extends PHPUnit_Framework_TestCase
{
  private $selenium;

  public function setUp()
  {
    $path= 'http://'.str_replace('http://', '', TESTS_BASE_PATH).'catroid/';
    $this->selenium = new Testing_Selenium(TESTS_BROWSER, $path);
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

  public function ajaxWait() {
    for($second = 0; $second <= 600; $second++) {
      if($second >= 600) break;
      try {
        if($this->selenium->isElementPresent("xpath=//input[@id='ajax-loader'][@value='off']")) {
          break;
        }
      } catch (Exception $e) {}
      sleep(1);
    }
  }

  /**
   * @dataProvider loginData
   */
  public function testLogin($user, $pass, $wrongUser, $wrongPass)
  {
    $this->selenium->open(TESTS_BASE_PATH);
    $this->selenium->waitForPageToLoad(10000);

    //wiki username creation
    $wikiUsername = ucfirst(strtolower($user));

    //check if we are not logged in to board & wiki
    $this->selenium->click("headerMenuButton");
    $this->selenium->waitForPageToLoad(10000);

    $this->assertTrue($this->selenium->isVisible("menuLoginButton"));

    $this->selenium->click("menuForumButton");
    $this->selenium->selectWindow("board");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Login"));
    $this->assertFalse($this->selenium->isTextPresent("Logout"));
    $this->selenium->close();
    $this->selenium->selectWindow(null);

    $this->selenium->click("menuWikiButton");
    $this->selenium->selectWindow("wiki");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertFalse($this->selenium->isTextPresent($wikiUsername));
    $this->selenium->close();
    $this->selenium->selectWindow(null);

    // test login
    $this->selenium->open(TESTS_BASE_PATH);
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isVisible("headerProfileButton"));
    $this->selenium->click("headerProfileButton");
    $this->assertFalse($this->selenium->isVisible("headerProfileButton"));
    $this->assertTrue($this->selenium->isVisible("headerCancelButton"));
    $this->assertTrue($this->selenium->isVisible("loginSubmitButton"));
    $this->assertTrue($this->selenium->isVisible("loginUsername"));
    $this->assertTrue($this->selenium->isVisible("loginPassword"));
    $this->selenium->click("headerCancelButton");
    $this->assertTrue($this->selenium->isVisible("headerProfileButton"));
    $this->assertFalse($this->selenium->isVisible("headerCancelButton"));
    $this->assertFalse($this->selenium->isVisible("loginSubmitButton"));
    $this->assertFalse($this->selenium->isVisible("loginUsername"));
    $this->assertFalse($this->selenium->isVisible("loginPassword"));
    $this->selenium->click("headerProfileButton");
    $this->assertFalse($this->selenium->isVisible("headerProfileButton"));
    $this->assertTrue($this->selenium->isVisible("headerCancelButton"));
    $this->assertTrue($this->selenium->isVisible("loginSubmitButton"));
    $this->assertTrue($this->selenium->isVisible("loginUsername"));
    $this->assertTrue($this->selenium->isVisible("loginPassword"));

    $this->selenium->type("loginUsername", $user);
    $this->selenium->type("loginPassword", $pass);
    
    $this->selenium->click("loginSubmitButton");
    $this->selenium->waitForCondition("", 5000);
    $this->assertTrue($this->selenium->isVisible("headerProfileButton"));
    $this->selenium->click("headerProfileButton");
    $this->assertTrue($this->selenium->isVisible("logoutSubmitButton"));
    $this->selenium->click("headerCancelButton");

    $this->selenium->click("headerMenuButton");
    $this->selenium->waitForPageToLoad(10000);

    $this->assertTrue($this->selenium->isVisible("menuLogoutButton"));

    $this->selenium->click("menuForumButton");
    $this->selenium->selectWindow("board");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertFalse($this->selenium->isTextPresent("Login"));
    $this->assertTrue($this->selenium->isTextPresent("Logout"));
    $this->assertTrue($this->selenium->isTextPresent($user));
    $this->selenium->close();
    $this->selenium->selectWindow(null);

    $this->selenium->click("menuWikiButton");
    $this->selenium->selectWindow("wiki");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent($wikiUsername));
    $this->selenium->click("xpath=//li[@id='pt-preferences']/a");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertEquals('Preferences', $this->selenium->getText("firstHeading"));
    $this->assertFalse($this->selenium->isTextPresent("Not logged in"));
    $this->selenium->close();
    $this->selenium->selectWindow(null);

    // test logout
    $this->selenium->open(TESTS_BASE_PATH);
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isVisible("headerProfileButton"));
    $this->selenium->click("headerProfileButton");
    $this->assertTrue($this->selenium->isVisible("logoutSubmitButton"));
    $this->selenium->click("logoutSubmitButton");
    $this->selenium->waitForCondition("", 5000);
    $this->assertTrue($this->selenium->isVisible("headerProfileButton"));
    $this->selenium->click("headerProfileButton");
    $this->assertTrue($this->selenium->isVisible("loginSubmitButton"));
    $this->selenium->click("headerCancelButton");

    $this->selenium->click("headerMenuButton");
    $this->selenium->waitForPageToLoad(10000);

    $this->assertTrue($this->selenium->isVisible("menuLoginButton"));

    $this->selenium->click("menuForumButton");
    $this->selenium->selectWindow("board");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Login"));
    $this->assertFalse($this->selenium->isTextPresent("Logout"));
    $this->selenium->close();
    $this->selenium->selectWindow(null);

    $this->selenium->click("menuWikiButton");
    $this->selenium->selectWindow("wiki");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertFalse($this->selenium->isTextPresent($wikiUsername));
    $this->selenium->close();
    $this->selenium->selectWindow(null);

    // test login with wrong user/pass
     $this->selenium->open(TESTS_BASE_PATH);
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isVisible("headerProfileButton"));
    $this->selenium->click("headerProfileButton");
    $this->assertFalse($this->selenium->isVisible("headerProfileButton"));
    $this->assertTrue($this->selenium->isVisible("headerCancelButton"));
    $this->assertTrue($this->selenium->isVisible("loginSubmitButton"));
    $this->assertTrue($this->selenium->isVisible("loginUsername"));
    $this->assertTrue($this->selenium->isVisible("loginPassword"));
    $this->selenium->click("headerCancelButton");
    $this->assertTrue($this->selenium->isVisible("headerProfileButton"));
    $this->assertFalse($this->selenium->isVisible("headerCancelButton"));
    $this->assertFalse($this->selenium->isVisible("loginSubmitButton"));
    $this->assertFalse($this->selenium->isVisible("loginUsername"));
    $this->assertFalse($this->selenium->isVisible("loginPassword"));
    $this->selenium->click("headerProfileButton");
    $this->assertFalse($this->selenium->isVisible("headerProfileButton"));
    $this->assertTrue($this->selenium->isVisible("headerCancelButton"));
    $this->assertTrue($this->selenium->isVisible("loginSubmitButton"));
    $this->assertTrue($this->selenium->isVisible("loginUsername"));
    $this->assertTrue($this->selenium->isVisible("loginPassword"));

    $this->selenium->type("loginUsername", $wrongUser);
    $this->selenium->type("loginPassword", $wrongPass);
    
    $this->selenium->click("loginSubmitButton");
    $this->selenium->waitForCondition("", 5000);
    $this->assertTrue($this->selenium->isVisible("loginSubmitButton"));
    $this->selenium->click("headerCancelButton");    

    $this->selenium->click("headerMenuButton");
    $this->selenium->waitForPageToLoad(10000);

    $this->assertTrue($this->selenium->isVisible("menuLoginButton"));

    $this->selenium->click("menuForumButton");
    $this->selenium->selectWindow("board");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Login"));
    $this->assertFalse($this->selenium->isTextPresent("Logout"));
    $this->selenium->close();
    $this->selenium->selectWindow(null);

    $this->selenium->click("menuWikiButton");
    $this->selenium->selectWindow("wiki");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertFalse($this->selenium->isTextPresent($wikiUsername));
    $this->selenium->close();
    $this->selenium->selectWindow(null);
  }

  /* *** DATA PROVIDERS *** */
  public function loginData() {
    $returnArray = array(
    array('catroweb', 'cat.roid.web', 'wrongUser', 'wrongPassword')
    );
    return $returnArray;
  }

}
?>

