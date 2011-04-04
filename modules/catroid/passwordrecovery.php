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

class passwordrecovery extends CoreAuthenticationNone {
  public function __construct() {
    parent::__construct();
    $this->addCss('passwordrecovery.css');
    $this->addCss('buttons.css');
  }

  public function __default() {
    if(($_POST) || ($_GET)) {
      if(isset($_POST['passwordRecoverySubmit'])) {
        if($this->doSendPasswordRecoveryMail($_POST['passwordRecoveryUserdata'])) {
          return true;
        }
      }
      if((isset($_GET['c']))) {
        if(isset($_POST['passwordSaveSubmit'])) {
          if(($_POST['passwordSavePassword'] != '') || ($_POST['passwordSavePasswordRepeat'] != '')) {
            if(!$this->doPasswordRecovery($_GET,$_POST)) {
              $this->showAppropriateForm($_GET);
            }
          }
          else {
            $this->showAppropriateForm($_GET);
            $this->answer .= $this->errorHandler->getError('registration', 'password_missing');
          }
        }
        else {
          $this->showAppropriateForm($_GET);
        }
      }
    }
  }
  
  public function doSendPasswordRecoveryMail($userData, $sendPasswordRecoveryEmail = SEND_NOTIFICATION_USER_EMAIL) {
    $statusCode = 500;
    
    try {
      if($this->checkUserData($userData)) {
        $this->createUserHash($userData);
        $this->sendPasswordRecoveryEmail($sendPasswordRecoveryEmail);
        
        $this->answer_ok .= 'An email was sent to your email address. ';
        $this->answer_ok .= 'Please check your inbox.';
        $this->statusCode = 200;

        return true;
      }
      return false;
      
    } catch(Exception $e) {
      $this->answer .= $e->getMessage().'<br>';
      return false;
    }
  }
  
