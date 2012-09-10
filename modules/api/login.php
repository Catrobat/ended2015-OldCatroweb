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

class login extends CoreAuthenticationNone {

  public function __construct() {
    parent::__construct();
  }

  public function __default() {
  }

  public function loginRequest() {
    try {
      if(!isset($_POST)) {
        throw new Exception($this->errorHandler->getError('registration', 'postdata_missing'), STATUS_CODE_LOGIN_MISSING_DATA);
      }

      $username = (isset($_POST['loginUsername'])) ? checkUserInput(trim($_POST['loginUsername'])) : '';
      if($username == '') {
        throw new Exception($this->errorHandler->getError('registration', 'username_missing'), STATUS_CODE_LOGIN_MISSING_USERNAME);
      }

      if(!isset($_POST['loginPassword']) || $_POST['loginPassword'] == '') {
        throw new Exception($this->errorHandler->getError('registration', 'password_missing'), STATUS_CODE_LOGIN_MISSING_PASSWORD);
      }

      $this->doCatroidLogin($username, $_POST['loginPassword']);
      $this->doBoardLogin($username, $_POST['loginPassword']);
      $this->doWikiLogin($username, $_POST['loginPassword']);

      if($this->requestFromBlockedIp()) {
        throw new Exception($this->errorHandler->getError('viewer', 'ip_is_blocked'), STATUS_CODE_AUTHENTICATION_FAILED);
      }
      if($this->requestFromBlockedUser()) {
        throw new Exception($this->errorHandler->getError('viewer', 'user_is_blocked'), STATUS_CODE_AUTHENTICATION_FAILED);
      }

      $this->setUserLanguage($this->session->userLogin_userId);
      $this->statusCode = STATUS_CODE_OK;
    } catch(Exception $e) {
      $this->logoutRequest();
      $this->statusCode = $e->getCode();
      $this->answer = $e->getMessage();
    }
  }
  
  public function logoutRequest() {
    $this->doCatroidLogout();
    $this->doBoardLogout();
    $this->doWikiLogout();
  
    $this->statusCode = STATUS_CODE_OK;
    $this->answer = $this->languageHandler->getString('catroid_logout_success');
  }

  private function doCatroidLogin($username, $password) {
    $user = getCleanedUsername($username);
    $md5pass = md5($password);

    $result = pg_execute($this->dbConnection, "get_user_login", array($user, $md5pass));
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)), STATUS_CODE_SQL_QUERY_FAILED);
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
        throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)), STATUS_CODE_SQL_QUERY_FAILED);
      }
      pg_free_result($result);
    } else {
      $result = pg_execute($this->dbConnection, "save_failed_attempts", array($ip));
      if(!$result) {
        throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)), STATUS_CODE_SQL_QUERY_FAILED);
      }
      pg_free_result($result);
      throw new Exception($this->errorHandler->getError('auth', 'password_or_username_wrong'), STATUS_CODE_AUTHENTICATION_FAILED);
    }
  }

  private function doBoardLogin($username, $password) {
    global $user, $auth;

    $user->session_begin();
    $auth->acl($user->data);
    $user->setup();

    $auth->login($username, $password, false, 1);
    if(intVal($user->data['user_id']) <= 0) {
      throw new Exception($this->errorHandler->getError('auth', 'board_authentication_failed'), STATUS_CODE_AUTHENTICATION_FAILED);
    }
  }

  private function doWikiLogin($username, $password) {
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
      throw new Exception($this->errorHandler->getError('auth', 'wiki_api_response_incorrect', $snoopy->results), STATUS_CODE_AUTHENTICATION_FAILED);
    }

    $loginVars['lgtoken'] = $response['login']['token'];
    $cookiePrefix = $response['login']['cookieprefix'];
    $snoopy->cookies[$cookiePrefix . "_session"] = $response['login']['sessionid'];

    $snoopy->submit($apiUrl, $loginVars);
    $response = unserialize($snoopy->results);

    if(!isset($response['login']['result']) || !isset($response['login']['lgtoken']) ||
        !isset($response['login']['cookieprefix']) || !isset($response['login']['lgusername']) ||
        !isset($response['login']['lgtoken']) || !isset($response['login']['sessionid'])) {
      throw new Exception($this->errorHandler->getError('auth', 'wiki_api_response_incorrect', $snoopy->results), STATUS_CODE_AUTHENTICATION_FAILED);
    }

    $cookieExpire = 0;//time() + (60*60*24*2);
    $cookieDomain = '';

    setcookie($cookiePrefix . 'UserName', $response['login']['lgusername'], $cookieExpire, "/", $cookieDomain, false, true);
    setcookie($cookiePrefix . 'UserID', $response['login']['lguserid'], $cookieExpire, "/", $cookieDomain, false, true);
    setcookie($cookiePrefix . 'Token', $response['login']['lgtoken'], $cookieExpire, "/", $cookieDomain, false, true);
    setcookie($cookiePrefix . '_session', $response['login']['sessionid'], 0, "/", $cookieDomain, false, true);
  }

  private function doCatroidLogout() {
    $this->session->userLogin_userId = 0;
    $this->session->userLogin_userNickname = '';
  }

  private function doBoardLogout() {
    global $user, $auth;

    if($user != NULL) {
      $user->session_begin();
      $auth->acl($user->data);
      $user->setup();
      $user->session_kill();
    }
  }

  private function doWikiLogout() {
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

  private function setUserLanguage() {
    $result = pg_execute($this->dbConnection, "get_user_language", array($this->session->userLogin_userId));

    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)), STATUS_CODE_SQL_QUERY_FAILED);
    }

    $row = pg_fetch_assoc($result);
    pg_free_result($result);

    if(strlen($row['language']) > 1) {
      $this->languageHandler->setLanguageCookie($row['language']);
    }
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>