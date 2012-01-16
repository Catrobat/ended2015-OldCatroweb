<?php
/*    Catroid: An on-device graphical programming language for Android devices
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

class profile extends CoreAuthenticationUser {

  public function __construct() {
    parent::__construct();
    $this->setupBoard();
    $this->addCss('profile.css');
    $this->addJs("profile.js");
  }

  public function __authenticationFailed() {

  }
  
  public function __default() {
    if($_GET['method'] && $this->checkUserValid($_GET['method'])) {
      if( strcmp($_GET['method'], $this->session->userLogin_userNickname) == 0 ) {
        $this->ownProfile = true;
        $this->requestedUser = $this->session->userLogin_userNickname;
      }
      else {
        $this->ownProfile = false;
        $this->requestedUser = $_GET['method'];
      }
    }
    else {
      if($this->session->userLogin_userId > 0) {
        header("Location: ".BASE_PATH."catroid/profile/".$this->session->userLogin_userNickname);
        exit;
      }
      else {
        header("Location: ".BASE_PATH."catroid/login/?requesturi=catroid/profile/");
        exit;
      }
    }
    $this->initDynamicProfileData($this->requestedUser);
    $this->setWebsiteTitle($this->languageHandler->getString('title', $this->requestedUser));
  }
  
  public function profilePasswordRequestQuery() {
    $postData = $_POST;
    if($postData) {
      if($this->doChangePassword($this->session->userLogin_userNickname, $postData['profileOldPassword'], $postData['profileNewPassword'])) {
        $this->statusCode = 200;
        $this->answer_ok .= $this->languageHandler->getString('password_success');
        return true;
      } else {
        $this->statusCode = 500;
        return false;
      }
    }
  }
  
  public function profileEmailRequestQuery() {
    $postData = $_POST;
    if($postData) {
      $requestType = $postData['requestType'];
      switch($requestType) {
        case 'change':
          if($this->doChangeEmailAddress($this->session->userLogin_userNickname, $postData['profileNewEmail'], $postData['profileOldEmail'])) {
            $this->statusCode = 200;
            $this->emailArray = $this->getUserEmailArray($this->session->userLogin_userId);
            $this->emailArrayDiv = $this->generateEmailHTMLDiv($this->emailArray);
            $this->answer_ok .= $this->languageHandler->getString('email_change_successful');
            return true;
          }
          else {
            $this->statusCode = 500;
            return false;
          } 
          break;
        case 'add':
          if($this->doAddEmailAddress($this->session->userLogin_userNickname, $postData['profileNewEmail'])) {
            $this->statusCode = 200;
            $this->emailArray = $this->getUserEmailArray($this->session->userLogin_userId);
            $this->emailArrayDiv = $this->generateEmailHTMLDiv($this->emailArray);
            $this->answer_ok .= $this->languageHandler->getString('email_add_successful');
            return true;
          }
          else {
            $this->statusCode = 500;
            return false;
          }
          break;
        case 'delete':
          if($this->doDeleteEmailAddress($this->session->userLogin_userNickname, $postData['profileEmail'])) {
            $this->statusCode = 200;
            $this->emailArray = $this->getUserEmailArray($this->session->userLogin_userId);
            $this->emailArrayDiv = $this->generateEmailHTMLDiv($this->emailArray);
            $this->answer_ok .= $this->languageHandler->getString('email_delete_success');
            return true;
          }
          else {
            $this->statusCode = 500;
            $this->answer .= "bin eh da!!";
            return false;
          }
          break;
      }
    }
  }
  
  public function profileGetUserEmailArrayRequestQuery() {
      $this->userEmailArray = $this->getUserEmailArray($this->session->userLogin_userId);
  }

  public function profileCountryRequestQuery() {
    $postData = $_POST;
    if($postData) {
      if($this->doChangeUserCountry($this->session->userLogin_userNickname, $postData['profileCountry'])) {
        $this->statusCode = 200;
        $this->answer_ok .= $this->languageHandler->getString('country_success');
        return true;
      } else {
        $this->statusCode = 500;
        return false;
      }
    }
  }
  
  public function profileCityRequestQuery() {
    $postData = $_POST;
    if($postData) {
      if($this->doChangeUserCity($this->session->userLogin_userNickname, $postData['profileCity'])) {
        $this->statusCode = 200;
        $this->answer_ok .= $this->languageHandler->getString('city_success');
        return true;
      } else {
        $this->statusCode = 500;
        return false;
      }
    }
  }

  public function profileBirthRequestQuery() {
    $postData = $_POST;
    if($postData) {
      if($this->doChangeUserBirth($this->session->userLogin_userNickname, $postData['profileMonth'], $postData['profileYear'])) {
        $this->statusCode = 200;
        $this->answer_ok .= $this->languageHandler->getString('birth_success');
        return true;
      } else {
        $this->statusCode = 500;
        return false;
      }
    }
  }
  
  public function profileGenderRequestQuery() {
    $postData = $_POST;
    if($postData) {
      if($this->doChangeUserGender($this->session->userLogin_userNickname, $postData['profileGender'])) {
        $this->statusCode = 200;
        $this->answer_ok .= $this->languageHandler->getString('gender_success');
        return true;
      } else {
        $this->statusCode = 500;
        return false;
      }
    }
  }
  
  private function doChangeEmailAddress($username, $new_email, $old_email) {
    $email_valid = true;
    $new_email = trim($new_email);
    $old_email = trim($old_email);
    
    try {
      $email_valid = $this->checkEmail($new_email, $old_email);
    } catch(Exception $e) {
      $this->answer .= $e->getMessage();
      $email_valid = false;
    }

    if($email_valid) {
      try {
        $query = "EXECUTE update_user_email_by_user_email('$new_email', '$old_email')";
        $result = @pg_query($this->dbConnection, $query);
        if(!$result || pg_num_rows($result) <= 0) {
          $query = "EXECUTE update_user_additional_email_by_user_email('$new_email', '$old_email')";
          $result = @pg_query($this->dbConnection, $query);
          if(!$result) {
            throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
          }
        }
      } catch(Exception $e) {
        $this->answer .= $this->errorHandler->getError('profile', 'email_update_failed', $e->getMessage());
        $email_valid = false;
      }
    }
    return $email_valid;
  }
  
  private function doAddEmailAddress($username, $email) {
    $user_id = 0;
    $email_valid = false;
    try {
      $this->checkEmail($email);
      $user_id = $this->session->userLogin_userId;
      $user_emails = $this->getUserEmailArray($user_id);
      $x = 0;
      while($x<count($user_emails)) {
        if(strcmp($email, $user_emails[$x]) == 0) {
           throw new Exception($this->errorHandler->getError('profile', 'email_address_exists'));
        }
        else {
          $email_valid = true;
          break;
        }      
      }
    } catch(Exception $e) {
      $this->answer .= $e->getMessage();
    }
    
    if($email_valid) {
      try {
        $query = "EXECUTE add_user_email('$user_id', '$email')";
        $result = @pg_query($this->dbConnection, $query);
        if(!$result) {
          throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
        }
      } catch(Exception $e) {
        $this->answer .= $this->errorHandler->getError('profile', 'email_update_failed', $e->getMessage());
        $email_valid = false;
      }
    }
    return $email_valid;
  } 
  
  private function doDeleteEmailAddress($username, $email) {
    $delete_email = true;
    $user_id = $this->session->userLogin_userId;
    if($user_id == 1) {
      $this->emailcount = count($this->getUserEmailArray($user_id)); 
      if(($this->emailcount <= 2)) {
        $this->answer .= $this->errorHandler->getError('profile', 'email_update_of_catroweb_failed');
        $delete_email = false;
        return false;
      }
    }
    
    if($delete_email) {
      try {
        $query = "EXECUTE get_user_email_by_email('$email')";
        $result = @pg_query($this->dbConnection, $query);
        if(!$result) { 
          throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
        }
        
        if(pg_num_rows($result) > 0) {
          $query = "EXECUTE update_user_email_from_additional_email_by_user_email('$user_id')";
          $result = @pg_query($this->dbConnection, $query);
          if(!$result) {
            $delete_email = false;
            throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
          }
          
          $query = "EXECUTE delete_user_email_from_additional_email_by_user_email('$user_id')";
          $result = @pg_query($this->dbConnection, $query);
          if(!$result) {
            $delete_email = false;
            throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
          }
        }
        else {
          $query = "EXECUTE delete_user_additional_email_by_email('$email')";
          $result = @pg_query($this->dbConnection, $query);
          if(!$result) {
            $delete_email = false;
            throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
          }
        }
      } catch(Exception $e) {
        $this->answer .= $this->errorHandler->getError('profile', 'email_update_failed', $e->getMessage());
        $delete_email = false;
      }
    }
    return $delete_email;
  } 

  private function doChangePassword($username, $oldPassword, $newPassword) {
    $userPasswordValid = true;
    
    try {
      $this->checkOldPassword($username, $oldPassword);
    } catch(Exception $e) {
      $userPasswordValid = false;
      $answer .= $e->getMessage();
    }
    try {
      $this->checkNewPassword($username, $newPassword);
    } catch(Exception $e) {
      $userPasswordValid = false;
      $answer .= $e->getMessage();
    }
    
    if($userPasswordValid) {
      try {
        $catroidPasswordRecoverySuccess = $this->doUpdateCatroidPassword($username, $newPassword);

        if($catroidPasswordRecoverySuccess) {
          try {
            $boardPasswordRecoverySuccess = $this->doUpdateBoardPassword($username, $newPassword);
            if($boardPasswordRecoverySuccess) {
              try {
                $wikiPasswordRecoverySuccess = $this->doUpdateWikiPassword($username, $newPassword);
                if(!$wikiPasswordRecoverySuccess) {
                  $answer .= $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage());
                  $userPasswordValid = false;
                }
              } catch(Exception $e) {
                $answer .= $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage());
                $userPasswordValid = false;
              }                  
            }        
          } catch(Exception $e) {
            $answer .= $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage());
            $userPasswordValid = false;
          }
        }
      } catch(Exception $e) {
        $answer .= $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage());
        $userPasswordValid = false;
      }
    }
    $this->answer .= $answer;
    $this->answer_ok .= $answer_ok;
    return $userPasswordValid;
  }

  private function doChangeUserCountry($username, $countryCode) {
    $userCountryValid = false;
    if($countryCode || $countryCode != 0) {
      try {
        $userCountryValid = $this->checkCountry($countryCode);
      } catch(Exception $e) {
        $this->answer .= $e->getMessage();
      }
      if($userCountryValid) {
        try {
          $query = "EXECUTE update_user_country('$countryCode', '$username')";
          $result = @pg_query($this->dbConnection, $query);
          if(!$result) {
            throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
          }
        } catch(Exception $e) {
          $this->answer .= $this->errorHandler->getError('profile', 'country_update_failed', $e->getMessage());
          $userCountryValid = false;
        }
      }
    }
    else {
      $this->answer .= $this->languageHandler->getString('country_is_empty');
    }
    return $userCountryValid;
  }

  private function doChangeUserCity($username, $city) {
    try {
      $query = "EXECUTE update_user_city('$city', '$username')";
      $result = @pg_query($this->dbConnection, $query);
      if(!$result) {
        $return_value = false;
        throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
      }
      $return_value = true;
    } catch(Exception $e) {
      $return_value = false;
      $this->answer .= $this->errorHandler->getError('profile', 'city_update_failed', $e->getMessage());
    }
    return $return_value;
  }
  
  private function doChangeUserBirth($username, $month, $year) {
    if((empty($year) || !$year || $year <= 1) && (empty($month) || !$month || $month <= 1)) {
      $date_of_birth = NULL;
      $query = "EXECUTE delete_user_birth('$username')";
    }
    else if(($year || $year > 1) && ($month || $month > 1)) {
      $date_of_birth = $year.'-'.sprintf("%02d", $month).'-01 00:00:01';
      $query = "EXECUTE update_user_birth('$date_of_birth', '$username')";
    }
    
    try {
      $result = @pg_query($this->dbConnection, $query);
      if(!$result) {
        $return_value = false;
        throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
      }
      $return_value = true;
    } catch(Exception $e) {
      $return_value = false;
      $this->answer .= $this->errorHandler->getError('profile', 'birth_update_failed', $e->getMessage());
    }
    return $return_value;
  }
  
  private function doChangeUserGender($username, $gender) {
    try {
      $query = "EXECUTE update_user_gender('$gender', '$username')";
      $result = @pg_query($this->dbConnection, $query);
      if(!$result) {
        $return_value = false;
        throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
      }
      $return_value = true;
    } catch(Exception $e) {
      $return_value = false;
      $this->answer .= $this->errorHandler->getError('profile', 'city_update_failed', $e->getMessage());
    }
    return $return_value;
  }  
  
  private function doUpdateCatroidPassword($username, $password) {
    $password = md5($password);
    $query = "EXECUTE update_password_by_username('$password', '$username')";
    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    return true;
  }
    
  private function doUpdateBoardPassword($username, $password) {
    global $db, $phpbb_root_path;
    require_once($phpbb_root_path .'includes/functions.php');

    $username = utf8_clean_string($username); 
    $username = mb_convert_case($username, MB_CASE_TITLE, "UTF-8");
    $password = phpbb_hash($password);
    
  	$sql = 'UPDATE phpbb_users SET user_password = \'' . $password . '\',
  		user_pass_convert = 0 WHERE username_clean = \'' . $username . '\'';

    if($db->sql_query($sql)) {
      return true;
    } else {
      throw new Exception($this->errorHandler->getError('registration', 'board_registration_failed'));
    }
  }

  private function doUpdateWikiPassword($username, $password) {

    $wikiDbConnection = pg_connect("host=".DB_HOST_WIKI." dbname=".DB_NAME_WIKI." user=".DB_USER_WIKI." password=".DB_PASS_WIKI);
    if(!$wikiDbConnection) {
      throw new Exception($this->errorHandler->getError('db', 'connection_failed', pg_last_error($this->dbConnection)));
    }
    global $phpbb_root_path;
    require_once($phpbb_root_path .'includes/utf/utf_tools.php');

    $username = utf8_clean_string($username);
    $username = mb_convert_case($username, MB_CASE_TITLE, "UTF-8");
    $hexSalt = sprintf("%08x", mt_rand(0, 0x7fffffff));
    $hash = md5($hexSalt.'-'.md5($password));    
    $password = ":B:$hexSalt:$hash";

    $query = "UPDATE mwuser SET user_password = '".$password."' WHERE user_name = '".$username."'";
    
    $result = @pg_query($wikiDbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    pg_free_result($result);
    pg_close($wikiDbConnection);
    return true;
  }
  
  private function checkOldPassword($username, $oldPassword) {
    $username = trim($username);
    $oldPassword = trim($oldPassword);
    if((empty($oldPassword) && strcmp('0', $oldPassword) != 0) || $oldPassword == '' || mb_strlen($oldPassword) < 1) {
      throw new Exception($this->errorHandler->getError('profile', 'password_old_missing'));
    }

    global $phpbb_root_path;
    require_once($phpbb_root_path .'includes/utf/utf_tools.php');
    
    $user = $username; //$this->session->userLogin_userNickname;
    $user = utf8_clean_string($user);
    $pass = md5($oldPassword);
    $query = "EXECUTE get_user_login('$user', '$pass')";

    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }

    if(!(pg_num_rows($result) > 0)) {
      throw new Exception($this->errorHandler->getError('profile', 'password_old_wrong'));
    }
    return true;
  }
  
  private function checkNewPassword($username, $newPassword) {
    $username = trim($username);
    $newPassword = trim($newPassword);
    if((empty($newPassword) && strcmp('0', $newPassword) != 0) || $newPassword == '' || mb_strlen($newPassword) < 1) {
      throw new Exception($this->errorHandler->getError('profile', 'password_new_missing'));
    }
 
    if(strcmp($username, $newPassword) != 0) {
      $text = '.{'.USER_MIN_PASSWORD_LENGTH.','.USER_MAX_PASSWORD_LENGTH.'}';
      $regEx = '/^'.$text.'$/';
      if(!preg_match($regEx, $newPassword)) {
        throw new Exception($this->errorHandler->getError('profile', 'password_new_length_invalid', '', USER_MIN_PASSWORD_LENGTH));
      }
    } else {
      throw new Exception($this->errorHandler->getError('profile', 'username_password_equal'));
    }
    return true;
  }

  private function checkUserValid($username) {
    $username = trim($username);
    $valid = false;
    $query = "EXECUTE get_user_row_by_username('".($username)."')";
    $result = pg_query($this->dbConnection, $query);
    if(!$result) {
      $valid = false;
    }
    if(pg_num_rows($result) > 0) {
      $this->answer .= $this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection));
      $valid = true;
    }
    return $valid;
  }
  
  private function checkEmail($new_email, $old_email = 0) {
    $email_valid = true;
    if(empty($new_email) && strcmp('0', $new_email) != 0) {
      throw new Exception($this->errorHandler->getError('registration', 'email_missing'));
    }
    
    $name = '[a-zA-Z0-9]((\.|\-|_)?[a-zA-Z0-9])*';
    $domain = '[a-zA-Z0-9]{2,}((\.|\-)?[a-zA-Z0-9])*';
    $tld = '[a-zA-Z]{2,8}';
    $regEx = '/^('.$name.')@('.$domain.')\.('.$tld.')$/';
    
    if(!preg_match($regEx, $new_email)) {
      throw new Exception($this->errorHandler->getError('registration', 'email_invalid'));
    }
    
    $query = "EXECUTE get_user_row_by_email('$new_email');";
    $result = pg_query($this->dbConnection, $query);

    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    
    if(pg_num_rows($result) > 0) {
      throw new Exception($this->errorHandler->getError('registration', 'email_already_exists'));
    }

    if($old_email) {
      $query = "EXECUTE get_user_row_by_email('$old_email');";
      $result = pg_query($this->dbConnection, $query);
      if(!$result) {
        throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
      }
      if(pg_num_rows($result) != -1) {
        $email_valid = true;
      }
      else {
        $email_valid = false;        
      }
    }
    return $email_valid;
  }
  
  
  private function checkCountry($countryCode) {
    $countryCode = trim($countryCode);
    if($countryCode == "undef") {
      return true;
  	} 
    $query = "EXECUTE get_country_from_countries_by_countrycode('".($countryCode)."')";
    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection))); 
    }
    if(pg_num_rows($result) > 0) {
      return true;
    } else {
      throw new Exception($this->errorHandler->getError('profile', 'country_codes_not_available'));
    }
  }
  
  private function initDynamicProfileData($requestedUser) {
    $answer .= '';
    try {
      $this->initCountryCodes();
    } catch(Exception $e) {
      $answer .= $e->getMessage();
    }
    try {
      $this->fillDynamicProfileData($requestedUser);
    } catch(Exception $e) {
      $answer .= $e->getMessage();
    }
    $this->answer .= $answer;
  }

  private function fillDynamicProfileData($userName) {
    $this->emailArray = $this->getUserEmailArray($this->session->userLogin_userId);
    $this->emailArrayDiv = $this->generateEmailHTMLDiv($this->emailArray);
    
    $userCountryCode = strtoupper($this->getUserCountry($userName)); 
    if($userCountryCode) {   
      $this->userCountryCode = $userCountryCode;
    }
    else {
      $this->userCountryCode = "undefined";
    }
        
    $this->countryCodeListHTML = $this->generateCountryCodeList();
    $this->countryTextHTML = $this->generateCountryTextDiv($this->ownProfile);
    $this->userCity = $this->getUserCity($userName);
    $this->userBirthArray = $this->getUserBirth($userName);
    $this->userGender = $this->getUserGender($userName);
    
    $this->monthListHTML = $this->generateMonthList();
    $this->yearListHTML = $this->generateYearList();
    $this->genderListHTML = $this->generateGenderList();
    $this->birthdayOpenText = $this->generateBirthdayOpenText();
    $this->birthdayCloseText = $this->generateBirthdayCloseText();
    $this->genderOpenText = $this->generateGenderOpenText();
    $this->genderCloseText = $this->generateGenderCloseText();
  }
  
  private function initCountryCodes() {
    $query = "EXECUTE get_country_from_countries";
    $result = @pg_query($this->dbConnection, $query);

    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection))); 
    }
    if(pg_num_rows($result) > 0) {
      $countryCodeList = array();
      $countryNameList = array();
      $x = 1;
      while($country = pg_fetch_assoc($result)) {
        $countryCodeList[$x] = $country['code'];
        $countryNameList[$x] = $country['name'];
        $x++;
      }
      // if user country is not in list
      $countryCodeList[$x] = "EM";
      $countryNameList[$x] = "Other";
      pg_free_result($result);      
    } else {
      throw new Exception($this->errorHandler->getError('profile', 'country_codes_not_available'));
    }

    $this->countryCodeList = $countryCodeList;
    $this->countryNameList = $countryNameList;
  }

 
  
  private function generateEmailHTMLDiv($emailArray) {
    $whiteSpace="                        ";
    $x = 0;
    $emailDiv = "";
    for($x; $x < count($emailArray); $x++) {
      $emailDiv .= $whiteSpace . "<div id='div". $x ."'><a href='javascript:;' class='profileText' id='email". $x ."'>". $emailArray[$x] ."</a></div>";
    }
    return $emailDiv;
  }

  private function getUserEmailArray($user_id) {

    $query = "EXECUTE get_user_emails_by_id($user_id)";
    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    $userEmailArray = array();
    $x=0;
    while($userEmails = pg_fetch_assoc($result)) {
      $userEmailArray[$x] = $userEmails['email'];
      $x++;
    }
    return $userEmailArray;
  }
  
  private function getUserCountry($userName) {
    $query = "EXECUTE get_user_country_by_username('$userName')";
    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection))); 
    }
    $userCountry = pg_fetch_assoc($result);

    return $userCountry['country'];
  }
  
  private function getUserCity($userName) {
    $userCity = 0;
    
    $query = "EXECUTE get_user_city_by_username('$userName')";
    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection))); 
    }
    
    if(pg_num_rows($result) > 0) {
      $userCity = pg_fetch_assoc($result);
      pg_free_result($result);      
    } else {
      pg_free_result($result);
      $userCity = 0;
    }
    return $userCity['city'];
  }
  
  private function getUserBirth($userName) {
    $userBirth = 0;

    $months = getMonthsArray($this->languageHandler);

    $query = "EXECUTE get_user_birth_by_username('$userName')";
    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection))); 
    }
    if(pg_num_rows($result) > 0) {
      $userBirth = pg_fetch_array($result);
      $userBirth = array('month_id' => intval($userBirth['month']), 'month' => $months[intval($userBirth['month'])], 'year' => intval($userBirth['year']));
    } else {
      pg_free_result($result);
      $userBirth = array('month_id' => 0, 'month' => 0, 'year' => 0);;
    }
    return $userBirth;
    //return 0; 
  }
  
  private function getUserGender($userName) {
    $userGender = 0;
    
    $query = "EXECUTE get_user_gender_by_username('$userName')";
    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection))); 
    }
    if(pg_num_rows($result) > 0) {
      $userGender = pg_fetch_assoc($result);
      pg_free_result($result);      
    } else {
      pg_free_result($result);
      $userGender = 0;
    }
     return $userGender['gender'];
  }
  
  private function generateCountryCodeList() {
    $whiteSpace="                          ";
    $optionList = "";
		$x = 0;
		$sumCount = count($this->countryCodeList);
		while($x < $sumCount+1) {
		  if($x == 0) {
        $optionList .= $whiteSpace . "<option value=\"0\" >".$this->languageHandler->getString('country')."</option>\r\n";
      }
		  else if(strcmp($this->countryCodeList[$x], $this->userCountryCode) == 0) {
        $optionList .= $whiteSpace . "<option value=\"" . $this->countryCodeList[$x] . "\" selected >" . $this->countryNameList[$x] . "</option>\r\n";
      }
      else {
        $optionList .= $whiteSpace . "<option value=\"" . $this->countryCodeList[$x] . "\" >" . $this->countryNameList[$x] . "</option>\r\n";
      }
      $x++;           
		}
    return $optionList;
  }
  
  private function generateCountryTextDiv($ownProfile) {
    $whiteSpace="                        ";
    $textDiv = "";
  	$x = 0;
  	$sumCount = count($this->countryCodeList);
  	while($x < $sumCount+1) {
  	  if(strcmp($this->countryCodeList[$x], $this->userCountryCode) == 0) {
  	    if($ownProfile) { 
  	      $textDiv .= $whiteSpace . '<a href="javascript:;" class="profileText" id="profileChangeCountryOpen">'.$this->countryNameList[$x]."</a>\n"; 
  	    }
  	    else { 
  	      $textDiv .= $whiteSpace . $this->countryNameList[$x]; 
  	    }
        break;
      }
      $x++;           
  	}
    return $textDiv;
  }
  
  private function generateMonthList() {
    $whiteSpace="                          ";
    $optionList = "";
    $x = 0;
    
    $months = getMonthsArray($this->languageHandler);
    
    while($x < 13) {
      $selected = "";
      $monthValue = "value";
      $monthString = "";
      if($x == 0 && !$this->userBirthArray["month_id"]) {
        $monthString = $this->languageHandler->getString('month');
        $selected = "selected";
      }
      else if($x == 0 && $this->userBirthArray["month_id"]) {
        $monthString = $this->languageHandler->getString('month');
      }
      else if($x == $this->userBirthArray["month_id"]) {
        $selected = "selected";
        $monthString = $months[$x];
        $monthValue = "value=\"" . $x ."\"";
      }                            
      else {
        $monthString = $months[$x];
        $monthValue = "value=\"" . $x ."\"";
      }
      $optionList .= $whiteSpace . "<option " . $monthValue . " ". $selected .">" . $monthString . "</option>\r\n";
      $x++; 
    }
    return $optionList;
  }

  private function generateYearList() {
    $whiteSpace="                          ";
    $optionList = "";
    $x = 0;

    $year_up = 0;
    $year_down = date('Y') + 1;
    if(intval($this->userBirthArray['year']) > 0) {
      while($year_up < 101) {
        $year_down--;
        $selected = "";
        $yearValue = "";
        $yearString = "";
        if($year_up == 0 && !$this->userBirthArray["year"]) {
          $yearValue = "value";
          $selected = "selected";
        }
        else if($year_up == 0 && $this->userBirthArray["year"]) {
          $yearValue = "value";
          $yearString = $this->languageHandler->getString('year');     
        }
        else if($year_down == $this->userBirthArray["year"]) {
          $yearValue = "value=\"" . $year_down ."\"";
          $yearString = $year_down;
          $selected = "selected";
        }
        else {
          $yearValue = "value=\"" . $year_down ."\"";
          $yearString = $year_down;
        }
        $optionList .= $whiteSpace . "<option " . $yearValue . " ". $selected .">" . $yearString . "</option>\r\n";
        $year_up++;
      }
    } else {
      while($year_up < 101) {
        $year_down--;
        if($year_up == 0) {
          $optionList .= $whiteSpace . '<option value="" selected >'.$this->languageHandler->getString('year')."</option>\r\n";
        }
        else {
          $optionList .= $whiteSpace . "<option value=\"" . $year_down . "\" >" . $year_down . "</option>\r\n";
        }
        $year_up++;
      }
    } 
    
    return $optionList;  
  }
  
  private function generateGenderList() {
    $whiteSpace="                          ";
    $optionList = "";
    
    if(strcmp($this->userGender, "female") == 0) {
      $optionList .= $whiteSpace . '<option value="" >'.$this->languageHandler->getString('gender')."</option>\r\n";
      $optionList .= $whiteSpace . "<option value=\"female\" selected >".$this->languageHandler->getString('female')."</option>\r\n";
      $optionList .= $whiteSpace . "<option value=\"male\" >".$this->languageHandler->getString('male')."</option>\r\n";
    }
    else if (strcmp($this->userGender, "male") == 0) {
      $optionList .= $whiteSpace . '<option value="" >'.$this->languageHandler->getString('gender')."</option>\r\n";
      $optionList .= $whiteSpace . "<option value=\"female\" >".$this->languageHandler->getString('female')."</option>\r\n";
      $optionList .= $whiteSpace . "<option value=\"male\" selected >".$this->languageHandler->getString('male')."</option>\r\n";
    } 
    else { 
      $optionList .= $whiteSpace . '<option value="" selected >'.$this->languageHandler->getString('gender')."</option>\r\n";
      $optionList .= $whiteSpace . "<option value=\"female\" >".$this->languageHandler->getString('female')."</option>\r\n";
      $optionList .= $whiteSpace . "<option value=\"male\" >".$this->languageHandler->getString('male')."</option>\r\n";
    }
    return $optionList;
  }
  
  private function generateBirthdayOpenText() {
    $text = "";
    if(intval($this->userBirthArray['year']) > 0) {
      $text = $this->languageHandler->getString('born_in').' '.$this->userBirthArray["month"].' '.$this->userBirthArray["year"];
    } else {
      $text = $this->languageHandler->getString('add_your_birth_date');
    }
    return $text;
  }
  
  private function generateBirthdayCloseText() {
    $text = "";
    if(intval($this->userBirthArray['year']) > 0) {
      $text = $this->languageHandler->getString('change_birth');
    } else {
      $text = $this->languageHandler->getString('select_birth_year_month');
    }
    return $text;
  }

  private function generateGenderOpenText() {
    $text = "";
    if($this->userGender) {
      $text = $this->languageHandler->getString($this->userGender);
    } else {
      $text = $this->languageHandler->getString('add_gender');
    }
    return $text;
  }
  
  private function generateGenderCloseText() {
    $text = "";
    if($this->userGender) {
      $text = $this->languageHandler->getString('change_gender');
    } else {
      $text = $this->languageHandler->getString('gender');
    }
    return $text;
  }
  
    
  public function __destruct() {
    parent::__destruct();
  }
}
?>
