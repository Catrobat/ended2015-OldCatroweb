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

class passwordrecovery extends CoreAuthenticationNone {
  public function __construct() {
    parent::__construct();
    $this->addCss('passwordrecovery.css');
    $this->addJs('passwordRecovery.js');
    $this->setWebsiteTitle($this->languageHandler->getString('title'));
  }

  public function __default() {
    $this->action = "default";
    $this->passedUserName = "";
    
    if(isset($_GET['username'])) {
      $this->clearUserName($_GET['username']);
    }
    
    if((isset($_GET['c']))) {
      if($this->showHTMLForm($_GET['c'])) {
        $this->action = "showPasswordChangeForm";
      } else {
        $this->action = "passwordUrlExpired";
      }
    }
  }
  
  public function clearUserName($username) {
    $username = preg_replace('/\s\s+/', ' ', $username); 
    $username= preg_replace("/(<\/?)(\w+)([^>]*>)/e","",$username);
    $username= preg_replace("/document[.].*=/e","",$username);
    $username= preg_replace("/window[.].*=/e","",$username);
    $username= preg_replace("/window[.].*=/e","",$username);
    $username = htmlentities($username);
    $username = htmlspecialchars($username);
    $this->passedUserName = $username;
  }

  public function passwordRecoverySendMailRequest() {
    if(($_POST)) {
      if($this->doSendPasswordRecoveryMail($_POST['passwordRecoveryUserdata'])) {
        $this->statusCode = STATUS_CODE_OK;
        return true;
      } else {
        $this->statusCode = STATUS_CODE_INTERNAL_SERVER_ERROR;
        return false;
      }
    }
  }

  public function passwordRecoveryChangeMyPasswordRequest() {
    if(isset($_POST['passwordSavePassword']) && isset($_POST['c'])) {
      if(!$this->doPasswordRecovery($_POST)) {
        $this->statusCode = STATUS_CODE_INTERNAL_SERVER_ERROR;
      }
      else {
        $this->statusCode = STATUS_CODE_OK;
      }
      if($this->showHTMLForm($_POST['c'])) {
        $this->action = "showPasswordSaved";
      } else {
        $this->action = "passwordUrlExpired";
      }
    }
    return;
  }
   
