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
class registration extends CoreAuthenticationNone {

  public function __construct() {
    parent::__construct();
    $this->setupBoard();
  }

  public function __default() {
  }

  public function registrationRequest() {
    if($_POST) {
      $this->doRegistration($_POST, $_SERVER);
    }
  }

  public function doRegistration($postData, $serverData) {
    $answer = '';
    $statusCode = 500;
    $registrationDataValid = true;
    $catroidRegistrationSuccess = false;
    $boardRegistrationSuccess = false;
    $wikiRegistrationSuccess = false;

    try {
      $this->checkUsername($postData['registrationUsername']);
    } catch(Exception $e) {
      $registrationDataValid = false;
      $answer .= $e->getMessage().'<br>';
    }
    try {
      $this->passOk = $this->checkPassword($postData['registrationUsername'], $postData['registrationPassword']);
    } catch(Exception $e) {
      $registrationDataValid = false;
      $answer .= $e->getMessage().'<br>';
    }
    try {
      $this->checkEmail($postData['registrationEmail']);
    } catch(Exception $e) {
      $registrationDataValid = false;
      $answer .= $e->getMessage().'<br>';
    }
    try {
      $this->checkCountry($postData['registrationCountry']);
    } catch(Exception $e) {
      $registrationDataValid = false;
      $answer .= $e->getMessage().'<br>';
    }
     
    if($registrationDataValid) {
      try {
        $catroidUserId = $this->doCatroidRegistration($postData, $serverData);
        $catroidRegistrationSuccess = true;
        $answer .= $this->languageHandler->getString('catroid_success').'<br>';
      } catch(Exception $e) {
        $answer = $this->errorHandler->getError('registration', 'catroid_registration_failed', $e->getMessage()).'<br>';
      }
      if($catroidRegistrationSuccess) {
        try {
          $boardUserId = $this->doBoardRegistration($postData);
          $boardRegistrationSuccess = true;
          $answer .= $this->languageHandler->getString('board_success').'<br>';
        } catch(Exception $e) {
          $answer = $this->errorHandler->getError('registration', 'board_registration_failed', $e->getMessage()).'<br>';
        }
        if($boardRegistrationSuccess) {
          try {
            $wikiUserId = $this->doWikiRegistration($postData);
            $wikiRegistrationSuccess = true;
            $answer .= $this->languageHandler->getString('wiki_success').'<br>';
          } catch(Exception $e) {
            $answer = $this->errorHandler->getError('registration', 'wiki_registration_failed', $e->getMessage()).'<br>';
          }
          if(!$wikiRegistrationSuccess) {
            //undo catroid & board reg
            try {
              $this->undoCatroidRegistration($catroidUserId);
            } catch(Exception $e) {
              $answer = $this->errorHandler->getError('registration', 'catroid_registration_failed', $e->getMessage()).'<br>';
            }
            try {
              $this->undoBoardRegistration($boardUserId);
            } catch(Exception $e) {
              $answer = $this->errorHandler->getError('registration', 'catroid_registration_failed', $e->getMessage()).'<br>';
            }
          }
        } else {
          //undo catroid reg
          try {
            $this->undoCatroidRegistration($catroidUserId);
          } catch(Exception $e) {
            $answer = $this->errorHandler->getError('registration', 'catroid_registration_failed', $e->getMessage()).'<br>';
          }
        }
      }
    }

    $this->answer .= $answer;
    $this->statusCode = $statusCode;

    if($boardRegistrationSuccess && $wikiRegistrationSuccess && $catroidRegistrationSuccess) {
      require_once 'modules/api/login.php';
      $login = new login();
      $login->doLogin(array('loginUsername'=>$postData['registrationUsername'], 'loginPassword'=>$postData['registrationPassword']));
      $this->statusCode = 200;
      return array("catroidUserId"=>$catroidUserId, "boardUserId"=>$boardUserId, "wikiUserId"=>$wikiUserId);
    } else {
      $this->postData = $postData;
      return false;
    }
  }

  public function doCatroidRegistration($postData, $serverData) {
    global $phpbb_root_path;
    require_once($phpbb_root_path .'includes/utf/utf_tools.php');

    $username = $postData['registrationUsername'];
    $usernameClean = utf8_clean_string($username);
    $password = md5($postData['registrationPassword']);
    $email = $postData['registrationEmail'];
    $ip_registered = $serverData['REMOTE_ADDR'];
    $country = $postData['registrationCountry'];
    $status = USER_STATUS_STRING_ACTIVE;
    $year = $postData['registrationYear'];
    $month = $postData['registrationMonth'];
    if($year == 0) {
      $year = '1900';
    }
    if($month == 0) {
      $month = '01';
    }
    $date_of_birth = $year.'-'.sprintf("%02d", $month).'-01 00:00:01';

    $gender = $postData['registrationGender'];
    $city = $postData['registrationCity'];

    $md5user = md5($username);
    $md5pass = md5($password);
    $authToken = md5($md5user.":".$md5pass);

    $query = "EXECUTE user_registration('$username', '$usernameClean', '$password', '$email', '$date_of_birth', '$gender', '$country', '$city', '$ip_registered', '$status', '$authToken')";
    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    $row = pg_fetch_assoc($result);
    $userId = $row['id'];
    return $userId;
  }

