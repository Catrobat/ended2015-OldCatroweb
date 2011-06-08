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

require_once('testsBootstrap.php');

class passwordRecoveryTest extends PHPUnit_Framework_TestCase
{
  protected $passwordrecoveryObj;
  protected $registrationObj;
  protected $random;
  protected $userHash;
  protected $catroidUserId;
  protected $wikiUserId;
  protected $backupGlobals = FALSE;

  protected function setUp() {
    require_once CORE_BASE_PATH.'modules/catroid/passwordrecovery.php';
    require_once CORE_BASE_PATH.'modules/catroid/registration.php';
    $this->passwordrecoveryObj = new passwordrecovery();
    $this->registrationObj = new registration();
    $this->userHash;
  }
  
//  protected function tearDown() {
//      //undo catroid registration
//    try {
//      $this->assertTrue($this->registrationObj->undoCatroidRegistration($this->catroidUserId));
//    } catch(Exception $e) {
//      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
//    }
//    //undo wiki registration
//    try {
//      $this->assertTrue($this->registrationObj->undoWikiRegistration($this->wikiUserId));
//    } catch(Exception $e) {
//      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
//    }
//  }
  
  /**
   * @dataProvider validRegistrationData
   */
  public function testDoRegistration($postData, $serverData) {
    try {
      $this->catroidUserId = $this->registrationObj->doCatroidRegistration($postData, $serverData);
      $this->assertGreaterThan(0, intval($this->catroidUserId));
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
    }
    try {
      $this->wikiUserId = $this->registrationObj->doWikiRegistration($postData);
      $this->assertGreaterThan(0, intval($this->wikiUserId));
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
    }
    
    try {
      $this->assertTrue($this->registrationObj->undoCatroidRegistration($this->catroidUserId));
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
    }
    //undo wiki registration
    try {
      $this->assertTrue($this->registrationObj->undoWikiRegistration($this->wikiUserId));
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
      $this->userHash = $this->passwordrecoveryObj->doSendPasswordRecoveryMail($userData, false); // false to disable sending mail, if mailserver is not available
      $this->assertNotNull($this->userHash);
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
  
  public function testUndoRegistration() {
      //undo catroid registration

  }
  
  
//  /**
//   * @dataProvider validRegistrationData
//   */
//  public function testDoRegistration($postData, $serverData) {
//    try {
//      $this->oneCatroidUserId = $this->obj->doCatroidRegistration($postData, $serverData);
//      $this->assertGreaterThan(0, intval($catroidUserId));
//      // check DBData vs Postdata
//      $query = "EXECUTE get_user_row_by_username('".utf8_encode($postData['username'])."')";
//      $result = pg_query($query);
//      $this->assertTrue($result["country"] == $postData["country"]);
//      $this->assertTrue($result["email"] == $postData["email"]);
//      $this->assertTrue($result["gender"] == $postData["gender"]);
//      $this->assertTrue($result["city"] == $postData["city"]);
//      $this->assertTrue($result["province"] == $postData["province"]);
//      
//    } catch(Exception $e) {
//      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
//    }
//
//    try {
//      $this->oneBoardUserId = $this->obj->doBoardRegistration($postData);
//      $this->assertGreaterThan(0, intval($this->boardUserId));
//    } catch(Exception $e) {
//      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
//    }  
//    
//    try {
//      $this->oneWikiUserId = $this->obj->doWikiRegistration($postData);
//      $this->assertGreaterThan(0, intval($this->wikiUserId));
//    } catch(Exception $e) {
//      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
//    }
//    //undo wiki registration
//
//  }
//  
 
//  /**
//   * @dataProvider validRegistrationData
//   */
//  public function testDeleteRegistration($catroidUserId, $boardUserId, $wikiUserId) {
//    //undo catroid registration
//    try {
//      $this->assertTrue($this->obj->undoCatroidRegistration($catroidUserId));
//    } catch(Exception $e) {
//      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
//    }
//    
//    try {
//      $this->assertTrue($this->obj->undoBoardRegistration($boardUserId));
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
//      try {
//      $this->assertTrue($this->obj->undoWikiRegistration($wikiUserId));
//    } catch(Exception $e) {
//      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
//    }
//  }
  
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
    $random = $this->random;
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
