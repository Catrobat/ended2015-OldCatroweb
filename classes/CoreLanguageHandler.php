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
  private $language = '';

  public function __construct($language, $moduleName) {
    $this->moduleName = $moduleName;
    $this->language = $language;
    $this->setStrings();
  }

  private function setStrings() {
    $file = CORE_BASE_PATH.LANGUAGE_PATH.$this->language.'/'.$this->moduleName.'.xml';
    if(file_exists($file)) {
      $xml = simplexml_load_file($file);
    } else {
      return false;
    }

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
    if($numargs > 3) {
      $args = array_slice(func_get_args(), 3);
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
    for($i=count($args); $i>0; $i--) {
      $placeholderString = '\$'.$i;
      $pattern = "/".$placeholderString."/";
      $msg = preg_replace($pattern, $args[$i-1], $msg);
    }  
    return $msg;
  }

  public function getStrings() {
    return $this->strings;
  }

  public function getLanguage() {
    return $this->language;
  }

  public function __destruct() {
  }
}

?>