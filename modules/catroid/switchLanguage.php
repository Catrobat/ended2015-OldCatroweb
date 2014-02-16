<?php
/*
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

class switchLanguage extends CoreAuthenticationNone {
  public function __construct() {
    parent::__construct();
  }

  public function __default() {
  }

  public function switchIt() {
    $this->statusCode = STATUS_CODE_INTERNAL_SERVER_ERROR;

    try {
      if(isset($_POST['language'])) {
        $this->loadModule('common/userFunctions');
        $this->userFunctions->updateLanguage($_POST['language']);
        $this->languageHandler->setLanguageCookie($_POST['language']);

        $this->statusCode = STATUS_CODE_OK;
        $this->answer = $this->languageHandler->getString('language_success');
      }
    } catch(Exception $e) {
      $this->statusCode = $e->getCode();
      $this->answer = $e->getMessage();
    }
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
