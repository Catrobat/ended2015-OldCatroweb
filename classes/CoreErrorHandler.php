<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or License, or License, or (at your option) any later version.
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

  public function __construct($session, $mailHandler) {
    $this->session = $session;
    $this->mailHandler = $mailHandler;
    $this->setErrors();
  }

  private function setErrors() {
    $file = CORE_BASE_PATH.XML_PATH.'errors_pub.xml';
    if(DEVELOPMENT_MODE) {
      $file = CORE_BASE_PATH.XML_PATH.'errors_dev.xml';
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
    if(isset($this->errors[$type][$code])) {
      if(DEVELOPMENT_MODE && $extraInfo != '') {
        return $this->errors[$type][$code].'<br>'.$extraInfo;
      } else {
        return $this->errors[$type][$code];
      }
    } else {
      return 'unknown error: "'.$code.'"!';
    }
  }

  public function showError($type, $code, $extraInfo = '') {
    if(!$this->errors[strval($type)][strval($code)]) {
      echo "unknown error: \"".$code."\"!";
      echo "<br /><a href='".BASE_PATH."'>Go back to start.</a>";
      exit();
    }

    echo 'ERROR: '.$this->errors[strval($type)][strval($code)];
    if(DEVELOPMENT_MODE) {
      echo ': <br />'.$extraInfo;
    }
    else {
      echo "<br /><a href='".BASE_PATH."'>Go back to start.</a>";
    }
    exit();
  }

  public function showErrorPage($type, $code, $extraInfo = '') {
    $this->session->errorType = $type;
    $this->session->errorCode = $code;
    $this->session->errorExtraInfo = $extraInfo;
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

  public function __destruct() {
  }
}

?>