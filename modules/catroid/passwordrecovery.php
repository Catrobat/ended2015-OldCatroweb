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

class passwordrecovery extends CoreAuthenticationNone {
  public function __construct() {
    parent::__construct();
    $this->setupBoard();
    $this->addCss('passwordrecovery.css');    
    $this->addJs('passwordRecovery.js');
  }

  public function __default() {
    if((isset($_GET['c']))) {
        $this->showHTMLForm($_GET['c']);
    }
  }
 
  public function passwordRecoverySendMailRequest() {
    if(($_POST)) {
      if($this->doSendPasswordRecoveryMail($_POST['passwordRecoveryUserdata'])) {
        $this->statusCode = 200;
        return true;
      } else {
        $this->statusCode = 500;
        return false;
      }
    }
  }
  
  public function passwordRecoveryChangeMyPasswordRequest() {
      if(isset($_POST['passwordSavePassword']) && ($_POST['c'] != '')) {
        if(!$this->doPasswordRecovery($_POST)) {
          $this->statusCode = 500;
        }
        else {
          $this->statusCode = 200;
        }
        $this->showHTMLForm($_POST['c']);
      }
      return;
  }
   
  public function doSendPasswordRecoveryMail($userData, $sendPasswordRecoveryEmail = SEND_NOTIFICATION_USER_EMAIL) {
    try {
      $this->checkUserData($userData);
      $userHash = $this->createUserHash($userData);
      $this->sendPasswordRecoveryEmail($userHash, $sendPasswordRecoveryEmail);
      $this->answer_ok .= 'An email was sent to your email address. ';
      $this->answer_ok .= 'Please check your inbox.';
      return $userHash;
    } catch(Exception $e) {
      $this->answer .= $e->getMessage().'<br>';
      return false;
    }
  }
  
