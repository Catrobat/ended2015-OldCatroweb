<?php
/**
 *    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2012 The Catroid Team
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

class userFunctionsTests extends PHPUnit_Framework_TestCase {
  protected $obj;
  protected $upload;    
  protected $insertIDArray = array();
  protected $dbConnection;
  
  protected function setUp() {
    require_once CORE_BASE_PATH . 'modules/common/userFunctions.php';
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
        $this->assertEquals($e->getMessage(), "http://catroid.local/catroid/passwordrecovery?c=" . $hash);
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
    $this->assertTrue($this->obj->checkLoginData("catroweb", md5("cat.roid.web")));
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
    $_REQUEST['token'] = $this->obj->generateAuthenticationToken($postData['registrationUsername'], $postData['registrationPassword']);

    $this->obj->tokenAuthentication();
    $this->assertTrue($this->obj->isLoggedIn());
  }

  /**
   * @dataProvider validRegistrationData
   */
  public function testLoginLogout($postData) {
    try {
      $this->obj->register($postData);
      $this->obj->login($postData['registrationUsername'], $postData['registrationPassword']);

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

  public function testGenerateAuthenticationToken() {
    $this->assertEquals($this->obj->generateAuthenticationToken('catroweb', 'cat.roid.web'), '31df676f845b4ce9908f7a716a7bfa50');
  }

  /**
   * @dataProvider validRegistrationData
   */
  public function testUpdatePassword($postData) {
    $newPassword = "testBlaBlub";
    try {
      $this->obj->register($postData);
      $this->obj->login($postData['registrationUsername'], $postData['registrationPassword']);
      
      $this->obj->updatePassword($postData['registrationUsername'], $newPassword);
      $this->obj->logout();
      $this->obj->login($postData['registrationUsername'], $newPassword);

      $this->obj->undoRegister();
      $this->assertTrue(true);
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED (origin: ' . $e->getLine() . '): ' . $e->getMessage());
    }
  }

  /**
   * @dataProvider validRegistrationData
   */
  public function testUpdateCity($postData) {
    $newCity = "NewCity";
    try {
      $this->obj->register($postData);
      try {
        $this->obj->updateCity($newCity);
        $this->fail('EXPECTED EXCEPTION NOT RAISED!');
      } catch(Exception $e) {
        $this->assertEquals($e->getMessage(), "There was a problem while updating your city.");
      }
      $this->obj->login($postData['registrationUsername'], $postData['registrationPassword']);
      
      $data = $this->obj->getUserData($postData['registrationUsername']);
      $this->assertEquals($data['city'], $postData['registrationCity']);
      $this->obj->updateCity($newCity);
      $data = $this->obj->getUserData($postData['registrationUsername']);
      $this->assertEquals($data['city'], $newCity);
      
      $this->obj->undoRegister();
      $this->assertTrue(true);
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
  public function testUpdateGender($postData) {
    $newGender = "female";
    try {
      $this->obj->register($postData);
      try {
        $this->obj->updateGender($newGender);
        $this->fail('EXPECTED EXCEPTION NOT RAISED!');
      } catch(Exception $e) {
        $this->assertEquals($e->getMessage(), "There was a problem while updating your gender.");
      }
      $this->obj->login($postData['registrationUsername'], $postData['registrationPassword']);
      
      $data = $this->obj->getUserData($postData['registrationUsername']);
      $this->assertEquals($data['gender'], $postData['registrationGender']);
      $this->obj->updateGender($newGender);
      $data = $this->obj->getUserData($postData['registrationUsername']);
      $this->assertEquals($data['gender'], $newGender);
      
      $this->obj->undoRegister();
      $this->assertTrue(true);
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED (origin: ' . $e->getLine() . '): ' . $e->getMessage());
    }
  }

  /**
   * @dataProvider validRegistrationData
   */
  public function testUpdateBirthday($postData) {
    $newMonth = "12";
    $newYear = "2012";
    try {
      $this->obj->register($postData);
      try {
        $this->obj->updateBirthday($newMonth, $newYear);
        $this->fail('EXPECTED EXCEPTION NOT RAISED!');
      } catch(Exception $e) {
        $this->assertEquals($e->getMessage(), "There was a problem while updating your birthday data.");
      }
      $this->obj->login($postData['registrationUsername'], $postData['registrationPassword']);
      
      $data = $this->obj->getUserData($postData['registrationUsername']);
      $this->assertEquals($data['month'], $postData['registrationMonth']);
      $this->assertEquals($data['year'], $postData['registrationYear']);

      $this->obj->updateBirthday($newMonth, $newYear);
      $data = $this->obj->getUserData($postData['registrationUsername']);
      $this->assertEquals($data['month'], $newMonth);
      $this->assertEquals($data['year'], $newYear);

      $this->obj->updateBirthday('', '');
      $data = $this->obj->getUserData($postData['registrationUsername']);
      $this->assertEquals($data['month'], null);
      $this->assertEquals($data['year'], null);

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
    
      $data = $this->obj->getEmailAddresses($this->obj->session->userLogin_userId);
      $this->assertTrue(in_array($postData['registrationEmail'], $data));
      
      $this->obj->addEmailAddress($this->obj->session->userLogin_userId, $newEmail);
      $data = $this->obj->getEmailAddresses($this->obj->session->userLogin_userId);
      $this->assertTrue(in_array($postData['registrationEmail'], $data));
      $this->assertTrue(in_array($newEmail, $data));
      
      $this->obj->deleteEmailAddress($postData['registrationEmail']);
      $data = $this->obj->getEmailAddresses($this->obj->session->userLogin_userId);
      $this->assertFalse(in_array($postData['registrationEmail'], $data));
      $this->assertTrue(in_array($newEmail, $data));
      
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
        $this->assertEquals($e->getMessage(), "http://catroid.local/catroid/passwordrecovery?c=" . $hash);
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
}
?>
