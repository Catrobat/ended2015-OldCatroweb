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
  protected $user;

  public function __construct() {
    parent::__construct();
    $this->addCss('profile.css');
    $this->addJs("profile.js");
    
    require_once(CORE_BASE_PATH . 'modules/common/userFunctions.php');
    $this->user = new UserFunctions();
  }

  public function __authenticationFailed() {
    header("Location: " . BASE_PATH . "catroid/login/?requesturi=catroid/profile/");
  }
  
  public function __default() {
    $userData = array();
    
    if(isset($_GET['method'])) {
      if(strcmp($_GET['method'], $this->session->userLogin_userNickname) == 0) {
        $this->htmlFile = "myProfile.php";
        $userData = $this->retrieveProfileData($this->session->userLogin_userNickname);
        $this->initProfile($userData);
      } else if ($this->checkUserValid($_GET['method'])) {       
        $this->htmlFile = "userProfile.php";
        $userData = $this->retrieveProfileData($_GET['method']);
      } else {
        $this->errorHandler->showErrorpage('profile','no such user');
      }
    } else {
      $this->htmlFile = "myProfile.php";
      $userData = $this->retrieveProfileData($this->session->userLogin_userNickname);
      $this->initProfile($userData);
    }
    
    $this->setWebsiteTitle($this->languageHandler->getString('title', $this->requestedUser));
    $this->userData = $userData;
  }
  
  private function retrieveProfileData($username) {
    $username = trim(strval($username));
    $result = pg_execute($this->dbConnection, "get_user_row_by_username", array($username)) or
                $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    
    $user = array();
    if(pg_num_rows($result) > 0) {
      $user = pg_fetch_assoc($result);  
    }
    
    pg_free_result($result);
    return $user;
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  public function initProfile($userData) {
    $language = getLanguageOptions($this->languageHandler);
    $this->laguageListHTML = $language['html'];
    $this->countryCodeListHTML = $this->generateCountryCodeList($userData);
    $this->monthListHTML = $this->generateMonthList($userData);
    $this->yearListHTML = $this->generateYearList($userData);
    $this->genderListHTML = $this->generateGenderList($userData);
  }

  private function generateUserEmailList() {
    $userEmailList = '';
    
    foreach($this->user->getEmailAddresses($this->session->userLogin_userId) as $email) {
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
      $this->user->updatePassword($newPassword);
      
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
  
    $loginSuccess = $this->user->login($this->session->userLogin_userNickname, $oldPassword);
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
    $this->user->checkPassword($this->session->userLogin_userNickname, $newPassword);
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function getEmailListRequest() {
    $this->answer = $this->generateUserEmailList();
  }
  
  public function addEmailRequest() {
    $addEmail = (isset($_POST['profileEmail']) ? trim(strval($_POST['profileEmail'])) : '');
    
    try {
      $this->user->addEmailAddress($this->session->userLogin_userId, $addEmail);
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
      $this->user->deleteEmailAddress($deleteEmail);
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
      $this->user->updateCity($city);
  
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
      $this->user->updateCountry($country);
  
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
      $this->user->updateGender($gender);
  
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
      $this->user->updateBirthday($birthdayMonth, $birthdayYear);
  
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
