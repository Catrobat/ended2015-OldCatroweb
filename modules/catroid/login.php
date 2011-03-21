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

class login extends CoreAuthenticationNone {

  public function __construct() {
    parent::__construct();
    $this->setupBoard();
    $this->addCss('login.css');
  }

  public function __default() {
    if($_POST) {
      if(isset($_POST['loginSubmit'])) {
        if(isset($_POST['requesturi'])) {
          $this->setRequestURI($_POST['requesturi']);
        }
        if($this->doLogin($_POST)) {
          header('Location: http://' . $_SERVER['HTTP_HOST'] . "/" . $this->requesturi);
          exit;
        }
      } else if(isset($_POST['logoutSubmit'])) {
        $this->doLogout();
        header('Location: ' . BASE_PATH . 'catroid/index');
        exit;
      }
    }
  }

  private function setRequestURI($uri) {
    if($uri != '') {
      $this->requesturi = $_POST['requesturi'];  
    }
    else {
      $this->requesturi = "/catroid/index";
    }
  }
  
  public function doLogin($postData) {
    //$postData = $this->encodeUtf8($postData);
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
    } catch (Exception $e) {
      $answer .= 'ERROR: WIKI Logout: '.$e->getMessage().'!<br>';
    }

    $this->answer = $answer;
  }
  
  public function doCatroidLogin($postData) {
    $user = utf8_encode($postData['loginUsername']);
    $pass = md5($postData['loginPassword']);
    $query = "EXECUTE get_user_login('$user', '$pass')";
    
    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection))); 
    }
    
    if(pg_num_rows($result) > 0) {
      $user = pg_fetch_assoc($result);
      $this->session->userLogin_userId = $user['id'];
      $this->session->userLogin_userNickname = utf8_decode($user['username']);
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
    $auth->login(utf8_encode($postData['loginUsername']), $postData['loginPassword'], false, 1);
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
    $login_vars['lgname'] = utf8_clean_string(utf8_encode($postData['loginUsername']));
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
  
  /*
  private function encodeUtf8($postData) {
    $postData['loginUsername'] = utf8_encode($postData['loginUsername']);
    return $postData;
  }
  */

  
  
  
  /*
   private function doWikiLogin($user, $pass, $token='') {
   $url = BASE_PATH.'addons/mediawiki'."/api.php?action=login&format=php";
   $params = "action=login&lgname=$user&lgpassword=$pass";
   if($token != '') {
   $params .= "&lgtoken=$token";
   }

   $data = $this->wikiHttpRequest($url, $params);

   if(count($data) <= 0) {
   throw new Exception("No data received from server. Check that API is enabled.");
   }

   $result = $data['login']['result'];
   $cookieexpire = time() + (60*60*24*2);
   //$cookiedomain = '.dev.catroid.localhost';
   //$cookiedomain = '.catroidwebtest.ist.tugraz.at';
   //$cookiedomain = BASE_PATH;
   $cookiedomain = '';
   if($result == 'NeedToken') {
   $token = $data['login']['token'];
   $sessionid = $data['login']['sessionid'];
   $cookieprefix = $data['login']['cookieprefix'];
   if(empty($token) || empty($sessionid) || empty($cookieprefix)) {
   throw new Exception("No token/sessionid/cookieprefix found!");
   }
   setcookie($cookieprefix.'_session', $sessionid, 0, "/", $cookiedomain, false, true);
   return $token;
   } elseif($result == 'Success') {
   $sessionid = $data['login']['sessionid'];
   $cookieprefix = $data['login']['cookieprefix'];
   $lguserid = $data['login']['lguserid'];
   $lgusername = $data['login']['lgusername'];
   $lgtoken = $data['login']['lgtoken'];
   if(empty($lgtoken) || empty($sessionid) || empty($cookieprefix) || empty($lguserid) || empty($lgusername)) {
   throw new Exception("No lgtoken/sessionid/cookieprefix/lguserid/lgusername found!");
   }

   setcookie($cookieprefix.'UserName', $lgusername, $cookieexpire, "/", $cookiedomain, false, true);
   setcookie($cookieprefix.'UserID', $lguserid, $cookieexpire, "/", $cookiedomain, false, true);
   setcookie($cookieprefix.'_session', $sessionid, 0, "/", $cookiedomain, false, true);

   return '';
   } else {
   //unknown result value
   throw new Exception("Unknown result value: $result");
   }
   }
   */

  /*
   public function doWikiLogoutCurl() {
   $url = BASE_PATH.'addons/mediawiki'."/api.php?action=logout&format=php";
   $this->wikiHttpRequest($url);
   }
   */

  /*
   private function wikiHttpRequest($url, $post="") {
   $cookiefile = 'cookies.tmp';
   $userAgent = "CatroidLoginBot/0.6";

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $url);
   //curl_setopt($ch, CURLOPT_POST, 1);
   if (!empty($post)) curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
   curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
   curl_setopt($ch, CURLOPT_ENCODING, "UTF-8" );
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);
   curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile);

   $returnData = curl_exec($ch);
   curl_close($ch);
   return unserialize($returnData);
   }
   */



  public function __destruct() {
    parent::__destruct();
  }
}
?>