  public function doPasswordRecovery($postData) {
    $username = '';
    $password = '';
    $passwordDataValid = false;
    $catroidPasswordRecoverySuccess = false;
    $boardPasswordRecoverySuccess = false;
    $wikiPasswordRecoverySuccess = false;
    
    try {
      $password = $postData['passwordSavePassword'];
      $hashValue = $postData['c'];
      $query = "EXECUTE get_user_row_by_recovery_hash('$hashValue')";
      $result = @pg_query($this->dbConnection, $query);
      if(!$result) {
        throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection))); 
      }
      if(pg_num_rows($result) > 0) {
        $user = pg_fetch_assoc($result);
        $username = $user['username'];
      } else {
        throw new Exception($this->errorHandler->getError('auth', 'password_or_username_wrong'));
      }
      
      $this->checkPassword($username, $password);
      $passwordDataValid = true;
    } catch(Exception $e) {
      $passwordDataValid = false;
      $this->answer .= $e->getMessage().'<br>';
    }
    if($passwordDataValid) {
      try {
        $catroidPasswordRecoverySuccess = $this->doUpdateCatroidPassword($username, $password);

        if($catroidPasswordRecoverySuccess) {
          try {
            $boardPasswordRecoverySuccess = $this->doUpdateBoardPassword($username, $password);
            if($boardPasswordRecoverySuccess) {
              try {
                $wikiPasswordRecoverySuccess = $this->doUpdateWikiPassword($username, $password);
                if(!$wikiPasswordRecoverySuccess) {
                  $this->answer .= $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage()).'<br>';
                  return false;
                }
              } catch(Exception $e) {
                $this->answer .= $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage()).'<br>';
                return false;
              }                  
            }        
          } catch(Exception $e) {
            $this->answer .= $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage()).'<br>';
            return false;
          }                  
        }
      } catch(Exception $e) {
        $this->answer .= $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage()).'<br>';
        return false;
      }
      $this->answer_ok .= 'Your new password is set.<br>';
    }
    return $passwordDataValid;
  }
  
  public function doUpdateCatroidPassword($username, $password) {
    $userid = 0;
    $password = md5($password);
    $resetRecoveryHash = 0;
    $query = "EXECUTE update_password_and_hash_by_username('$password', '$resetRecoveryHash','$username')";
    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    return true;
  }
    
  public function doUpdateBoardPassword($username, $password) {
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

  public function doUpdateWikiPassword($username, $password) {

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
  
  public function checkUserData($userData) {
    $userData = trim($userData);
    if(empty($userData) && strcmp('0', $userData) != 0) {
      throw new Exception($this->errorHandler->getError('passwordrecovery', 'userdata_missing'));
    }

    global $phpbb_root_path;
    require_once($phpbb_root_path .'includes/utf/utf_tools.php');
    $userData = utf8_clean_string($userData);  
    $query = "EXECUTE get_user_row_by_username_clean('".($userData)."')";
    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    if(pg_num_rows($result) > 0) {
      $this->userData = pg_fetch_assoc($result);
    }
    else {
      $query = "EXECUTE get_user_row_by_email('".($userData)."')";
      $result = @pg_query($this->dbConnection, $query);
      if(!$result) {
        throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
      }
      if(pg_num_rows($result) > 0) {
        $this->userData = pg_fetch_assoc($result);
      }
      else {
        throw new Exception($this->errorHandler->getError('passwordrecovery', 'userdata_not_exists'));
      }
    }
    return true;
  }

  public function createUserHash() {
    if($this->userData != '') {
      $data = $this->userData['username'].$this->userData['email'];
      $salt = hash("md5", $this->userData['password'].rand());
      $hash = hash("md5", $data.$salt);
      $this->userHash = $hash;
    }
    else {
      throw new Exception($this->errorHandler->getError('passwordrecovery', 'create_hash_failed'));
    }
    return $hash;
  }
  
  public function checkPassword($username, $password) {
    if((empty($password) && strcmp('0', $password) != 0) || $password == '' || mb_strlen($password) < 1) {
      throw new Exception($this->errorHandler->getError('registration', 'password_missing'));
    }
    
    if(strcmp($username, $password) != 0) {
      $text = '.{'.USER_MIN_PASSWORD_LENGTH.','.USER_MAX_PASSWORD_LENGTH.'}';
      $regEx = '/^'.$text.'$/';
      if(!preg_match($regEx, $password)) {
        throw new Exception($this->errorHandler->getError('registration', 'password_length_invalid', '', USER_MIN_PASSWORD_LENGTH));
      }
    } else {
      throw new Exception($this->errorHandler->getError('registration', 'username_password_equal'));
    }
    return true;
  }

  public function showHTMLForm($hashValue) {
    if($hashValue) {
      $query = "EXECUTE get_user_password_hash_time('$hashValue')";
      $result = @pg_query($this->dbConnection, $query);
      
      if(!$result) {
        throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection))); 
      }
      
      if(pg_num_rows($result) > 0) {
        $this->userData = pg_fetch_assoc($result);

        $hoursDifference = $this->timeDifference();
        if($hoursDifference < 24*60*60) {
          $this->showForm = 1;
        }
        else {
          $this->showForm = 2;
        }
        return true;
      }
      else {
        $this->showForm = 2; 

        return false;
      }
    }
  }
 
  public function sendPasswordRecoveryEmail($userHash, $sendPasswordRecoveryEmail) {
    $resetPasswordLink = BASE_PATH.'catroid/passwordrecovery?c='.$userHash; //$this->userHash;
    $userid = $this->userData['id'];
    $recoveryhash = $this->userHash;
    $date = new DateTime();
    $recoverytime = $date->format('U');
    
    $query = "EXECUTE update_recovery_hash_recovery_time_by_id('$recoveryhash', '$recoverytime', '$userid')";
    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    if($sendPasswordRecoveryEmail) {
      $userMailAddress = $this->userData['email'];
      $mailSubject = '[CATROID] Your Catroid.org Password!';
      $mailText = "Hello ".$this->userData['username']."!\n\n";
      $mailText .= "Please click on the following link to create your new password:\n";
      $mailText .= $resetPasswordLink."\n\n";
      $mailText .= "You can use your nickname and your password at any time to access the catroid community.\n\n";
      $mailText .= "To do so, just visit the following page: http://www.catroid.org/catroid/login\n\n\n";
      $mailText .= "Catroid\nwww.catroid.org";
      if (DEVELOPMENT_MODE)
        $this->answer_ok .= '<a id="forgotPassword" target="_self" href="'.$resetPasswordLink.'">'.$resetPasswordLink.'</a><br>';
      
      if(!($this->mailHandler->sendUserMail($mailSubject, $mailText, $userMailAddress))) {
        throw new Exception($this->errorHandler->getError('sendmail', 'sendmail_failed'));
      }
    }
    else {
      if (DEVELOPMENT_MODE)
        $this->answer_ok .= '<a id="forgotPassword" target="_self" href="'.$resetPasswordLink.'">'.$resetPasswordLink.'</a><br>';
    }
    return true;
  }
  
  public function timeDifference() {
    $date = new DateTime();
    $timenow = $date->format('U');
    $hashtime = $this->userData['recovery_time'];
  
    if ($timenow > $hashtime) {
      $difftime = ($timenow - $hashtime);
    }
    else {
      $difftime = ($hashtime - $timenow);
    }
    return $difftime;
  }   

 
  public function __destruct() {
    parent::__destruct();
  }
}
?>
