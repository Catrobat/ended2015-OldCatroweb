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
 *    An additional term exception under section 7 of the GNU Affero
 *    General Public License, version 3, is available at
 *    http://www.catroid.org/catroid/licenseadditionalterm
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once('testsBootstrap.php');

class passwordRecoveryTest extends PHPUnit_Framework_TestCase
{
  protected $passwordrecoveryObj;

  protected function setUp() {
    require_once CORE_BASE_PATH.'modules/catroid/passwordrecovery.php';
    $this->passwordrecoveryObj = new passwordrecovery();
  }
  
  /**
   * @dataProvider validRegistrationData
   */
  public function testDoRegistration($postData, $serverData) {
    try {
      require_once CORE_BASE_PATH.'modules/api/registration.php';      
      $registrationObj = new registration();
      
      $catroidUserId = $registrationObj->doCatroidRegistration($postData, $serverData);
      $this->assertGreaterThan(0, intval($catroidUserId));

      $wikiUserId = $registrationObj->doWikiRegistration($postData);
      $this->assertGreaterThan(0, intval($wikiUserId));

      $registrationObj->undoRegistration($catroidUserId, 0, $wikiUserId);
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
    }
  }  
  
  /**
   * @dataProvider validUserData
   */
  public function testCheckUserData($userData) {
    try {
      $this->assertTrue($this->passwordrecoveryObj->checkUserData($userData));
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
    }
  }

  /**
   * @dataProvider invalidUserData
   */
  public function testCheckInvalidUserData($userData) {
    try {
      $this->passwordrecoveryObj->checkUserData($userData);
    } catch(Exception $e) {
      return;
    }
    $this->fail('EXPECTED EXCEPTION NOT RAISED!');
  }

  /**
   * @dataProvider validPasswords
   */
  public function testCheckPassword($user, $pass) {
    try {
      $this->assertTrue($this->passwordrecoveryObj->checkPassword($user, $pass));
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
    }
  }

  /**
   * @dataProvider invalidPasswords
   */
  public function testCheckInvalidPassword($pass) {
    try {
      $this->passwordrecoveryObj->checkPassword($pass);
    } catch(Exception $e) {
      return;
    }
    $this->fail('EXPECTED EXCEPTION NOT RAISED!');
  }
  
  /**
   * @dataProvider validUserData
   */
  public function testDoSendPasswordRecoveryMail($userData) {
    try {
      $userHash = $this->passwordrecoveryObj->doSendPasswordRecoveryMail($userData, false); // false to disable sending mail, if mailserver is not available
      $this->assertNotNull($userHash);
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
    }
  }
  
  /**
   * @dataProvider invalidUserData
   */
  public function testInvalidDoSendPasswordRecoveryMail($userData) {
    try {
      $this->assertFalse($this->passwordrecoveryObj->doSendPasswordRecoveryMail($userData, false));
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
      return;
    }
  }
  
  /* *** DATA PROVIDERS *** */
  public function validUserData() {
    $dataArray = array(
    array('catroweb'),
    array('webmaster@catroid.org')
    );
    return $dataArray;
  }

  public function invalidUserData() {
    $dataArray = array(
    array('catr0web'),  //not in db - 0 is zero
    array('xxx'),  //messword
    array('cartroweb'),  //not in db
    array('testusing@catroid.org'),  //not in db
    array('testuser@catroid.org'),
    array('info@catroweb.org'),  //not in db
    array(''),  //empty username
    array('0'),  //zero as username is not allowed
    array('t[ob]i'), // squared braces not allowed (because of wiki)
    array('{ubi}'),  // curly braces not allowed (because of wiki)
    array('h|ol|y'), // vertical bars not allowed (because of wiki)
    array('#1'),     // hash sign not allowed (because of wiki)
    array('unit_test'), // underscores not allowed (because of wiki)
    array('<i>'), // greater/smaller than not allowed (because of wiki)
    array('129.0.12.123') //IP address style (because of wiki)
    );
    return $dataArray;
  }
  
  public function validPasswords() {
    $dataArray = array(
    array('mynickname', 'mypassword'),
    array('mynickname', 'myPassWorD'),
    array('mynickname', '89277823409'),
    array('mynickname', '012345'),
    array('mynickname', 'abcdef'),
    array('mynickname', 'Äjkasdkfäadfäppöü'),
    array('mynickname', '______')
    );
    return $dataArray;
  }

  public function invalidPasswords() {
    $dataArray = array(
    array('short', 'okpassword'),
    array('longmypasswordmypasmypasswordmypasmypasswordmypasmypasswordmypasmypassword','okpassword'),
    array('0', '000'),
    array('', ''),
    array('mynickname', 'mynickname') // passwords must not be equal to uernames
    );
    return $dataArray;
  }
  
  public function validRegistrationData() {
    $dataArray = array(
    array(
    array('registrationUsername'=>'myUnitTestUsername', 'registrationPassword'=>'myPassword123',
    	    'registrationEmail'=>'unittest@unit.test',
    		'registrationGender'=>'male', 'registrationMonth'=>'1', 'registrationYear'=>'1980',
    		'registrationCountry'=>'AT', 'registrationProvince'=>'Steiermark', 'registrationCity'=>'Graz',
            'registrationSubmit'=>'submit'),
    array('REMOTE_ADDR'=>'127.0.0.1'))
    );
    return $dataArray;
  }
}
?>
