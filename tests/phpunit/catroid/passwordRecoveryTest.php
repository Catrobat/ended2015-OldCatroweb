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

require_once('testsBootstrap.php');

class passwordRecoveryTest extends PHPUnit_Framework_TestCase
{
  protected $passwordrecoveryObj;
  protected $loginObj;
  protected $backupGlobals = FALSE;

  protected function setUp() {
    require_once CORE_BASE_PATH.'modules/catroid/passwordrecovery.php';
    require_once CORE_BASE_PATH.'modules/catroid/login.php';
    $this->passwordrecoveryObj = new passwordrecovery();
    $this->loginObj = new login();
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
  public function testCheckPassword($pass, $passRepeat) {
    try {
      $this->assertTrue($this->passwordrecoveryObj->checkPassword($pass, $passRepeat));
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
    }
  }

  /**
   * @dataProvider invalidPasswords
   */
  public function testCheckInvalidPassword($pass, $passRepeat) {
    try {
      $this->passwordrecoveryObj->checkPassword($pass, $passRepeat);
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
      $this->assertTrue($this->passwordrecoveryObj->doSendPasswordRecoveryMail($userData, false)); // false to disable sending mail, if mailserver is not available
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
  
  
//  
//  /**
//   * @dataProvider validRegistrationData
//   */
//  public function testDoRegistration($postData, $serverData) {
//    try {
//      $catroidUserId = $this->obj->doCatroidRegistration($postData, $serverData);
//      $this->assertGreaterThan(0, intval($catroidUserId));
//    } catch(Exception $e) {
//      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
//    }
//    //undo catroid registration
//    try {
//      $this->assertTrue($this->obj->undoCatroidRegistration($catroidUserId));
//    } catch(Exception $e) {
//      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
//    }
//    
//    try {
//      $wikiUserId = $this->obj->doWikiRegistration($postData);
//      $this->assertGreaterThan(0, intval($wikiUserId));
//    } catch(Exception $e) {
//      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
//    }
//    //undo wiki registration
//    try {
//      $this->assertTrue($this->obj->undoWikiRegistration($wikiUserId));
//    } catch(Exception $e) {
//      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
//    }
//  }


  
  /* *** DATA PROVIDERS *** */
  public function validUserData() {
    $dataArray = array(
    array('catroweb'),
    array('testuser'),
    array('webmaster@catroid.org'),
    array('testuser@catroid.org')
    );
    return $dataArray;
  }

  public function invalidUserData() {
    $dataArray = array(
    array('catr0web'),  //not in db - 0 is zero
    array('xxx'),  //messword
    array('cartroweb'),  //not in db
    array('testusing@catroid.org'),  //not in db
    array('info@catroweb.org'),  //not in db
    array(''),  //empty username
    array('0'),  //zero as username
    array('abc'), //too short username
    array('129.0.12.123'), //IP address style (because of wiki)
    array('[myusername]'), //contains invalid char
    array('my*username'),  //contains invalid char
    array('my:username'),  //contains invalid char
    array('<my>username'), //contains invalid char
    array('myusername!'),  //contains invalid char
    array('myuser/name'),  //contains invalid char
    array('\my\username'), //contains invalid char
    array('my%username'),  //contains invalid char
    array('my$username'),  //contains invalid char
    array('myusername&'),  //contains invalid char
    array('myus-ername'),   //contains invalid char
    array('unit_test')    //contains invalid char
    );
    return $dataArray;
  }

  public function validPasswords() {
    $dataArray = array(
    array('mypassword', 'mypassword'),
    array('myPassWorD', 'myPassWorD'),
    array('89277823409', '89277823409'),
    array('012345', '012345'),
    array('abcdef', 'abcdef'),
    array('äöüÜÜßß§$%§%%/', 'äöüÜÜßß§$%§%%/'),
    array('______', '______')
    );
    return $dataArray;
  }

  public function invalidPasswords() {
    $dataArray = array(
    array('mypasswordmypasswordmypasswordmypassword', 'mypasswordmypasswordmypasswordmypassword'),
    array('mypassword', 'myotherpassword'),
    array('mypassword', 'myPassword'),
    array('0', '0'),
    array('', ''),
    array('', 'mypassword'),
    array('mypassword', '0'),
    array('short', 'short')
    );
    return $dataArray;
  }

  
  
  
  
  
  
  public function validRegistrationData() {
    $dataArray = array(
    array(
    array('registrationUsername'=>'myUnitTest', 'registrationPassword'=>'myPassword123',
    	    'registrationPasswordRepeat'=>'myPassword123', 'registrationEmail'=>'unittest@unit.test',
    		'registrationGender'=>'male', 'registrationMonth'=>'1', 'registrationYear'=>'1980',
    		'registrationCountry'=>'AT', 'registrationProvince'=>'Steiermark', 'registrationCity'=>'Graz',
            'registrationSubmit'=>'submit'),
    array('REMOTE_ADDR'=>'127.0.0.1')),
    array(
    array('registrationUsername'=>'myÜnitTÄßt', 'registrationPassword'=>'mySpe§§ialChÄrPaßßword!!',
    	    'registrationPasswordRepeat'=>'mySpe§§ialChÄrPaßßword!!', 'registrationEmail'=>'_123unit@test.test',
    		'registrationGender'=>'female', 'registrationMonth'=>'2', 'registrationYear'=>'1987',
    		'registrationCountry'=>'AT', 'registrationProvince'=>'Kärnten', 'registrationCity'=>'Villach',
    'registrationSubmit'=>'submit'),
    array('REMOTE_ADDR'=>'127.0.0.1')),
    array(
    array('registrationUsername'=>'1234567', 'registrationPassword'=>'__bla__',
    	    'registrationPasswordRepeat'=>'__bla__', 'registrationEmail'=>'unit2test@unit.at',
    		'registrationGender'=>'male', 'registrationMonth'=>'3', 'registrationYear'=>'1989',
    		'registrationCountry'=>'DE', 'registrationProvince'=>'Bayern', 'registrationCity'=>'München',
    'registrationSubmit'=>'submit'),
    array('REMOTE_ADDR'=>'127.0.0.1'))
    );
    return $dataArray;
  }
}
?>
