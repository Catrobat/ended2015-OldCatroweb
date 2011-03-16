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
   * @dataProvider validGender
   */
  public function testCheckGender($gender) {
    try {
      $this->assertTrue($this->obj->checkGender($gender));
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
    }
  }
  
  /**
   * @dataProvider invalidGender
   */
  public function testCheckInvalidGender($gender) {
    try {
      $this->obj->checkGender($gender);
    } catch(Exception $e) {
      return; 
    }
    $this->fail('EXPECTED EXCEPTION NOT RAISED!');
  }

    /**
   * @dataProvider validBirth
   */
  public function testCheckBirth($month, $year) {
    try {
      $this->assertTrue($this->obj->checkBirth($month, $year));
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
    }
  }
  
  /**
   * @dataProvider invalidBirth
   */
  public function testCheckInvalidBirth($month, $year) {
    try {
      $this->obj->checkBirth($month, $year);
    } catch(Exception $e) {
      return; 
    }
    $this->fail('EXPECTED EXCEPTION NOT RAISED!');
  }

  /**
   * @dataProvider validCountry
   */
  public function testCheckCountry($country) {
    try {
      $this->assertTrue($this->obj->checkCountry($country));
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
    }
  }
  
  /**
   * @dataProvider invalidCountry
   */
  public function testCheckInvalidCountry($country) {
    try {
      $this->obj->checkCountry($country);
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
    array('myus-ername'),   //contains invalid char
    array('unit_test')    //contains invalid char
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

  public function validGender() {
    $dataArray = array(
    array('male'),
    array('female'),
    );
    return $dataArray;
  }

  public function invalidGender() {
    $dataArray = array(
    array(''),
    array('0'),
    array('some-gender'),
    );
    return $dataArray;
  }
  
  public function validBirth() {
    $currentYear = strftime("%Y");
    $dataArray = array(
    array('1', $currentYear),
    array('2', '1920'),
    array('3', '1980'),
    array('4', '1998'),
    array('5', '2001'),
    array('6', '2002'),
    array('7', '2003'),
    array('8', '2004'),
    array('9', '2005'),
    array('10', '2006'),
    array('11', '2007'),
    array('12', '1950'),
    );
    return $dataArray;
  }

  public function invalidBirth() {
    $dataArray = array(
    array('01', '2020'),
    array('02', '2920'),
    array('03', '1900'),
    array('04', '199'),
    array('16', '2002'),
    array('-2', '2003'),
    array('13', '2004'),
    array('AA', '2005'),
    array('00', 'A002'),
    array('00', '0000'),
    array(' ', '    '),
    array('', ',,,,'),
    array('', ''),
    array('0', '0'),
    );
    return $dataArray;
  }

  public function validCountry() {
    $dataArray = array(
    array('AT'),
    array('DE'),
    array('US'),
    array('GB'),
    array('undef'),
    );
    return $dataArray;
  }

  public function invalidCountry() {
    $dataArray = array(
    array('ATX'),
    array('DAX'),
    array('U'),
    array('A0'),
    array('AA '),
    array(' AA'),
    array('  '),
    array('0A'),
    array(''),
    array('0'),
    array('-'),
    array('undefined'),
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
