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
      // if logged in -> redirect to profile page
      header("location: ".BASE_PATH."catroid/profile/");
      ob_end_flush();
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
    $whiteSpace="                  ";
    $optionList = $whiteSpace . "<option value=\"\" selected=\"selected\">" . $this->languageHandler->getString('select_country') . "</option>\r\n";

    foreach($countryCodeList as $key => $value) {
      $optionList .= $whiteSpace . "<option value=\"" . $key . "\">" . $value . "</option>\r\n";
    }

    $optionList .= $whiteSpace . "<option value=\"em\">" . $this->languageHandler->getString('other') . "</option>\r\n";
    return $optionList;
  }
  
  private function generateMonthList() {
    $months = getMonthsArray($this->languageHandler);
    $whiteSpace="                    ";
    $optionList = $whiteSpace . "<option value=\"\" selected=\"selected\">" . $this->languageHandler->getString('select_month') . "</option>\r\n";

    for($i=1; $i<13; $i++) {
      $optionList .= $whiteSpace . "<option value=\"" . $i . "\">" . $months[$i] . "</option>\r\n";
    }
    return $optionList;
  }

  private function generateYearList() {
    $whiteSpace="                    ";
    $optionList = $whiteSpace . "<option value=\"\" selected=\"selected\">" . $this->languageHandler->getString('select_year') . "</option>\r\n";

    $year = date('Y') + 1;
    for($i=1; $i<101; $i++) {
      $year--;
      $optionList .= $whiteSpace . "<option value=\"" . $year . "\">" . $year . "</option>\r\n";
    }
    return $optionList;
  }
  
  public function __destruct() {
    parent::__destruct();
  }
}
?>
