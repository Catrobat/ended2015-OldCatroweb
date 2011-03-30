<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or License, or (at your option) any later version.
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

class registrationTest extends PHPUnit_Framework_TestCase
{
  protected $obj;
  protected $backupGlobals = FALSE;

  protected function setUp() {
    require_once CORE_BASE_PATH.'modules/catroid/registration.php';
    $this->obj = new registration();
  }

  /**
   * @dataProvider validUsernames
   */
  public function testCheckUsername($user) {
    try {
      $this->assertTrue($this->obj->checkUsername($user));
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
    }
  }

  /**
   * @dataProvider invalidUsernames
   */
  public function testCheckInvalidUsername($user) {
    try {
      $this->obj->checkUsername($user);
    } catch(Exception $e) {
      return;
    }
    $this->fail('EXPECTED EXCEPTION NOT RAISED!');
  }

  /**
   * @dataProvider validEmails
   */
  public function testCheckEmail($email) {
    try {
      $this->assertTrue($this->obj->checkEmail($email));
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
    }
  }

  /**
   * @dataProvider invalidEmails
   */
  public function testCheckInvalidEmail($email) {
    try {
      $this->obj->checkEmail($email);
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
      $this->assertTrue($this->obj->checkPassword($pass, $passRepeat));
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
    }
  }

  /**
   * @dataProvider invalidPasswords
   */
  public function testCheckInvalidPassword($pass, $passRepeat) {
    try {
      $this->obj->checkPassword($pass, $passRepeat);
    } catch(Exception $e) {
      return;
    }
    $this->fail('EXPECTED EXCEPTION NOT RAISED!');
  }

  /**
   * @dataProvider validRegistrationData
   */
  public function testDoRegistration($postData, $serverData) {
    try {
      $catroidUserId = $this->obj->doCatroidRegistration($postData, $serverData);
      $this->assertGreaterThan(0, intval($catroidUserId));
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
    }
    //undo catroid registration
    try {
      $this->assertTrue($this->obj->undoCatroidRegistration($catroidUserId));
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
    }
    
    try {
      $wikiUserId = $this->obj->doWikiRegistration($postData);
      $this->assertGreaterThan(0, intval($wikiUserId));
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
    }
    //undo wiki registration
    try {
      $this->assertTrue($this->obj->undoWikiRegistration($wikiUserId));
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
    }
  }

  /* *** DATA PROVIDERS *** */
  public function validUsernames() {
    $dataArray = array(
    array('unittest'),
    array('UniTTesT'),
    array('Unittest'),
    array('0123unItEst234'),
    array('9765786'),
    array('0123'),
    array('unit.te..st'),
    array('unit test'),
    array('ÜnitTäßt')
    );
    return $dataArray;
  }

  public function invalidUsernames() {
    $dataArray = array(
    array('catroweb'),  //existing username
    array('Catroweb'),  //all mixed case forms of existing username should also be invalid
    array('cAtRoWeB'),  //all mixed case forms of existing username should also be invalid
    array('CATROWEB'),  //all mixed case forms of existing username should also be invalid
    array('catroweB'),  //all mixed case forms of existing username should also be invalid
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
    array('unit_test'),    //contains invalid char
    array('myus-ername')   //contains invalid char
    );
    return $dataArray;
  }

  public function validEmails() {
    $dataArray = array(
    array('a@domain.com'),
    array('a.a@domain.com'),
    array('a-5@domain.com'),
    array('a@s5.domain.com'),
    array('a@s-5.domain.com'),
    array('a@s.5.domain.com'),
    array('a@sub.domain-5.com'),
    array('abc_12345@test.com')
    );
    return $dataArray;
  }

  public function invalidEmails() {
    $dataArray = array(
    array(''),
    array('webmaster@catroid.org'),
    array('domain.com'),
    array('aaa@domain'),
    array('@domain.com'),
    array('@domain.com'),
    array('.a@domain.com'),
    array('-a@domain.com'),
    array('a.@domain.com'),
    array('a-@domain.com'),
    array('a@.com'),
    array('a@ä.com'),
    array('a@.domain.com'),
    array('a@-domain.com'),
    array('a@domain..com'),
    array('a@domain-.com'),
    array('a@domain.'),
    array('a@domain. '),
    array('a@domain.5'),
    array('a@domain.c.m'),
    array('a@domain.c-m'),
    array('a@domain.c5m'),
    array('special_char_äöü@sub.domÄin-5.com')
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
            'registrationSubmit'=>'submit'),
    array('REMOTE_ADDR'=>'127.0.0.1')),
    array(
    array('registrationUsername'=>'myÜnitTÄßt', 'registrationPassword'=>'mySpe§§ialChÄrPaßßword!!',
    	    'registrationPasswordRepeat'=>'mySpe§§ialChÄrPaßßword!!', 'registrationEmail'=>'_123unit@test.test',
            'registrationSubmit'=>'submit'),
    array('REMOTE_ADDR'=>'127.0.0.1')),
    array(
    array('registrationUsername'=>'1234567', 'registrationPassword'=>'__bla__',
    	    'registrationPasswordRepeat'=>'__bla__', 'registrationEmail'=>'unit2test@unit.at',
            'registrationSubmit'=>'submit'),
    array('REMOTE_ADDR'=>'127.0.0.1'))
    );
    return $dataArray;
  }
}
?>
