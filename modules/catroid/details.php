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

class details extends CoreAuthenticationNone {
  protected $oldVersions;

  public function __construct() {
    parent::__construct();
    $this->addCss('details.css');
    $this->addJs('details.js');

    $this->isMobile = $this->clientDetection->isMobile();
    $this->oldVersions = array("", "0.4.3d", "0.5.4a", "0.6.0beta", "&lt; 0.7.0beta");
  }

  public function __default() {
    $projectId = $_REQUEST['method'];
    $this->project = $this->getProjectDetails($projectId);
    
    $this->setWebsiteTitle($this->project['title']);
    
    $this->remixedProject = $this->getRemixedProject();
    $this->numberOfRemixes = $this->getNumberOfRemixes();
  }

  public function getProjectDetails($projectId) {
    if(!$projectId || !is_numeric($projectId)) {
      $this->errorHandler->showErrorPage('db', 'no_entry_for_id', 'ID: '.$projectId);
      exit();
    }
    $query = null;
    if($this->session->adminUser) {
      $query = pg_execute($this->dbConnection, "get_project_by_id", array($projectId)) or
                $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    } else {
      $query = pg_execute($this->dbConnection, "get_visible_project_by_id", array($projectId)) or
                $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    }
    $project = pg_fetch_assoc($query);
    pg_free_result($query);

    if(!$project) {
      $this->errorHandler->showErrorPage('db', 'no_entry_for_id', 'ID: '.$projectId);
      exit();
    }
    $project['image'] = getProjectImageUrl($project['id']);
    
    $project['publish_type'] = $this->languageHandler->getString('uploaded');
    $project['publish_time_in_words'] = getTimeInWords(strtotime($project['upload_time']), $this->languageHandler, time());
    if($project['update_time']) {
      $project['publish_type'] = $this->languageHandler->getString('updated');
      $project['publish_time_in_words'] = getTimeInWords(strtotime($project['update_time']), $this->languageHandler, time());
    }
    
    $project['uploaded_by_string'] = $this->languageHandler->getString('uploaded_by', $project['uploaded_by']);
    $project['publish_time_precice'] = date('Y-m-d H:i:s', strtotime($project['upload_time']));
    $project['fileSize'] = convertBytesToMegabytes($project['filesize_bytes']);
    $project['description'] = wrapUrlsWithAnchors($project['description']);
    if(mb_strlen($project['description'], 'UTF-8') > PROJECT_SHORT_DESCRIPTION_MAX_LENGTH) {
      $project['description_short'] = makeShortString($project['description'], PROJECT_SHORT_DESCRIPTION_MAX_LENGTH, '...');
    } else {
      $project['description_short'] = '';
    }
    $project['qr_code_catroid_image'] = "no_qr_code";

    $project['is_app_present'] = file_exists(CORE_BASE_PATH.PROJECTS_DIRECTORY.$projectId.APP_EXTENSION);
    if($project['is_app_present']) {
      $project['qr_code_app_image'] = "no_qr_code";
      $project['appFileSize'] = convertBytesToMegabytes(filesize(CORE_BASE_PATH.PROJECTS_DIRECTORY.$projectId.APP_EXTENSION));
    }
    
    $project['show_warning'] = false;
    if(in_array($project['version_name'], $this->oldVersions)) {
      $project['show_warning'] = true;
    }
    
    $project['showReportAsInappropriateButton'] = $this->showReportAsInappropriateButton($projectId, $project['user_id']);
    $this->incrementViewCounter($projectId);
    return $project;
  }

  public function incrementViewCounter($projectId) {
    pg_execute($this->dbConnection, "increment_view_counter", array($projectId)) or
               $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
  }
  
  public function showReportAsInappropriateButton($projectId, $userId) {
    if($this->session->userLogin_userId <= 0) {
      return array('show' => false, 'message' => $this->languageHandler->getString('report_as_inappropriate_please_login', '<a href="' . BASE_PATH . 'login/?requestUri=details/' . $projectId . '">' . $this->languageHandler->getString('login') . '</a>'));
    }
    if($this->session->userLogin_userId == $userId) {
      return array('show' => false, 'message' => $this->languageHandler->getString('report_as_inappropriate_own_project'));
    }
    
    $result = pg_execute($this->dbConnection, "has_user_flagged_project", array($projectId, $this->session->userLogin_userId)) or
              $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $alreadyFlagged = pg_num_rows($result);
    pg_free_result($result);
    
    if($alreadyFlagged > 0) {
      return array('show' => false, 'message' => $this->languageHandler->getString('report_as_inappropriate_already_flagged'));
    }
    return array('show' => true, 'message' => "");
  }
  
  public function getRemixedProject() {
    $result = pg_execute($this->dbConnection, "get_remixof_id", array($this->project['remixof'])) or
              $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    
    return pg_fetch_assoc($result);
  }
  
  public function getNumberOfRemixes() {
    $result = pg_execute($this->dbConnection, "get_num_remixes_of_program", array($this->project['id'])) or
              $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    
    return pg_num_rows($result);
  }
  
  public function __destruct() {
    parent::__destruct();
  }
}
?>
