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
  private $moduleName = '';
  private $className = '';
  private $language = '';
  private $browserLanguage = '';

  public function __construct($moduleName, $className, $browserLanguage) {
    $this->moduleName = $moduleName;
    //print "\n\nmodulename: $moduleName\n\n";
    $this->className = $className;
    $this->browserLanguage = $browserLanguage;
    $this->setSiteLanguage();
    $this->setStrings();
    $this->setTemplateStrings();
  }

  private function setStrings() {
    $file = $this->getLanguageFile($this->className.'.xml');
    if(!$file) {
      //die("text string file not found!"); //at least an english string xml file must exist for each module -> see phpunit/catroid/languageTest.php
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

  private function setTemplateStrings() {
    $file = $this->getLanguageFile('template.xml');
    if(!$file) {
      //die("text string file not found!"); //at least an english string xml file must exist for each module -> see phpunit/catroid/languageTest.php
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

  public function getString($code) {
    $numargs = func_num_args();
    $args = array();
    if($numargs > 1) {
      $args = array_slice(func_get_args(), 1);
    }
    if(isset($this->strings[$code])) {
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
    $defaultLanguagefile = CORE_BASE_PATH.LANGUAGE_PATH.$this->moduleName.'/'.SITE_DEFAULT_LANGUAGE.'/'.$fileName;
    $selectedLanguagefile = CORE_BASE_PATH.LANGUAGE_PATH.$this->moduleName.'/'.$this->language.'/'.$fileName;
    if(!file_exists($defaultLanguagefile)) {
      //print "not exist: $defaultLanguagefile\n";
      return false;
    }
    if(!file_exists($selectedLanguagefile)) {
      return $defaultLanguagefile;
    }
    $defaultLanguageXml = simplexml_load_file($defaultLanguagefile);
    $selectedLanguageXml = simplexml_load_file($selectedLanguagefile);

    if(count($defaultLanguageXml->children()) == count($selectedLanguageXml->children())) {
      return $selectedLanguagefile;
    } else {
      return $defaultLanguagefile;
    }
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
    if(isset($_GET['userLanguage'])) {
      $lang = $_GET['userLanguage'];
      setcookie('site_language', $lang, 0, "/", '', false, true);
    } else if(isset($_COOKIE['site_language'])) {
      $lang = $_COOKIE['site_language'];
    } else if(isset($_POST['userLanguage'])) {
      $lang = $_POST['userLanguage'];
    } else {
      $lang = $this->browserLanguage;
    }
    $supportedLanguages = getSupportedLanguagesArray();
    if(!isset($supportedLanguages[$lang])) {
      $lang = SITE_DEFAULT_LANGUAGE;
    }
    $this->language = $lang;
  }

  public function __destruct() {
  }
}

?>