<?php
/**
 *    Catroid: An on-device graphical programming language for Android devices
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

class profile extends CoreAuthenticationUser {

  public function __construct() {
    parent::__construct();
    $this->addCss('profile.css');
    $this->addJs("profile.js");
    
    $this->loadModule('common/userFunctions');
  }

  public function __authenticationFailed() {
    header("Location: " . BASE_PATH . "catroid/login/?requestUri=catroid/profile/");
    exit();
  }
  
  public function __default() {
    $showUser = "";
    $ownProfile = false;
    
    if(isset($_GET['method']) && trim($_GET['method']) != '') {
      if(strcmp($_GET['method'], $this->session->userLogin_userNickname) == 0) {
        $showUser = $this->session->userLogin_userNickname;
        $ownProfile = true;
      } else if($this->userFunctions->checkUserExists($_GET['method'])) {
        $showUser = checkUserInput($_GET['method']);
        $ownProfile = false;
      } else {
        $this->errorHandler->showErrorPage('profile','no_such_user');
      }
    } else {
      $showUser = $this->session->userLogin_userNickname;
      $ownProfile = true;
    }
    
    $this->userData = $this->userFunctions->getUserData($showUser);
    if($ownProfile) {
      $this->setWebsiteTitle($this->languageHandler->getString('myTitle'));
      $this->initProfile($this->userData);
      $this->loadView('myProfile');
    } else {
      $this->setWebsiteTitle($this->languageHandler->getString('userTitle'));
      $this->loadView('userProfile');
    }
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  public function initProfile($userData) {
    $language = getLanguageOptions($this->languageHandler, $userData['language']);
    $this->laguageListHTML = $language['html'];
    $this->countryCodeListHTML = $this->generateCountryCodeList($userData);
    $this->monthListHTML = $this->generateMonthList($userData);
    $this->yearListHTML = $this->generateYearList($userData);
    $this->genderListHTML = $this->generateGenderList($userData);
  }

  private function generateUserEmailList() {
    $userEmailList = '';
    
    foreach($this->userFunctions->getEmailAddresses($this->session->userLogin_userId) as $email) {
      $userEmailList .= '<div style="width:408px; padding: 10px 0 10px 0;">' . $email . '</div><button name="' . $email . '" style="margin: 5px;" class="button orange compact"><img name="' . $email . '" width="24px" src="' . BASE_PATH . 'images/symbols/trash_recyclebin.png"></button><br />';
    }
    
    return $userEmailList;
  }

  private function generateCountryCodeList($userData) {
    $countryCodeList = getCountryArray($this->languageHandler);
    asort($countryCodeList);
    $countryCodeList['em'] = $this->languageHandler->getString('other');
    
    $optionList = "<option></option>";
    
    foreach($countryCodeList as $key => $value) {
      $selected = "";
      if(strcasecmp($key, $userData['country']) == 0) {
        $selected = "selected='selected'";
      }
      $optionList .= "<option value='" . $key . "'" . $selected . ">" . $value . "</option>";
    }
  
    return $optionList;
  }
  
  private function generateMonthList($userData) {
    $months = getMonthsArray($this->languageHandler);
    $optionList = '<option value="0"></option>';
  
    for($i = 1; $i < 13; $i++) {
      $selected = "";
      if(intval($userData['month']) == $i) {
        $selected = "selected='selected'";
      }
      $optionList .= "<option value='" . $i . "'" . $selected . ">" . $months[$i] . "</option>";
    }
    return $optionList;
  }
  
  private function generateYearList($userData) {
    $optionList = '<option value="0"></option>';
  
    $year = date('Y') + 1;
    for($i=1; $i<101; $i++) {
      $year--;
      $selected = "";
      if(intval($userData['year']) == $year) {
        $selected = "selected='selected'";
      }
      $optionList .= "<option value='" . $year . "'" . $selected . ">" . $year . "</option>";
    }
    return $optionList;
  }

  private function generateGenderList($userData) {
    $optionList = "";
    $options = array('', 'female', 'male');
    
    foreach($options as $option) {
      $selected = "";
      if(strcmp($userData['gender'], $option) == 0) {
        $selected = " selected='selected'";
      }
      
      $optionList .= "<option value='" . $option . "'" . $selected . ">" . $this->languageHandler->getString($option) . "</option>";
    }
    return $optionList;
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  public function updatePasswordRequest() {
    $oldPassword = (isset($_POST['profileOldPassword']) ? trim(strval($_POST['profileOldPassword'])) : '');
    $newPassword = (isset($_POST['profileNewPassword']) ? trim(strval($_POST['profileNewPassword'])) : '');
    
    try {
      $this->checkOldPassword($oldPassword);
      $this->checkNewPassword($newPassword);
      $this->userFunctions->updatePassword($this->session->userLogin_userNickname, $newPassword);
      
      $this->statusCode = STATUS_CODE_OK;
      $this->answer = $this->languageHandler->getString('password_success');
    } catch(Exception $e) {
      $this->statusCode = $e->getCode();
      $this->answer = $e->getMessage();
    }
  }
  
  private function checkOldPassword($oldPassword) {
    if($oldPassword == '') {
      throw new Exception($this->errorHandler->getError('profile', 'password_old_missing'),
          STATUS_CODE_PROFILE_OLD_PASSWORD_MISSING);
    }
  
    $loginSuccess = $this->userFunctions->checkLoginData($this->session->userLogin_userNickname, md5($oldPassword));
    if(!$loginSuccess) {
      throw new Exception($this->errorHandler->getError('profile', 'password_old_wrong'),
          STATUS_CODE_PROFILE_OLD_PASSWORD_WRONG);
    }
  }
  
  private function checkNewPassword($newPassword) {
    if($newPassword == '') {
      throw new Exception($this->errorHandler->getError('profile', 'password_new_missing'),
          STATUS_CODE_PROFILE_NEW_PASSWORD_MISSING);
    }
    $this->userFunctions->checkPassword($this->session->userLogin_userNickname, $newPassword);
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function getEmailListRequest() {
    $this->answer = $this->generateUserEmailList();
  }
  
  public function addEmailRequest() {
    $addEmail = (isset($_POST['profileEmail']) ? trim(strval($_POST['profileEmail'])) : '');
    
    try {
      $this->userFunctions->addEmailAddress($this->session->userLogin_userId, $addEmail);
      $this->statusCode = STATUS_CODE_OK;
      $this->answer = $this->languageHandler->getString('email_add_success');
    } catch(Exception $e) {
      $this->statusCode = $e->getCode();
      $this->answer = $e->getMessage();
    }
  }
  
  public function deleteEmailRequest() {
    $deleteEmail = (isset($_POST['profileEmail']) ? trim(strval($_POST['profileEmail'])) : '');
    
    try {
      $this->userFunctions->deleteEmailAddress($deleteEmail);
      $this->statusCode = STATUS_CODE_OK;
      $this->answer = $this->languageHandler->getString('email_delete_success');
    } catch(Exception $e) {
      $this->statusCode = $e->getCode();
      $this->answer = $e->getMessage();
    }
  }

  //--------------------------------------------------------------------------------------------------------------------  
  public function updateCityRequest() {
    $city = (isset($_POST['city']) ? trim(strval($_POST['city'])) : '');
  
    try {
      $this->userFunctions->updateCity($city);
  
      $this->statusCode = STATUS_CODE_OK;
      $this->answer = $this->languageHandler->getString('city_success');
    } catch(Exception $e) {
      $this->statusCode = $e->getCode();
      $this->answer = $e->getMessage();
    }
  }

  //--------------------------------------------------------------------------------------------------------------------  
  public function updateCountryRequest() {
    $country = (isset($_POST['country']) ? trim(strval($_POST['country'])) : '');
  
    try {
      $this->userFunctions->updateCountry($country);
  
      $this->statusCode = STATUS_CODE_OK;
      $this->answer = $this->languageHandler->getString('country_success');
    } catch(Exception $e) {
      $this->statusCode = $e->getCode();
      $this->answer = $e->getMessage();
    }
  }

  //--------------------------------------------------------------------------------------------------------------------  
  public function updateGenderRequest() {
    $gender = (isset($_POST['gender']) ? trim(strval($_POST['gender'])) : '');
  
    try {
      $this->userFunctions->updateGender($gender);
  
      $this->statusCode = STATUS_CODE_OK;
      $this->answer = $this->languageHandler->getString('gender_success');
    } catch(Exception $e) {
      $this->statusCode = $e->getCode();
      $this->answer = $e->getMessage();
    }
  }

  //--------------------------------------------------------------------------------------------------------------------  
  public function updateBirthdayRequest() {
    $birthdayMonth = (isset($_POST['birthdayMonth']) ? intval($_POST['birthdayMonth']) : '');
    $birthdayYear = (isset($_POST['birthdayYear']) ? intval($_POST['birthdayYear']) : '');

    try {
      $this->userFunctions->updateBirthday($birthdayMonth, $birthdayYear);
  
      $this->statusCode = STATUS_CODE_OK;
      $this->answer = $this->languageHandler->getString('birthday_success');
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