  public function doSendPasswordRecoveryMail($userData, $sendPasswordRecoveryEmail = SEND_NOTIFICATION_USER_EMAIL) {
    try {
      $this->checkUserData($userData);
      $userHash = $this->createUserHash($userData);
      $this->sendPasswordRecoveryEmail($userHash, $sendPasswordRecoveryEmail);
      $this->answer_ok .= $this->languageHandler->getString('sent_1');
      $this->answer_ok .= $this->languageHandler->getString('sent_2');
      return $userHash;
    } catch(Exception $e) {
      $this->answer .= $e->getMessage();
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
      $result = pg_execute($this->dbConnection, "get_user_row_by_recovery_hash", array($hashValue)) or
                $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
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
      $this->answer .= $e->getMessage();
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
                  $this->answer .= $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage(), CONTACT_EMAIL).'<br>';
                  return false;
                }
              } catch(Exception $e) {
                $this->answer .= $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage(), CONTACT_EMAIL).'<br>';
                return false;
              }
            }
          } catch(Exception $e) {
            $this->answer .= $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage(), CONTACT_EMAIL).'<br>';
            return false;
          }
        }
      } catch(Exception $e) {
        $this->answer .= $this->errorHandler->getError('passwordrecovery', 'catroid_password_recovery_failed', $e->getMessage(), CONTACT_EMAIL).'<br>';
        return false;
      }
      $this->saving_password = $this->languageHandler->getString('saving_password');
      $this->answer_ok = $this->languageHandler->getString('password_ok'); //.'&requesturi=catroid/profile'
      $this->username = $username;

    }
    return $passwordDataValid;
  }

  public function doUpdateCatroidPassword($username, $password) {
    $userid = 0;
    $password = md5($password);
    $resetRecoveryHash = 0;
    $result = pg_execute($this->dbConnection, "update_password_and_hash_by_username", array($password, $resetRecoveryHash, $username)) or
              $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    return true;
  }

  public function doUpdateBoardPassword($username, $password) {

    $username = getCleanedUsername($username);
    $username = mb_convert_case($username, MB_CASE_TITLE, "UTF-8");
    $password = getHashedBoardPassword($password);

    $sql = 'UPDATE phpbb_users SET user_password = \'' . $password . '\',
  		user_pass_convert = 0 WHERE username_clean = \'' . $username . '\'';

    if(boardSqlQuery($sql)) {
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

    $username = getCleanedUsername($username);
    $username = mb_convert_case($username, MB_CASE_TITLE, "UTF-8");
    $hexSalt = sprintf("%08x", mt_rand(0, 0x7fffffff));
    $hash = md5($hexSalt.'-'.md5($password));
    $password = ":B:$hexSalt:$hash";
    
    pg_prepare($wikiDbConnection, "update_wiki_user_password", "UPDATE mwuser SET user_password=$1 WHERE user_name=$2") or
               $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $result = pg_execute($wikiDbConnection, "update_wiki_user_password", array($password, $username)) or
              $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
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

    $username = getCleanedUsername($userData);
    $result = pg_execute($this->dbConnection, "get_user_row_by_username_clean", array($username)) or
              $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    if(pg_num_rows($result) > 0) {
      $this->userData = pg_fetch_assoc($result);
    }
    else {
      $result = pg_execute($this->dbConnection, "get_user_row_by_email", array($userData)) or
                $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
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
        throw new Exception($this->errorHandler->getError('passwordrecovery', 'password_length_invalid', '', USER_MIN_PASSWORD_LENGTH, USER_MAX_PASSWORD_LENGTH));
      }
    } else {
      throw new Exception($this->errorHandler->getError('registration', 'username_password_equal'));
    }
    return true;
  }

  public function showHTMLForm($hashValue) {
    if($hashValue) {
      $result = pg_execute($this->dbConnection, "get_user_password_hash_time", array($hashValue)) or
                $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());

      if(!$result) {
        throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
      }

      if(pg_num_rows($result) == 1) {
        $row = pg_fetch_assoc($result);

        $hoursDifference = $this->getTimeDifference(intVal($row['recovery_time']));
        if($hoursDifference < 24*60*60) {
          return true;
        }
      }
    }
    return false;
  }

  public function sendPasswordRecoveryEmail($userHash, $sendPasswordRecoveryEmail) {
    $catroidPasswordResetUrl = BASE_PATH.'catroid/passwordrecovery?c='.$userHash; //$this->userHash;
    $catroidProfileUrl = BASE_PATH.'catroid/profile';
    $catroidLoginUrl = BASE_PATH.'catroid/login';

    $userid = $this->userData['id'];
    $recoveryhash = $this->userHash;
    $date = new DateTime();
    $recoverytime = $date->format('U');

    $result = pg_execute($this->dbConnection, "update_recovery_hash_recovery_time_by_id", array($recoveryhash, $recoverytime, $userid)) or
              $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }
    if($sendPasswordRecoveryEmail) {
      $userMailAddress = $this->userData['email'];
      $mailSubject = $this->languageHandler->getString('mail_subject');
      $mailText =    $this->languageHandler->getString('mail_text_row1', $this->userData['username']) . "!\n\n";
      $mailText .=   $this->languageHandler->getString('mail_text_row2') . "\n\n";
      $mailText .=   $this->languageHandler->getString('mail_text_row3') . "\n";
      $mailText .=   $catroidPasswordResetUrl."\n\n";
      $mailText .=   $this->languageHandler->getString('mail_text_row5') . "\n\n";
      $mailText .=   $this->languageHandler->getString('mail_text_row6') . "\n";
      $mailText .=   $catroidLoginUrl."\n\n";
      $mailText .=   $this->languageHandler->getString('mail_text_row7') . "\n";
      $mailText .=   $catroidProfileUrl."\n\n\n";
      $mailText .=   $this->languageHandler->getString('mail_text_row8') . "\n";
      $mailText .=   "www.catroid.org";
      if (DEVELOPMENT_MODE)
      $this->answer_ok .= '<a id="forgotPassword" target="_self" href="'.$catroidPasswordResetUrl.'">'.$catroidPasswordResetUrl.'</a><br>';

      if(!($this->mailHandler->sendUserMail($mailSubject, $mailText, $userMailAddress))) {
        throw new Exception($this->errorHandler->getError('sendmail', 'sendmail_failed', '', CONTACT_EMAIL));
      }
    }
    else {
      if (DEVELOPMENT_MODE)
      $this->answer_ok .= '<a id="forgotPassword" target="_self" href="'.$catroidPasswordResetUrl.'">'.$catroidPasswordResetUrl.'</a><br>';
    }
    return true;
  }

  public function getTimeDifference($hashtime) {
    return abs($hashtime - time());
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
