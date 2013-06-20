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

class registration extends CoreAuthenticationNone {
  public function __construct() {
    parent::__construct();
    
    $this->loadModule('common/userFunctions');

    if($this->userFunctions->isLoggedIn()) {
      header("location: " . BASE_PATH . "profile/");
      exit();
    }
    
    $this->addCss('registration.css');
    $this->addJs('registration.js');
    $this->setWebsiteTitle($this->languageHandler->getString('title'));
  }

  public function __default() {
  }

  public function generateCountryCodeList() {
    $countryCodeList = getCountryArray($this->languageHandler);
    asort($countryCodeList);
    $optionList = "<option selected='selected'>" . $this->languageHandler->getString('select_country') . "</option>";

    foreach($countryCodeList as $key => $value) {
      $optionList .= "<option value='" . $key . "'>" . $value . "</option>";
    }

    $optionList .= "<option value='em'>" . $this->languageHandler->getString('other') . "</option>";
    return $optionList;
  }
  
  public function generateMonthList() {
    $months = getMonthsArray($this->languageHandler);
    $optionList = "<option selected='selected'>" . $this->languageHandler->getString('select_month') . "</option>";

    for($i = 1; $i < 13; $i++) {
      $optionList .= "<option value='" . $i . "'>" . $months[$i] . "</option>";
    }
    return $optionList;
  }

  public function generateYearList() {
    $optionList = "<option selected='selected'>" . $this->languageHandler->getString('select_year') . "</option>";

    $year = date('Y') + 1;
    for($i=1; $i<101; $i++) {
      $year--;
      $optionList .= "<option value='" . $year . "'>" . $year . "</option>";
    }
    return $optionList;
  }
  
  public function registrationRequest() {
    try {
      if(!isset($_POST)) {
        throw new Exception($this->errorHandler->getError('registration', 'postdata_missing'),
            STATUS_CODE_LOGIN_MISSING_DATA);
      }
      
      $this->userFunctions->register($_POST);
      $this->userFunctions->login($_POST['registrationUsername'], $_POST['registrationPassword']);
      
      $this->statusCode = STATUS_CODE_OK;
      $this->answer = $this->languageHandler->getString('registration_success');
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
