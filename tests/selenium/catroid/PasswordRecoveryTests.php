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

class PasswordRecoveryTests extends PHPUnit_Framework_TestCase
{
  private $selenium;

  public function setUp()
  {
    $path= 'http://'.str_replace('http://', '', TESTS_BASE_PATH).'catroid/login';
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
  
  public function ajaxWait()
  {
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

  public function testPasswordRecoveryIntro() {
    $this->selenium->open(TESTS_BASE_PATH."catroid/login");
    $this->selenium->waitForPageToLoad(10000);

    // check password recovery link
    $this->assertTrue($this->selenium->isTextPresent("Login"));
    $this->assertTrue($this->selenium->isTextPresent("click here if you forgot your password?"));
    $this->selenium->isElementPresent("xpath=//div[@class='loginMain']");
    $this->selenium->isElementPresent("xpath=//div[@class='loginFormContainer']");
    $this->selenium->isElementPresent("xpath=//div[@class='loginHelper']");
    $this->selenium->isElementPresent("xpath=//a[@id='forgotPassword']");
    $this->selenium->click("xpath=//a[@id='forgotPassword']"); 

    // check password recovery form
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Change your password"));
  }    
 
  /**
   * @dataProvider passwordRecoveryResetUsernames 
   */
  public function testPasswordRecoveryReset($user, $pass, $email, $month, $year, $gender, $country, $city) {
    // do registration process first, to create a new user with known password 
    $this->selenium->open(TESTS_BASE_PATH."catroid/registration");
    $this->selenium->waitForPageToLoad(10000);
    
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@name='registrationUsername']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@name='registrationPassword']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@name='registrationEmail']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//select[@name='registrationMonth']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//select[@name='registrationYear']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//select[@name='registrationGender']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//select[@name='registrationCountry']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@name='registrationCity']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@name='registrationSubmit']"));
    
