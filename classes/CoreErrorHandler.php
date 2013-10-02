<?php
/**
  * Catroid: An on-device visual programming system for Android devices
  * Copyright (C) 2010-2013 The Catrobat Team
  * (<http://developer.catrobat.org/credits>)
  * 
  * This program is free software: you can redistribute it and/or modify
  * it under the terms of the GNU Affero General Public License as
  * published by the Free Software Foundation, either version 3 of the
  * License, or (at your option) any later version.
  * 
  * An additional term exception under section 7 of the GNU Affero
  * General Public License, version 3, is available at
  * http://developer.catrobat.org/license_additional_term
  * 
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  * GNU Affero General Public License for more details.
  * 
  * You should have received a copy of the GNU Affero General Public License
  * along with this program. If not, see <http://www.gnu.org/licenses/>.
  */

class CoreErrorHandler {
  private $errors = array();
  private $defaultLanguageErrors = array();
  private $session;
  private $mailHandler;
  private $moduleName;
  private $language;

  public function __construct($session, $mailHandler, $moduleName) {
    $this->session = $session;
    $this->mailHandler = $mailHandler;
    $this->moduleName = $moduleName;
    $this->language = $this->session->SITE_LANGUAGE;
    if(strcmp($this->language, SITE_DEFAULT_LANGUAGE) != 0) {
      $this->setDefaultErrors();
    }
    $this->setErrors();
  }

  private function setErrors() {
    $file = CORE_BASE_PATH.LANGUAGE_PATH.$this->language.'/'.'errors/'.DEFAULT_PUB_ERRORS_FILE;
    if(DEVELOPMENT_MODE) {
      $file = CORE_BASE_PATH.LANGUAGE_PATH.$this->language.'/'.'errors/'.DEFAULT_DEV_ERRORS_FILE;
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

  private function setDefaultErrors() {
    $file = CORE_BASE_PATH.LANGUAGE_PATH.SITE_DEFAULT_LANGUAGE.'/'.'errors/'.DEFAULT_PUB_ERRORS_FILE;
    if(DEVELOPMENT_MODE) {
      $file = CORE_BASE_PATH.LANGUAGE_PATH.SITE_DEFAULT_LANGUAGE.'/'.'errors/'.DEFAULT_DEV_ERRORS_FILE;
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
          $this->defaultLanguageErrors[strval($error_type->getName())][strval($attributes['name'])] = strval($error);
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
    } elseif(isset($this->defaultLanguageErrors[$type][$code])) {
      if(DEVELOPMENT_MODE && $extraInfo != '') {
        return $this->parseErrorMessage($this->defaultLanguageErrors[$type][$code], $args).'<br>'.$extraInfo;
      } else {
        return $this->parseErrorMessage($this->defaultLanguageErrors[$type][$code], $args);
      }
    } else {
      return 'unknown error: "'.$code.'"!';
    }
  }

  public function showErrorPage($type, $code, $extraInfo = '') {
    $this->session->errorType = $type;
    $this->session->errorCode = $code;
    $this->session->errorExtraInfo = $extraInfo;
    $this->sendNotificationEmail($type, $code, $extraInfo);
    
    if(!headers_sent()) {
      if(defined('UNITTESTS') && UNITTESTS) {
        return false;
      }
    
      header("Location: " . BASE_PATH . "error");
      exit();
    } else {
      return false;
    }
  }

  private function sendNotificationEmail($type, $code, $extraInfo) {
    $http_ref = "";
    if (isset($_SERVER["HTTP_REFERER"])) $http_ref = $_SERVER["HTTP_REFERER"];
    $mailSubject = 'An error occurred during processing a page!';
    $mailText = "Hello ".APPLICATION_URL_TEXT." Administrator!\n\n";
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
	
    if ($code != "not_a_tugraz_ip")
    	return($this->mailHandler->sendAdministrationMail($mailSubject, $mailText));
    else
    	return null;
  }

  public function parseErrorMessage($msg, $args) {
    if(count($args) <= 0) {
      return $msg;
    }
    if(!$this->checkParamCount($msg, count($args))) {
      return $msg;
    }
    for($i=0; $i<count($args); $i++) {
      $pattern = "/[{][\*][a-zA-Z0-9_]+[\*][}]/";
      $msg = preg_replace($pattern, $args[$i], $msg, 1);
    }
    return $msg;
  }

  public function checkParamCount($msg, $num) {
    $ret = array();
    $paramCount = preg_match_all("/[{][\*][a-zA-Z0-9_]+[\*][}]/", $msg, $ret);
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