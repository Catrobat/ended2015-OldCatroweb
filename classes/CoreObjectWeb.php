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
  public $globalCssFiles;
  public $jsFiles;
  public $globalJsFiles;
  public $websiteTitle;

  public function __construct() {
    parent::__construct();
    $this->session = CoreSession::getInstance();
    $this->cssFiles = array();
    $this->globalCssFiles = array();
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
  public function addGlobalCss($file) {
    if(!in_array($file, $this->globalCssFiles)) {
      array_push($this->globalCssFiles, $file);
    }
  }

  public function getCss() {
    return $this->cacheCss($this->cssFiles, $this->me->name);;
  }

  public function getGlobalCss($admin=false) {
    $scope = 'gcatroid';
    if($admin) {
      $scope = 'gadmin';
    }
    return $this->cacheCss($this->globalCssFiles, $scope);
  }

  private function cacheCss($cssFiles = array(), $name = '') {
    $timestamp = 0;
  
    foreach($cssFiles as $css) {
      $file = CORE_BASE_PATH . CSS_PATH . $css;
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
  
    $filename = CACHE_PATH . $name . $timestamp . '.css';
    $filenameMinified = CACHE_PATH . $name . $timestamp . '.min.css';
    if(!file_exists(CORE_BASE_PATH . $filename)) {
      $oldFiles = glob(CORE_BASE_PATH . CACHE_PATH . $name . '*.css');
      if(count($oldFiles) > 0) {
        array_walk($oldFiles, function ($file) {
          unlink($file);
        });
      }
  
      $handle = fopen(CORE_BASE_PATH . $filename, "w");
      foreach($cssFiles as $css) {
        $file = CORE_BASE_PATH . CSS_PATH . $css;
        if(file_exists($file)) {
          $content = file_get_contents($file);
  
          if($content !== FALSE) {
            fwrite($handle, $content);
          }
        }
      }
  
      fclose($handle);
      $this->compressCss($name . $timestamp);
    }
  
    if(!DEVELOPMENT_MODE && file_exists(CORE_BASE_PATH . $filenameMinified)) {
      $filename = $filenameMinified;
    }

    return '<link rel="stylesheet" href="' . BASE_PATH . $filename . '">';
  }
  
  private function compressCss($filename) {
    $source = CORE_BASE_PATH . CACHE_PATH . $filename . '.css';
    $minified = CORE_BASE_PATH . CACHE_PATH . $filename . '.min.css';
  
    system("java -jar " . CORE_BASE_PATH . "tools/stylesheets.jar " . $source . " --allow-unrecognized-functions --allow-unrecognized-properties --output-file " . $minified, $returnVal);
    if($returnVal != 0) {
      if(DEVELOPMENT_MODE) {
        echo '<script type="text/javascript">common.showAjaxErrorMsg("Warning couldn\'t minify css code. ' .
            'Have a look at line ' . (__LINE__ - 3) . ' in file ' . __FILE__ . '");</script>';
      }
      @unlink($minified);
    }
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
  
  public function getGlobalJs($admin=false) {
    $scope = 'gcatroid';
    if($admin) {
      $scope = 'gadmin';
    }
    return $this->cacheJs($this->globalJsFiles, $scope);
  }

  private function cacheJs($jsFiles = array(), $name = '') {
    $timestamp = 0;

    $jquery = 'jquery' . JQUERY_VERSION . '.min.js';
    if(!file_exists(CORE_BASE_PATH . CACHE_PATH . $jquery)) {
      file_put_contents(CORE_BASE_PATH . CACHE_PATH . $jquery, 
          file_get_contents("http://ajax.googleapis.com/ajax/libs/jquery/" . JQUERY_VERSION . "/jquery.min.js"));
    }
    
    foreach($jsFiles as $js) {
      $file = CORE_BASE_PATH . SCRIPT_PATH . $js;
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
    
    $filename = CACHE_PATH . $name . $timestamp . '.js';
    $filenameMinified = CACHE_PATH . $name . $timestamp . '.min.js';
    if(!file_exists(CORE_BASE_PATH . $filename)) {
      $oldFiles = glob(CORE_BASE_PATH . CACHE_PATH . $name . '*.js');
      if(count($oldFiles) > 0) {
        array_walk($oldFiles, function ($file) {
          unlink($file);
        });
      }
      
      $handle = fopen(CORE_BASE_PATH . $filename, "w");
      foreach($jsFiles as $js) {
        $file = CORE_BASE_PATH . SCRIPT_PATH . $js;
        if(file_exists($file)) {
          $content = file_get_contents($file);

          if($content !== FALSE) {
            fwrite($handle, $content);
          } 
        }
      }

      fclose($handle);
      $this->compressJs($name . $timestamp);
    }

    if(!DEVELOPMENT_MODE && file_exists(CORE_BASE_PATH . $filenameMinified)) {
      $filename = $filenameMinified;
    }

    return '<script src="' . BASE_PATH . $filename . '"></script>';
  }
  
  private function compressJs($filename) {
    $source = CORE_BASE_PATH . CACHE_PATH . $filename . '.js';
    $minified = CORE_BASE_PATH . CACHE_PATH . $filename . '.min.js';
    
    system("java -jar " . CORE_BASE_PATH . "tools/compiler.jar --compilation_level SIMPLE_OPTIMIZATIONS --js " . 
        $source . " --js_output_file " . $minified, $returnVal);
    if($returnVal != 0) {
      if(DEVELOPMENT_MODE) {
        echo '<script type="text/javascript">common.showAjaxErrorMsg("Warning couldn\'t minify javascript code. ' .
             'Have a look at line ' . (__LINE__ - 3) . ' in file ' . __FILE__ . '");</script>';
      }
      @unlink($minified);
    }
  }
  
  public function setWebsiteTitle($title) {
    $this->websiteTitle .= ' - ' . $title;
  }
  
  public function getWebsiteTitle() {
    return $this->websiteTitle;
  }
  
  public function loadModule($module) {
    $modulePath = CORE_BASE_PATH . MODULE_PATH . $module . '.php';
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
      
    $viewerPath = CORE_BASE_PATH . VIEWER_PATH . $module . '/' . $viewer . '.php';
    if(file_exists($viewerPath)) {
      $this->htmlFile = $viewer . '.php';
    } else {
      exit('unknown viewer: ' . $viewerPath);
    }
  }
}
?>