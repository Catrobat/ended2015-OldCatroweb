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

class flagInappropriate extends CoreAuthenticationNone {
  public function __construct() {
    parent::__construct();
  }

  public function __default() {
    
  }
  
  public function flag() {
    $this->flagProject($_POST, $_SERVER);
  }
  
  public function flagProject($postData, $serverData, $sendNotificationEmail = true) { 
    $answer = '';
    $statusCode = STATUS_CODE_INTERNAL_SERVER_ERROR;
    if(isset($postData['projectId']) && $postData['flagReason'] != '') {
      $userIp = $serverData['REMOTE_ADDR'];
      $userId = 0;
      if(isset($this->session) &&  $this->session->userLogin_userId != "") {
        $userId = $this->session->userLogin_userId;
      }
      $projectId = $postData['projectId'];
      $flagReason = pg_escape_string($postData['flagReason']);
      $result = pg_execute($this->dbConnection, "insert_new_flagged_project", array($projectId, $userId, $flagReason, $userIp)) or
                $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
      if($result) {
        if(($numberOfFlags = $this->getProjectFlags($projectId)) >= PROJECT_FLAG_NOTIFICATION_THRESHOLD) {
          $this->hideProject($projectId);
          if($sendNotificationEmail) {
            $this->sendNotificationEmail($projectId, $flagReason);
            pg_execute($this->dbConnection, "set_mail_sent_on_inappropriate", array($projectId)) or
                       $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
          }
        }
        $answer = $this->languageHandler->getString('msg_flagged_successful');
        $statusCode = STATUS_CODE_OK;
      } else {
        $answer = $this->errorHandler->getError('flag', 'sql_insert_failed').pg_last_error();
      }
    } else {
      $answer = $this->errorHandler->getError('flag', 'missing_post_data');
    }
    
    $this->answer = $answer;
    $this->statusCode = $statusCode;
    return;
  }
  
  public function getProjectFlags($projectId) {
    $result = pg_execute($this->dbConnection, "get_flags_for_project", array($projectId)) or
              $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    if($result) {
      return pg_num_rows($result);
    } else {
      return false;
    }
  }
  
  public function hideProject($projectId) {
    $result = pg_execute($this->dbConnection, "hide_project", array($projectId)) or
              $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    if($result) {
      return true;
    } else {
      return false;
    }
  }
  
  public function sendNotificationEmail($projectId, $flagReason) {
    $result = pg_execute($this->dbConnection, "get_project_by_id", array($projectId)) or
              $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $project = pg_fetch_assoc($result);
    pg_free_result($result);
    
    $mailSubject = 'Project reported as inappropriate!';
    $mailText = "Hello ".APPLICATION_URL_TEXT." Administrator!\n\n";
    $mailText .= "The following project was reported as inappropriate by ".$this->getProjectFlags($projectId)." user(s):\n\n";
    $mailText .= "---PROJECT DETAILS---\nID: ".$project['id']."\nTITLE: ".$project['title']."\n\n";
    $mailText .= "Reason: ".$flagReason."\n";
    $mailText .= "User IP: <".$_SERVER['REMOTE_ADDR'].">\n";
    $mailText .= "--- *** ---\n\n";
    $mailText .= "You should check this!";

    return($this->mailHandler->sendAdministrationMail($mailSubject, $mailText));
  }
  
  public function __destruct() {
    parent::__destruct();
  }
}
?>