    $this->selenium->type("xpath=//input[@name='registrationUsername']", $user);
    $this->selenium->type("xpath=//input[@name='registrationPassword']", $pass);
    $this->selenium->type("xpath=//input[@name='registrationEmail']", $email);
    $this->selenium->type("xpath=//select[@name='registrationMonth']", $month);
    $this->selenium->type("xpath=//select[@name='registrationYear']", $year);
    $this->selenium->type("xpath=//select[@name='registrationGender']", $gender);
    $this->selenium->type("xpath=//select[@name='registrationCountry']", $country);
    $this->selenium->type("xpath=//input[@name='registrationCity']", $city);
    $this->selenium->click("xpath=//input[@name='registrationSubmit']");
    
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("CATROID registration successfull!"));
    $this->assertTrue($this->selenium->isTextPresent("BOARD registration successfull!"));
    $this->assertTrue($this->selenium->isTextPresent("WIKI registration successfull!"));
    
    // goto lost password page and test reset by email and nickname, at first use some wrong nickname or email
    $this->selenium->open(TESTS_BASE_PATH."catroid/passwordrecovery");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Enter your nickname or email address:"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@name='passwordRecoveryUserdata']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@name='passwordRecoverySubmit']"));
    $this->selenium->type("xpath=//input[@name='passwordRecoveryUserdata']", $user." to test");
    $this->selenium->click("xpath=//input[@name='passwordRecoverySubmit']");
    
    // check error message
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Enter your nickname or email address:"));
    $this->assertTrue($this->selenium->isTextPresent("The nickname or email address was not found."));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@name='passwordRecoveryUserdata']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@name='passwordRecoverySubmit']"));

    // now use real name 
    $this->selenium->type("xpath=//input[@name='passwordRecoveryUserdata']", $user);
    $this->selenium->click("xpath=//input[@name='passwordRecoverySubmit']");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent(TESTS_BASE_PATH."catroid/passwordrecovery?c="));
    $this->assertTrue($this->selenium->isTextPresent("An email was sent to your email address. Please check your inbox."));
    $this->selenium->click("xpath=//a[@id='forgotPassword']");    

    // enter 2short password
    $this->selenium->waitForPageToLoad(10000);
    $recovery_url = $this->selenium->getLocation();
    $this->assertTrue($this->selenium->isTextPresent("Please enter your new password:"));
    $this->selenium->type("xpath=//input[@name='passwordSavePassword']", "short");
    $this->selenium->click("xpath=//input[@name='passwordSaveSubmit']");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Please enter your new password:"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@name='passwordSavePassword']"));
    $this->assertTrue($this->selenium->isTextPresent("The password must have at least 6 characters."));

    // enter the new password correctly
    $this->selenium->type("xpath=//input[@name='passwordSavePassword']", $pass." new");
    $this->selenium->click("xpath=//input[@name='passwordSaveSubmit']");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Your new password is set. Please log in now."));
    // login link muss vorhanden sein!
    
    $this->assertFalse($this->selenium->isElementPresent("xpath=//input[@name='passwordSavePassword']"));

    // and try to login with the old credentials to verify password recovery worked
    $this->selenium->open(TESTS_BASE_PATH."catroid/login");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Login"));
    $this->assertTrue($this->selenium->isTextPresent("click here if you forgot your password?"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@class='loginMain']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@class='loginFormContainer']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@class='loginHelper']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//a[@id='forgotPassword']"));
    $this->selenium->type("xpath=//input[@name='loginUsername']", $user);
    $this->selenium->type("xpath=//input[@name='loginPassword']", $pass);
    $this->selenium->click("xpath=//input[@name='loginSubmit']");
    
    // check bad login
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("The catroid authentication failed."));
    $this->assertTrue($this->selenium->isTextPresent("The password or username was incorrect."));    
  
    // and try to login now with the new credentials
    $this->selenium->open(TESTS_BASE_PATH."catroid/login");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Login"));
    $this->assertTrue($this->selenium->isTextPresent("click here if you forgot your password?"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@class='loginMain']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@class='loginFormContainer']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@class='loginHelper']"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//a[@id='forgotPassword']"));
    $this->selenium->type("xpath=//input[@name='loginUsername']", $user);
    $this->selenium->type("xpath=//input[@name='loginPassword']", $pass." new");
    $this->selenium->click("xpath=//input[@name='loginSubmit']");
    
    // check login
    $this->selenium->waitForPageToLoad(10000);
    $this->ajaxWait();
    $this->assertTrue($this->selenium->isTextPresent("Newest Projects"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@id='projectContainer']"));
    
    // check board login
    $this->selenium->open(TESTS_BASE_PATH."addons/board");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Registered users:"));
    $this->assertTrue($this->selenium->isTextPresent($user));
    $this->assertTrue($this->selenium->isTextPresent("Members"));
    $this->assertTrue($this->selenium->isTextPresent("Logout [ $user ]"));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//li[@class='icon-logout']"));

    // check wiki login 
    $this->selenium->open(TESTS_BASE_PATH.'wiki/');
    $this->selenium->waitForPageToLoad(10000);   
    //$this->assertFalse($this->selenium->isTextPresent($user));
    $this->assertTrue($this->selenium->isTextPresent($user));
    $this->selenium->click("xpath=//li[@id='pt-preferences']/a");
    $this->selenium->waitForPageToLoad(10000);
    //$this->assertFalse($this->selenium->isTextPresent($user));
    $this->assertTrue($this->selenium->isTextPresent($user));
    
    $this->selenium->open(TESTS_BASE_PATH."catroid/login");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@name='logoutSubmit']"));
    
    // Recovery URL should not work again
    $this->selenium->open($recovery_url);
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Sorry! Your recovery url has expired. Please try again."));
    $this->assertTrue($this->selenium->isElementPresent("xpath=//input[@name='passwordNextSubmit']"));
    
  }  

  /* *** DATA PROVIDERS *** */
  public function passwordRecoveryResetUsernames() {
    $returnArray = array(
      array("John Test ".rand(1,9999), "just a simple password!", "john".rand(1,9999)."@catroid.org", "2", "1980", "male", "IT", "Padua")
    );
    return $returnArray;
  }

}
?>