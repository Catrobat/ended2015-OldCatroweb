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

class login extends CoreAuthenticationNone {

  public function __construct() {
    parent::__construct();
  }

  public function __default() {
  }

  public function logoutRequest() {
    $this->logout();
    $this->requesturi = NULL;
  }

  public function loginRequest() {
    if($_POST) {
      if($this->doLogin($_POST)) {
        $this->statusCode = 200;  
        $this->setUserLanguage($this->session->userLogin_userId);     
        return true;
      } else {
      	
      	$ip = $_SERVER["REMOTE_ADDR"];
    	$query = "EXECUTE is_ip_blocked_temporarily('$ip')";
      	$result = pg_query($this->dbConnection, $query) or die('db query_failed '.pg_last_error());
      	
      	if(pg_num_rows($result)) {
      		$this->statusCode = 200;
      	}
      	else
      	{
      		$this->statusCode = 500;
      	}
      	
        
        if($this->clientDetection->isMobile())
          $this->helperDiv = "<a id='signUp' target='_self' href='". BASE_PATH ."catroid/passwordrecovery'>". $this->languageHandler->getString('click_if_forgot_password') ."</a><br>". $this->languageHandler->getString('or') ."<br><a id='signUp' target='_self' href='". BASE_PATH ."catroid/registration'>". $this->languageHandler->getString('create_new_account') ."</a>";
        else 
          $this->helperDiv = "<a id='signUp' target='_self' href='". BASE_PATH ."catroid/passwordrecovery'>". $this->languageHandler->getString('click_if_forgot_password') ."</a> ". $this->languageHandler->getString('or') ." <a id='signUp' target='_self' href='". BASE_PATH ."catroid/registration'>". $this->languageHandler->getString('create_new_account') ."</a>";
        return false;
      }
    }
  }

  public function logout() {
    $this->doLogout();
  }

  private function setRequestURI($uri) {
    if($uri != '') {
      $this->requesturi = $uri;
    }
    else {
      $this->requesturi = "catroid/index";
    }
  }
  
