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
    $this->addCss('registration.css');
    $this->addCss('buttons.css');
    
    $this->addJs('registration.js');
    
    $this->initRegistration();
  }

//  public function __default() {
//    if($_POST) {
//      if(isset($_POST['registrationSubmit'])) {
//        $this->doRegistration($_POST, $_SERVER);
//        $this->initRegistration();
//      }
//    }
//  }

  
  public function __default() {

  }

  public function registrationRequest() {
    $this->registration($_POST);
  }
  
  public function registration($postData) {
    if($postData) {
      $this->doRegistration($_POST, $_SERVER);
      $this->initRegistration();
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
//    try { 
//      $this->checkBirth($postData['registrationMonth'], $postData['registrationYear']);
//    } catch(Exception $e) {
//      $registrationDataValid = false;
//      $answer .= $e->getMessage().'<br>';
//    }
//    try { 
//      $this->checkGender($postData['registrationGender']);
//    } catch(Exception $e) {
//      $registrationDataValid = false;
//      $answer .= $e->getMessage().'<br>';
//    }

    
    if($registrationDataValid) {
      try {
        $catroidUserId = $this->doCatroidRegistration($postData, $serverData);
        $catroidRegistrationSuccess = true;
        $answer .= 'CATROID registration successfull!<br>';
      } catch(Exception $e) {
        $answer = $this->errorHandler->getError('registration', 'catroid_registration_failed', $e->getMessage()).'<br>';
      }
      if($catroidRegistrationSuccess) {
        try {
          $boardUserId = $this->doBoardRegistration($postData);
          $boardRegistrationSuccess = true;
          $answer .= 'BOARD registration successfull!<br>';
        } catch(Exception $e) {
          $answer = $this->errorHandler->getError('registration', 'board_registration_failed', $e->getMessage()).'<br>';
        }
        if($boardRegistrationSuccess) {
          try {
            $wikiUserId = $this->doWikiRegistration($postData);
            $wikiRegistrationSuccess = true;
            $answer .= 'WIKI registration successfull!<br>';
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
    
    $query = "EXECUTE user_registration('$username', '$usernameClean', '$password', '$email', '$date_of_birth', '$gender', '$country', '$city', '$ip_registered', '$status')";
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
      'user_email' => $email,
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
    if(empty($username) && strcmp('0', $username) != 0) {
      throw new Exception($this->errorHandler->getError('registration', 'username_missing'));
    }

//    if(!$this->badWordsFilter->areThereInsultingWords($username)) {
//			$statusCode = 506;
//			throw new Exception($this->errorHandler->getError('registration', 'insulting_words_in_username_field'));
//    }
    
    //username must not look like an IP-address
    $oktettA = '([1-9][0-9]?)|(1[0-9][0-9])|(2[0-4][0-9])|(25[0-4])';
    $oktettB = '(0)|([1-9][0-9]?)|(1[0-9][0-9])|(2[0-4][0-9])|(25[0-4])';
    $ip = '('.$oktettA.')(\.('.$oktettB.')){2}\.('.$oktettA.')';
    $regEx = '/^'.$ip.'$/';
    if(preg_match($regEx, $username)) {
      throw new Exception($this->errorHandler->getError('registration', 'username_invalid'));
    }

    //username must consist of alpha numerical chars and spaces and Umlaute!
    //min. 4, max. 32 chars
    /*$text = '[a-zA-Z0-9äÄöÖüÜß|.| ]{'.USER_MIN_USERNAME_LENGTH.','.USER_MAX_USERNAME_LENGTH.'}';
    $regEx = '/^'.$text.'$/';
    if(!preg_match($regEx, $username)) {
      throw new Exception($this->errorHandler->getError('registration', 'username_invalid'));
    }*/
    
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

    $query = "EXECUTE get_user_row_by_username('".($username)."')";
    $result = pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    if(pg_num_rows($result) > 0) {
      throw new Exception($this->errorHandler->getError('registration', 'username_already_exists'));
    }

    $query = "EXECUTE get_user_row_by_username_clean('$usernameClean')";
    $result = pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    if(pg_num_rows($result) > 0) {
      throw new Exception($this->errorHandler->getError('registration', 'username_aready_exists'));
    }
    return true;
  }

  public function checkPassword($username, $password) {
    if((empty($password) && strcmp('0', $password) != 0) || $password == '' || mb_strlen($password) < 1) {
      throw new Exception($this->errorHandler->getError('registration', 'password_missing'));
    }
    if(strcmp($username, $password) != 0) {
      $text = '.{'.USER_MIN_PASSWORD_LENGTH.','.USER_MAX_PASSWORD_LENGTH.'}';
      $regEx = '/^'.$text.'$/';
      if(!preg_match($regEx, $password)) {
        throw new Exception($this->errorHandler->getError('registration', 'password_length_invalid'));
      }
    } else {
      throw new Exception($this->errorHandler->getError('registration', 'username_password_equal'));
    }
    return true;
  }

  public function checkEmail($email) {
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
  
  
  
  public function initRegistration() {
    $answer = '';
    try {
      $this->initBirth('');
    } catch(Exception $e) {
      $answer .= $e->getMessage().'<br>';
    }
    try {
      $this->initGender();
    } catch(Exception $e) {
      $answer .= $e->getMessage().'<br>';
    }
    try {
      $this->initCountryCodes('');
    } catch(Exception $e) {
      $answer .= $e->getMessage().'<br>';
    }
    $this->answer .= $answer;
  }

  private function initGender() {
    if(!$this->postData['registrationGender']) {
      $genderlist[0] = "<option value=\"0\" selected>select your gender</option>\r";
      $genderlist[1] = "<option value=\"female\">female</option>\r";
      $genderlist[2] = "<option value=\"male\">male</option>\r";
    }
    else {
      $genderlist[0] = "<option value=\"0\">Select</option>\r";
      if($this->postData['registrationGender'] == 'male') {
        $genderlist[1] = "<option value=\"female\">female</option>\r";
        $genderlist[2] = "<option value=\"male\" selected>male</option>\r";
      }
      else {
        $genderlist[1] = "<option value=\"female\" selected>female</option>\r";  
        $genderlist[2] = "<option value=\"male\">male</option>\r";
      }
    }
    $this->gender = $genderlist;
  }
  
  private function initBirth() {
    $months = array(
      0=>"select month",
      1=>"Jan",
      2=>"Feb",
      3=>"Mar",
      4=>"Apr",
      5=>"May",
      6=>"Jun",
      7=>"Jul",
      8=>"Aug",
      9=>"Sep",
      10=>"Oct",
      11=>"Nov",
      12=>"Dec"
    );
    
    $registrationMonth = '';
    $registrationYear = '';
    
    if(!$this->postData['registrationMonth']) {
      $monthlist[0] = "<option value=\"0\" selected>" . $months[0] . "</option>\r";
    }
    else {
      $registrationMonth = $this->postData['registrationMonth'];
      $monthlist[0] = "<option value=\"0\">" . $months[0] . "</option>\r";
    }
    if(!$this->postData['registrationYear']) {
      $yearlist[0] = "<option value=\"0\" selected>select year</option>\r";
    }
    else {
      $registrationYear = $this->postData['registrationYear'];
      $yearlist[0] = "<option value=\"0\">select year</option>\r";
    }
    
    $x = 0;
    while($x++ < 12) {
      if($registrationMonth == $x) {
        $monthlist[] = "<option value=\"" . $x . "\" selected>" . $months[$x] . "</option>\r";
      }
      else {
        $monthlist[] = "<option value=\"" . $x . "\">" . $months[$x] . "</option>\r";
      }
    }
    $x = 0;
    $year = date('Y') + 1;
    while($x++ < 100) {
      $year--;
      if($registrationYear == $year) {
        $yearlist[] = "<option value=\"" . $year . "\" selected>" . $year . "</option>\r";
      }
      else {
        $yearlist[] = "<option value=\"" . $year . "\">" . $year . "</option>\r";
      }
    }
    $this->month = $monthlist;
    $this->year = $yearlist;
  }
  
  private function initCountryCodes() {
    $query = "EXECUTE get_country_from_countries";
    $result = @pg_query($this->dbConnection, $query);

    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection))); 
    }

    if(pg_num_rows($result) > 0) {
      $countrylist = array();
      
      if($this->postData['registrationCountry']) {
        $registrationCountry = $this->postData['registrationCountry'];
        $countrylist[] = "<option value=\"0\">Select country</option>\r";
      }
      else { 
        $registrationCountry = '';
        $countrylist[] = "<option value=\"0\" selected>select your country</option>\r";
      }
      
      while($country = pg_fetch_assoc($result)) {
        if($registrationCountry == $country['code'])
          $countrylist[] = "<option value=\"" . $country['code'] . "\" selected>" . $country['name'] . "</option>\r";
        else
          $countrylist[] = "<option value=\"" . $country['code'] . "\">" . $country['name'] . "</option>\r";           
      }
      if($registrationCountry != 'undef')
        $countrylist[] = "<option value=\"undef\">undefined</option>\r";
      else
        $countrylist[] = "<option value=\"undef\" selected>undefined</option>\r";
      
      $this->countrylist = $countrylist;
      pg_free_result($result);      
    } else {
      $countrylist[] = "<option value=\"0\">select your country</option>";
      $this->countrylist = $countrylist;
      throw new Exception($this->errorHandler->getError('registration', 'country_codes_not_available'));
    }
  }
    
  public function deleteRegistration($userId, $boardUserId, $wikiUserId) {
    // get_userid_by_username
//    try {
//      $query = "EXECUTE get_userid_by_username('$username')";
//  
//      $result = @pg_query($this->dbConnection, $query);
//      if(!$result) {
//        throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
//      }
//      $row = pg_fetch_assoc($result);
//      $userId = $row['id'];
//      $this->answer .= $userId.'<br>';
//    } catch(Exception $e) {
//      $registrationDataValid = false;
//      $answer .= $e->getMessage().'<br>';
//    }
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
  
  
//  private function utfCleanString($string) {
//    global $wikiUpperChars;
//    global $wikiLowerChars;
//    
//    $username = utf8_clean_string($string);
//    $username = mb_convert_case($username, MB_CASE_TITLE, "UTF-8");;
//
//    return $username;
//  }
  
  
  public function __destruct() {
    parent::__destruct();
  }
}
?>
