<?php
/*
 * Catroid: An on-device visual programming system for Android devices
 * Copyright (C) 2010-2013 The Catrobat Team
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

class login extends CoreAuthenticationNone {

  public function __construct() {
    parent::__construct();
    $this->setWebsiteTitle($this->languageHandler->getString('title'));
    $this->loadModule('common/userFunctions');
    $this->addJs('login.js');
    $this->addCss('login.css');
  }

  public function __default() {
    if($this->session->userLogin_userId > 0) {
      if(isset($_REQUEST['requestUri'])) {
        header('Location: ' . BASE_PATH . $_REQUEST['requestUri']);
        exit();
      } else {
        header('Location: ' . BASE_PATH . 'index');
        exit();
      }
    } else {
      $this->loadView('login');
    }
  }

  public function loginRequest() {
    try {
      if(!isset($_POST)) {
        throw new Exception($this->errorHandler->getError('registration', 'postdata_missing'),
            STATUS_CODE_LOGIN_MISSING_DATA);
      }
  
      $username = (isset($_POST['loginUsername'])) ? checkUserInput(trim($_POST['loginUsername'])) : '';
      if($username == '') {
        throw new Exception($this->errorHandler->getError('userFunctions', 'username_missing'),
            STATUS_CODE_LOGIN_MISSING_USERNAME);
      }
  
      if(!isset($_POST['loginPassword']) || $_POST['loginPassword'] == '') {
        throw new Exception($this->errorHandler->getError('userFunctions', 'password_missing'),
            STATUS_CODE_LOGIN_MISSING_PASSWORD);
      }
  
      $this->userFunctions->login($username, $_POST['loginPassword']);
  
      if($this->requestFromBlockedIp()) {
        throw new Exception($this->errorHandler->getError('viewer', 'ip_is_blocked'),
            STATUS_CODE_AUTHENTICATION_FAILED);
      }
      if($this->requestFromBlockedUser()) {
        throw new Exception($this->errorHandler->getError('viewer', 'user_is_blocked'),
            STATUS_CODE_AUTHENTICATION_FAILED);
      }
  
      $this->statusCode = STATUS_CODE_OK;
    } catch(Exception $e) {
      $this->userFunctions->logout();
      $this->statusCode = $e->getCode();
      $this->answer = $e->getMessage();
    }
  }
  
  public function logoutRequest() {
    $this->userFunctions->logout();
    $this->statusCode = STATUS_CODE_OK;
    $this->answer = $this->languageHandler->getString('catroid_logout_success');
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