  private function setUserLanguage($userid) {
    try {
      $result = pg_execute($this->dbConnection, "get_user_language", array($userid)) or
                $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
      if(!$result) {
        throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection))); 
      }
      $language = pg_fetch_assoc($result);
      if(strlen($language['language']) > 1) {
        $this->languageHandler->setLanguageCookie($language['language']);
      }     
      return $language['language'];
    } catch(Exception $e) {
      $this->answer .= $this->errorHandler->getError('profile', 'language_update_failed', $e->getMessage());
      return false;
    }
  }

  public function doLogin($postData) {
    $answer = '';
    $statusCode = 200;
    $boardLoginSuccess = false;
    $wikiLoginSuccess = false;
    $catroidLoginSuccess = false;
    $loginDataValid = true;
    
    $username = trim($postData['loginUsername']);
    if(empty($username) && strcmp('', $username) == 0) {
      $answer .= $this->errorHandler->getError('registration', 'username_missing');
      $loginDataValid = false;
    }
    if(empty($postData['loginPassword']) && strcmp('', $postData['loginPassword']) == 0) {
      $answer .= $this->errorHandler->getError('registration', 'password_missing');
      $loginDataValid = false;
    }
    if(!$loginDataValid) {
      $this->answer = $answer;
      return $loginDataValid;        
    }

    try {
      $catroidUserId = $this->doCatroidLogin($postData);
      $catroidLoginSuccess = true;
      $answer .= $this->languageHandler->getString('catroid_login_success');
    } catch(Exception $e) {
      $answer .= $this->errorHandler->getError('auth', 'catroid_authentication_failed', $e->getMessage());
    }

    if($catroidLoginSuccess) {
      $boardUserId = $this->doBoardLogin($postData);
      if($boardUserId > 1) {
        $boardLoginSuccess = true;
        $answer .= $this->languageHandler->getString('board_login_success');
      } else {
        $answer .= $this->errorHandler->getError('auth', 'board_authentication_failed');
      }

      if($boardLoginSuccess) {
        try {
          $this->doWikiLogin($postData);
          $wikiLoginSuccess = true;
          $answer .= $this->languageHandler->getString('wiki_login_success');
          $statusCode = 200;
        } catch(Exception $e) {
          $answer .= $this->errorHandler->getError('auth', 'wiki_authentication_failed', $e->getMessage());
          //logout catroid & board
          $this->doCatroidLogout();
          $this->doBoardLogout();
        }
      } else {
        //logout catroid
        $this->doCatroidLogout();
      }
    }

    $this->statusCode = $statusCode;
    $this->answer .= $answer;

    if($boardLoginSuccess && $wikiLoginSuccess && $catroidLoginSuccess) {
      return true;
    } else {
      return false;
    }
  }

  public function doLogout() {
    $answer = '';
    $statusCode = 500;

    //catroid logout
    $this->doCatroidLogout();
    $answer .= $this->languageHandler->getString('catroid_logout_success').'<br>';

    //board logout
    $this->doBoardLogout();
    $answer .= $this->languageHandler->getString('catroid_logout_success').'<br>';

    //wiki logout
    try {
      $this->doWikiLogout();
      $answer .= $this->languageHandler->getString('catroid_logout_success').'<br>';
      $statusCode = 200;
    } catch (Exception $e) {
      $answer .= $this->languageHandler->getString('wiki_logout_error', ' '.$e->getMessage().'!<br>');
    }

    $this->statusCode = $statusCode;
    $this->answer = $answer;
  }

  public function doCatroidLogin($postData) {
    $user = getCleanedUsername($postData['loginUsername']);
    $md5pass = md5($postData['loginPassword']);
    
    $result = pg_execute($this->dbConnection, "get_user_login", array($user, $md5pass)) or
              $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }

    if(pg_num_rows($result) > 0) {
      $user = pg_fetch_assoc($result);
      $this->session->userLogin_userId = $user['id'];
      $this->session->userLogin_userNickname = ($user['username']);
      
      $ip = $_SERVER["REMOTE_ADDR"];
      $query = "EXECUTE reset_failed_attempts('$ip')";
      $result = @pg_query($this->dbConnection, $query);

      if(!$result) {
      	throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
      }      
    } else {
    	
    	$ip = $_SERVER["REMOTE_ADDR"];
    	$query = "EXECUTE save_failed_attempts('$ip')";
    	$result = @pg_query($this->dbConnection, $query);

     	if(!$result) {
     		throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
     	}
    	
      $this->answer .= "CatroidLogin: ";
      throw new Exception($this->errorHandler->getError('auth', 'password_or_username_wrong'));
    }
    return true;
  }

  public function doCatroidLogout() {
    $this->session->userLogin_userId = 0;
    $this->session->userLogin_userNickname = '';
    return true;
  }

  public function doBoardLogin($postData) {
    global $user, $auth;
    $user->session_begin();
    $auth->acl($user->data);
    $user->setup();
    
    $auth->login(trim($postData['loginUsername']), $postData['loginPassword'], false, 1);
    //$auth->login(trim($username), $password, false, 1);
    return($user->data['user_id']);
  }

  public function doBoardLogout() {
    initBoardFunctions();
    global $user, $auth;

    $user->session_begin();
    $auth->acl($user->data);
    $user->setup();
    $user->session_kill();
  }

  public function doWikiLogin($postData) {
    require_once("Snoopy.php");
    $snoopy = new Snoopy();
    $snoopy->curl_path = false;
    $wikiroot = BASE_PATH.'addons/mediawiki';
    $api_url = $wikiroot . "/api.php";

    $login_vars['action'] = "login";
    $username = getCleanedUsername($postData['loginUsername']);
    
    //wiki login needs first letter capitalized 
    $username = mb_convert_case($username, MB_CASE_TITLE, "UTF-8");
    $login_vars['lgname'] = $username;
    $login_vars['lgpassword'] = $postData['loginPassword'];
    $login_vars['format'] = "php";

    $snoopy->submit($api_url, $login_vars);
    $response = unserialize($snoopy->results);
    if(!isset($response['login']['result']) || !isset($response['login']['token']) ||
    !isset($response['login']['cookieprefix']) || !isset($response['login']['sessionid'])) {
      throw new Exception($this->errorHandler->getError('auth', 'wiki_api_response_incorrect', $snoopy->results));
    }
    $login_vars['lgtoken'] = $response['login']['token'];
    $cookieprefix = $response['login']['cookieprefix'];
    $snoopy->cookies[$cookieprefix."_session"] = $response['login']['sessionid'];

    $snoopy->submit($api_url, $login_vars);
    $response = unserialize($snoopy->results);

    if(!isset($response['login']['result']) || !isset($response['login']['lgtoken']) ||
    !isset($response['login']['cookieprefix']) || !isset($response['login']['lgusername']) ||
    !isset($response['login']['lgtoken']) || !isset($response['login']['sessionid'])) {
      throw new Exception($this->errorHandler->getError('auth', 'wiki_api_response_incorrect', $snoopy->results));
    }

    $cookieexpire = 0;//time() + (60*60*24*2);
    $cookiedomain = '';

    setcookie($cookieprefix.'UserName', $response['login']['lgusername'], $cookieexpire, "/", $cookiedomain, false, true);
    setcookie($cookieprefix.'UserID', $response['login']['lguserid'], $cookieexpire, "/", $cookiedomain, false, true);
    setcookie($cookieprefix.'Token', $response['login']['lgtoken'], $cookieexpire, "/", $cookiedomain, false, true);
    setcookie($cookieprefix.'_session', $response['login']['sessionid'], 0, "/", $cookiedomain, false, true);

    return true;
  }

  public function doWikiLogout() {
    include_once "Snoopy.php";
    $snoopy = new Snoopy();
    $snoopy->curl_path = false;
    $wikiroot = BASE_PATH.'addons/mediawiki';
    $api_url = $wikiroot . "/api.php?action=logout";

    $logout_vars['action'] = "logout";
    $snoopy->submit($api_url, $logout_vars);
    $response = $snoopy->results;

    $now = date('YmdHis', time());
    $cookieexpire = time() + (60*60*24*2);
    $cookieexpire_over = time() - (60*60*24*2);
    $cookiedomain = '';

    setcookie('catrowikiLoggedOut', $now, $cookieexpire, "/", $cookiedomain, false, true);
    setcookie('catrowiki_session', '', $cookieexpire_over, "/", $cookiedomain, false, true);
    setcookie('catrowikiUserID', '', $cookieexpire_over, "/", $cookiedomain, false, true);
    setcookie('catrowikiUserName', '', $cookieexpire_over, "/", $cookiedomain, false, true);
    setcookie('catrowikiToken', '', $cookieexpire_over, "/", $cookiedomain, false, true);
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>