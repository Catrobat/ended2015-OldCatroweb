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

    //$this->loadModule('common/userFunctions');

    $this->isMobile = $this->clientDetection->isMobile();
    $this->oldVersions = array("", "0.4.3d", "0.5.4a", "0.6.0beta", "&lt; 0.7.0beta");
  }

  public function __default() {
    $projectId = $_REQUEST['method'];
    $this->project = $this->getProjectDetails($projectId);
    $this->tag = $this->getTags($projectId);

    if(strcmp($this->project['uploaded_by'],$this->session->userLogin_userNickname) == 0 )
    {
      $this->loadView('myDetails');
    }
    else
    {
      $this->loadView('details');
    }

    $this->setWebsiteTitle($this->project['title']);
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

  public function getTags($projectId) {
    $result = null;
    $result = pg_execute($this->dbConnection, "get_tags_name" ,array($projectId));
    $tags = array();
    while($value = pg_fetch_assoc($result))
    {
      array_push($tags, $value['tag_name']);
    }
    pg_free_result($result);


    return $tags;

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

  public function __destruct() {
    parent::__destruct();
  }

  public function updateTagsRequest() {
    try{
      $updatedTagString = (isset($_POST['editedTags']) ? trim(strval($_POST['editedTags'])) : '');
      $projectId = (isset($_POST['projectId']) ? trim(strval($_POST['projectId'])) : '');
      $projectId = $projectId + 0; //To convert to integer type

      $updatedTags = explode(',',$updatedTagString);
      $updatedTags = array_unique($updatedTags);
      $existingTags = $this->getTags($projectId);

      foreach($existingTags as $value) {
        if(!in_array($value,$updatedTags)) {
          // Remove particular tag
          $this->removeTag($value, $projectId);
        }
      }

      foreach($updatedTags as $value) {
        if($value != '') {
          if(!in_array($value,$existingTags)) {
            // Add particular tag
            $this->addTag($value, $projectId);
          }
        }
      }

      $this->statusCode = STATUS_CODE_OK;
      $this->answer = $this->languageHandler->getString('tag_edit_success');
    } catch(Exception $e) {
       $this->statusCode = $e->getCode();
       $this->answer = $e->getMessage();
     }
  }

  public function addTag($tag, $projectId) {

    $queryForTagId = pg_execute($this->dbConnection, "get_tag_id", array($tag));
    $tagRow = pg_fetch_assoc($queryForTagId);
    if(!$tagRow) {
      $queryForTagId = pg_execute($this->dbConnection, "insert_tag" ,array($tag));
      $tagRow = pg_fetch_assoc($queryForTagId);
    }

    $args = array($projectId, $tagRow['id']);
    pg_execute($this->dbConnection, "insert_tag_into_projects_tags", $args);

    pg_free_result($queryForTagId);

  }

  public function removeTag($tag, $projectId) {
    $resultForTagId = pg_execute($this->dbConnection, "get_tag_id", array($tag));
    $tagRow = pg_fetch_assoc($resultForTagId);

    $args = array($projectId, $tagRow['id']);
    pg_execute($this->dbConnection, "delete_entry_from_projects_tags", $args);

    pg_free_result($resultForTagId);
  }
}
?>
