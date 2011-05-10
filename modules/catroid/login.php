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

class login extends CoreAuthenticationNone {

  public function __construct() {
    parent::__construct();

    $this->setupBoard();
    $this->addCss('login.css?'.VERSION);
    $this->addCss('buttons.css?'.VERSION);
    $this->addJs('login.js');
  }

  public function __default() {
    if($this->session->userLogin_userId > 0) {
      header("Location: ".BASE_PATH."catroid/index");
      exit;
    }

  }

  public function logoutRequest() {
    $this->logout($_POST);
  }

  public function loginRequest() {
    $postData = $_POST;
    if($postData) {
      if(isset($postData['requesturi'])) {
        $this->setRequestURI($postData['requesturi']);
      }
      $this->doLogin($postData);
    }
  }

  public function logout($postData) {
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

  public function doLogin($postData) {
    $answer = '';
    $statusCode = 500;
    $boardLoginSuccess = false;
    $wikiLoginSuccess = false;
    $catroidLoginSuccess = false;

    try {
      $catroidUserId = $this->doCatroidLogin($postData);
      $catroidLoginSuccess = true;
      $answer .= 'CATROID Login successfull!<br>';
    } catch(Exception $e) {
      $answer .= $this->errorHandler->getError('auth', 'catroid_authentication_failed', $e->getMessage()).'<br>';
    }

    if($catroidLoginSuccess) {
      $boardUserId = $this->doBoardLogin($postData);
      if($boardUserId > 1) {
        $boardLoginSuccess = true;
        $answer .= 'BOARD Login successfull!<br>';
      } else {
        $answer = $this->errorHandler->getError('auth', 'board_authentication_failed').'<br>';
      }

      if($boardLoginSuccess) {
        try {
          $this->doWikiLogin($postData);
          $wikiLoginSuccess = true;
          $answer .= 'WIKI Login successfull!<br>';
          $statusCode = 200;
        } catch(Exception $e) {
          $answer = $this->errorHandler->getError('auth', 'wiki_authentication_failed', $e->getMessage()).'<br>';
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
    $this->answer = $answer;

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
    $answer .= 'CATROID Logout successfull!<br>';

    //board logout
    $this->doBoardLogout();
    $answer .= 'BOARD Logout successfull!<br>';

    //wiki logout
    try {
      $this->doWikiLogout();
      $answer .= 'WIKI Logout successfull!<br>';
      $statusCode = 200;
    } catch (Exception $e) {
      $answer .= 'ERROR: WIKI Logout: '.$e->getMessage().'!<br>';
    }

    $this->statusCode = $statusCode;
    $this->answer = $answer;
  }

  public function doCatroidLogin($postData) {
    global $phpbb_root_path;
    require_once($phpbb_root_path .'includes/utf/utf_tools.php');

    $user = $postData['loginUsername'];
    $user = utf8_clean_string($user);
    $pass = md5($postData['loginPassword']);
    $query = "EXECUTE get_user_login('$user', '$pass')";

    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)));
    }

    if(pg_num_rows($result) > 0) {
      $user = pg_fetch_assoc($result);
      $this->session->userLogin_userId = $user['id'];
      $this->session->userLogin_userNickname = ($user['username']);
    } else {
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
    $auth->login($postData['loginUsername'], $postData['loginPassword'], false, 1);
    return($user->data['user_id']);
  }

  public function doBoardLogout() {
    global $user, $auth;
    $user->session_begin();
    $auth->acl($user->data);
    $user->setup();
    $user->session_kill();
  }

  public function doWikiLogin($postData) {
    require_once("Snoopy.php");
    global $phpbb_root_path;
    require_once($phpbb_root_path .'includes/utf/utf_tools.php');
    $snoopy = new Snoopy();
    $snoopy->curl_path = false;
    $wikiroot = BASE_PATH.'addons/mediawiki';
    $api_url = $wikiroot . "/api.php";

    $login_vars['action'] = "login";
    $username = $postData['loginUsername'];
    $username = utf8_clean_string($username);
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