  public function doBoardRegistration($postData) {
    global $user, $auth, $phpbb_root_path;
    $user->session_begin();
    $auth->acl($user->data);
    $user->setup();

    require_once($phpbb_root_path .'includes/functions_user.php');

    $username = $postData['registrationUsername'];
    $password = md5($postData['registrationPassword']);
    $email = $postData['registrationEmail'];

    $user_row = array(
      'username' => $username,
      'user_password' => $password, 
      'user_email' => '', //$email,
      'group_id' => '2',
      'user_timezone' => '0',
      'user_dst' => '0',
      'user_lang' => 'en',
      'user_type' => '0',
      'user_actkey' => '',
      'user_dateformat' => 'D M d, Y g:i a',
      'user_style' => '1',
      'user_regdate' => time()
    );
    if($phpbb_user_id = user_add($user_row)) {
      return $phpbb_user_id;
    } else {
      throw new Exception($this->errorHandler->getError('registration', 'board_registration_failed'));
    }
    return false;
  }

  public function doWikiRegistration($postData) {
    $wikiDbConnection = pg_connect("host=".DB_HOST_WIKI." dbname=".DB_NAME_WIKI." user=".DB_USER_WIKI." password=".DB_PASS_WIKI);
    if(!$wikiDbConnection) {
      throw new Exception($this->errorHandler->getError('db', 'connection_failed', pg_last_error($this->dbConnection)));
    }
    global $phpbb_root_path;
    require_once($phpbb_root_path .'includes/utf/utf_tools.php');

    $username = $postData['registrationUsername'];
    $username = utf8_clean_string($username);
    $username = mb_convert_case($username, MB_CASE_TITLE, "UTF-8");

    $userToken = md5($username);
    $hexSalt = sprintf("%08x", mt_rand(0, 0x7fffffff));
    $hash = md5($hexSalt.'-'.md5($postData['registrationPassword']));
    $password = ":B:$hexSalt:$hash";

    $query = "INSERT INTO mwuser (user_name, user_token, user_password, user_registration) VALUES ('$username', '$userToken', '$password', now()) RETURNING user_id";
    $result = @pg_query($wikiDbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    $row = pg_fetch_assoc($result);
    $userId = $row['user_id'];
    pg_free_result($result);
    pg_close($wikiDbConnection);
    return $userId;
  }

  public function undoWikiRegistration($userId) {
    $wikiDbConnection = pg_connect("host=".DB_HOST_WIKI." dbname=".DB_NAME_WIKI." user=".DB_USER_WIKI." password=".DB_PASS_WIKI);
    if(!$wikiDbConnection) {
      throw new Exception($this->errorHandler->getError('db', 'connection_failed', pg_last_error($this->dbConnection)));
    }
    $query = "DELETE FROM mwuser WHERE user_id='$userId'";
    $result = @pg_query($wikiDbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($wikiDbConnection)));
    }
    pg_close($wikiDbConnection);
    return true;
  }

  public function undoBoardRegistration($userId) {
    global $user, $auth, $phpbb_root_path;
    $user->session_begin();
    $auth->acl($user->data);
    $user->setup();

    require_once($phpbb_root_path .'includes/functions_user.php');
    user_delete('remove', $userId);
    return true;
  }

  public function undoCatroidRegistration($userId) {
    $query = "EXECUTE delete_user_by_id ('$userId')";
    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    return true;
  }


  public function checkUsername($username) {
    $username = trim($username);
    if(empty($username) && strcmp('0', $username) != 0) {
      throw new Exception($this->errorHandler->getError('registration', 'username_missing'));
    }

    if($this->badWordsFilter->areThereInsultingWords($username)) {
      $statusCode = 506;
      throw new Exception($this->errorHandler->getError('registration', 'insulting_words_in_username_field'));
    }

    //username must not look like an IP-address
    $oktettA = '([1-9][0-9]?)|(1[0-9][0-9])|(2[0-4][0-9])|(25[0-4])';
    $oktettB = '(0)|([1-9][0-9]?)|(1[0-9][0-9])|(2[0-4][0-9])|(25[0-4])';
    $ip = '('.$oktettA.')(\.('.$oktettB.')){2}\.('.$oktettA.')';
    $regEx = '/^'.$ip.'$/';
    if(preg_match($regEx, $username)) {
      throw new Exception($this->errorHandler->getError('registration', 'username_invalid'));
    }

    // # < > [ ] | { }
    if(preg_match('/_|^_$/', $username)) {
      throw new Exception($this->errorHandler->getError('registration', 'username_invalid_underscore'));
    }
    if(preg_match('/#|^#$/', $username)) {
      throw new Exception($this->errorHandler->getError('registration', 'username_invalid_hash'));
    }
    if(preg_match('/\||^\|$/', $username)) {
      throw new Exception($this->errorHandler->getError('registration', 'username_invalid_verticalbar'));
    }
    if(preg_match('/\{|^\{$/', $username) || preg_match('/\}|^\}$/', $username)) {
      throw new Exception($this->errorHandler->getError('registration', 'username_invalid_curlybrace'));
    }
    if(preg_match('/\<|^\<$/', $username) || preg_match('/\>|^\>$/', $username)) {
      throw new Exception($this->errorHandler->getError('registration', 'username_invalid_lessgreater'));
    }
    if(preg_match('/\[|^\[$/', $username) || preg_match('/\]|^\]$/', $username)) {
      throw new Exception($this->errorHandler->getError('registration', 'username_invalid_squarebracket'));
    }

    global $phpbb_root_path;
    require_once($phpbb_root_path .'includes/utf/utf_tools.php');
    $usernameClean = utf8_clean_string(($username));
    if(empty($usernameClean)) {
      throw new Exception($this->errorHandler->getError('registration', 'username_invalid'));
    }

    if(in_array($username, getUsernameBlacklistArray()) || in_array($usernameClean, getUsernameBlacklistArray())) {
      throw new Exception($this->errorHandler->getError('registration', 'username_blacklisted'));
    }

    $query = "EXECUTE get_user_row_by_username_or_username_clean('$username', '$usernameClean')";
    $result = pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    if(pg_num_rows($result) > 0) {
      throw new Exception($this->errorHandler->getError('registration', 'username_already_exists'));
    }
    return true;
  }

  public function checkPassword($username, $password) {
    $username = trim($username);
    if((empty($password) && strcmp('0', $password) != 0) || $password == '' || mb_strlen($password) < 1) {
      throw new Exception($this->errorHandler->getError('registration', 'password_missing'));
    }
    if(strcmp($username, $password) != 0) {
      $text = '.{'.USER_MIN_PASSWORD_LENGTH.','.USER_MAX_PASSWORD_LENGTH.'}';
      $regEx = '/^'.$text.'$/';
      if(!preg_match($regEx, $password)) {
        throw new Exception($this->errorHandler->getError('registration', 'password_length_invalid', '', USER_MIN_PASSWORD_LENGTH, USER_MAX_PASSWORD_LENGTH));
      }
    } else {
      throw new Exception($this->errorHandler->getError('registration', 'username_password_equal'));
    }
    return true;
  }

  public function checkEmail($email) {
    $email = trim($email);
    if(empty($email) && strcmp('0', $email) != 0) {
      throw new Exception($this->errorHandler->getError('registration', 'email_missing'));
    }

    $name = '[a-zA-Z0-9]((\.|\-|_)?[a-zA-Z0-9])*';
    $domain = '[a-zA-Z]((\.|\-)?[a-zA-Z0-9])*';
    $tld = '[a-zA-Z]{2,8}';
    $regEx = '/^('.$name.')@('.$domain.')\.('.$tld.')$/';
    if(!preg_match($regEx, $email)) {
      throw new Exception($this->errorHandler->getError('registration', 'email_invalid'));
    }

    $query = "EXECUTE get_user_row_by_email('$email')";
    $result = pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    if(pg_num_rows($result) > 0) {
      throw new Exception($this->errorHandler->getError('registration', 'email_already_exists'));
    }
    return true;
  }

  public function checkGender($gender) {
    if(strcmp ( $gender , 'male' ) == 0 || strcmp ( $gender , 'female' ) == 0) {
      return true;
    }
    else {
      throw new Exception($this->errorHandler->getError('registration', 'gender_missing'));
    }
  }

  public function checkBirth($month,$year) {
    $cyear = strftime("%Y");
    if (($month >= 1 && $month <= 12) && ($year <= $cyear && $year >= $cyear-100)) {
      return true;
    } else {
      throw new Exception($this->errorHandler->getError('registration', 'birth_missing'));
    }
  }

  public function checkCountry($country) {
    if ($country == "undef") {
      return true;
    } elseif (strlen($country) == 2 && preg_replace("/[A-Z]/", "", $country) == "") {
      return true;
    } else {
      throw new Exception($this->errorHandler->getError('registration', 'country_missing'));
    }
  }

  public function deleteRegistration($userId, $boardUserId, $wikiUserId) {
    try {
      $this->undoWikiRegistration($userId);
    } catch(Exception $e) {
      $answer = $this->errorHandler->getError('registration', 'catroid_registration_failed', $e->getMessage()).'<br>';
    }
    try {
      $this->undoBoardRegistration($boardUserId);
    } catch(Exception $e) {
      $answer = $this->errorHandler->getError('registration', 'catroid_registration_failed', $e->getMessage()).'<br>';
    }
    try {
      $this->undoWikiRegistration($wikiUserId);
    } catch(Exception $e) {
      $answer = $this->errorHandler->getError('registration', 'catroid_registration_failed', $e->getMessage()).'<br>';
    }
    return true;

  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
