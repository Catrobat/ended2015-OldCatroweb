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

class UserFunctions extends CoreAuthenticationNone {

  public function __construct() {
      parent::__construct();
  }

  public function __default() {
	}

	public function checkPassword($username, $password) {
	  $password = trim(strval($password));
	  if($password == '') {
	    throw new Exception($this->errorHandler->getError('profile', 'password_new_missing'),
	        STATUS_CODE_USER_PASSWORD_MISSING);
	  }
	  
	  if(strcasecmp($username, $password) == 0) {
	    throw new Exception($this->errorHandler->getError('profile', 'username_password_equal'),
	        STATUS_CODE_USER_USERNAME_PASSWORD_EQUAL);
	  }
	  
	  if(strlen($password) < USER_MIN_PASSWORD_LENGTH) {
	    throw new Exception($this->errorHandler->getError('profile', 'password_new_too_short', '', USER_MIN_PASSWORD_LENGTH),
	        STATUS_CODE_USER_PASSWORD_TOO_SHORT);
	  }
	  
	  if(strlen($password) > USER_MAX_PASSWORD_LENGTH) {
	    throw new Exception($this->errorHandler->getError('profile', 'password_new_too_long', '', USER_MAX_PASSWORD_LENGTH),
	        STATUS_CODE_USER_PASSWORD_TOO_LONG);
	  }
	}
	
	public function checkEmail($email) {
	  $email = trim(strval($email));
	  if($email == '') {
	    throw new Exception($this->errorHandler->getError('registration', 'email_missing'));
	  }
	
	  $name = '[a-zA-Z0-9]((\.|\-|_)?[a-zA-Z0-9])*';
	  $domain = '[a-zA-Z]((\.|\-)?[a-zA-Z0-9])*';
	  $tld = '[a-zA-Z]{2,8}';
	  $regEx = '/^('.$name.')@('.$domain.')\.('.$tld.')$/';
	  if(!preg_match($regEx, $email)) {
	    throw new Exception($this->errorHandler->getError('registration', 'email_invalid'));
	  }
	  $result = pg_execute($this->dbConnection, "get_user_row_by_email", array($email)) or
	  $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
	  if(!$result) {
	    throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
	  }
	  if(pg_num_rows($result) > 0) {
	    throw new Exception($this->errorHandler->getError('registration', 'email_already_exists'));
	  }
	}
	
	public function checkCountry($country) {
	  $country = strtoupper($country);
	  if(!preg_match("/^[A-Z][A-Z]$/i", $country)) {
	    throw new Exception($this->errorHandler->getError('registration', 'country_missing'));
	  }
	}
	
	public function login($username, $password) {
	  $result = pg_execute($this->dbConnection, "get_user_login", array(getCleanedUsername($username), md5($password)));
	  if(!$result) {
	    throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_SQL_QUERY_FAILED);
	  }
	  
	  $loginSuccess = (pg_num_rows($result) == 1);
	  pg_free_result($result);

