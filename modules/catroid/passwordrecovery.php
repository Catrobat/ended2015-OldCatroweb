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
        if($this->doSendPasswordRecoveryMail($_POST)) {
          $this->answer .= 'RECOVERY <br>';
          return true;
        }
      }
      if((isset($_GET['c']))) {
        if(isset($_POST['passwordSaveSubmit'])) {
          if(($_POST['passwordSavePassword'] != '') || ($_POST['passwordSavePasswordRepeat'] != '')) {
            if(!$this->doPasswordRecovery($_GET,$_POST)) {
              $this->showAppropriateForm($_GET);
              $this->answer .= 'filled ->> into if';
            }
          }
          else {
            $this->showAppropriateForm($_GET);
            $this->answer .= 'SaveForm';  
          }
        }
        else {
          $this->showAppropriateForm($_GET);
          $this->answer .= '$_GET';  
        }
      }
    }
  }
  
  private function doPasswordRecovery($getData,$postData) {
    $this->answer .= 'doSaveNewPassword<br>';
    $answer = '';
    $statusCode = 500;
    $passwordDataValid = true;
    
    $this->setupBoard();
    
    try {
      $this->checkPassword($postData['passwordSavePassword'], $postData['passwordSavePasswordRepeat']);
    } catch(Exception $e) {
      $passwordDataValid = false;
      $answer .= $e->getMessage().'<br>';
    }
    if($passwordDataValid) {
      try {
        $this->doUpdateCatroidPassword($getData, $postData);
        $passwordDataValid = true;
        $html = '<div class="passwordRecoveryInfoText">Your new password is set. Please log in now.</div>';
        $this->saveFormFields = $html;
      } catch(Exception $e) {
        $passwordDataValid = false;
        $answer = $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage()).'<br>';
      }
      if($passwordDataValid) {
        try {
          $this->doUpdateBoardPassword($postData);
          $passwordDataValid = true;
          $html = '<div class="passwordRecoveryInfoText">Your new password is set. Please log in now.</div>';
          $this->saveFormFields = $html;
        } catch(Exception $e) {
          $passwordDataValid = false;
          $answer = $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage()).'<br>';
        }        
      }
    }
    
    return $passwordDataValid;
  }
  
  private function doUpdateCatroidPassword($getData,$postData) {
    $userid = 0;
    $password = md5($postData['passwordSavePassword']);
    $this->answer .= $password.' md5<br>';
    $hashValue = $getData['c'];
    
    $query = "EXECUTE get_user_row_by_recovery_hash('$hashValue')";
    
    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection))); 
    }
    if(pg_num_rows($result) > 0) {
      $user = pg_fetch_assoc($result);
      $username = $user['username'];
      $resetRecoveryHash = 0;
      $query = "EXECUTE update_password_and_hash_by_username('$password', '$resetRecoveryHash','$username')";
      $result = @pg_query($this->dbConnection, $query);
      if(!$result) {
        throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
      }
    } else {
      throw new Exception($this->errorHandler->getError('auth', 'password_or_username_wrong'));
    }
    

    return $userid;
    
  }
  
  
  private function doUpdateBoardPassword($postData) {
    global $db, $phpbb_root_path;

    require_once($phpbb_root_path .'includes/functions.php');

    $username = utf8_encode($postData['registrationUsername']);
    $password = phpbb_hash($postData['passwordSavePassword']); //md5($postData['passwordSavePassword']);
    $email = $postData['registrationEmail'];

  // ich weiß grad noch nicht, was da nicht geht... aber es geht NICHT

		$sql = "UPDATE " . USERS_TABLE . "
			SET user_password = '" . $password . "'
			WHERE username = '" . $username . "'";
		$db->sql_query($sql);
	  
		
 
  }

  private function doUpdateWikiPassword($postData) {

  }
  
  
  
  
  
  private function doSendPasswordRecoveryMail($postData, $sendPasswordRecoverEmail = true) {
    $answer = '';
    $statusCode = 500;
    $catroidUserDataValid = true;
    
    try {
      if($this->checkUserData($postData['passwordRecoveryUserdata'])) {
        $this->createUserHash();
        $this->sendPasswordRecoveryEmail();
        
        $answer = 'An email is sent to your email address. ';
        $answer .= 'Please check your mailbox';
        $statusCode = 200;

      }
    } catch(Exception $e) {
      $catroidUserDataValid = false;
      $answer .= $e->getMessage().'<br>'; //$this->errorHandler->getError('passwordrecovery', 'userdata_invalid',
    }

    $this->answer .= $answer;
    $this->statusCode = $statusCode;
    return true;
    
  }
  
  private function checkUserData($postUserData) {
    if(empty($postUserData)) {
      throw new Exception($this->errorHandler->getError('passwordrecovery', 'userdata_missing'));
    }
    
    $this->userData = '';    
    $query = "EXECUTE get_user_row_by_username('".utf8_encode($postUserData)."')";
    $result = pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    if(pg_num_rows($result) > 0) {
      $this->answer .= 'usernumrows > 0<br>';
      $this->userData = pg_fetch_assoc($result);
      return true;
    }
    else {
      $query = "EXECUTE get_user_row_by_email('".utf8_encode($postUserData)."')";
      $result = pg_query($this->dbConnection, $query);
      if(!$result) {
        throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
      }
      if(pg_num_rows($result) > 0) {
        $this->answer .= 'emailnumrows > 0<br>';
        $this->userData = pg_fetch_assoc($result);
        return true;
      }
      else {
        $this->answer .= 'fucking end<br>';
        throw new Exception($this->errorHandler->getError('passwordrecovery', 'userdata_not_exists'));
        return false;
      }
    }
  }
  
  private function checkPassword($password, $passwordRepeat) {
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

  private function showAppropriateForm($formData) {
    
    if($_GET) {
      $hashValue = $formData['c']; 
      
      $this->answer .= $hashValue.'<br>';
      echo $hashValue.'<br>';
      
      $query = "EXECUTE get_user_password_hash_time('$hashValue')";
      $result = @pg_query($this->dbConnection, $query);
      
      if(!$result) {
        echo 'TEST<br>';
        throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection))); 
      }
      
      if(pg_num_rows($result) > 0) {
        $this->userData = pg_fetch_assoc($result);

        $hoursDifference = $this->timeDifference();
        if($hoursDifference < 24*60*60) {
          $html = '<form method="post" action="./passwordrecovery?c='.$hashValue.'">';
          $html .= '  <div class="passwordRecoveryInfoText">Please enter your new password:</div>';
          $html .= '  Password*<br>';
          $html .= '  <input type="password" name="passwordSavePassword" ><br>';
          $html .= '  Repeat password*<br>';
          $html .= '  <input type="password" name="passwordSavePasswordRepeat" ><br>';
          $html .= '  <input type="submit" name="passwordSaveSubmit" value="Change password now"><br>';
          $html .= '</form>';
        }
        else {
          $html = 'nooooooooo!!';
          $html .= '<form method="post" action="./passwordrecovery">';
          $html .= '  <div class="passwordRecoveryInfoText">Sorry! Your recovery url has expired. Please try again.</div>';
          $html .= '  <input type="submit" name="passwordNextSubmit" value="Next"><br>';
          $html .= '</form>';
        }
        $this->saveFormFields = $html;
        return true;
      }
      else {
        $html = 'nooooooooo!!';
        $html .= '<form method="post" action="./passwordrecovery">';
        $html .= '  <div class="passwordRecoveryInfoText">Sorry! Your recovery url has expired. Please try again.</div>';
        $html .= '  <input type="submit" name="passwordNextSubmit" value="Next"><br>';
        $html .= '</form>';

        $this->saveFormFields = $html;
        
        $this->answer .= 'fucking end<br>';
        //throw new Exception($this->errorHandler->getError('passwordrecovery', 'passwordhash_invalid'));
        return false;
      }
    }
    else {
    
    }

    
  }
  
  
  private function createUserHash() {
    if($this->userData != '') {
      $data = $this->userData['username'].$this->userData['email'];
      $this->answer .= $data.'<br>';
      $salt = hash("md5", $this->userData['password'].rand());
      $this->answer .= $salt.'<br>';
      $hash = hash("md5", $data.$salt);
      $this->userHash = $hash;
      $this->answer .= $hash.'<br>';
      
      return true;
    }
    else {
      throw new Exception($this->errorHandler->getError('passwordrecovery', 'create_hash_failed'));
    }
  }
  
  private function sendPasswordRecoveryEmail() {
    $resetPasswordLink = BASE_PATH.'catroid/passwordrecovery?c='.$this->userHash;
    $this->answer .= $resetPasswordLink.'<br>';
    $userid = $this->userData['id'];
    $recoveryhash = $this->userHash;
    $date = new DateTime();
    $recoverytime = $date->getTimestamp(); 
    $query = "EXECUTE update_recovery_hash_recovery_time_by_id('$recoveryhash', '$recoverytime', '$userid')";
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    
    $userMailAddress = $this->userData['email'];
    
    $mailSubject = 'Your Catroid.org Password!';
    $mailText = "Hello ".$this->userData['username']."!\n\n";
    $mailText .= "Please click on the following link to create your new password:\n";
    $mailText .= $resetPasswordLink."\n\n";
    $mailText .= "You can use your nickname or email address and your password at any time to access your member profile.\n\n";
    $mailText .= "To do so, just visit the following page: http://www.catroid.org/catroid/login\n\n";
    $mailText .= "Catroid\nwww.catroid.org";
    
    return($this->mailHandler->sendUserMail($mailSubject, $mailText, $userMailAddress));

  }
  
  private function timeDifference() {
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
