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

class CoreLanguageHandler {
  private $strings = array();
  private $defaultLanguageStrings = array();
  private $moduleName = '';
  private $className = '';
  private $language = '';
  private $browserLanguage = '';

  public function __construct($moduleName, $className, $browserLanguage) {
    $this->moduleName = $moduleName;
    $this->className = $className;
    $this->browserLanguage = $browserLanguage;
    $this->setSiteLanguage();
    if(strcmp($this->language, SITE_DEFAULT_LANGUAGE) != 0) {
      $this->setDefaultStrings();
      $this->setDefaultTemplateStrings();
    }
    $this->setStrings();
    $this->setTemplateStrings();
  }

  private function setStrings() {
    $file = $this->getLanguageFile($this->className.'.xml');
    if(!$file) {
      return false;
    }
    $xml = simplexml_load_file($file);
    foreach($xml->children() as $string) {
      $attributes = $string->attributes();
      if($string->getName() && $attributes['name']) {
        $this->strings[strval($attributes['name'])] = strval($string);
      }
    }
  }

  private function setDefaultStrings() {
    $file = $this->getDefaultLanguageFile($this->className.'.xml');
    if(!$file) {
      return false;
    }
    $xml = simplexml_load_file($file);
    foreach($xml->children() as $string) {
      $attributes = $string->attributes();
      if($string->getName() && $attributes['name']) {
        $this->defaultLanguageStrings[strval($attributes['name'])] = strval($string);
      }
    }
  }

  private function setTemplateStrings() {
    $file = $this->getLanguageFile(DEFAULT_TEMPLATE_LANGUAGE_FILE);
    if(!$file) {
      return false;
    }
    $xml = simplexml_load_file($file);
    foreach($xml->children() as $string) {
      $attributes = $string->attributes();
      if($string->getName() && $attributes['name']) {
        $this->strings[strval($attributes['name'])] = strval($string);
      }
    }
  }

  private function setDefaultTemplateStrings() {
    $file = $this->getDefaultLanguageFile(DEFAULT_TEMPLATE_LANGUAGE_FILE);
    if(!$file) {
      return false;
    }
    $xml = simplexml_load_file($file);
    foreach($xml->children() as $string) {
      $attributes = $string->attributes();
      if($string->getName() && $attributes['name']) {
        $this->defaultLanguageStrings[strval($attributes['name'])] = strval($string);
      }
    }
  }

  public function getString($code) {
    $numargs = func_num_args();
    $args = array();
    if($numargs > 1) {
      $args = array_slice(func_get_args(), 1);
    }
    if(isset($this->strings[$code])) {
      return $this->parseString($this->strings[$code], $args);
    } else if(isset($this->defaultLanguageStrings[$code])) {
      return $this->parseString($this->strings[$code], $args);
    } else {
      return 'unknown string: "'.$code.'"!';
    }
  }

  public function parseString($msg, $args) {
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

  public function getStrings() {
    return $this->strings;
  }

  public function getLanguage() {
    return $this->language;
  }

  private function getLanguageFile($fileName) {
    $selectedLanguagefile = CORE_BASE_PATH.LANGUAGE_PATH.$this->language.'/'.$this->moduleName.'/'.$fileName;
    if(!file_exists($selectedLanguagefile)) {
      return false;
    }
    return $selectedLanguagefile;
  }

  private function getDefaultLanguageFile($fileName) {
    $defaultLanguagefile = CORE_BASE_PATH.LANGUAGE_PATH.SITE_DEFAULT_LANGUAGE.'/'.$this->moduleName.'/'.$fileName;
    if(!file_exists($defaultLanguagefile)) {
      return false;
    }
    return $defaultLanguagefile;
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

  private function setSiteLanguage() {
    if(isset($_REQUEST['userLanguage'])) {
      $lang = $_REQUEST['userLanguage'];
    } else if(isset($_COOKIE['site_language'])) {
      $lang = $_COOKIE['site_language'];
    } else {
      $lang = $this->browserLanguage;
    }
    if(!in_array($lang, getSupportedLanguagesArray())) {
      $lang = SITE_DEFAULT_LANGUAGE;
    }
    $this->setLanguageCookie($lang);
    $this->language = $lang;
  }

  public function setLanguageCookie($lang) {
    if(!defined('UNITTESTS')) {
      setcookie('site_language', $lang, 0, "/", '', false, true);
    }
  }

  public function __destruct() {
  }
}

?>