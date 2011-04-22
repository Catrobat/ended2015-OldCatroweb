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

abstract class CoreModule extends CoreObjectWeb {
    protected $data = array();
    public $errorHandler = null;
    public $languageHandler = null;
    public $mailHandler = null;
    public $clientDetection = null;
    public $name = null;
    public $presenter = null;
    public $tplFile = null;
    public $htmlFile = null;
    public $htmlHeaderFile = null;
    public $htmlFooterFile = null;
    public $moduleName = null;
    public $pageTemplateFile = null;
    public $badWordsFilter = null;

    public function __construct() {
      parent::__construct();
      $this->name = $this->me->getName();
      $this->coreRegistry->setMailHandler(new CoreMailHandler());
      $this->mailHandler = $this->coreRegistry->getMailHandler();
      $this->coreRegistry->setClientDetection(new CoreClientDetection());
      $this->clientDetection = $this->coreRegistry->getClientDetection();
      $this->setSiteLanguage();
      $this->coreRegistry->setErrorHandler(new CoreErrorHandler($this->session, $this->mailHandler));
      $this->errorHandler = $this->coreRegistry->getErrorHandler();
      $this->coreRegistry->setLanguageHandler(new CoreLanguageHandler($this->session->SITE_LANGUAGE, $this->name));
      $this->languageHandler = $this->coreRegistry->getLanguageHandler();
      $this->coreRegistry->setBadwordsFilter(new CoreBadwordsFilter($this->dbConnection));
      $this->badWordsFilter = $this->coreRegistry->getBadwordsFilter();
      
    }

    abstract public function __default();

    public function getData() {
        return $this->data;
    }

    public static function isValid($module) {
        return (is_object($module) &&
                $module instanceof CoreModule &&
                $module instanceof CoreAuthentication);
    }

    public function __set($property, $value) {
        $this->data[$property] = $value;
    }

    public function __get($property) {
        if(isset($this->data[$property])) {
            return $this->data[$property];
        }
        return null;
    }

    public function __call($method, $args) {
        $method = MVC_DEFAULT_METHOD;
        $this->$method($args);
    }

    public function __destruct() {
        parent::__destruct();
    }
    
    private function setSiteLanguage() {
      if(!isset($this->session->SITE_LANGUAGE)) {
        if(isset($_GET['userLanguage'])) {
          $lang = $_GET['userLanguage'];
          setcookie('site_language', $lang, 0, "/", '', false, true);
        } else if(isset($_COOKIE['site_language'])) {
          $lang = $_COOKIE['site_language'];
        } else if(isset($_POST['userLanguage'])) {
          $lang = $_POST['userLanguage'];
        } else {
          $lang = $this->clientDetection->getBrowserLanguage();
        }
      }
      $supportedLanguages = getSupportedLanguagesArray();
      if(!$supportedLanguages[$lang]) {
        $lang = SITE_DEFAULT_LANGUAGE;
      }
      $this->session->SITE_LANGUAGE = $lang;
    }
}

?>