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

class passwordrecovery extends CoreAuthenticationNone {

  public function __construct() {
    parent::__construct();
    $this->addCss('passwordrecovery.css');
    $this->addJs('passwordRecovery.js');
    $this->setWebsiteTitle($this->languageHandler->getString('title'));
    
    $this->loadModule('common/userFunctions');
  }

  public function __default() {
    if($this->session->userLogin_userId > 0) {
      header('Location: ' . BASE_PATH . 'profile');
      exit();
    }

    if(isset($_GET['c'])) {
      try {
        $this->userFunctions->isRecoveryHashValid($_GET['c']);
        $this->loadView('passwordRecoveryChangeForm');
      } catch(Exception $e) {
        $this->statusCode = $e->getCode();
        $this->answer = $e->getMessage();
        $this->loadView('passwordRecoveryRequest');
      }
    } else {
      $this->loadView('passwordRecoveryRequest');
    }
  }
  
  public function sendMailRequest() {
    $userData = (isset($_POST['passwordRecoveryUserdata']) ? trim(strval($_POST['passwordRecoveryUserdata'])) : '');
    
    try {
      $this->userFunctions->recover($userData);
      $this->statusCode = STATUS_CODE_OK;
      $this->answer = $this->languageHandler->getString('email_sent');
    } catch(Exception $e) {
      $this->statusCode = $e->getCode();
      $this->answer = $e->getMessage();
    }
  }
  
  public function changeMyPasswordRequest() {
    if(isset($_POST['c']) && isset($_POST['passwordSavePassword'])) {
      try {
        $this->userFunctions->isRecoveryHashValid($_POST['c']);
        $data = $this->userFunctions->getUserDataByRecoveryHash($_POST['c']);
        $this->userFunctions->checkPassword($data['username'], $_POST['passwordSavePassword']);

        $this->userFunctions->updatePassword($data['username'], $_POST['passwordSavePassword']);
        $this->userFunctions->login($data['username'], $_POST['passwordSavePassword']);

        $this->statusCode = STATUS_CODE_OK;
        $this->answer = $this->languageHandler->getString('saving_password');
      } catch(Exception $e) {
        $this->statusCode = $e->getCode();
        $this->answer = $e->getMessage();
      }
    }
  }
    
  public function __destruct() {
    parent::__destruct();
  }
}
?>
