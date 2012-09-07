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
class registration extends CoreAuthenticationNone {

  public function __construct() {
    parent::__construct();

    if($this->checkLogin()) {
      header("location: " . BASE_PATH . "catroid/profile/");
    }

    $this->addCss('registration.css');
    $this->addJs('registration.js');
    $this->initRegistration();
    $this->setWebsiteTitle($this->languageHandler->getString('title'));
  }

  public function __default() {
  }

  private function checkLogin() {
    if(($this->session->userLogin_userNickname != "")) {
      return true;
    }
    return false;
  }

  public function initRegistration() {
    $this->countryCodeListHTML = $this->generateCountryCodeList();
    $this->monthListHTML = $this->generateMonthList();
    $this->yearListHTML = $this->generateYearList();
  }

  private function generateCountryCodeList() {
    $countryCodeList = getCountryArray($this->languageHandler);
    asort($countryCodeList);
    $optionList = "<option selected='selected'>" . $this->languageHandler->getString('select_country') . "</option>";

    foreach($countryCodeList as $key => $value) {
      $optionList .= "<option value='" . $key . "'>" . $value . "</option>";
    }

    $optionList .= "<option value='em'>" . $this->languageHandler->getString('other') . "</option>";
    return $optionList;
  }
  
  private function generateMonthList() {
    $months = getMonthsArray($this->languageHandler);
    $optionList = "<option selected='selected'>" . $this->languageHandler->getString('select_month') . "</option>";

    for($i = 1; $i < 13; $i++) {
      $optionList .= "<option value='" . $i . "'>" . $months[$i] . "</option>";
    }
    return $optionList;
  }

  private function generateYearList() {
    $optionList = "<option selected='selected'>" . $this->languageHandler->getString('select_year') . "</option>";

    $year = date('Y') + 1;
    for($i=1; $i<101; $i++) {
      $year--;
      $optionList .= "<option value='" . $year . "'>" . $year . "</option>";
    }
    return $optionList;
  }
  
  public function __destruct() {
    parent::__destruct();
  }
}
?>
