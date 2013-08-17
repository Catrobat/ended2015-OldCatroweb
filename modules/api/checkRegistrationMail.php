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

class checkRegistrationMail extends CoreAuthenticationNone {
  public function __construct() {
    parent::__construct();
    $this->loadModule('common/userFunctions');
  }

  public function __default() {
  }

  public function checkRegistrationMail() {
    if($_POST) {
      try {
        if(!filter_var($_POST['registrationEMail'], FILTER_VALIDATE_EMAIL)) {
          throw new Exception($this->errorHandler->getError('userFunctions', 'email_invalid'),
              STATUS_CODE_REGISTRATION_EMAIL_INVALID);
        }
        if($this->userFunctions->checkEmailExists($_POST['registrationEMail'])) {
          throw new Exception($this->errorHandler->getError('userFunctions', 'email_already_exists'),
              STATUS_CODE_REGISTRATION_EMAIL_ALREADY_EXISTS);
        }
        $this->statusCode = STATUS_CODE_OK;
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
