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

class ProfileTests extends PHPUnit_Framework_TestCase
{
  private $selenium;

  public function ajaxWait()
  {
    for($second = 0; $second <= 6; $second++) {
      if($second >= 600) break;
      try {
        if($this->selenium->isElementPresent("xpath=//input[@id='ajax-loader'][@value='off']")) {
          break;
        }
      } catch (Exception $e) {}
      sleep(1);
    }
  }

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
  
  /**
   * @dataProvider loginData
   */
  public function testProfilePage($dataArray)
  {
    
    $this->selenium->open(TESTS_BASE_PATH.'catroid/profile/');
    $this->selenium->waitForPageToLoad(10000);
    $this->assertEquals('Login', $this->selenium->getText("xpath=//div[@class='webMainContentTitle']"));
    $this->assertTrue($this->selenium->isTextPresent('Login'));
    
    $this->selenium->click("xpath=//button[@id='headerMenuButton']");
    $this->selenium->waitForPageToLoad(10000);
    
    $this->assertTrue($this->selenium->isVisible("xpath=//button[@id='menuProfileButton']"));
    $this->assertTrue($this->selenium->isVisible("xpath=//button[@id='menuLoginButton']"));
    $this->assertFalse($this->selenium->isVisible("xpath=//button[@id='menuLogoutButton']"));
    
    $this->selenium->click("xpath=//button[@id='menuProfileButton']");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertEquals('Login', $this->selenium->getText("xpath=//div[@class='webMainContentTitle']"));
    $this->assertTrue($this->selenium->isTextPresent('Login'));
        
    
    $this->selenium->type("xpath=//input[@name='loginUsername']", $dataArray["user"]);
    $this->selenium->type("xpath=//input[@name='loginPassword']", $dataArray["pass"]);
    $this->selenium->click("xpath=//input[@name='loginSubmit']");
    $this->selenium->waitForPageToLoad(10000);
    $this->ajaxWait();
    
    $this->selenium->click("xpath=//button[@id='headerMenuButton']");
    $this->selenium->waitForPageToLoad(10000);
    
    $this->assertTrue($this->selenium->isVisible("xpath=//button[@id='menuProfileButton']"));
    $this->assertFalse($this->selenium->isVisible("xpath=//button[@id='menuLoginButton']"));
    $this->assertTrue($this->selenium->isVisible("xpath=//button[@id='menuLogoutButton']"));
        
    $this->assertTrue($this->selenium->isTextPresent('Profile'));
    $this->selenium->click("xpath=//button[@id='menuProfileButton']");
    $this->selenium->waitForPageToLoad(10000);

    $this->assertTrue($this->selenium->isTextPresent($dataArray["user"].'\'s Profile'));
    $this->assertTrue($this->selenium->isTextPresent('change my password'));
    $this->assertTrue($this->selenium->isTextPresent('change my e-mail address'));
    $this->assertTrue($this->selenium->isTextPresent('from '));
    
    $this->selenium->click("xpath=//a[@id='profileChangePassword']");
    $this->ajaxWait();
    
    $this->assertTrue($this->selenium->isVisible("xpath=//input[@id='profileOldPassword']"));
    $this->assertTrue($this->selenium->isVisible("xpath=//input[@id='profileNewPassword']"));
    $this->assertTrue($this->selenium->isVisible("xpath=//input[@id='profilePasswordSubmit']"));

    $this->selenium->type("xpath=//input[@id='profileOldPassword']", $dataArray["pass"]);
    $this->selenium->type("xpath=//input[@id='profileNewPassword']", $dataArray["newPass"]);
    $this->selenium->click("xpath=//input[@id='profilePasswordSubmit']");
    $this->ajaxWait();
    
    $this->assertTrue($this->selenium->isTextPresent('You updated your password successfully.'));
    
    $this->selenium->click("xpath=//a[@id='profileChangePassword']");
    $this->ajaxWait();
    
    $this->selenium->type("xpath=//input[@id='profileOldPassword']", $dataArray["pass"]);
    $this->selenium->type("xpath=//input[@id='profileNewPassword']", $dataArray["shortPass"]);
    $this->selenium->click("xpath=//input[@id='profilePasswordSubmit']");
    $this->ajaxWait();
    
    $this->assertTrue($this->selenium->isTextPresent('The old password was incorrect.'));
    $this->assertTrue($this->selenium->isTextPresent('The new password must have at least 6 characters.'));
    
    $this->selenium->type("xpath=//input[@id='profileOldPassword']", $dataArray["newPass"]);
    $this->selenium->type("xpath=//input[@id='profileNewPassword']", $dataArray["emptyPass"]);
    $this->selenium->click("xpath=//input[@id='profilePasswordSubmit']");
    $this->ajaxWait();

    $this->assertTrue($this->selenium->isTextPresent('The new password is missing.'));
    
    $this->selenium->type("xpath=//input[@id='profileOldPassword']", $dataArray["emptyPass"]);
    $this->selenium->type("xpath=//input[@id='profileNewPassword']", $dataArray["pass"]);
    $this->selenium->click("xpath=//input[@id='profilePasswordSubmit']");
    $this->ajaxWait();
    
    $this->assertTrue($this->selenium->isTextPresent('The old password is empty.'));
    $this->assertTrue($this->selenium->isTextPresent('The new password must have at least 6 characters.'));
    
    $this->selenium->type("xpath=//input[@id='profileOldPassword']", $dataArray["newPass"]);
    $this->selenium->type("xpath=//input[@id='profileNewPassword']", $dataArray["pass"]);
    $this->selenium->click("xpath=//input[@id='profilePasswordSubmit']");
    $this->ajaxWait();
    
    $this->assertTrue($this->selenium->isTextPresent('You updated your password successfully.'));
    
    $this->selenium->click("xpath=//a[@id='profileChangePassword']");
    $this->ajaxWait();
    
    $this->selenium->type("xpath=//input[@id='profileOldPassword']", $dataArray["pass"]);
    $this->selenium->type("xpath=//input[@id='profileNewPassword']", $dataArray["pass"]);
    $this->selenium->click("xpath=//input[@id='profilePasswordSubmit']");
    $this->ajaxWait();
    
    $this->assertTrue($this->selenium->isTextPresent('You updated your password successfully.'));
    
    //"webmaster@catroid.org"
    $this->selenium->click("xpath=//a[@id='profileChangeEmailText']");
    $this->ajaxWait();
    
    $this->selenium->type("xpath=//input[@id='profileEmail']", $dataArray["email"]);
    $this->selenium->click("xpath=//input[@id='profileEmailSubmit']");
    $this->selenium->waitForPageToLoad(10000);
    $this->ajaxWait();
    
    $this->assertTrue($this->selenium->isTextPresent($dataArray["email"]));
    
    
    

//
  }  
  /* *** DATA PROVIDERS *** */
  public function loginData() {
    $dataArray = array(
    array(
    array("user" => "catroweb", 
    			"pass" => "cat.roid.web", 
    			"newPass" => "cat.roid", 
    			"wrongUser" => "wrongUser", 
          "wrongPass" => "wrongPassword", 
          "shortPass" => "short",
          "emptyPass" => " ",  
          "email" => "webmaster@catroid.org"
    ))
    );
    return $dataArray;
                    
//$user, $pass, $wrongUser, $wrongPass, $oldPassword1, $newPassword1, $oldPassword2, $newPassword2, $shortPassword
                    //    array(
//      array('catroweb', 'cat.roid.web', 'wrongUser', 'wrongPassword', 'cat.roid.web', 'cat.roid',  'cat.roid', 'cat.roid.web', 'short' )
//    );
//    return $dataArray;
  }

  
}
?>

