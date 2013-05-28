<?php
/*
 * Catroid: An on-device visual programming system for Android devices
 * Copyright (C) 2010-2013 The Catrobat Team
 * (<http://developer.catrobat.org/credits>)
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * An additional term exception under section 7 of the GNU Affero
 * General Public License, version 3, is available at
 * http://developer.catrobat.org/license_additional_term
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

require_once('testsBootstrap.php');
require_once CORE_BASE_PATH . 'modules/common/userFunctions.php';

class userFunctionsTests extends PHPUnit_Framework_TestCase {
  protected $obj;
  protected $upload;    
  protected $insertIDArray = array();
  protected $dbConnection;
  
  protected function setUp() {
    $this->obj = new userFunctions();
    $_SERVER['REMOTE_ADDR'] = "127.0.0.1";
  } 
  
  public function testIsLoggedIn() {
    $this->obj->session->userLogin_userId = 0;
    $this->obj->session->userLogin_userNickname = '';
    $this->assertFalse($this->obj->isLoggedIn());

    $this->obj->session->userLogin_userNickname = 'catroweb';
    $this->assertFalse($this->obj->isLoggedIn());

    $this->obj->session->userLogin_userId = 1;
    $this->assertTrue($this->obj->isLoggedIn());
  }

  /**
   * @dataProvider validRegistrationData
   */
  public function testIsRecoveryHashValid($postData) {
    try {
      $this->obj->register($postData);
      
      $data = $this->obj->getUserDataForRecovery($postData['registrationUsername']);
      $hash = $this->obj->createUserHash($data);
      try {
        $this->obj->isRecoveryHashValid($hash);
        $this->fail('EXPECTED EXCEPTION NOT RAISED!');
      } catch(Exception $e) {
        $this->assertEquals($e->getMessage(), "Recovery hash was not found.");
      }
      
      try {
        $this->obj->sendPasswordRecoveryEmail($hash, $data['id'], $data['username'], $data['email']);
        $this->fail('EXPECTED EXCEPTION NOT RAISED!');
      } catch(Exception $e) {
        $this->assertEquals($e->getMessage(), "http://catroid.local/passwordrecovery?c=" . $hash);
      }

      $this->obj->isRecoveryHashValid($hash);
      $this->assertTrue(true);
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED (origin: ' . $e->getLine() . '): ' . $e->getMessage());
    }
  }

  public function testCheckUserExists() {
    $this->assertFalse($this->obj->checkUserExists("abc"));
    $this->assertTrue($this->obj->checkUserExists("catroweb"));
  }

  /**
   * @dataProvider validUsername
   */
  public function testCheckUsernameValid($data) {
    try {
      $this->obj->checkUsername($data);
      $this->assertTrue(true);
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED (origin: ' . $e->getLine() . '): ' . $e->getMessage());
    }
  }

  /**
   * @dataProvider invalidUsername
   */
  public function testCheckUsernameInvalid($data) {
    try {
      $this->obj->checkUsername($data[0]);
      $this->fail('EXPECTED EXCEPTION NOT RAISED!');
    } catch(Exception $e) {
      $this->assertEquals($e->getMessage(), $data[1]);
    }
  }

  /**
   * @dataProvider validPassword
   */
  public function testCheckPasswordValid($data) {
    try {
      $this->obj->checkPassword($data[0], $data[1]);
      $this->assertTrue(true);
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED (origin: ' . $e->getLine() . '): ' . $e->getMessage());
    }
  }

  /**
   * @dataProvider invalidPassword
   */
  public function testCheckPasswordInvalid($data) {
    try {
      $this->obj->checkPassword($data[0], $data[1]);
      $this->fail('EXPECTED EXCEPTION NOT RAISED!');
    } catch(Exception $e) {
      $this->assertEquals($e->getMessage(), $data[2]);
    }
  }

  public function testCheckLoginData() {
    $this->assertFalse($this->obj->checkLoginData("", ""));
    $this->assertTrue($this->obj->checkLoginData("catroweb", $this->obj->hashPassword("catroweb", "cat.roid.web")));
  }

  /**
   * @dataProvider validEmail
   */
  public function testCheckEmailValid($data) {
    try {
      $this->obj->checkEmail($data);
      $this->assertTrue(true);
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED (origin: ' . $e->getLine() . '): ' . $e->getMessage());
    }
  }

  /**
   * @dataProvider invalidEmail
   */
  public function testCheckEmailInvalid($data) {
    try {
      $this->obj->checkEmail($data[0]);
      $this->fail('EXPECTED EXCEPTION NOT RAISED!');
    } catch(Exception $e) {
      $this->assertEquals($e->getMessage(), $data[1]);
    }
  }

  /**
   * @dataProvider validCountry
   */
  public function testCheckCountryValid($data) {
    try {
      $this->obj->checkCountry($data);
      $this->assertTrue(true);
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED (origin: ' . $e->getLine() . '): ' . $e->getMessage());
    }
  }

  /**
   * @dataProvider invalidCountry
   */
  public function testCheckCountryInvalid($data) {
    try {
      $this->obj->checkCountry($data[0]);
      $this->fail('EXPECTED EXCEPTION NOT RAISED!');
    } catch(Exception $e) {
      $this->assertEquals($e->getMessage(), $data[1]);
    }
  }

  /**
   * @dataProvider validRegistrationData
   */
  public function testTokenAuthentication($postData) {
    $this->obj->register($postData);
    
    $this->assertFalse($this->obj->isLoggedIn());

    $usernameClean = utf8_clean_string(trim($postData['registrationUsername']));
    $result = pg_execute($this->obj->dbConnection, "get_user_token", array($usernameClean));
    if($result) {
      $row = pg_fetch_array($result);
      $_REQUEST['username'] = $postData['registrationUsername'];
      $_REQUEST['token'] = $row['auth_token'];
      pg_free_result($result);
    }
    
    $this->obj->tokenAuthentication();
    $this->assertTrue($this->obj->isLoggedIn());
  }

  /**
   * @dataProvider validRegistrationData
   */
  public function testLoginLogout($postData) {
    try {
      $this->obj->register($postData);
      $token = $this->obj->login($postData['registrationUsername'], $postData['registrationPassword']);
      $this->assertNotEquals('-1', $token);

      $this->assertGreaterThan(0, intval($this->obj->session->userLogin_userId));
      $this->assertEquals($postData['registrationUsername'], $this->obj->session->userLogin_userNickname);
      
      $this->obj->logout();

      $this->assertEquals(0, intval($this->obj->session->userLogin_userId));
      $this->assertEquals('', $this->obj->session->userLogin_userNickname);
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED (origin: ' . $e->getLine() . '): ' . $e->getMessage());
    }
  }

  /**
   * @dataProvider validRegistrationData
   */
  public function testTemporarilyLoginBlock($postData) {
    try {
      $this->obj->register($postData);
      
      $count = 5;
      while($count-- > 0) {
        try {
          $this->obj->login($postData['registrationUsername'], "wrong password");
          $this->fail('EXPECTED EXCEPTION NOT RAISED!');
        } catch(Exception $e) {
          $this->assertEquals($e->getMessage(), "The password or username was incorrect.");
        }
      }
      
      try {
        $this->obj->login($postData['registrationUsername'], "wrong password");
        $this->fail('EXPECTED EXCEPTION NOT RAISED!');
      } catch(Exception $e) {
        $this->assertEquals($e->getMessage(), "Your IP-Address has been blocked for 30 seconds.");
        pg_execute($this->obj->dbConnection, "reset_failed_attempts", array($_SERVER["REMOTE_ADDR"]));
      }
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED (origin: ' . $e->getLine() . '): ' . $e->getMessage());
    }
  }

  /**
   * @dataProvider validRegistrationData
   */
  public function testRegister($postData) {
    try {
      $this->obj->register($postData);
      $this->obj->undoRegister();
      $this->assertTrue(true);
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED (origin: ' . $e->getLine() . '): ' . $e->getMessage());
    }
  }

  /**
   * @dataProvider validRegistrationData
   */
  public function testUpdateAuthToken($postData) {
    $newPassword = "testBlaBlub";
    $usernameClean = utf8_clean_string(trim($postData['registrationUsername']));
    try {
      $this->obj->register($postData);
    
      $pgAuthTokenBefore = '';
      $result = pg_execute($this->obj->dbConnection, "get_user_token", array($usernameClean));
      if($result) {
        $row = pg_fetch_array($result);
        $pgAuthTokenBefore = $row['auth_token'];
        pg_free_result($result);
      }

      $this->obj->updatePassword($postData['registrationUsername'], $newPassword);

      $pgAuthTokenAfter = '';
      $result = pg_execute($this->obj->dbConnection, "get_user_token", array($usernameClean));
      if($result) {
        $row = pg_fetch_array($result);
        $pgAuthTokenAfter = $row['auth_token'];
        pg_free_result($result);
      }

      $this->assertEquals(32, strlen($pgAuthTokenAfter));
      $this->assertNotEquals($pgAuthTokenBefore, $pgAuthTokenAfter);
      
      $this->obj->undoRegister();
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED (origin: ' . $e->getLine() . '): ' . $e->getMessage());
    }
  }
  
  /**
   * @dataProvider validRegistrationData
   */
  public function testUpdatePassword($postData) {
    $newPassword = "testBlaBlub";
    $usernameClean = utf8_clean_string(trim($postData['registrationUsername']));
    try {
      $this->obj->register($postData);
      $this->obj->login($postData['registrationUsername'], $postData['registrationPassword']);
      $this->obj->updatePassword($postData['registrationUsername'], $newPassword);
      $this->obj->logout();
      
      $this->assertFalse($this->obj->isLoggedIn());
      
      try{
        $this->obj->login($postData['registrationUsername'], $postData['registrationPassword']);
      } catch (Exception $e) { 
        $this->assertFalse($this->obj->isLoggedIn());
      }
      
      $this->obj->login($postData['registrationUsername'], $newPassword);      
      $this->assertTrue($this->obj->isLoggedIn());
      $this->obj->logout();
      $this->assertFalse($this->obj->isLoggedIn());
      $this->obj->undoRegister();
      
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED (origin: ' . $e->getLine() . '): ' . $e->getMessage());
    }
  }

  /**
   * @dataProvider validRegistrationData
   */
  public function testUpdateCountry($postData) {
    $newCountry = "XX";
    try {
      $this->obj->register($postData);
      try {
        $this->obj->updateCountry($newCountry);
        $this->fail('EXPECTED EXCEPTION NOT RAISED!');
      } catch(Exception $e) {
        $this->assertEquals($e->getMessage(), "There was a problem while updating your home country.");
      }
      $this->obj->login($postData['registrationUsername'], $postData['registrationPassword']);
      
      $data = $this->obj->getUserData($postData['registrationUsername']);
      $this->assertEquals($data['country'], $postData['registrationCountry']);
      $this->obj->updateCountry($newCountry);
      $data = $this->obj->getUserData($postData['registrationUsername']);
      $this->assertEquals($data['country'], $newCountry);
      
      $this->obj->undoRegister();
      $this->assertTrue(true);
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED (origin: ' . $e->getLine() . '): ' . $e->getMessage());
    }
  }

  /**
   * @dataProvider validRegistrationData
   */
  public function testUpdateLanguage($postData) {
    $newLanguage = "de";
    try {
      $this->obj->register($postData);
      $this->obj->login($postData['registrationUsername'], $postData['registrationPassword']);
      
      $data = $this->obj->getUserData($postData['registrationUsername']);
      $this->assertEquals($data['language'], $postData['registrationLanguage']);
      $this->obj->updateLanguage($newLanguage);
      $data = $this->obj->getUserData($postData['registrationUsername']);
      $this->assertEquals($data['language'], $newLanguage);
      
      $this->obj->undoRegister();
      $this->assertTrue(true);
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED (origin: ' . $e->getLine() . '): ' . $e->getMessage());
    }
  }
  
  /**
   * @dataProvider validRegistrationData
   */
  public function testEmailActions($postData) {
    $newEmail = "myNewMail@catroid.org";
    try {
      $this->obj->register($postData);
      $this->obj->login($postData['registrationUsername'], $postData['registrationPassword']);
    
      $data = $this->obj->getUserData($this->obj->session->userLogin_userNickname);
      $this->assertTrue($this->in_arrayr($postData['registrationEmail'], $data));
      
      try {
        $this->obj->updateAdditionalEmailAddress($this->obj->session->userLogin_userId, $newEmail);
        $this->fail('EXPECTED EXCEPTION NOT RAISED!');
      } catch(Exception $e) {
        $this->assertEquals(200, $e->getCode());
      }
      $data = $this->obj->getUserData($this->obj->session->userLogin_userNickname);
      $this->assertTrue($this->in_arrayr($postData['registrationEmail'], $data));
      $this->assertTrue($this->in_arrayr($newEmail, $data));
      
      try {
        $this->obj->updateEmailAddress($this->obj->session->userLogin_userId, '');
        $this->fail('EXPECTED EXCEPTION NOT RAISED!');
      } catch(Exception $e) {
        $this->assertEquals($e->getMessage(), "Error while updating this e-mail address. You must have at least one validated e-mail address.");
      }
      
      $this->obj->updateAdditionalEmailAddress($this->obj->session->userLogin_userId, '');
      $data = $this->obj->getUserData($this->obj->session->userLogin_userNickname);
      $this->assertFalse($this->in_arrayr($newEmail, $data));
      $this->assertTrue($this->in_arrayr($postData['registrationEmail'], $data));
      
      $this->obj->undoRegister();
      $this->assertTrue(true);
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED (origin: ' . $e->getLine() . '): ' . $e->getMessage());
    }    
  }

  /**
   * @dataProvider validRegistrationData
   */
  public function testPasswordRecovery($postData) {
    $newPassword = "myNewPassword123";
    try {
      $this->obj->register($postData);
  
      $data = $this->obj->getUserDataForRecovery($postData['registrationUsername']);
      $hash = $this->obj->createUserHash($data);
      try {
        $this->obj->isRecoveryHashValid($hash);
        $this->fail('EXPECTED EXCEPTION NOT RAISED!');
      } catch(Exception $e) {
        $this->assertEquals($e->getMessage(), "Recovery hash was not found.");
      }
  
      try {
        $this->obj->sendPasswordRecoveryEmail($hash, $data['id'], $data['username'], $data['email']);
        $this->fail('EXPECTED EXCEPTION NOT RAISED!');
      } catch(Exception $e) {
        $this->assertEquals($e->getMessage(), "http://catroid.local/passwordrecovery?c=" . $hash);
      }
  
      $this->obj->isRecoveryHashValid($hash);
      $data = $this->obj->getUserDataByRecoveryHash($hash);
      $this->assertNotEquals(0, intval($data['recovery_time']));
      $this->assertEquals($hash, $data['recovery_hash']);
      
      $this->obj->updatePassword($postData['registrationUsername'], $newPassword);
      try {
        $this->obj->isRecoveryHashValid($hash);
        $this->fail('EXPECTED EXCEPTION NOT RAISED!');
      } catch(Exception $e) {
        $this->assertEquals($e->getMessage(), "Recovery hash was not found.");
      }
      
      $this->assertTrue(true);
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED (origin: ' . $e->getLine() . '): ' . $e->getMessage());
    }
  }

  public function validRegistrationData() {
    $dataArray = array(
        array(
            array('registrationUsername' => 'myUnitTestUsername',
                'registrationPassword' => 'myPassword123',
                'registrationEmail' => 'unittest@unit.test',
                'registrationGender' => 'male',
                'registrationMonth' => '1',
                'registrationYear' => '1980',
                'registrationLanguage' => 'en',
                'registrationCountry' => 'at',
                'registrationCity' => 'Graz'
            )
        ), array(
            array('registrationUsername' => '卡爾迈耶',
                'registrationPassword' => 'мойпароль123',
                'registrationEmail' => 'unittest@unit.test',
                'registrationGender' => 'male',
                'registrationMonth' => '1',
                'registrationYear' => '1980',
                'registrationLanguage' => 'en',
                'registrationCountry' => 'at',
                'registrationCity' => 'グラーツ'
            )
        )
    );
    return $dataArray;
  }

  
  public function validUsername() {
    $dataArray = array(
        array('myVeryNewUsername'),
        array('funny-dragon'),
        array('ゲーム.'),
        array('проектПоУмолчанию'),
        array('äpfel-sind-gesund')
    );
    return $dataArray;
  }

  public function invalidUsername() {
    $dataArray = array(
        array(array('', 'The nickname is missing.')),
        array(array('username invalid _', 'The nickname is invalid. Underscores (_) are not allowed.')),
        array(array('username invalid #', 'The nickname is invalid. Hash signs (#) are not allowed.')),
        array(array('username invalid |', 'The nickname is invalid. Vertical bars (|) are not allowed.')),
        array(array('username invalid {', 'The nickname is invalid. Curly braces ({ or }) are not allowed.')),
        array(array('username invalid }', 'The nickname is invalid. Curly braces ({ or }) are not allowed.')),
        array(array('username invalid <', 'The nickname is invalid. Less than or greater than signs (< or >) are not allowed.')),
        array(array('username invalid >', 'The nickname is invalid. Less than or greater than signs (< or >) are not allowed.')),
        array(array('username invalid [', 'The nickname is invalid. Square brackets ([ or ]) are not allowed.')),
        array(array('username invalid ]', 'The nickname is invalid. Square brackets ([ or ]) are not allowed.')),
        array(array('username invalid', 'The nickname is invalid. Spaces (" ") are not allowed.')),
        array(array('129.0.12.123', 'The nickname is invalid.')),
        array(array('aDmIn', 'This nickname is on the blacklist and not allowed.')),
        array(array('caTRoid', 'This nickname is on the blacklist and not allowed.')),
        array(array('kittyroiD', 'This nickname is on the blacklist and not allowed.')),
        array(array('anonymous', 'This nickname already exists.')),
        array(array('shit', 'The nickname is invalid. There are insulting words in the username field!'))
    );
    return $dataArray;
  }

  public function validPassword() {
    $dataArray = array(
        array(array('catroweb', 'mein-tolles-passwort')),
        array(array('catroweb', 'ein-a#d3res-p@sswort'))
    );
    return $dataArray;
  }

  public function invalidPassword() {
    $dataArray = array(
        array(array('catroweb', '', 'The password is missing.')),
        array(array('catroweb', 'catroweb', 'The password must differ from the nickname.')),
        array(array('catroweb', 'abc', 'Your password must have at least 6 characters.')),
        array(array('catroweb', 'very-very-very-very-long-password', 'Your password can have a maximum of 32 characters.'))
    );
    return $dataArray;
  }
  
  public function validEmail() {
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

  public function invalidEmail() {
    $dataArray = array(
        array(array('', 'The email address is missing.')),
        array(array('webmaster@catroid.org', 'This email address already exists.')),
        array(array('domain.com', 'The email address is not valid.')),
        array(array('aaa@domain', 'The email address is not valid.')),
        array(array('@domain.com', 'The email address is not valid.')),
        array(array('@domain.com', 'The email address is not valid.')),
        array(array('.a@domain.com', 'The email address is not valid.')),
        array(array('-a@domain.com', 'The email address is not valid.')),
        array(array('a.@domain.com', 'The email address is not valid.')),
        array(array('a-@domain.com', 'The email address is not valid.')),
        array(array('a@.com', 'The email address is not valid.')),
        array(array('a@ゲーム.com', 'The email address is not valid.')),
        array(array('a@.domain.com', 'The email address is not valid.')),
        array(array('a@-domain.com', 'The email address is not valid.')),
        array(array('a@domain..com', 'The email address is not valid.')),
        array(array('a@domain-.com', 'The email address is not valid.')),
        array(array('a@domain.', 'The email address is not valid.')),
        array(array('a@domain. ', 'The email address is not valid.')),
        array(array('a@domain.5', 'The email address is not valid.')),
        array(array('a@domain.c.m', 'The email address is not valid.')),
        array(array('a@domain.c-m', 'The email address is not valid.')),
        array(array('a@domain.c5m', 'The email address is not valid.')),
        array(array('проектПоУмолчанию@sub.domÃ„in-5.com', 'The email address is not valid.'))
    );
    return $dataArray;
  }

  public function validCountry() {
    $dataArray = array(
        array('At'),
        array('dE'),
        array('us'),
        array('GB'),
        array('EM') //stands for empty
    );
    return $dataArray;
  }
  
  public function invalidCountry() {
    $dataArray = array(
        array(array('ATX', 'The country is missing.')),
        array(array('DAX', 'The country is missing.')),
        array(array('U', 'The country is missing.')),
        array(array('A0', 'The country is missing.')),
        array(array('AA ', 'The country is missing.')),
        array(array('  ', 'The country is missing.')),
        array(array('0A', 'The country is missing.')),
        array(array('', 'The country is missing.')),
        array(array('0', 'The country is missing.')),
        array(array('-', 'The country is missing.'))
    );
    return $dataArray;
  }

  protected function tearDown() {
    $this->obj->undoRegister();
  }
  
  private function in_arrayr($needle, $haystack) {
    $found = false;
    foreach($haystack as $value) {
      if(is_array($value)) {
        $found = $this->in_arrayr($needle, $value);
      } elseif($needle === $value) {
        $found = true;
      }
      
      if($found) {
        break;
      }
    }
    return $found;
  }
}
?>