	  return $loginSuccess;
	}
	
	public function updatePassword($newPassword) {
	  $this->updateCatroidPassword($newPassword);
	  $this->updateBoardPassword($newPassword);
	  $this->updateWikiPassword($newPassword);
	}

	private function updateCatroidPassword($password) {
	  $password = md5($password);
	  $result = pg_execute($this->dbConnection, "update_password_by_user_id", array($password, $this->session->userLogin_userId));
	  if(!$result) {
	    throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_SQL_QUERY_FAILED);
	  }
	  pg_free_result($result);
	}
	
	private function updateBoardPassword($password) {
	  $username = getCleanedUsername($this->session->userLogin_userNickname);
	  $password = getHashedBoardPassword($password);
	
	  $sql = "UPDATE phpbb_users SET user_password='" . $password . "',
	    user_pass_convert = 0 WHERE username_clean='" . $username . "'";
	
	  if(!boardSqlQuery($sql)) {
	    throw new Exception($this->errorHandler->getError('profile', 'password_new_board_update_failed'),
	        STATUS_CODE_USER_NEW_PASSWORD_BOARD_UPDATE_FAILED);
	  }
	}
	
	private function updateWikiPassword($password) {
	  $wikiDbConnection = pg_connect("host=" . DB_HOST_WIKI . " dbname=" . DB_NAME_WIKI . " user=" . DB_USER_WIKI .
	      " password=" . DB_PASS_WIKI);
	  if(!$wikiDbConnection) {
	    throw new Exception($this->errorHandler->getError('db', 'connection_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_SQL_CONNECTION_FAILED);
	  }
	
	  $username = getCleanedUsername($this->session->userLogin_userNickname);
	  $username = mb_convert_case($username, MB_CASE_TITLE, "UTF-8");
	  $hexSalt = sprintf("%08x", mt_rand(0, 0x7fffffff));
	  $hash = md5($hexSalt.'-'.md5($password));
	  $password = ":B:$hexSalt:$hash";
	
	  pg_prepare($wikiDbConnection, "update_wiki_user_password", "UPDATE mwuser SET user_password=$1 WHERE user_name=$2");
	  $result = pg_execute($wikiDbConnection, "update_wiki_user_password", array($password, $username));
	  if(!$result) {
	    throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_SQL_QUERY_FAILED);
	  }
	  pg_free_result($result);
	  pg_close($wikiDbConnection);
	}

	public function updateCity($city) {
	  $username = getCleanedUsername($this->session->userLogin_userNickname);
    $result = pg_execute($this->dbConnection, "update_user_city", array($city, $username));
   
	  if(!$result) {
      throw new Exception($this->errorHandler->getError('profile', 'city_update_failed', pg_last_error($this->dbConnection)),
          STATUS_CODE_USER_UPDATE_CITY_FAILED);
    }
    pg_free_result($result);
	}

	public function updateCountry($country) {
	  $username = getCleanedUsername($this->session->userLogin_userNickname);
	  
	  $this->checkCountry($country);
    $result = pg_execute($this->dbConnection, "update_user_country", array($country, $username));

    if(!$result) {
      throw new Exception($this->errorHandler->getError('profile', 'country_update_failed', pg_last_error($this->dbConnection)),
          STATUS_CODE_USER_UPDATE_COUNTRY_FAILED);
    }
    pg_free_result($result);
	}

	public function updateGender($gender) {
	  $username = getCleanedUsername($this->session->userLogin_userNickname);
	  
    $result = pg_execute($this->dbConnection, "update_user_gender", array($gender, $username));
    if(!$result) {
      throw new Exception($this->errorHandler->getError('profile', 'gender_update_failed', pg_last_error($this->dbConnection)),
          STATUS_CODE_USER_UPDATE_GENDER_FAILED);
    }
    pg_free_result($result);
	}

	public function updateBirthday($birthdayMonth, $birthdayYear) {
	  $username = getCleanedUsername($this->session->userLogin_userNickname);

	  if($birthdayMonth == 0 && $birthdayYear == 0) {
	    $result = pg_execute($this->dbConnection, "delete_user_birth", array($username));
	    if(!$result) {
	      throw new Exception($this->errorHandler->getError('profile', 'birth_update_failed', pg_last_error($this->dbConnection)),
	          STATUS_CODE_USER_UPDATE_BIRTHDAY_FAILED);
	    }
	    pg_free_result($result);
	  } else if($birthdayMonth > 1 && $birthdayYear > 1) {
	    $birthday = sprintf("%04d", $birthdayYear) . '-' . sprintf("%02d", $birthdayMonth) . '-01 00:00:01';
	    $result = pg_execute($this->dbConnection, "update_user_birth", array($birthday, $username));
	    if(!$result) {
	      throw new Exception($this->errorHandler->getError('profile', 'birth_update_failed', pg_last_error($this->dbConnection)),
	          STATUS_CODE_USER_UPDATE_BIRTHDAY_FAILED);
	    }
	    pg_free_result($result);
	  }
	}
	
	public function getEmailAddresses($userId) {
	  $result = pg_execute($this->dbConnection, "get_user_emails_by_id", array(intval($userId)));
	  if(!$result) {
	    return array();
	  }
	  
	  $emails = array();
	  while($email = pg_fetch_assoc($result)) {
	    array_push($emails, $email['email']);
	  }
	  pg_free_result($result);
	  
	  return $emails;
	}

	public function addEmailAddress($userId, $email) {
	  $this->checkEmail($email);

    $userEmails = $this->getEmailAddresses($userId);
    foreach($userEmails as $current) {
      if($current === $email) {
        throw new Exception($this->errorHandler->getError('profile', 'email_address_exists'),
            STATUS_CODE_USER_ADD_EMAIL_EXISTS);
      }
    }

    $result = pg_execute($this->dbConnection, "add_user_email", array($userId, $email));
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
          STATUS_CODE_SQL_QUERY_FAILED);
    }
    pg_free_result($result);
	}
	
	public function deleteEmailAddress($email) {
	  $userId = intval($this->session->userLogin_userId);
	
	  if($userId == 1 && count($this->getEmailAddresses($userId)) < 3) {
	    throw new Exception($this->errorHandler->getError('profile', 'email_update_of_catroweb_failed'),
	        STATUS_CODE_USER_DELETE_EMAIL_FAILED);
	  }
	
	  $result = pg_execute($this->dbConnection, "get_user_email_by_email", array($email));
	  if(!$result) {
	    throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_SQL_QUERY_FAILED);
	  }
	
	  $getEmailFromAdditionalEmailsList = (pg_num_rows($result) > 0);
	  pg_free_result($result);
	
	  if($getEmailFromAdditionalEmailsList) {
	    $result = pg_execute($this->dbConnection, "update_user_email_from_additional_email_by_user_email", array($userId));
	    if(!$result) {
	      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	          STATUS_CODE_SQL_QUERY_FAILED);
	    }
	    pg_free_result($result);
	
	    $result = pg_execute($this->dbConnection, "delete_user_email_from_additional_email_by_user_email", array($userId));
	    if(!$result) {
	      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	          STATUS_CODE_SQL_QUERY_FAILED);
	    }
	    pg_free_result($result);
	  } else {
	    $result = pg_execute($this->dbConnection, "delete_user_additional_email_by_email", array($email));
	    if(!$result) {
	      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	          STATUS_CODE_SQL_QUERY_FAILED);
	    }
	    pg_free_result($result);
	  }
	}

  public function __destruct() {
    parent::__destruct();
  }
}

?>
