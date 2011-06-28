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
  
  set_include_path(get_include_path().PATH_SEPARATOR.'./include/lib/'.PATH_SEPARATOR.'./modules/catroid/');
  spl_autoload_register('__autoload');
  require_once('config.php');
  require_once('passwords.php');
  require_once('commonFunctions.php');
  function __autoload($class) {
    $classfile = CORE_BASE_PATH.'classes/'.$class.'.php';
    if(is_file($classfile))
  	  include_once $classfile;
  }

  $controller = new CoreController();
  $controller->parseURL($_GET);
  $controller->execute();
?>
