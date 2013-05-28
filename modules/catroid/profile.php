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

class profile extends CoreAuthenticationUser {

  public function __construct() {
    parent::__construct();
    $this->addCss('profile.css');
    $this->addJs('projectLoader.js');
    $this->addJs('projectContentFiller.js');
    $this->addJs('projectObject.js');
    $this->addJs("profile.js");
    
    $this->loadModule('common/userFunctions');
  }

  public function __authenticationFailed() {
    header("Location: " . BASE_PATH . "login/?requestUri=" . ltrim($_SERVER['REQUEST_URI'], '/'));
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
      if(isset($_GET['delete'])) {
        $this->deleteProject();
      }
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

    $this->loadModule('api/projects');
    $pageNr = 1;
    $projectsPerRow = 9;
    
    $requestedPage = $this->projects->get(($pageNr - 1) * $projectsPerRow,
        $projectsPerRow, PROJECT_MASK_GRID_ROW_AGE, PROJECT_SORTBY_AGE, '', $this->userData['username']);
    $this->numberOfPages = max(1, intval(ceil(max(0, intval($requestedPage['CatrobatInformation']['TotalProjects'])) /
        $projectsPerRow)));
    
    $params = array();
    $params['layout'] = PROJECT_LAYOUT_GRID_ROW;
    $params['container'] = '#userProjectContainer';
    $params['loader'] = '#userProjectLoader';
    $params['noResults'] = '#profileNoResults';
    $params['buttons'] = array('prev' => null,
        'next' => '#moreResults'
    );
    $params['content'][0] = $requestedPage;
    $params['numProjects'] = intval($requestedPage['CatrobatInformation']['TotalProjects']);
    
    $params['page'] = array('number' => $pageNr,
        'numProjectsPerPage' => $projectsPerRow,
        'pageNrMax' => $this->numberOfPages
    );
    
    $params['mask'] = PROJECT_MASK_GRID_ROW_AGE;
    $params['sort'] = PROJECT_SORTBY_AGE;
    $params['filter'] = array('query' => '',
        'author' => $this->userData['username']
    );
    
    $params['config'] = array('LAYOUT_GRID_ROW' => PROJECT_LAYOUT_GRID_ROW,
        'sortby' => array('age' => PROJECT_SORTBY_AGE,
            'downloads' => PROJECT_SORTBY_DOWNLOADS,
            'views' => PROJECT_SORTBY_VIEWS,
            'random' => PROJECT_SORTBY_RANDOM
        )
    );
    
    $this->jsParams = "'" . addslashes(json_encode($params)) . "'";
  }

  private function deleteProject() {
    $projectId = intval($_GET['delete']);
    if($projectId > 0 && $this->session->userLogin_userId > 0) {
      pg_execute($this->dbConnection, "hide_user_project", array($projectId, $this->session->userLogin_userId)) or
        $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    }
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  public function initProfile($userData) {
    $language = getLanguageOptions($this->languageHandler, $userData['language']);
    $this->laguageListHTML = $language['html'];
    $this->countryCodeListHTML = $this->generateCountryCodeList($userData);
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
  
  //--------------------------------------------------------------------------------------------------------------------
  public function updateAvatarRequest() {
    try {
      $this->avatar = $this->userFunctions->updateAvatar();
    
      $this->statusCode = STATUS_CODE_OK;
      $this->answer = $this->languageHandler->getString('avatar_success');
    } catch(Exception $e) {
      $this->statusCode = $e->getCode();
      $this->answer = $e->getMessage();
    }
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  public function updatePasswordRequest() {
    $newPassword =  (isset($_POST['profileNewPassword']) ? trim(strval($_POST['profileNewPassword'])) : '');
    $repeatPassword = (isset($_POST['profileRepeatPassword']) ? trim(strval($_POST['profileRepeatPassword'])) : '');
    
    try {
       $this->checkNewPassword($newPassword);
       $this->checkNewPassword($repeatPassword);
       $this->checkPasswordsEquality($newPassword, $repeatPassword);
       $this->userFunctions->updatePassword($this->session->userLogin_userNickname, $newPassword);
       $this->statusCode = STATUS_CODE_OK;
       $this->answer = $this->languageHandler->getString('password_success');
     } catch(Exception $e) {
       $this->statusCode = $e->getCode();
       $this->answer = $e->getMessage();
    }
  }
  

  private function checkNewPassword($newPassword) {
    if($newPassword == '') {
      throw new Exception($this->errorHandler->getError('profile', 'password_missing'),
          STATUS_CODE_PROFILE_NEW_PASSWORD_MISSING);
    }
    $this->userFunctions->checkPassword($this->session->userLogin_userNickname, $newPassword);
  }
  
  private function checkPasswordsEquality($newPassword, $repeatPassword) {
    if($newPassword != $repeatPassword) {
      throw new Exception($this->errorHandler->getError('profile', 'password_equal'),
          STATUS_CODE_PROFILE_NEW_PASSWORD_MISSING);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function updateEmailRequest() {
    $email = (isset($_POST['email']) ? trim(strval($_POST['email'])) : '');
    try {
       $this->userFunctions->updateEmailAddress($this->session->userLogin_userId, $email);
       $this->statusCode = STATUS_CODE_OK;
       $this->answer = $this->languageHandler->getString('email_add_success');
     } catch(Exception $e) {
       $this->statusCode = $e->getCode();
       $this->answer = $e->getMessage();
     }
  }

  public function updateAdditionalEmailRequest() {
    $email = (isset($_POST['email']) ? trim(strval($_POST['email'])) : '');
    try {
       $this->userFunctions->updateAdditionalEmailAddress($this->session->userLogin_userId, $email);
       $this->statusCode = STATUS_CODE_OK;
       $this->answer = $this->languageHandler->getString('email_add_success');
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

}
?>
