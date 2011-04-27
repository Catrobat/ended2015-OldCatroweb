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

class CoreErrorHandler {
  private $errors = array();
  private $session;
  private $mailHandler;
  private $moduleName;

  public function __construct($session, $mailHandler, $moduleName) {
    $this->session = $session;
    $this->mailHandler = $mailHandler;
    $this->moduleName = $moduleName;
    $this->setErrors();
  }

  private function setErrors() {
    $file = CORE_BASE_PATH.LANGUAGE_PATH.$this->moduleName.'/'.$this->session->SITE_LANGUAGE.'/errors_pub.xml';
    if(DEVELOPMENT_MODE) {
      $file = CORE_BASE_PATH.LANGUAGE_PATH.$this->moduleName.'/'.$this->session->SITE_LANGUAGE.'/errors_dev.xml';
    }
    if(file_exists($file)) {
      $xml = simplexml_load_file($file);
    } else {
      return false;
    }

    foreach($xml->children() as $error_type) {
      foreach($error_type as $error) {
        $attributes = $error->attributes();
        if($error_type->getName() && $attributes['name']) {
          $this->errors[strval($error_type->getName())][strval($attributes['name'])] = strval($error);
        }
      }
    }
  }

  public function getErrors() {
    return $this->errors;
  }

  public function getError($type, $code, $extraInfo = '') {
    $numargs = func_num_args();
    $args = array();
    if($numargs > 3) {
      $args = array_slice(func_get_args(), 3);
    }
    if(isset($this->errors[$type][$code])) {
      if(DEVELOPMENT_MODE && $extraInfo != '') {
        return $this->parseErrorMessage($this->errors[$type][$code], $args).'<br>'.$extraInfo;
      } else {
        return $this->parseErrorMessage($this->errors[$type][$code], $args);
      }
    } else {
      return 'unknown error: "'.$code.'"!';
    }
  }

  public function showErrorPage($type, $code, $extraInfo = '') {
    $numargs = func_num_args();
    $args = array();
    if($numargs > 3) {
      $args = array_slice(func_get_args(), 3);
    }
    $this->session->errorType = $type;
    $this->session->errorCode = $code;
    $this->session->errorExtraInfo = $extraInfo;
    $this->session->errorArgs = $args;
    $this->sendNotificationEmail($type, $code, $extraInfo);
    if(!headers_sent()) {
      header("Location: ".BASE_PATH."catroid/errorPage");
      exit();
    } else {
      return false;
    }
  }

  private function sendNotificationEmail($type, $code, $extraInfo) {
    $http_ref = "";
    if (isset($_SERVER["HTTP_REFERER"])) $http_ref = $_SERVER["HTTP_REFERER"];
    $mailSubject = 'An error occurred during processing a page!';
    $mailText = "Hello catroid.org Administrator!\n\n";
    $mailText .= "An error message was produced during processing the page\n";
    $mailText .= "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."\n\n";
    $mailText .= "--- ERROR DETAILS ---\n";
    $mailText .= "Error Type: <".$type.">\n";
    $mailText .= "Error Code: <".$code.">\n";
    $mailText .= "Error Message: <".$this->getError($type, $code).">\n";
    $mailText .= "Error Extra Info: <".$extraInfo.">\n";
    $mailText .= "Server Time: <".date('Y-m-d H:i:s', time()).">\n";
    $mailText .= "--- *** ---\n\n";
    $mailText .= "--- USER DETAILS ---\n";
    $mailText .= "User IP: <".$_SERVER['REMOTE_ADDR'].">\n";
    $mailText .= "User HTTP Referer: <".$http_ref.">\n";
    $mailText .= "--- *** ---\n\n";
    $mailText .= "You should check this!";

    return($this->mailHandler->sendAdministrationMail($mailSubject, $mailText));
  }

  public function parseErrorMessage($msg, $args) {
    if(count($args) <= 0) {
      return $msg;
    }
    if(!$this->checkParamCount($msg, count($args))) {
      return $msg;
    }
    for($i=count($args); $i>0; $i--) {
      $placeholderString = '\$'.$i;
      $pattern = "/".$placeholderString."/";
      $msg = preg_replace($pattern, $args[$i-1], $msg);
    }

    return $msg;
  }

  public function checkParamCount($msg, $num) {
    $ret = array();
    $paramCount = preg_match_all("/[\$][0-9]+/", $msg, $ret);
    if($paramCount == $num) {
      return true;
    } else {
      return false;
    }
  }

  public function __destruct() {
  }
}

?>