  public function doPasswordRecovery($getData,$postData) {
    $username = '';
    $password = '';
    $statusCode = 500;
    $passwordDataValid = false;
    $catroidPasswordRecoverySuccess = false;
    $boardPasswordRecoverySuccess = false;
    $wikiPasswordRecoverySuccess = false;
    
    $this->setupBoard();
    
    try {
      $this->checkPassword($postData['passwordSavePassword'], $postData['passwordSavePasswordRepeat']);
      $passwordDataValid = true;
    } catch(Exception $e) {
      $passwordDataValid = false;
      $this->answer .= $e->getMessage().'<br>';
    }
    if($passwordDataValid) {
      try {
        $password = $postData['passwordSavePassword'];
        
        $hashValue = $getData['c'];
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
        $catroidPasswordRecoverySuccess = $this->doUpdateCatroidPassword($username, $password);

        if($catroidPasswordRecoverySuccess) {
          try {
            $boardPasswordRecoverySuccess = $this->doUpdateBoardPassword($username, $password);
            if($boardPasswordRecoverySuccess) {
              try {
                $wikiPasswordRecoverySuccess = $this->doUpdateWikiPassword($username, $password);
                if(!$wikiPasswordRecoverySuccess) {
                  $this->answer = $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage()).'<br>';
                  return false;
                }
              } catch(Exception $e) {
                $this->answer = $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage()).'<br>';
                return false;
              }                  
            }        
          } catch(Exception $e) {
            $this->answer = $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage()).'<br>';
            return false;
          }                  
        }
      } catch(Exception $e) {
        $this->answer = $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage()).'<br>';
        return false;
      }
      $html .= '<div class="passwordRecoveryInfoText">Your new password is set. Please log in now.</div>';
      $this->passwordRecoveryForm = $html;
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

    $username = utf8_encode($username);
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

    $username = ucfirst(utf8_clean_string($username));
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
  
  public function checkUsername($username) {
    if(empty($username)) {
      throw new Exception($this->errorHandler->getError('registration', 'username_missing'));
    }

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
    $text = '[a-zA-Z0-9äÄöÖüÜß|.| ]{'.USER_MIN_USERNAME_LENGTH.','.USER_MAX_USERNAME_LENGTH.'}';
    $regEx = '/^'.$text.'$/';
    if(!preg_match($regEx, $username)) {
      throw new Exception($this->errorHandler->getError('registration', 'username_invalid'));
    }
    return true;
  }
  
  public function checkUserData($userData) {
    if(empty($userData)) {
      throw new Exception($this->errorHandler->getError('passwordrecovery', 'userdata_missing'));
    }
    
    $this->userData = '';    
    $query = "EXECUTE get_user_row_by_username('".utf8_encode($userData)."')";
    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    if(pg_num_rows($result) > 0) {
      $this->userData = pg_fetch_assoc($result);
    }
    else {
      $query = "EXECUTE get_user_row_by_email('".utf8_encode($userData)."')";
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
      
      return true;
    }
    else {
      throw new Exception($this->errorHandler->getError('passwordrecovery', 'create_hash_failed'));
    }
  }
  
  public function checkPassword($password, $passwordRepeat) {
    if(empty($password)) {
      throw new Exception($this->errorHandler->getError('registration', 'password_missing'));
    }
    if(empty($passwordRepeat)) {
      throw new Exception($this->errorHandler->getError('registration', 'password_repeat_missing'));
    }
    $text = '.{'.USER_MIN_PASSWORD_LENGTH.','.USER_MAX_PASSWORD_LENGTH.'}';
    $regEx = '/^'.$text.'$/';
    if(!preg_match($regEx, $password)) {
      throw new Exception($this->errorHandler->getError('registration', 'password_invalid'));
    }
    if(strcmp($password, $passwordRepeat) != 0) {
      throw new Exception($this->errorHandler->getError('registration', 'password_repeat_differs'));
    }
    return true;
  }

  public function showAppropriateForm($formData) {
    
    if($_GET) {
      $hashValue = $formData['c']; 
      
      $query = "EXECUTE get_user_password_hash_time('$hashValue')";
      $result = @pg_query($this->dbConnection, $query);
      
      if(!$result) {
        throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection))); 
      }
      
      if(pg_num_rows($result) > 0) {
        $this->userData = pg_fetch_assoc($result);

        $hoursDifference = $this->timeDifference();
        if($hoursDifference < 24*60*60) {
          $html = '<form method="post" action="./passwordrecovery?c='.$hashValue.'">';
          $html .= '  <div class="passwordRecoveryHeadline">Please enter your new password:</div>';
          $html .= '  Password*<br>';
          $html .= '  <input type="password" name="passwordSavePassword" ><br>';
          $html .= '  Repeat password*<br>';
          $html .= '  <input type="password" name="passwordSavePasswordRepeat" ><br>';
          $html .= '  <div class="passwordRecoveryInfoText">Your password must be between '. USER_MIN_PASSWORD_LENGTH;
          $html .= '   to '. USER_MAX_PASSWORD_LENGTH .' characters.</div>';
          $html .= '  <input type="submit" name="passwordSaveSubmit" value="Change password now"><br>';
          $html .= '</form>';
        }
        else {
          $html = '';
          $html .= '<form method="post" action="./passwordrecovery">';
          $html .= '  <div class="passwordRecoveryHeadline">Sorry! Your recovery url has expired. Please try again.</div>';
          $html .= '  <input type="submit" name="passwordNextSubmit" value="Next"><br>';
          $html .= '</form>';
        }
        $this->passwordRecoveryForm = $html;
        return true;
      }
      else {
        $html = '';
        $html .= '<form method="post" action="./passwordrecovery">';
        $html .= '  <div class="passwordRecoveryHeadline">Sorry! Your recovery url has expired. Please try again.</div>';
        $html .= '  <input type="submit" name="passwordNextSubmit" value="Next"><br>';
        $html .= '</form>';

        $this->passwordRecoveryForm = $html;
        
        return false;
      }
    }
  }
  
 
  public function sendPasswordRecoveryEmail($sendPasswordRecoveryEmail) {
    $resetPasswordLink = BASE_PATH.'catroid/passwordrecovery?c='.$this->userHash;
    $userid = $this->userData['id'];
    $recoveryhash = $this->userHash;
    $date = new DateTime();
    $recoverytime = $date->getTimestamp(); 
    
    $query = "EXECUTE update_recovery_hash_recovery_time_by_id('$recoveryhash', '$recoverytime', '$userid')";
    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    if($sendPasswordRecoveryEmail) {
      $userMailAddress = $this->userData['email'];
      $mailSubject = 'Your Catroid.org Password!';
      $mailText = "Hello ".$this->userData['username']."!\n\n";
      $mailText .= "Please click on the following link to create your new password:\n";
      $mailText .= $resetPasswordLink."\n\n";
      $mailText .= "You can use your nickname and your password at any time to access the catroid community.\n\n";
      $mailText .= "To do so, just visit the following page: http://www.catroid.org/catroid/login\n\n\n";
      $mailText .= "Catroid\nwww.catroid.org";
      if (DEVELOPMENT_MODE)
        $this->answer .= '<a id="forgotPassword" target="_self" href="'.$resetPasswordLink.'">'.$resetPasswordLink.'</a><br><br>';
      
      if(!($this->mailHandler->sendUserMail($mailSubject, $mailText, $userMailAddress))) {
        throw new Exception($this->errorHandler->getError('sendmail', 'sendmail_failed'));
      }
    }
    else {
      if (DEVELOPMENT_MODE)
        $this->answer .= '<a id="forgotPassword" target="_self" href="'.$resetPasswordLink.'">'.$resetPasswordLink.'</a><br><br>';
    }
    return true;
  }
  
  public function timeDifference() {
    $date = new DateTime();
    $timenow = $date->getTimestamp(); 
    $hashtime = $this->userData['recovery_time'];
  
    if ($timenow > $hashtime) {
      $difftime = ($timenow - $hashtime);
    }
    else {
      $difftime = ($hashtime - $timenow);
    }
    
//    $sec = floor(($difftime) % 60);
//    $min = floor(($difftime / (60)) % 60);
//    $std = floor(($difftime / (60*60)));
//
//    echo "$hashtime".' $hashtime &nbsp;&nbsp;<br>';
//    echo "$timenow".' $timenow &nbsp;&nbsp;<br>';
//    echo "$difftime".' $difftime&nbsp;&nbsp;<br>';
//    echo $std." Stunden&nbsp;".$min." Minuten ".$sec." Sekunden".'<br>';

    return $difftime;
  }   

 
  public function __destruct() {
    parent::__destruct();
  }
}
?>
