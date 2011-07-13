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

class profile extends CoreAuthenticationNone {

  public function __construct() {
    parent::__construct();
    $this->setupBoard();
    $this->addCss('profile.css');
    $this->addJs("profile.js");
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
        header("Location: ".BASE_PATH."catroid/login/".$this->session->userLogin_userNickname);
        exit;
      }
    }
    $this->initProfileData($this->requestedUser);
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
      if($this->doChangeUserEmail($this->session->userLogin_userNickname, $postData['profileEmail'])) {
        $this->statusCode = 200;
        $this->answer_ok .= $this->languageHandler->getString('email_success');
        return true;
      } else {
        $this->statusCode = 500;
        return false;
      }
    }
  }
  
  
  // all necessary data for the text field generatrion
  public function profileAddRemoveEmailRequestQuery() {
    $postData = $_POST;
    if($postData) {
      $emailRequest = $postData['emailRequest'];
      switch($emailRequest) {
        case 'change':
          if($this->doChangeEmailAddress($this->session->userLogin_userNickname, $postData['profileEmail'])) {
            $this->statusCode = 200;
            $this->answer_ok .= $this->languageHandler->getString('email_change_successful');
            return true;
          }
          else {
            $this->statusCode = 500;
            return false;
          } 
        case 'add':
          if($this->doAddEmailAddress($this->session->userLogin_userNickname, $postData['profileEmail'])) {
            $this->statusCode = 200;
            $this->answer_ok .= $this->languageHandler->getString('email_add_successful');
            return true;
          }
          else {
            $this->statusCode = 500;
            return false;
          }
        case 'remove':
          if($this->doRemoveEmailAddress($this->session->userLogin_userNickname, $postData['profileEmail'])) {
            $this->statusCode = 200;
            $this->answer_ok .= $this->languageHandler->getString('email_delete_successful');
            return true;
          }
          else {
            $this->statusCode = 500;
            return false;
          }
        default:
        
      }
//      if($postData['profileEmail'] == '' || strlen($postData['profileEmail']) == 0 || empty($postData['profileEmail']) || strcmp('', $postData['profileEmail']) != 0) {
//        if(($postData['profileEmailInputId']) == 0) {
//          if($this->doAddEmailAddress($this->session->userLogin_userNickname, $postData['profileEmail'])) {
//            
//          }
//        } 
//      } 
//      else {
//        if($this->doAddEmailAddress($this->session->userLogin_userNickname, $postData['profileEmail'])) {
//  //        if($this->getUserEmails($postData['username'])) {
//  //          $this->statusCode = 200;
//  //          $this->answer_ok .= $this->languageHandler->getString('email_success');
//  //          return true;
//  //        }
//  //      } else {
//  //        $this->statusCode = 500;
//  //        return false;
//        }
//      }

      
    }
  }
  
  
  // all necessary data for the email text field creation
  public function profileAddEmailTextFieldRequestQuery() {
    $this->addNewEmailLanguageString = $this->languageHandler->getString('add_new_email'); 
    $this->addNewEmailPlaceholderLanguageString = $this->languageHandler->getString('add_new_email_placeholder');
    $this->addNewEmailButtonLanguageString = $this->languageHandler->getString('add_new_email_button');
    $this->emailNotDeletableText = $this->languageHandler->getString('email_not_deleteable');
    //$this->emailCount = getNumberOfUserEmails(); 
    $this->statusCode = 200; 
    
  }
  
  
  public function profileGetEmailCountRequestQuery() {
    $this->emailCount = $this->getNumberOfUserEmails();
  }
  

  public function profileCountryRequestQuery() {
    $postData = $_POST;
    if($postData) {
      if($this->doChangeUserCountry($this->session->userLogin_userNickname, $postData['profileCountry'])) {
        $this->statusCode = 200;
        $this->answer_ok .= $this->languageHandler->getString('password_success');
        return true;
      } else {
        $this->statusCode = 500;
        return false;
      }
    }
  }
  
  private function doAddEmailAddress($username, $email) {
    $userEmailValid = false;
    try {
      $userEmailValid = $this->checkEmail($email);
    } catch(Exception $e) {
      $this->answer .= $e->getMessage().'<br>';
    }
    
    if($userEmailValid) {
      try {
        $query = "EXECUTE add_user_email('$email', '$username')";
        $result = @pg_query($this->dbConnection, $query);
        if(!$result) {
          throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
        }
      } catch(Exception $e) {
        $this->answer .= $this->errorHandler->getError('profile', 'email_update_failed', $e->getMessage()).'<br>';
        $userEmailValid = false;
      }
    }
    return $userEmailValid;
  } 
  
  private function doChangeEmailAddress($username, $email) {
    $userEmailValid = false;
    try {
      $userEmailValid = $this->checkEmail($email);
    } catch(Exception $e) {
      $this->answer .= $e->getMessage().'<br>';
    }
    
    if($userEmailValid) {
      try {
        $query = "EXECUTE add_user_email('$email', '$username')";
        $result = @pg_query($this->dbConnection, $query);
        if(!$result) {
          throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
        }
      } catch(Exception $e) {
        $this->answer .= $this->errorHandler->getError('profile', 'email_update_failed', $e->getMessage()).'<br>';
        $userEmailValid = false;
      }
    }
    return $userEmailValid;
  } 
  
  private function doDeleteEmailAddress($username, $email) {
    $userEmailValid = false;
    try {
      $userEmailValid = $this->checkEmail($email);
    } catch(Exception $e) {
      $this->answer .= $e->getMessage().'<br>';
    }
    
    if($userEmailValid) {
      try {
        $query = "EXECUTE add_user_email('$email', '$username')";
        $result = @pg_query($this->dbConnection, $query);
        if(!$result) {
          throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
        }
      } catch(Exception $e) {
        $this->answer .= $this->errorHandler->getError('profile', 'email_update_failed', $e->getMessage()).'<br>';
        $userEmailValid = false;
      }
    }
    return $userEmailValid;
  } 
  
  private function doChangePassword($username, $oldPassword, $newPassword) {
    $userPasswordValid = true;
    
    try {
      $this->checkOldPassword($username, $oldPassword);
    } catch(Exception $e) {
      $userPasswordValid = false;
      $answer .= $e->getMessage().'<br>';
    }
    try {
      $this->checkNewPassword($username, $newPassword);
    } catch(Exception $e) {
      $userPasswordValid = false;
      $answer .= $e->getMessage().'<br>';
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
                  $answer .= $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage()).'<br>';
                  $userPasswordValid = false;
                }
              } catch(Exception $e) {
                $answer .= $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage()).'<br>';
                $userPasswordValid = false;
              }                  
            }        
          } catch(Exception $e) {
            $answer .= $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage()).'<br>';
            $userPasswordValid = false;
          }
        }
      } catch(Exception $e) {
        $answer .= $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage()).'<br>';
        $userPasswordValid = false;
      }
    }
    $this->answer .= $answer;
    $this->answer_ok .= $answer_ok;
    return $userPasswordValid;
  }

  private function doChangeUserEmail($username, $email) {
    $userEmailValid = false;
    try {
      $userEmailValid = $this->checkEmail($email);
    } catch(Exception $e) {
      $this->answer .= $e->getMessage().'<br>';
    }
    
    if($userEmailValid) {
      try {
        $query = "EXECUTE update_user_email('$email', '$username')";
        $result = @pg_query($this->dbConnection, $query);
        if(!$result) {
          throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
        }
      } catch(Exception $e) {
        $this->answer .= $this->errorHandler->getError('profile', 'email_update_failed', $e->getMessage()).'<br>';
        $userEmailValid = false;
      }
    }
    return $userEmailValid;
  }
  
  private function doChangeUserCountry($username, $countryCode) {
    $userCountryValid = false;
    try {
      $userCountryValid = $this->checkCountry($countryCode);
    } catch(Exception $e) {
      $this->answer .= $e->getMessage().'<br>';
    }
    if($userCountryValid) {
      try {
        $query = "EXECUTE update_user_country('$countryCode', '$username')";
        $result = @pg_query($this->dbConnection, $query);
        if(!$result) {
          throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
        }
      } catch(Exception $e) {
        $this->answer .= $this->errorHandler->getError('profile', 'email_update_failed', $e->getMessage()).'<br>';
        $userCountryValid = false;
      }
    }
    return $userCountryValid;
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
  
  private function checkEmail($email) {
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

    
    		// "EXECUTE increment_view_counter('$projectId');";
    //pg_prepare($this->dbConnection, get_user_row_by_mail, "SELECT u.* FROM cusers u, cusers_additional_email e WHERE (u.email=$1 OR e.email=$1) AND u.id=e.user_id;");
    $query = "EXECUTE get_user_row_by_email('$email');";
    //$query = "SELECT u.* FROM cusers u, cusers_additional_email e WHERE (u.email='$email' OR e.email='$email') AND u.id=e.user_id";
    
    
    $result = pg_query($this->dbConnection, $query);

    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    if(pg_num_rows($result) > 0) {
      throw new Exception($this->errorHandler->getError('registration', 'email_already_exists'));
    }
    return true;
  }
  
  // get all user emails here! 
  private function getNumberOfUserEmails($username = 0) {

    $this->userEmailsCount = 2;
    return $this->userEmailsCount;
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
      throw new Exception($this->errorHandler->getError('registration', 'country_codes_not_available'));
    }
  }
  
  private function initProfileData($requestedUser) {
    $answer .= '';
    try {
      $this->initCountryCodes();
    } catch(Exception $e) {
      $answer .= $e->getMessage().'<br>';
    }
    try {
      $this->initUserData($requestedUser);
      $this->getNumberOfUserEmails();
    } catch(Exception $e) {
      $answer .= $e->getMessage().'<br>';
    }
    $this->answer .= $answer;
  }

  private function initUserData($userName) {
    $query = "EXECUTE get_user_country_by_username('$userName')";
    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection))); 
    }
    $userCountryResult = pg_fetch_assoc($result); 
    
    if($userCountryResult) {   
      $this->userCountryCode = $userCountryResult['country'];
    }
    else {
      $this->userCountryCode = "undefined";
    }
    
    $query = "EXECUTE get_user_email_by_username('$userName')";
    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection))); 
    }
    $userEmailResult = pg_fetch_assoc($result); 
    $this->userEmail = $userEmailResult['email'];
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
      $countryCodeList[$x] = "undef";
      $countryNameList[$x] = "undefined";
      pg_free_result($result);      
    } else {
      throw new Exception($this->errorHandler->getError('registration', 'country_codes_not_available'));
    }

    $this->countryCodeList = $countryCodeList;
    $this->countryNameList = $countryNameList;
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
