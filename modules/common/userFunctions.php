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

class userFunctions extends CoreAuthenticationNone {
  protected $registerCatroidId;
  protected $registerBoardId;
  protected $registerWikiId;

  public function __construct() {
      parent::__construct();
      $this->registerCatroidId = 0;
      $this->registerBoardId = 0;
      $this->registerWikiId = 0;
  }

  public function __default() {
	}
	
	public function isLoggedIn() {
	  if($this->session->userLogin_userId == 0 || $this->session->userLogin_userNickname == "") {
	    return false;
	  }
	  return true;
	}
	
	public function isRecoveryHashValid($hash) {
	  $hash = trim(strval($hash));
	  $result = pg_execute($this->dbConnection, "get_user_password_hash_time", array($hash));
	  
	  if(!$result) {
	    throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_SQL_QUERY_FAILED);
	  }
	  
	  $numRows = pg_num_rows($result);
	  $row = pg_fetch_assoc($result);
	  pg_free_result($result);
	  
	  if($numRows != 1) {
	    throw new Exception($this->errorHandler->getError('passwordrecovery', 'hash_not_found', pg_last_error($this->dbConnection)),
	        STATUS_CODE_USER_RECOVERY_EXPIRED);
	  }
	  
	  if((intVal($row['recovery_time']) + 24*60*60) < time()) {
	    throw new Exception($this->errorHandler->getError('passwordrecovery', 'expired_url', pg_last_error($this->dbConnection)),
	        STATUS_CODE_USER_RECOVERY_EXPIRED);
	  }
	}

	public function checkUserExists($username) {
	  $username = trim($username);
	  $result = pg_execute($this->dbConnection, "get_user_row_by_username", array($username));
	  
	  if(!$result) {
	    throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_SQL_QUERY_FAILED);
	  }
	  $userExists = (pg_num_rows($result) == 1);
	  pg_free_result($result);
	  
	  return $userExists; 
	}

	public function checkUsername($username) {
	  $username = trim(strval($username));
	  if($username == '') {
	    throw new Exception($this->errorHandler->getError('registration', 'username_missing'),
	        STATUS_CODE_USER_USERNAME_MISSING);
	  }
	
	  // # < > [ ] | { }
	  if(preg_match('/_|^_$/', $username)) {
	    throw new Exception($this->errorHandler->getError('registration', 'username_invalid_underscore'), 
	        STATUS_CODE_USER_USERNAME_INVALID_CHARACTER);
	  }
	  if(preg_match('/#|^#$/', $username)) {
	    throw new Exception($this->errorHandler->getError('registration', 'username_invalid_hash'), 
	        STATUS_CODE_USER_USERNAME_INVALID_CHARACTER);
	  }
	  if(preg_match('/\||^\|$/', $username)) {
	    throw new Exception($this->errorHandler->getError('registration', 'username_invalid_verticalbar'),
	        STATUS_CODE_USER_USERNAME_INVALID_CHARACTER);
	  }
	  if(preg_match('/\{|^\{$/', $username) || preg_match('/\}|^\}$/', $username)) {
	    throw new Exception($this->errorHandler->getError('registration', 'username_invalid_curlybrace'),
	        STATUS_CODE_USER_USERNAME_INVALID_CHARACTER);
	  }
	  if(preg_match('/\<|^\<$/', $username) || preg_match('/\>|^\>$/', $username)) {
	    throw new Exception($this->errorHandler->getError('registration', 'username_invalid_lessgreater'),
	        STATUS_CODE_USER_USERNAME_INVALID_CHARACTER);
	  }
	  if(preg_match('/\[|^\[$/', $username) || preg_match('/\]|^\]$/', $username)) {
	    throw new Exception($this->errorHandler->getError('registration', 'username_invalid_squarebracket'),
	        STATUS_CODE_USER_USERNAME_INVALID_CHARACTER);
	  }
	  if(preg_match("/\\s/", $username)) {
	    throw new Exception($this->errorHandler->getError('registration', 'username_invalid_spaces'),
	        STATUS_CODE_USER_USERNAME_INVALID_CHARACTER);
	  }
	
	  if($this->badWordsFilter->areThereInsultingWords($username)) {
	    throw new Exception($this->errorHandler->getError('registration', 'username_invalid_insulting_words'),
	        STATUS_CODE_INSULTING_WORDS);
	  }
	
	  //username must not look like an IP-address
	  $oktettA = '([1-9][0-9]?)|(1[0-9][0-9])|(2[0-4][0-9])|(25[0-4])';
	  $oktettB = '(0)|([1-9][0-9]?)|(1[0-9][0-9])|(2[0-4][0-9])|(25[0-4])';
	  $ip = '('.$oktettA.')(\.('.$oktettB.')){2}\.('.$oktettA.')';
	  $regEx = '/^'.$ip.'$/';
	  if(preg_match($regEx, $username)) {
	    throw new Exception($this->errorHandler->getError('registration', 'username_invalid'),
	        STATUS_CODE_USER_USERNAME_INVALID);
	  }
	
	  $usernameClean = getCleanedUsername($username);
	  if(empty($usernameClean)) {
	    throw new Exception($this->errorHandler->getError('registration', 'username_invalid'),
	        STATUS_CODE_USER_USERNAME_INVALID);
	  }
	
	  if(in_array($username, getUsernameBlacklistArray()) || in_array($usernameClean, getUsernameBlacklistArray())) {
	    throw new Exception($this->errorHandler->getError('registration', 'username_blacklisted'),
	        STATUS_CODE_USER_USERNAME_INVALID);
	  }
	
	  foreach(getPublicServerBlacklistArray() as $value) {
	    if(preg_match("/".$value."/i", $username)) {
	      throw new Exception($this->errorHandler->getError('registration', 'username_blacklisted'),
	          STATUS_CODE_USER_USERNAME_INVALID);
	    }
	  }
	  
	  if($this->checkUserExists($username)) {
	    throw new Exception($this->errorHandler->getError('registration', 'username_already_exists'), 
	        STATUS_CODE_USER_USERNAME_INVALID);
	  }
	}

	public function checkPassword($username, $password) {
	  $password = trim(strval($password));
	  if($password == '') {
	    throw new Exception($this->errorHandler->getError('registration', 'password_missing'),
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
	
	public function checkLoginData($username, $password) {
	  $result = pg_execute($this->dbConnection, "get_user_login", array(getCleanedUsername($username), md5($password)));
	  if(!$result) {
	    throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_SQL_QUERY_FAILED);
	  }
	  
	  $loginSuccess = (pg_num_rows($result) == 1);
	  pg_free_result($result);

	  return $loginSuccess;
	}
	
	public function checkEmail($email) {
	  $email = trim(strval($email));
	  if($email == '') {
	    throw new Exception($this->errorHandler->getError('registration', 'email_missing'),
	        STATUS_CODE_USER_EMAIL_INVALID);
	  }
	
	  $name = '[a-zA-Z0-9]((\.|\-|_)?[a-zA-Z0-9])*';
	  $domain = '[a-zA-Z]((\.|\-)?[a-zA-Z0-9])*';
	  $tld = '[a-zA-Z]{2,8}';
	  $regEx = '/^('.$name.')@('.$domain.')\.('.$tld.')$/';
	  if(!preg_match($regEx, $email)) {
	    throw new Exception($this->errorHandler->getError('registration', 'email_invalid'),
	        STATUS_CODE_USER_EMAIL_INVALID);
	  }
	  $result = pg_execute($this->dbConnection, "get_user_row_by_email", array($email));
	  if(!$result) {
	    throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_SQL_QUERY_FAILED);
	  }
	  if(pg_num_rows($result) > 0) {
	    throw new Exception($this->errorHandler->getError('registration', 'email_already_exists'),
	        STATUS_CODE_USER_EMAIL_INVALID);
	  }
	}
	
	public function checkCountry($country) {
	  $country = strtoupper($country);
	  if(!preg_match("/^[A-Z][A-Z]$/i", $country)) {
	    throw new Exception($this->errorHandler->getError('registration', 'country_missing'), 
	        STATUS_CODE_USER_COUNTRY_INVALID);
	  }
	}
	
	public function login($username, $password) {
	  $this->loginCatroid($username, $password);
	  $this->loginBoard($username, $password);
	  $this->loginWiki($username, $password);
	  $this->setUserLanguage($this->session->userLogin_userId);
	}
	
	private function loginCatroid($username, $password) {
	  $user = getCleanedUsername($username);
	  $md5pass = md5($password);
	
	  $result = pg_execute($this->dbConnection, "get_user_login", array($user, $md5pass));
	  if(!$result) {
	    throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_SQL_QUERY_FAILED);
	  }
	
	  $row = pg_fetch_assoc($result);
	  pg_free_result($result);
	
	  $ip = '';
	  if(isset($_SERVER["REMOTE_ADDR"])) {
	    $ip = $_SERVER["REMOTE_ADDR"];
	  }
	
	  if(is_array($row)) {
	    $this->session->userLogin_userId = $row['id'];
	    $this->session->userLogin_userNickname = $row['username'];
	
	    $result = pg_execute($this->dbConnection, "reset_failed_attempts", array($ip));
	    if(!$result) {
	      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	          STATUS_CODE_SQL_QUERY_FAILED);
	    }
	    pg_free_result($result);
	  } else {
	    $result = pg_execute($this->dbConnection, "save_failed_attempts", array($ip));
	    if(!$result) {
	      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	          STATUS_CODE_SQL_QUERY_FAILED);
	    }
	    pg_free_result($result);
	    throw new Exception($this->errorHandler->getError('auth', 'password_or_username_wrong'),
	        STATUS_CODE_AUTHENTICATION_FAILED);
	  }
	}
	
	private function loginBoard($username, $password) {
	  return;
	  global $user, $auth;
	
	  $user->session_begin();
	  $auth->acl($user->data);
 	  $user->setup();
	
	  $auth->login($username, $password, false, 1);
	  if(intVal($user->data['user_id']) <= 0) {
	    throw new Exception($this->errorHandler->getError('auth', 'board_authentication_failed'),
	        STATUS_CODE_AUTHENTICATION_FAILED);
	  }
	}
	
	private function loginWiki($username, $password) {
	  require_once(CORE_BASE_PATH . 'include/lib/Snoopy.php');
	  $snoopy = new Snoopy();
	  $snoopy->curl_path = false;
	  $wikiroot = BASE_PATH . 'addons/mediawiki';
	  $apiUrl = $wikiroot . "/api.php";
	
	  $loginVars['action'] = "login";
	  //wiki login needs first letter capitalized
	  $loginVars['lgname'] = mb_convert_case(getCleanedUsername($username), MB_CASE_TITLE, "UTF-8");
	  $loginVars['lgpassword'] = $password;
	  $loginVars['format'] = "php";
	
	  $snoopy->submit($apiUrl, $loginVars);
	  $response = unserialize($snoopy->results);
	  if(!isset($response['login']['result']) || !isset($response['login']['token']) ||
	      !isset($response['login']['cookieprefix']) || !isset($response['login']['sessionid'])) {
	    throw new Exception($this->errorHandler->getError('auth', 'wiki_api_response_incorrect', $snoopy->results),
	        STATUS_CODE_AUTHENTICATION_FAILED);
	  }
	
	  $loginVars['lgtoken'] = $response['login']['token'];
	  $cookiePrefix = $response['login']['cookieprefix'];
	  $snoopy->cookies[$cookiePrefix . "_session"] = $response['login']['sessionid'];
	
	  $snoopy->submit($apiUrl, $loginVars);
	  $response = unserialize($snoopy->results);
	
	  if(!isset($response['login']['result']) || !isset($response['login']['lgtoken']) ||
	      !isset($response['login']['cookieprefix']) || !isset($response['login']['lgusername']) ||
	      !isset($response['login']['lgtoken']) || !isset($response['login']['sessionid'])) {
	    throw new Exception($this->errorHandler->getError('auth', 'wiki_api_response_incorrect', $snoopy->results),
	        STATUS_CODE_AUTHENTICATION_FAILED);
	  }
	
	  $cookieExpire = 0;//time() + (60*60*24*2);
	  $cookieDomain = '';
	
	  setcookie($cookiePrefix . 'UserName', $response['login']['lgusername'], $cookieExpire, "/", $cookieDomain, false, true);
	  setcookie($cookiePrefix . 'UserID', $response['login']['lguserid'], $cookieExpire, "/", $cookieDomain, false, true);
	  setcookie($cookiePrefix . 'Token', $response['login']['lgtoken'], $cookieExpire, "/", $cookieDomain, false, true);
	  setcookie($cookiePrefix . '_session', $response['login']['sessionid'], 0, "/", $cookieDomain, false, true);
	}

	private function setUserLanguage($userId) {
	  $result = pg_execute($this->dbConnection, "get_user_language", array($userId));
	
	  if(!$result) {
	    throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_SQL_QUERY_FAILED);
	  }
	
	  $row = pg_fetch_assoc($result);
	  pg_free_result($result);
	
	  if(strlen($row['language']) > 1) {
	    $this->languageHandler->setLanguageCookie($row['language']);
	  }
	}

	public function logout() {
	  $this->logoutCatroid();
	  $this->logoutBoard();
	  $this->logoutWiki();
	}

	private function logoutCatroid() {
	  $this->session->userLogin_userId = 0;
	  $this->session->userLogin_userNickname = '';
	}
	
	private function logoutBoard() {
	  initBoardFunctions();
	  global $user, $auth;
	
	  if($user != NULL) {
	    $user->session_begin();
	    $auth->acl($user->data);
	    $user->setup();
	    $user->session_kill();
	  }
	}
	
	private function logoutWiki() {
	  require_once(CORE_BASE_PATH . 'include/lib/Snoopy.php');
	  $snoopy = new Snoopy();
	  $snoopy->curl_path = false;
	  $wikiroot = BASE_PATH.'addons/mediawiki';
	  $apiUrl = $wikiroot . "/api.php?action=logout";
	
	  $logoutVars['action'] = "logout";
	  $snoopy->submit($apiUrl, $logoutVars);
	  $response = $snoopy->results;
	
	  $now = date('YmdHis', time());
	  $cookieExpires = time() + (60*60*24*2);
	  $cookieExpired = time() - (60*60*24*2);
	  $cookieDomain = '';
	
	  setcookie('catrowikiLoggedOut', $now, $cookieExpires, "/", $cookieDomain, false, true);
	  setcookie('catrowiki_session', '', $cookieExpired, "/", $cookieDomain, false, true);
	  setcookie('catrowikiUserID', '', $cookieExpired, "/", $cookieDomain, false, true);
	  setcookie('catrowikiUserName', '', $cookieExpired, "/", $cookieDomain, false, true);
	  setcookie('catrowikiToken', '', $cookieExpired, "/", $cookieDomain, false, true);
	}
	
	public function register($postData) {
	  try {
	    $this->checkUsername($postData['registrationUsername']);
	    $this->checkPassword($postData['registrationUsername'], $postData['registrationPassword']);
	    $this->checkEmail($postData['registrationEmail']);
	    $this->checkCountry($postData['registrationCountry']);
	    
  	  $this->registerCatroidId = $this->registerCatroid($postData);
  	  $this->registerBoardId = $this->registerBoard($postData);
  	  $this->registerWikiId = $this->registerWiki($postData);
  	  
  	  $this->sendRegistrationEmail($postData);
    } catch(Exception $e) {
      $this->undoRegister();
      throw new Exception($e->getMessage(), $e->getCode());
    }
	}

	private function registerCatroid($postData) {
	  $username = checkUserInput($postData['registrationUsername']);
	  $md5user = md5(strtolower($username));
	  $usernameClean = getCleanedUsername($username);
	  $md5password = md5($postData['registrationPassword']);
	  $authToken = md5($md5user.":".$md5password);
	
	  $email = checkUserInput($postData['registrationEmail']);
	  $ipRegistered = $_SERVER['REMOTE_ADDR'];
	  $country = checkUserInput($postData['registrationCountry']);
	  $status = USER_STATUS_STRING_ACTIVE;
	  
	  $dateOfBirth = NULL;
	  $year = checkUserInput($postData['registrationYear']);
	  $month = checkUserInput($postData['registrationMonth']);
	  if($month != 0 && $year != 0) {
	    $year = '1900';
	    $dateOfBirth = $year . '-' . sprintf("%02d", $month) . '-01 00:00:01';
	  }
	
	  $gender = checkUserInput($postData['registrationGender']);
	  $city = checkUserInput($postData['registrationCity']);
	  $language = $this->languageHandler->getLanguage();
	
	  $result = pg_execute($this->dbConnection, "user_registration", array($username, $usernameClean, $md5password,
	      $email, $dateOfBirth, $gender, $country, $city, $ipRegistered, $status, $authToken, $language));
	  if(!$result) {
	    throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_SQL_QUERY_FAILED);
	  }
	
	  $row = pg_fetch_assoc($result);
	  pg_free_result($result);
	
	  return $row['id'];
	}
	
	private function registerBoard($postData) {
	  return;
	  global $user, $auth, $phpbb_root_path;
	  $user->session_begin();
	  $auth->acl($user->data);
	  $user->setup();
	
	  require_once($phpbb_root_path . 'includes/functions_user.php');
	
	  $username = checkUserInput($postData['registrationUsername']);
	  $password = md5($postData['registrationPassword']);
	  $email = checkUserInput($postData['registrationEmail']);
	
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
	    throw new Exception($this->errorHandler->getError('registration', 'board_registration_failed'),
	        STATUS_CODE_USER_REGISTRATION_FAILED);
	  }
	}
	
	private function registerWiki($postData) {
	  $wikiDbConnection = pg_connect("host=" . DB_HOST_WIKI . " dbname=" . DB_NAME_WIKI . " user=" . DB_USER_WIKI .
	      " password=" . DB_PASS_WIKI);
	  if(!$wikiDbConnection) {
	    throw new Exception($this->errorHandler->getError('db', 'connection_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_SQL_QUERY_FAILED);
	  }
	
	  $username = checkUserInput($postData['registrationUsername']);
	  $username = getCleanedUsername($username);
	  $username = mb_convert_case($username, MB_CASE_TITLE, "UTF-8");
	  $userToken = md5($username);
	  $hexSalt = sprintf("%08x", mt_rand(0, 0x7fffffff));
	  $hash = md5($hexSalt . '-' . md5($postData['registrationPassword']));
	  $password = ":B:$hexSalt:$hash";
	
	  pg_prepare($wikiDbConnection, "add_wiki_user", "INSERT INTO mwuser (user_name, user_token, user_password, user_registration) VALUES (\$1, \$2, \$3, now()) RETURNING user_id");
	  $result = pg_execute($wikiDbConnection, "add_wiki_user", array($username, $userToken, $password));
	  pg_query($wikiDbConnection, 'DEALLOCATE add_wiki_user');
	  if(!$result) {
	    throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_SQL_QUERY_FAILED);
	  }
	
	  $row = pg_fetch_assoc($result);
	  pg_free_result($result);
	  pg_close($wikiDbConnection);
	
	  return $row['user_id'];
	}
	
	public function undoRegister() {
	  $this->undoRegisterCatroid();
	  $this->undoRegisterBoard();
	  $this->undoRegisterWiki();
	}

	private function undoRegisterCatroid() {
	  if($this->registerCatroidId != 0) {
  	  $result = pg_execute($this->dbConnection, "delete_user_by_id", array($this->registerCatroidId));
  	  if($result) {
    	  pg_free_result($result);
  	  }
  	  $this->registerCatroidId = 0;
	  }
	}
	
	private function undoRegisterBoard() {
	  if($this->registerBoardId != 0) {
  	  global $user, $auth, $phpbb_root_path;
  	  $user->session_begin();
  	  $auth->acl($user->data);
  	  $user->setup();
  	
  	  require_once($phpbb_root_path .'includes/functions_user.php');
  	  user_delete('remove', $this->registerBoardId);
  	  $this->registerBoardId = 0;
	  }
	}
	
	private function undoRegisterWiki() {
	  if($this->registerWikiId != 0) {
  	  $wikiDbConnection = pg_connect("host=" . DB_HOST_WIKI . " dbname=" . DB_NAME_WIKI . " user=" . DB_USER_WIKI .
  	      " password=" . DB_PASS_WIKI);

  	
  	  pg_prepare($wikiDbConnection, "delete_wiki_user", "DELETE FROM mwuser WHERE user_id=$1");
  	  $result = pg_execute($wikiDbConnection, "delete_wiki_user", array($this->registerWikiId));
  	  if($result) {
    	  pg_free_result($result);
  	  }
  	  pg_close($wikiDbConnection);
  	  $this->registerWikiId = 0;
	  }
	}
	
	public function recover($userData) {
	  $userData = trim(strval($userData));
	  
    if($userData == '') {
      throw new Exception($this->errorHandler->getError('passwordrecovery', 'userdata_missing'),
          STATUS_CODE_USER_POST_DATA_MISSING);
    }
	  
	  $data = $this->getUserDataForRecovery($userData);
	  $hash = $this->createUserHash($data);
	  $this->sendPasswordRecoveryEmail($hash, $data['id'], $data['username'], $data['email']);
	}
	
	public function updatePassword($username, $newPassword) {
	  $username = getCleanedUsername($username);

	  $this->updateCatroidPassword($username, $newPassword);
	  $this->updateBoardPassword($username, $newPassword);
	  $this->updateWikiPassword($username, $newPassword);
	}

	private function updateCatroidPassword($username, $password) {
	  $password = md5($password);
	  $result = pg_execute($this->dbConnection, "update_password_by_username", array($password, $username));
	  if(!$result) {
	    throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_SQL_QUERY_FAILED);
	  }
	  pg_free_result($result);
	}
	
	private function updateBoardPassword($username, $password) {
	  $password = getHashedBoardPassword($password);
	
	  $sql = "UPDATE phpbb_users SET user_password='" . $password . "',
	    user_pass_convert = 0 WHERE username_clean='" . $username . "'";
	
	  if(!boardSqlQuery($sql)) {
	    throw new Exception($this->errorHandler->getError('profile', 'password_new_board_update_failed'),
	        STATUS_CODE_USER_NEW_PASSWORD_BOARD_UPDATE_FAILED);
	  }
	}
	
	private function updateWikiPassword($username, $password) {
	  $wikiDbConnection = pg_connect("host=" . DB_HOST_WIKI . " dbname=" . DB_NAME_WIKI . " user=" . DB_USER_WIKI .
	      " password=" . DB_PASS_WIKI);
	  if(!$wikiDbConnection) {
	    throw new Exception($this->errorHandler->getError('db', 'connection_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_SQL_CONNECTION_FAILED);
	  }
	
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
	  if($this->session->userLogin_userId > 0) {
      $result = pg_execute($this->dbConnection, "update_user_city", array(checkUserInput($city),
          $this->session->userLogin_userId));
     
  	  if(!$result) {
        throw new Exception($this->errorHandler->getError('profile', 'city_update_failed', pg_last_error($this->dbConnection)),
            STATUS_CODE_USER_UPDATE_CITY_FAILED);
      }
      pg_free_result($result);
	  } else {
      throw new Exception($this->errorHandler->getError('profile', 'city_update_failed', pg_last_error($this->dbConnection)),
          STATUS_CODE_USER_UPDATE_CITY_FAILED);
	  }
	}

	public function updateCountry($country) {
	  if($this->session->userLogin_userId > 0) {
  	  $this->checkCountry($country);
      $result = pg_execute($this->dbConnection, "update_user_country", array($country, $this->session->userLogin_userId));
  
      if(!$result) {
        throw new Exception($this->errorHandler->getError('profile', 'country_update_failed', pg_last_error($this->dbConnection)),
            STATUS_CODE_USER_UPDATE_COUNTRY_FAILED);
      }
      pg_free_result($result);
	  } else {
  	  throw new Exception($this->errorHandler->getError('profile', 'country_update_failed', pg_last_error($this->dbConnection)),
  	      STATUS_CODE_USER_UPDATE_COUNTRY_FAILED);
	  }
	}

	public function updateGender($gender) {
	  if($this->session->userLogin_userId > 0) {
      $result = pg_execute($this->dbConnection, "update_user_gender", array($gender, $this->session->userLogin_userId));
      if(!$result) {
        throw new Exception($this->errorHandler->getError('profile', 'gender_update_failed', pg_last_error($this->dbConnection)),
            STATUS_CODE_USER_UPDATE_GENDER_FAILED);
      }
      pg_free_result($result);
	  } else {
  	  throw new Exception($this->errorHandler->getError('profile', 'gender_update_failed', pg_last_error($this->dbConnection)),
  	      STATUS_CODE_USER_UPDATE_GENDER_FAILED);
	  }
	}

	public function updateBirthday($birthdayMonth, $birthdayYear) {
	  if($this->session->userLogin_userId > 0) {
  	  if($birthdayMonth == 0 && $birthdayYear == 0) {
  	    $result = pg_execute($this->dbConnection, "delete_user_birth", array($this->session->userLogin_userId));
  	    if(!$result) {
  	      throw new Exception($this->errorHandler->getError('profile', 'birth_update_failed', pg_last_error($this->dbConnection)),
  	          STATUS_CODE_USER_UPDATE_BIRTHDAY_FAILED);
  	    }
  	    pg_free_result($result);
  	  } else if($birthdayMonth > 1 && $birthdayYear > 1) {
  	    $birthday = sprintf("%04d", $birthdayYear) . '-' . sprintf("%02d", $birthdayMonth) . '-01 00:00:01';
  	    $result = pg_execute($this->dbConnection, "update_user_birth", array($birthday, $this->session->userLogin_userId));
  	    if(!$result) {
  	      throw new Exception($this->errorHandler->getError('profile', 'birth_update_failed', pg_last_error($this->dbConnection)),
  	          STATUS_CODE_USER_UPDATE_BIRTHDAY_FAILED);
  	    }
  	    pg_free_result($result);
  	  }
	  } else {
  	  throw new Exception($this->errorHandler->getError('profile', 'birth_update_failed', pg_last_error($this->dbConnection)),
  	      STATUS_CODE_USER_UPDATE_BIRTHDAY_FAILED);
	  }
	}
	
	public function updateLanguage($language) {
	  if(intval($this->session->userLogin_userId) == 0) {
	    return;
	  }
	  
	  if($language == '') {
	    throw new Exception($this->errorHandler->getError('profile', 'language_update_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_USER_UPDATE_LANGUAGE_FAILED);
	  }

	  $result = pg_execute($this->dbConnection, "update_user_language_by_id", array($language, $this->session->userLogin_userId));
	  if(!$result) {
	    throw new Exception($this->errorHandler->getError('profile', 'language_update_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_USER_UPDATE_LANGUAGE_FAILED);
	  }
	  pg_free_result($result);
	}
	
	public function getUserData($username) {
	  $username = trim(strval($username));
	  $result = pg_execute($this->dbConnection, "get_user_row_by_username", array($username));
	  
	  if(!$result) {
      return array();	    
	  }
	  
	  $user = array();
	  if(pg_num_rows($result) > 0) {
	    $user = pg_fetch_assoc($result);
	  }
	  
	  pg_free_result($result);
	  return $user;
	}
	
	public function getUserDataByRecoveryHash($hash) {
	  $hash = trim(strval($hash));
	  $result = pg_execute($this->dbConnection, "get_user_row_by_recovery_hash", array($hash));
	  
	  if(!$result) {
	    return array();	    
	  }
	  
	  $user = array();
	  if(pg_num_rows($result) > 0) {
	    $user = pg_fetch_assoc($result);
	  }
	  
	  pg_free_result($result);
	  return $user;
	}

	public function getUserDataForRecovery($userData) {
	  $userData = trim(strval($userData));
	  $result = pg_execute($this->dbConnection, "get_user_row_by_username_or_username_clean", array($userData, getCleanedUsername($userData)));
	  if(!$result) {
	    throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_SQL_QUERY_FAILED);
	  }
	
	  $user = array();
	  if(pg_num_rows($result) == 1) {
	    $user = pg_fetch_assoc($result);
	  }
	
	  pg_free_result($result);
	  if(!empty($user)) {
	    return $user;
	  }
	   
	  $result = pg_execute($this->dbConnection, "get_user_row_by_email", array($userData));
	  if(!$result) {
	    throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_SQL_QUERY_FAILED);
	  }
	   
	  if(pg_num_rows($result) == 1) {
	    $user = pg_fetch_assoc($result);
	  }
	
	  pg_free_result($result);
		if(!empty($user)) {
	    return $user;
	  }

	  throw new Exception($this->errorHandler->getError('passwordrecovery', 'userdata_not_exists'),
	      STATUS_CODE_USER_RECOVERY_NOT_FOUND);
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
	  $numberOfEmailAddresses = count($this->getEmailAddresses($userId));
	
	  if($userId == 1 && $numberOfEmailAddresses < 3) {
	    throw new Exception($this->errorHandler->getError('profile', 'email_update_of_catroweb_failed'),
	        STATUS_CODE_USER_DELETE_EMAIL_FAILED);
	  } elseif($numberOfEmailAddresses < 2) {
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

	public function createUserHash($userData) {
	  if(is_array($userData)) {
	    $data = $userData['username'] . ':' . $userData['email'];
	    $salt = hash("md5", $userData['password'] . rand());
	    $hash = hash("md5", $data . ':' . $salt);
	    return $hash;
	  }
	  throw new Exception($this->errorHandler->getError('passwordrecovery', 'create_hash_failed'),
	      STATUS_CODE_USER_RECOVERY_HASH_CREATION_FAILED);
	}

	public function sendRegistrationEmail($postData) {
	  $catroidProfileUrl = BASE_PATH . 'catroid/profile';
	  $catroidLoginUrl = BASE_PATH . 'catroid/login';
	  $catroidRecoveryUrl = BASE_PATH . 'catroid/passwordrecovery';
	
	  if(SEND_NOTIFICATION_USER_EMAIL) {
	    $username = $postData['registrationUsername'];
	    $password = $postData['registrationPassword'];
	    $userMailAddress = $postData['registrationEmail'];
	    $mailSubject = $this->languageHandler->getString('registration_mail_subject');
	    $mailText =    $this->languageHandler->getString('registration_mail_text_row1') . "\n\n";
	    $mailText .=   $this->languageHandler->getString('registration_mail_text_row2') . "\n";
	    $mailText .=   $this->languageHandler->getString('registration_mail_text_row3', $username) . "\n";
	    $mailText .=   $this->languageHandler->getString('registration_mail_text_row5', $password) . "\n\n";
	    $mailText .=   $this->languageHandler->getString('registration_mail_text_row6') . "\n\n";
	    $mailText .=   $this->languageHandler->getString('registration_mail_text_row7') . "\n";
	    $mailText .=   $catroidLoginUrl."\n\n";
	    $mailText .=   $this->languageHandler->getString('registration_mail_text_row8') . "\n";
	    $mailText .=   $catroidProfileUrl."\n\n";
	    $mailText .=   $this->languageHandler->getString('registration_mail_text_row9') . "\n";
	    $mailText .=   $catroidRecoveryUrl."\n\n";
	    $mailText .=   "www.catroid.org";
	    $mailText .=   "\n\n";
	
	    if(!$this->mailHandler->sendUserMail($mailSubject, $mailText, $userMailAddress)) {
	      throw new Exception($this->errorHandler->getError('sendmail', 'sendmail_failed', '', CONTACT_EMAIL),
	          STATUS_CODE_SEND_MAIL_FAILED);
	    }
	  }
	}

	public function sendPasswordRecoveryEmail($userHash, $userId, $userName, $userEmail) {
	  $catroidPasswordResetUrl = BASE_PATH . 'catroid/passwordrecovery?c=' . $userHash;
	  $catroidProfileUrl = BASE_PATH . 'catroid/profile';
	  $catroidLoginUrl = BASE_PATH . 'catroid/login';
	
	  $result = pg_execute($this->dbConnection, "update_recovery_hash_recovery_time_by_id", array($userHash, time(), $userId));
	  if(!$result) {
	    throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
	        STATUS_CODE_SQL_QUERY_FAILED);
	  }
	  pg_free_result($result);
	  
	  if(DEVELOPMENT_MODE) {
	    throw new Exception($catroidPasswordResetUrl, STATUS_CODE_OK);
	  }

	  if(SEND_NOTIFICATION_USER_EMAIL) {
	    $mailSubject = $this->languageHandler->getString('recovery_mail_subject');
	    $mailText =    $this->languageHandler->getString('recovery_mail_text_row1', $userName) . "!\n\n";
	    $mailText .=   $this->languageHandler->getString('recovery_mail_text_row2') . "\n\n";
	    $mailText .=   $this->languageHandler->getString('recovery_mail_text_row3') . "\n";
	    $mailText .=   $catroidPasswordResetUrl . "\n\n";
	    $mailText .=   $this->languageHandler->getString('recovery_mail_text_row5') . "\n\n";
	    $mailText .=   $this->languageHandler->getString('recovery_mail_text_row6') . "\n";
	    $mailText .=   $catroidLoginUrl . "\n\n";
	    $mailText .=   $this->languageHandler->getString('recovery_mail_text_row7') . "\n";
	    $mailText .=   $catroidProfileUrl . "\n\n\n";
	    $mailText .=   $this->languageHandler->getString('recovery_mail_text_row8') . "\n";
	    $mailText .=   "www.catroid.org";
	
	    if(!$this->mailHandler->sendUserMail($mailSubject, $mailText, $userEmail)) {
	      throw new Exception($this->errorHandler->getError('sendmail', 'sendmail_failed', '', CONTACT_EMAIL),
	          STATUS_CODE_SEND_MAIL_FAILED);
	    }
	  }
	}

  public function __destruct() {
    parent::__destruct();
  }
}

?>
