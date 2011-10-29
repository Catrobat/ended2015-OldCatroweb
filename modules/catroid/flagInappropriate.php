<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team 
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    An additional term exception under section 7 of the GNU Affero
 *    General Public License, version 3, is available at
 *    http://www.catroid.org/catroid/licenseadditionalterm
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
    $statusCode = 500;
    if(isset($postData['projectId']) && $postData['flagReason'] != '') {
      $userIp = $serverData['REMOTE_ADDR'];
      $userId = 0;
      if(isset($this->session) &&  $this->session->userLogin_userId != "") {
        $userId = $this->session->userLogin_userId;
      }
      $projectId = $postData['projectId'];
      $flagReason = pg_escape_string($postData['flagReason']);
      $query = "EXECUTE insert_new_flagged_project('$projectId', '$userId', '$flagReason', '$userIp')";
      $result = pg_query($this->dbConnection, $query);
      if($result) {
        if(($numberOfFlags = $this->getProjectFlags($projectId)) >= PROJECT_FLAG_NOTIFICATION_THRESHOLD) {
          $this->hideProject($projectId);
          if($sendNotificationEmail) {
            $this->sendNotificationEmail($projectId, $flagReason);
            $query = "EXECUTE set_mail_sent_on_inappropriate('$projectId')";
            pg_query($this->dbConnection, $query);
          }
        }
        $answer = $this->languageHandler->getString('msg_flagged_successful');
        $statusCode = 200;
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
    $query = "EXECUTE get_flags_for_project('$projectId')";
    $result = @pg_query($this->dbConnection, $query);
    if($result) {
      return pg_num_rows($result);
    } else {
      return false;
    }
  }
  
  public function hideProject($projectId) {
    $query = "EXECUTE hide_project('$projectId')";
    $result = @pg_query($this->dbConnection, $query);
    if($result) {
      return true;
    } else {
      return false;
    }
  }
  
  public function sendNotificationEmail($projectId, $flagReason) {
    $query = "EXECUTE get_project_by_id('$projectId');";
    $result = @pg_query($this->dbConnection, $query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $project = pg_fetch_assoc($result);
    pg_free_result($result);
    
    $mailSubject = 'Project reported as inappropriate!';
    $mailText = "Hello catroid.org Administrator!\n\n";
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
