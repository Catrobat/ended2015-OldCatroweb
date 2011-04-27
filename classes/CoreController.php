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

class CoreController {
  public $module;
  public $class;
  public $method;
  public $view;

  public function __construct() {

  }

  public function parseURL($getData) {
    $this->setDefault();

    if(isset($getData['method']) && $getData['method'] != '')
    $this->method = $getData['method'];

    if(isset($getData['view']) && $getData['view'] != '' && file_exists(CORE_BASE_PATH.'classes/'.'CorePresenter_'.$getData['view'].'.php'))
    $this->view = $getData['view'];

    if(isset($getData['module']) && $getData['module'] != '' && is_dir(CORE_BASE_PATH.'modules/'.$getData['module']))
    $this->module = $getData['module'];

    if(isset($getData['class']) && $getData['class'] != '' && file_exists(CORE_BASE_PATH.'modules/'.$this->module.'/'.$getData['class'].'.php'))
    $this->class = $getData['class'];
  }

  public function execute() {
    $classFile = CORE_BASE_PATH.'modules/'.$this->module.'/'.$this->class.'.php';
    if(file_exists($classFile)) {
      require_once($classFile);
      if(class_exists($this->class)) {
        try {
          $instance = new $this->class();
          if (!CoreModule::isValid($instance)) {
            die("Requested module is not a valid CORE module!");
          }
          $instance->moduleName = $this->module;
          if($instance->authenticate()) {
            try {
              $instance->presenter = 'CorePresenter_'.$this->view;
              $method = $this->method;
              $result = $instance->$method();
              if(file_exists(CORE_BASE_PATH.'classes/'.$instance->presenter.'.php')) {
                $view = CorePresenter::factory($instance->presenter, $instance);
              } else {
                die("Could not find viewerClass: ".CORE_BASE_PATH.'classes/'.$instance->presenter.'.php!');
              }
              if(!$view->display()) {
                die('There is no suitable viewer-file for this model!');
              }
            } catch (Exception $error) {
              die($error->getMessage());
            }
          } else {
            die("Authentication required!");
          }
        } catch (Exception $error) {
          die($error->getMessage());
        }
      } else {
        die("An valid module for your request was not found!");
      }
    } else {
      die("Could not find: $classFile!");
    }
    return true;
  }

  private function setDefault() {
    $this->module = MVC_DEFAULT_MODULE;
    $this->class = MVC_DEFAULT_CLASS;
    $this->method = MVC_DEFAULT_METHOD;
    $this->view = MVC_DEFAULT_VIEW;
  }
}

?>