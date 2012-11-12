<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2012 The Catroid Team
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

abstract class CoreObjectWeb extends CoreObjectDatabase {
  public $session;
  public $cssFiles;
  public $jsFiles;
  public $globalJsFiles;
  public $websiteTitle;

  public function __construct() {
    parent::__construct();
    $this->session = CoreSession::getInstance();
    $this->cssFiles = array();
    $this->jsFiles = array();
    $this->globalJsFiles = array();
    $this->websiteTitle = SITE_DEFAULT_TITLE;
  }

  public function _destruct() {
    parent::_destruct();
  }

  public function addCss($file) {
    if(!in_array($file, $this->cssFiles)) {
      array_push($this->cssFiles, $file);
    }
  }

  public function getCss() {
    return array_shift($this->cssFiles);
  }

  public function addJs($file) {
    if(!in_array($file, $this->jsFiles)) {
      array_push($this->jsFiles, $file);
    }
  }

  public function addGlobalJs($file) {
    if(!in_array($file, $this->globalJsFiles)) {
      array_push($this->globalJsFiles, $file);
    }
  }
  
  public function getJs() {
    return $this->cacheJs($this->jsFiles, $this->me->name);
  }
  
  public function getGlobalJs() {
    return $this->cacheJs($this->globalJsFiles, 'global');
  }

  public function cacheJs($jsFiles = array(), $name = '') {
    $timestamp = 0;
    
    foreach($jsFiles as $js) {
      $file = CORE_BASE_PATH . 'include/script/' . $js;
      if(file_exists($file)) {
        $filetime = filemtime($file);
        if($filetime > $timestamp) {
          $timestamp = $filetime;
        }
      }
    }

    if($timestamp == 0) {
      return "";
    }
    
    $filename = 'cache/' . $name . $timestamp . '.js';
    if(!file_exists(CORE_BASE_PATH . '/' . $filename)) {
      $handle = fopen(CORE_BASE_PATH . '/' . $filename, "w");
      
      foreach($jsFiles as $js) {
        $file = CORE_BASE_PATH . 'include/script/' . $js;
        if(file_exists($file)) {
          $content = file_get_contents($file);

          if($content !== FALSE) {
            fwrite($handle, $content);
          } 
        }
      }

      fclose($handle);
    }
    return '<script src="' . BASE_PATH . $filename . '"></script>';
  }
  
  public function setWebsiteTitle($title) {
    $this->websiteTitle .= ' - ' . $title;
  }
  
  public function getWebsiteTitle() {
    return $this->websiteTitle;
  }
  
  public function loadModule($module) {
    $modulePath = CORE_BASE_PATH . 'modules/' . $module . '.php';
    if(file_exists($modulePath)) {
      $moduleName = basename($modulePath, '.php');

      require_once($modulePath);
      eval("\$this->" . $moduleName . " = new " . $moduleName . "();");      
    } else {
      exit('unknown module: ' . $modulePath);
    }
  }
  
  public function loadView($viewer) {
    $exception = new Exception();
    $traces = $exception->getTrace();
    
    $callerFile = "";
    foreach($traces as $trace) {
      if($trace['function'] == 'loadView') {
        $callerFile = $trace['file'];
        break;
      }
    }

    $module = '';
    $fragments = explode('/', $callerFile);
    for($i = 0, $length = count($fragments); $i < $length; $i++) {
      if($fragments[$i] == 'modules' && ($i + 1) < $length) {
        $module = $fragments[++$i]; 
        break;
      }
    }
      
    $viewerPath = CORE_BASE_PATH . 'viewer/' . $module . '/' . $viewer . '.php';
    if(file_exists($viewerPath)) {
      $this->htmlFile = $viewer . '.php';
    } else {
      exit('unknown viewer: ' . $viewerPath);
    }
  }
}
?>