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

class errorPage extends CoreAuthenticationNone {

  public function __construct() {
    parent::__construct();
    $this->addCss('errorPage.css');
  }

  public function __default() {
  	$type = $this->session->errorType;
  	$code = $this->session->errorCode;
  	$extraInfo = $this->session->errorExtraInfo;
  	$args = $this->session->errorArgs;
  	$arr = array($type, $code, $extraInfo);
  	if(is_array($args)) {
  	  $params = array_merge($arr, $args);
  	} else {
  	  $params = $arr;
  	}
  	$errorMessage = call_user_func_array(array($this->errorHandler, 'getError'), $params);
    $this->errorMessage = $errorMessage;
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
