<?php
/**
  * Catroid: An on-device visual programming system for Android devices
  * Copyright (C) 2010-2014 The Catrobat Team
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

class CoreRegistry {
  const KEY_ERROR = 'errorHandler';
  const KEY_LANGUAGE = 'languageHandler';
  const KEY_MAIL = 'mailHandler';
  const KEY_CLIENTDETECTION = 'clientDetection';
  const KEY_BADWORDS = 'badWordsFilter';

  protected static $instance = null;
  protected $values = array();

  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CoreRegistry();
    }
    return self::$instance;
  }

  private function __construct() {

  }

  protected function get($var) {
    if(isset($this->values[$var])) {
      return $this->values[$var];
    }
    return null;
  }

  protected function set($key, $value) {
    $this->values[$key] = $value;
  }

  public function setErrorHandler(CoreErrorHandler $error) {
    $this->set(self::KEY_ERROR, $error);
  }

  public function getErrorHandler() {
    return $this->get(self::KEY_ERROR);
  }

  public function setLanguageHandler(CoreLanguageHandler $lang) {
    $this->set(self::KEY_LANGUAGE, $lang);
  }

  public function getLanguageHandler() {
    return $this->get(self::KEY_LANGUAGE);
  }

  public function setMailHandler(CoreMailHandler $mail) {
    $this->set(self::KEY_MAIL, $mail);
  }

  public function getMailHandler() {
    return $this->get(self::KEY_MAIL);
  }

  public function setClientDetection(CoreClientDetection $clientDetection) {
    $this->set(self::KEY_CLIENTDETECTION, $clientDetection);
  }

  public function getClientDetection() {
    return $this->get(self::KEY_CLIENTDETECTION);
  }

  public function setBadwordsFilter(CoreBadwordsFilter $badWordsFilter) {
    $this->set(self::KEY_BADWORDS, $badWordsFilter);
  }

  public function getBadwordsFilter() {
    return $this->get(self::KEY_BADWORDS);
  }

  private function __clone() {
  }

  public function __destruct() {

  }

}

?>