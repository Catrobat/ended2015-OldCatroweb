<?php
/*
 * Catroid: An on-device visual programming system for Android devices
* Copyright (C) 2010-2014 The Catrobat Team
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

require_once CORE_BASE_PATH . 'modules/common/userFunctions.php';

class tools extends CoreAuthenticationAdmin {
  public function __construct() {
    parent::__construct();
    $this->addCss('adminLayout.css?'.VERSION);
  }

  public function __default() {

  }

  public function removeInconsistantProjectFiles() {
    $directory = CORE_BASE_PATH . PROJECTS_DIRECTORY;
    $files = scandir($directory);
    $answer = '';
    foreach($files as $fileName) {
      if($fileName != '.' && $fileName != '..') {
        $projectId = substr($fileName, 0, strpos($fileName, '.'));
        if(is_numeric($projectId)) {
          if($this->isProjectInDatabase(intval($projectId))) {
            $this->updateProjectFilesizeInDatabase($projectId, filesize($directory.$fileName));
            $answer .= 'FOUND: ';
          } else {
            $this->deleteFile($directory.$fileName);
            $answer .= 'NOT FOUND: ';
          }
          $answer .= $fileName.'<br />';
        }
      }
    }
    if(strlen($answer) == 0) {
      $answer = 'There are no projects.';
    }
    $this->answer = $answer;
  }

  private function sendEmailNotificationToUser($userHash, $userId, $userName, $userEmail) {
    $passwordResetUrl = BASE_PATH . 'passwordrecovery?c=' . $userHash;

    $result = pg_execute($this->dbConnection, "update_recovery_hash_recovery_time_by_id", array($userHash, time(), $userId));
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
          STATUS_CODE_SQL_QUERY_FAILED);
    }
    pg_free_result($result);
     
    if(DEVELOPMENT_MODE) {
      throw new Exception($passwordResetUrl, STATUS_CODE_OK);
    }

    if(SEND_NOTIFICATION_USER_EMAIL) {
      $mailSubject = APPLICATION_URL_TEXT." - Password reset required";
      $mailText  = 'Dear '.$userName.'.'. "\r\n\r\n";
      $mailText .= 'TODO: text';
      $mailText .= 'Best regards,'. "\r\n";
      $mailText .= 'Your '.APPLICATION_NAME.' Team'. "\r\n";

      if(!$this->mailHandler->sendUserMail($mailSubject, $mailText, $userEmail)) {
        throw new Exception($this->errorHandler->getError('userFunctions', 'sendmail_failed', '', CONTACT_EMAIL),
            STATUS_CODE_SEND_MAIL_FAILED);
      }
    }
  }

  public function sendEmailNotification() {
    if(isset($_REQUEST['sendEmailNotification'])) {
      $usernames = array();
      $common = new userFunctions();
      $successCount = 0;

      if(!isset($_REQUEST['username'])) {
        $answer = "ERROR: No e-mails selected.";
      }
      else {
        $usernames = $_REQUEST['username'];
        $answer = "Number of emails selected: ".count($usernames)."<br/>";
      }

      if(is_array($usernames)) {
        foreach($usernames as $username){
          try {
            $data = $common->getUserDataForRecovery($username);
            $hash = $common->createUserHash($data);
            try {
              $this->sendEmailNotificationToUser($hash, $data['id'], $data['username'], $data['email']);
              $answer .= 'Sending message to user "'.$username.'" (id= '.$data['id'].', '.$data['email'].'): OK.'.'<br/>';
              $successCount++;
            }
            catch(Exception $e) {
              if(($e->getCode() == 200) && (DEVELOPMENT_MODE)) {
                $answer .= 'Sending message to user "'.$username.'" (id= '.$data['id'].', '.$data['email'].'): OK.'.'<br/>';
                $successCount++;
              }
              else {
                $answer .= 'Sending message to user "'.$username.'" (id= '.$data['id'].', '.$data['email'].'): FAILED.'.'<br/>';
                $answer .= 'Error: '.$e->getMessage()."<br/><br/>";
              }
            }
          }
          catch(Exception $e) {
            $answer .= $e->getMessage()."<br/>";
          }
        }
      }
      $answer .= "<br/>Status: ".$successCount.' of '.count($usernames).' e-mails sent. ('.(count($usernames) - $successCount).' failed)';
      if(DEVELOPMENT_MODE) {
        $answer .= "<br/><b>DEVELOPMENT_MODE is ON! No e-mails sent!<b/><br/>";
      }
      $this->answer = $answer;
    }

    $this->htmlFile = "sendEmailNotification.php";
    $this->allusers = $this->getListOfUsersFromDatabase();
  }

  public function editProjects() {
    if(isset($_POST['delete'])) {
      if($this->deleteProject($_POST['projectId'])) {
        $answer = "The project was succesfully deleted!";
      } else {
        $answer = "Error: could NOT delete the project!";
      }
      $this->answer = $answer;
    }
    $this->htmlFile = "editProjectList.php";
    $this->projects = $this->retrieveAllProjectsFromDatabase();
    $this->tags = $this->retrieveAllTagsFromDatabase();
  }
  
  public function addStarterProject() {
    $retval = null;
    
    if(isset($_POST['add'])) {
      $id = $_POST['projectId'];
      $group = $_POST['group'];
      $visible = $_POST['visible'];
      $query = "EXECUTE get_starter_project_by_id('$id');";
      $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
      
      if($result && pg_affected_rows($result) == 0) {
        $query = "EXECUTE insert_new_starter_project('$id', '$visible', '$group');";
        $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
        
        if($result && pg_affected_rows($result) == 1) {
          $answer = "The starter project was added!";
          $retval = STATUS_CODE_OK;
        }
        else {
          $answer = "The starter project could NOT be added!";
          $retval = STATUS_CODE_STARTER_PROJECT_NOT_ADDED;
        }
      }
      else {
        $answer = "This project is already a starter project! - Group: ".$_POST['group'];
        $retval = STATUS_CODE_STARTER_PROJECT_ALREADY_ADDED;
      }
      $this->answer = $answer;
    }
  
    $this->htmlFile = "addStarterProject.php";
    $this->projects = $this->retrieveAllProjectsFromDatabase();
    $this->starterProjects = $this->retrieveAllStarterProjectsFromDatabase();
    $starterProjectIds = array();
    if($this->starterProjects) {
      foreach($this->starterProjects as $sp) {
        array_push($starterProjectIds, $sp['project_id']);;
      }
      $this->starterProjectIds = $starterProjectIds;
    }
    
    return $retval;
  }
  
  public function removeStarterProject() {

    $retval = null;
    
    $id = $_POST['projectId'];
    
    $query = "EXECUTE remove_starter_project_by_id('$id');";
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    
    if($result)
      $retval = STATUS_CODE_OK;
    else
      $retval = STATUS_CODE_STARTER_PROJECT_NOT_REMOVED;
    
    $this->htmlFile = 'addStarterProject.php';
    $this->projects = $this->retrieveAllProjectsFromDatabase();
    $this->starterProjects = $this->retrieveAllStarterProjectsFromDatabase();
    $starterProjectIds = array();
    if($this->starterProjects) {
      foreach($this->starterProjects as $sp) {
        array_push($starterProjectIds, $sp['project_id']);
      }
      $this->starterProjectIds = $starterProjectIds;
    }
   
    return $retval;
  }
  
  public function retrieveStarterProjectById($projectId) {
    
    $query = "EXECUTE get_starter_project_by_id('$projectId');";
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    
    $starterProject = pg_fetch_object($result);
    
    echo $this->getStarterProjectGroupById($starterProject->group);
  }
  
  public function getStarterProjectGroupById($id) {
    switch ($id) {
      case 1:
        return "Games";
      case 2:
        return "Animations";
      case 3:
        return "Interactiv Art and Stories";
      case 4:
        return "Music and Dance";        
    }
  }

  public function addFeaturedProject() {
    if(isset($_POST['add'])) {
      $id = $_POST['projectId'];
      $query = "EXECUTE get_featured_project_by_id('$id');";
      $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
      if($result && pg_affected_rows($result) == 0) {
        $query = "EXECUTE insert_new_featured_project('$id', 'f');";
        $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
        print_r($result);
        if($result && pg_affected_rows($result) == 1) {
          $answer = "The featured project was added!";
        }
        else {
          $answer = "The featured project could NOT be added!";
        }
      }
      else {
        $answer = "This project is already featured!";
      }
      $this->answer = $answer;
    }

    $this->htmlFile = "addFeaturedProject.php";
    $this->projects = $this->retrieveAllProjectsFromDatabase();
    $this->featuredProjects = $this->retrieveAllFeaturedProjectsFromDatabase();
    $featuredProjectIds = array();
    if($this->featuredProjects) {
      foreach($this->featuredProjects as $fp) {
        array_push($featuredProjectIds, $fp['project_id']);;
      }
      $this->featuredProjectIds = $featuredProjectIds;
    }
  }
  public function updateFeaturedProjectsImage() {
    if($_POST['projectId']) {
      switch($_FILES['file']['type']) {
        case "image/jpeg":
          $imageSource = imagecreatefromjpeg($_FILES['file']['tmp_name']);
          break;
        case "image/png":
          $imageSource = imagecreatefrompng($_FILES['file']['tmp_name']);
          break;
        default:
          $answer = "ERROR: Image upload failed! (unsupported file type)";
      }

      if($imageSource) {
        $width = imagesx($imageSource);
        $height = imagesy($imageSource);

        if($width == 0 || $height == 0) {
          $answer = "ERROR: Image upload failed! (image size 0?)";
        }
        if(($width != 1024) || ($height != 400)) {
          $answer = "ERROR: Image upload failed! File dimensions mismatch (must be 1024x400px)!";
        }
      }
      
      $image_400 = imagecreatetruecolor(400, 156);
      $image_720 = imagecreatetruecolor(720, 281);
      
      imagecopyresampled($image_400, $imageSource, 0, 0, 0, 0, 400, 156, 1024, 400);
      imagecopyresampled($image_720, $imageSource, 0, 0, 0, 0, 720, 281, 1024, 400);     

      if($answer == "") {
        $path = CORE_BASE_PATH.PROJECTS_FEATURED_DIRECTORY.$_POST['projectId'].PROJECTS_FEATURED_EXTENSION;
        $path1 = CORE_BASE_PATH.PROJECTS_FEATURED_DIRECTORY.$_POST['projectId']."_400".PROJECTS_FEATURED_EXTENSION;
        $path2 = CORE_BASE_PATH.PROJECTS_FEATURED_DIRECTORY.$_POST['projectId']."_720".PROJECTS_FEATURED_EXTENSION;
  
        if(!imagejpeg($imageSource, $path, 100)) {
          $answer = "ERROR: Image upload failed! Could not save image!";
          $answer .= "<br/>path: ".$path."<br/>";
          imagedestroy($imageSource);
        }
        if(!imagejpeg($image_400, $path1, 100)) {
          $answer = "ERROR: Image upload failed! Could not save image!";
          $answer .= "<br/>path: ".$path1."<br/>";
          imagedestroy($image_400);
        }
        if(!imagejpeg($image_720, $path2, 100)) {
          $answer = "ERROR: Image upload failed! Could not save image!";
          $answer .= "<br/>path: ".$path2."<br/>";
          imagedestroy($image_720);
        }          
        else
          $answer .= "SUCCESS: Featured image updated!<br/>file=".$path.", size=".round((filesize($path)/1024),2)." kb";
          $answer .= "<br/>file=".$path1.", size=".round((filesize($path1)/1024),2)." kb";
          $answer .= "<br/>file=".$path2.", size=".round((filesize($path2)/1024),2)." kb<br />";
      }
    }
    $this-> answer = $answer;
    $this->htmlFile = "editFeaturedProjects.php";
    $this->projects = $this->retrieveAllFeaturedProjectsFromDatabase();
  }

  public function editFeaturedProjects() {
    if(isset($_POST['delete'])) {
      if($this->deleteFeaturedProject($_POST['featuredId'])) {
        $answer = "The featured project was succesfully deleted!";
      } else {
        $answer = "Error: could NOT delete the featured project!";
      }
    }
    $projects = $this->retrieveAllFeaturedProjectsFromDatabase();
    if(count($projects) > 0 ) {
      for($i=0; $i < count($projects); $i++) {
        if($projects[$i]['image'] == "") {
          $id = $projects[$i]['id'];
          if($projects[$i]['visible'] == "t") {
            $query = "EXECUTE edit_featured_project_visibility_by_id('$id','f');";
            $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
            if(pg_affected_rows($result)) {
              $projects[$i]['visible'] = "f";
              $answer .= "<br/>Hiding featured project #".$projects[$i]['id']." (project_id=".$projects[$i]['project_id'].", title=".$projects[$i]['title'].") because no image was found!<br/>";
            }
            pg_free_result($result);
          }
      }
        }
    }
    $this->projects = $projects;
    $this->htmlFile = "editFeaturedProjects.php";
    $this->answer = $answer;
  }

  public function toggleFeaturedProjectsVisiblity() {
    if(isset($_POST['toggle'])) {
      $id = $_POST['featuredId'];
      if ($_POST['toggle'] == "visible") {
        if(getFeaturedProjectImageUrl($_POST['projectId']) == "") {
          $answer = "ERROR: Could NOT change featured project to state visible, because no image was found!";
        }
        else {
          $query = "EXECUTE edit_featured_project_visibility_by_id('$id','t');";
          $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
          if(pg_affected_rows($result)) {
            $answer = "The featured project was succesfully set to state visible!";
          } else {
            $answer = "ERROR: Could NOT change featured project to state visible!";
          }
        }
        $this->answer = $answer;
      }
      if ($_POST['toggle'] == "invisible") {
        $query = "EXECUTE edit_featured_project_visibility_by_id('$id','f');";
        $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
        if(pg_affected_rows($result)) {
          $answer = "The featured project was succesfully set to state invisible!";
        } else {
          $answer = "ERROR: Could NOT change featured project to state invisible!";
        }
        $this->answer = $answer;
      }
    }
    $this->htmlFile = "editFeaturedProjects.php";
    $this->projects = $this->retrieveAllFeaturedProjectsFromDatabase();
  }

  public function deleteFeaturedProject($id) {
    $query = "EXECUTE get_featured_project_by_id('$id');";
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    if(pg_affected_rows($result) != 1) {
      return false;
    }

    $project =  pg_fetch_assoc($result);
    if($project['project_id'] > 0) {
      if(file_exists(CORE_BASE_PATH.'/'.PROJECTS_FEATURED_DIRECTORY.'/'.$project['project_id'].PROJECTS_FEATURED_EXTENSION)) {
        @unlink(CORE_BASE_PATH.'/'.PROJECTS_FEATURED_DIRECTORY.'/'.$project['project_id'].PROJECTS_FEATURED_EXTENSION);
        @unlink(CORE_BASE_PATH.'/'.PROJECTS_FEATURED_DIRECTORY.'/'.$project['project_id']."_400".PROJECTS_FEATURED_EXTENSION);
        @unlink(CORE_BASE_PATH.'/'.PROJECTS_FEATURED_DIRECTORY.'/'.$project['project_id']."_720".PROJECTS_FEATURED_EXTENSION);
      }
    }

    $query = "EXECUTE delete_featured_project_by_id('$id');";
    $result = @pg_query($query) or $this->errorHandler->showError('db', 'query_failed', pg_last_error());
    if(pg_affected_rows($result)) {
      return true;
    }
     
    return false;
  }

  public function addBlockedIp() {
    if(isset($_POST['blockip'])) {
      $this->blockIp($_POST['blockip']);
      $answer = "The IP-address <b>".$_POST["blockip"]."</b> was added to blacklist.";
      $this->answer = $answer;
    }
    $this->htmlFile = "editBlockedIps.php";
    $this->blockedips = $this->getListOfBlockedIpsFromDatabase();
  }

  public function removeBlockedIp() {
    if(isset($_POST['blockedip'])) {
      $this->unblockIp($_POST['blockedip']);
      $answer = "The IP-address <b>".$_POST["blockedip"]."</b> was removed from blacklist.";
      $this->answer = $answer;
    }
    $this->htmlFile = "editBlockedIps.php";
    $this->blockedips = $this->getListOfBlockedIpsFromDatabase();
  }

  public function editBlockedIps() {
    if(isset($_POST['blockip'])) {
      if($this->blockIp($_POST['blockip'])) {
        $answer = "The IP-address <b>".$_POST["blockip"]."</b> was added to blacklist.";
      } else {
        $answer = "Error: could NOT add IP-address to blacklist!";
      }
      $this->answer = $answer;
    }
    $this->htmlFile = "editBlockedIps.php";
    $this->blockedips = $this->getListOfBlockedIpsFromDatabase();
  }

  public function addBlockedUser() {
    if(isset($_POST['blockUserValue'])) {
      $username = $this->blockUserID($_POST['blockUserValue']);
      $answer = "The username <b>".$username."</b> was added to blacklist.";
      $this->answer = $answer;
    }
    $this->htmlFile = "editBlockedUsers.php";
    $this->blockedusers = $this->getListOfBlockedUsersFromDatabase();
    $this->allusers = $this->getListOfUsersFromDatabase();
  }

  public function removeBlockedUser() {
    if(isset($_POST['unblockUserValue'])) {
      $username = $this->unblockUserID($_POST['unblockUserValue']);
      $answer = "The username <b>".$username."</b> was removed from blacklist.";
      $this->answer = $answer;
    }
    $this->htmlFile = "editBlockedUsers.php";
    $this->blockedusers = $this->getListOfBlockedUsersFromDatabase();
    $this->allusers = $this->getListOfUsersFromDatabase();
  }

  public function editBlockedUsers() {
    if(isset($_POST['blockuser'])) {
      if($this->blockUser($_POST['blockuser'])) {
        $answer = "The username <b>".$_POST["blockuser"]."</b> was added to blacklist.";
      } else {
        $answer = "Error: could NOT add username <b>".$_POST["blockuser"]."</b> to blacklist!";
      }
      $this->answer = $answer;
    }
    $this->htmlFile = "editBlockedUsers.php";
    $this->blockedusers = $this->getListOfBlockedUsersFromDatabase();
    $this->allusers = $this->getListOfUsersFromDatabase();
  }
  
  public function deletingUser() {
    if(isset($_POST['deleteUserValue'])) {
      $this->delete_user_projects($_POST['deleteUserValue']);
      $username = $this->deleteUserByID($_POST['deleteUserValue']);
      $answer = "The user <b>".$username."</b> was deleted.";
      $this->answer = $answer;
    }
    $this->htmlFile = "deleteUsers.php";
    $this->allusers = $this->getListOfUsersFromDatabase();
  }
  
  public function deleteUsers() {
    $this->htmlFile = "deleteUsers.php";
    $this->allusers = $this->getListOfUsersFromDatabase();
  }

  public function toggleProjects() {
    if(isset($_POST['toggle'])) {
      if ($_POST['toggle'] == "visible") {
        if($this->showProject($_POST['projectId'])) {
          $answer = "The project was succesfully set to state visible!";
        } else {
          $answer = "Error could NOT change project to state visible!";
        }
        $this->answer = $answer;
      }
      if ($_POST['toggle'] == "invisible") {
        if($this->hideProject($_POST['projectId'])) {
          $answer = "The project was succesfully set to state invisible!";
        } else {
          $answer = "Error could NOT change project to state invisible!";
        }
        $this->answer = $answer;
      }
    }
    $this->htmlFile = "editProjectList.php";
    $this->projects = $this->retrieveAllProjectsFromDatabase();
  }
  
  public function toggleApprovedProjects() {
  if(isset($_POST['toggle'])) {
      if ($_POST['toggle'] == "approve") {
        if($this->approveProject($_POST['projectId'])) {
          $answer = "The project was succesfully set to state approved!";
        } else {
          $answer = "Error could NOT change project to state approved!";
        }
        $this->answer = $answer;
      }
      if ($_POST['toggle'] == "unapprove") {
        if($this->unapproveProject($_POST['projectId'])) {
          $answer = "The project was succesfully set to state unapproved!";
        } else {
          $answer = "Error could NOT change project to state unapproved!";
        }
        $this->answer = $answer;
      }
    }
    
    $this->htmlFile = "editProjectList.php";
    $this->projects = $this->retrieveAllProjectsFromDatabase();
  }

  public function approveWords() {
    if(isset($_POST['approve'])) {
      $meaning = $_POST['meaning'];
      $wordId = $_POST['wordId'];
      if($meaning == 0) {
        if($this->hideProjectsContainingInsultingWords($wordId)) {
          if($this->setWordMeaning('false', $wordId)) {
            if($this->deleteWordFromUnapprovedWordList($wordId)) {
              $answer = "The word was succesfully approved!";
            } else {
              $answer = "Error: could NOT remove word from list!";
            }
          } else {
            $answer = "Error: could NOT approve the word!";
          }
        } else {
          $answer = "Error: could NOT hide project!";
        }
      } else if($meaning == 1) {
        if($this->setWordMeaning('true', $wordId)) {
          if($this->deleteWordFromUnapprovedWordList($wordId)) {
            $answer = "The word was succesfully approved!";
          } else {
            $answer = "Error: could NOT remove word from list!";
          }
        } else {
          $answer = "Error: could NOT approve the word!";
        }
      } else {
        $answer = "Error: no word meaning selected!";
      }
      $this->answer = $answer;
    }
    
    if(isset($_POST['delete'])) {
      if($this->deleteWord($_POST['wordId'])) {
        if($this->deleteWordFromUnapprovedWordList($_POST['wordId'])) {
          $answer = "The word was succesfully deleted!";
        } else {
          $answer = "Error: could NOT remove word from list!";
        }
      } else {
        $answer = "Error: could NOT delete the word!";
      }
      $this->answer = $answer;
    }
    
    $this->htmlFile = "approveWordsList.php";
    $this->count = $this->countAllUnapprovedWordsFromDatabase();

    $this->start = isset($_GET['page_number'])?(int)$_GET['page_number']:1;
    $this->per_page = isset($_GET['per_page'])?(int)$_GET['per_page']:20;
    if ($this->per_page != 10 AND $this->per_page != 20 AND $this->per_page != 50 AND $this->per_page != $this->count)
      $this->per_page = 20;
    if ($this->per_page > 0)
      $this->num_pages = ceil($this->count/$this->per_page);
    
    if ($this->start < 1)
      $this->start = 1;
    if ($this->start > $this->num_pages)
      $this->start = $this->num_pages;
    //$this->words = $this->retrieveAllUnapprovedWordsFromDatabase();
    $offset = ($this->start-1)*$this->per_page;
    if ($offset < 1)
      $offset = 0;
    $this->words = $this->retrieveAllUnapprovedWordsFromDatabaseLimit($this->per_page, $offset);
  }
  
  public function platzhalter() {
    if(isset($_POST['approve'])) {
      $meaning = $_POST['meaning'];
      $wordId = $_POST['wordId'];
      if($meaning == 0) {
        if($this->hideProjectsContainingInsultingWords($wordId)) {
          if($this->setWordMeaning('false', $wordId)) {
            if($this->deleteWordFromUnapprovedWordList($wordId)) {
              $answer = "The word was succesfully approved!";
            } else {
              $answer = "Error: could NOT remove word from list!";
            }
          } else {
            $answer = "Error: could NOT approve the word!";
          }
        } else {
          $answer = "Error: could NOT hide project!";
        }
      } else if($meaning == 1) {
        if($this->setWordMeaning('true', $wordId)) {
          if($this->deleteWordFromUnapprovedWordList($wordId)) {
            $answer = "The word was succesfully approved!";
          } else {
            $answer = "Error: could NOT remove word from list!";
          }
        } else {
          $answer = "Error: could NOT approve the word!";
        }
      } else {
        $answer = "Error: no word meaning selected!";
      }
      $this->answer = $answer;
    }

    if(isset($_POST['delete'])) {
      if($this->deleteWord($_POST['wordId'])) {
        if($this->deleteWordFromUnapprovedWordList($_POST['wordId'])) {
          $answer = "The word was succesfully deleted!";
        } else {
          $answer = "Error: could NOT remove word from list!";
        }
      } else {
        $answer = "Error: could NOT delete the word!";
      }
      $this->answer = $answer;
    }
    $this->htmlFile = "approveWordsList.php";
    $this->words = $this->retrieveAllUnapprovedWordsFromDatabase();
  }

  public function inappropriateProjects() {
    if(isset($_POST['resolve'])) {
      if($this->resolveInappropriateProject($_POST['projectId'])) {
        $answer = "The project was succesfully restored and set to visible!";
      } else {
        $answer = "Error during the resolve process!";
      }
      $this->answer = $answer;
    } elseif(isset($_POST['delete'])) {
      if($this->deleteProject($_POST['projectId'])) {
        $answer = "The project was succesfully deleted!";
      } else {
        $answer = "Error: could NOT delete the project!";
      }
      $this->answer = $answer;
    }
    $this->htmlFile = "inappropriateProjectList.php";
    $this->projects = $this->retrieveAllInappropriateProjectsFromDatabase();
  }

  public function thumbnailUploader() {
    if(isset($_FILES['upload']['tmp_name']) && $_FILES['upload']['error'] == 0) {
      if($this->uploadThumbnail($_FILES)) {
        $this->answer = "Upload: successful";
      } else {
        $this->answer = "Upload: failed";
      }
    }
    $this->htmlFile = "thumbnailUploader.php";
  }

  public function uploadThumbnail($fileData) {
    $upfile = $fileData['upload']['name'];
    $updir = CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$upfile;
    return(copy($fileData['upload']['tmp_name'], $updir));
  }

  public function retrieveAllUnapprovedWordsFromDatabase() {
    $query = 'EXECUTE get_unapproved_words;';
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $words =  pg_fetch_all($result);
    pg_free_result($result);
    return($words);
  }
  
  public function retrieveAllUnapprovedWordsFromDatabaseLimit($limit, $offset) {
    $query = "EXECUTE get_unapproved_words_limit('$limit','$offset');";
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $words =  pg_fetch_all($result);
    pg_free_result($result);
    return($words);
  }
  
  public function countAllUnapprovedWordsFromDatabase() {
    $query = 'EXECUTE get_count_unapproved_words;';
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $count =  pg_num_rows($result);
    //pg_free_result($count);
    return($count);
  }

  private function retrieveAllProjectsFromDatabase() {
    $query = 'EXECUTE get_projects_ordered_by_uploadtime;';
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $projects =  pg_fetch_all($result);
    
    $query = 'EXECUTE get_intern_tagging_list_reference;';
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $tags =  pg_fetch_all($result);
    
    if($projects) {
      for($i=0;$i<count($projects);$i++) {
        $projects[$i]['num_flags'] = $this->countFlags($projects[$i]['id']);
        $projects[$i]['tags'] = array();
        if($tags)
        {
          foreach($tags as $tag)
          {
            if($projects[$i]['id']==$tag["id_project"])
            {
              $projects[$i]['tags'][] = $tag["id_tag"];
            }
          }
        }
      }
    }
    return($projects);
  }
  
  private function retrieveAllTagsFromDatabase() {
    $query = 'EXECUTE get_intern_tagging_list;';
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $tags =  pg_fetch_all($result);
    if(!$tags)
      return array();
    return($tags);
  }
  
  private function retrieveAllStarterProjectsFromDatabase() {
    $query = 'EXECUTE get_starter_projects_admin_ordered_by_update_time;';
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $projects =  pg_fetch_all($result);   
    
    pg_free_result($result);
//     if($projects) {
//       for($i=0;$i<count($projects);$i++) {
//         $projects[$i]['image'] = getFeaturedProjectImageUrl($projects[$i]['project_id']);
//       }
//     }
    return($projects);
  }

  private function retrieveAllFeaturedProjectsFromDatabase() {
    $query = 'EXECUTE get_featured_projects_admin_ordered_by_update_time;';
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $projects =  pg_fetch_all($result);
    pg_free_result($result);
    if($projects) {
      for($i=0;$i<count($projects);$i++) {
        $projects[$i]['image'] = getFeaturedProjectImageUrl($projects[$i]['project_id']);
      }
    }
    return($projects);
  }

  private function retrieveAllInappropriateProjectsFromDatabase() {
    $query = 'EXECUTE get_flagged_projects_ordered_by_uploadtime;';
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $projects =  pg_fetch_all($result);
    if($projects) {
      for($i=0;$i<count($projects);$i++) {
        $projects[$i]['num_flags'] = $this->countFlags($projects[$i]['id']);
        $projects[$i]['flag_details'] = $this->getFlaggedReasonAndUser($projects[$i]['id']);
      }
    }
    pg_free_result($result);
    return($projects);
  }

  private function getListOfBlockedIpsFromDatabase() {
    $query = 'EXECUTE get_all_blocked_ips;';
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    return pg_fetch_all($result);
  }

  private function getListOfBlockedUsersFromDatabase() {
    $query = 'EXECUTE get_all_blocked_users;';
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    return pg_fetch_all($result);
  }

  private function getListOfUsersFromDatabase() {
    $query = 'EXECUTE get_all_users;';
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    return pg_fetch_all($result);
  }

  public function resolveInappropriateProject($projectId) {
    $query = "EXECUTE show_project('$projectId')";
    $query2 = "EXECUTE set_resolved_on_inappropriate('$projectId')";
    if(@pg_query($query) && @pg_query($query2)) {
      return true;
    } else {
      return false;
    }
  }

  private function countFlags($projectId) {
    $query = "EXECUTE get_flags_for_project('$projectId')";
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    return pg_num_rows($result);
  }

  private function getFlaggedReasonAndUser($projectId) {
    $fquery = "EXECUTE get_flagged_projects_reason_and_user('$projectId')";
    $fresult = @pg_query($fquery) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $fprojects =  pg_fetch_all($fresult);
    $details = "<ul>";
    if($fprojects) {
      foreach($fprojects as $fproject) {
        $details.= "<li>";
        $details.= $fproject['username'].", ";
        $details.= $fproject['time']."<br>";
        $details.= "<i>\"".$fproject['reason']."\"</i>";
      }
    }
    $details.= "</ul>";
    pg_free_result($fresult);
    return($details);
  }
  
  public function delete_user_projects($user_id) {
    $query = "EXECUTE get_projects_by_userid('$user_id');";
    $result = pg_query($query) or die($this->errorHandler->showError('db', 'query_failed', pg_last_error()));
    while($row = pg_fetch_array($result)) {
      $this->deleteProject($row['id']);
    }
  }

  public function deleteUserByID($user_id) {
    $username = $this->getUsernameById($user_id);
    $query = "EXECUTE admin_unblock_user_id('$user_id');";
    $result = pg_query($query) or die($this->errorHandler->showError('db', 'query_failed', pg_last_error()));

    $query = "EXECUTE delete_user_by_id('$user_id');";
    $result = pg_query($query) or die($this->errorHandler->showError('db', 'query_failed', pg_last_error()));
  
    return $username;
  }
  
  
  public function deleteProject($id) {
    $directory = CORE_BASE_PATH.PROJECTS_DIRECTORY;
    $thumbnailDirectory = CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY;

    $query = "EXECUTE get_project_by_id('$id');";
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $project =  pg_fetch_assoc($result);
    $fileName = $project['source'];
    $thumbnailSmallName = $project['id'].PROJECTS_THUMBNAIL_EXTENSION_SMALL;
    $thumbnailLargeName = $project['id'].PROJECTS_THUMBNAIL_EXTENSION_LARGE;
    $thumbnailLargeName = $project['id'].PROJECTS_THUMBNAIL_EXTENSION_LARGE;

    if($id > 0) {
      $projectBaseDir = CORE_BASE_PATH.'/'.PROJECTS_UNZIPPED_DIRECTORY.$id;
      $projectSoundDir = $projectBaseDir.'/sounds';
      $projectImageDir = $projectBaseDir.'/images';

      if(file_exists(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$id.PROJECTS_THUMBNAIL_EXTENSION_SMALL))
        @unlink(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$id.PROJECTS_THUMBNAIL_EXTENSION_SMALL);
      if(file_exists(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$id.PROJECTS_THUMBNAIL_EXTENSION_LARGE))
        @unlink(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$id.PROJECTS_THUMBNAIL_EXTENSION_LARGE);
      if(file_exists(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$id.PROJECTS_THUMBNAIL_EXTENSION_ORIG))
        @unlink(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$id.PROJECTS_THUMBNAIL_EXTENSION_ORIG);

      if(is_dir($projectSoundDir)) $this->removeProjectDir($projectSoundDir);
      if(is_dir($projectImageDir)) $this->removeProjectDir($projectImageDir);
      if(is_dir($projectBaseDir)) $this->removeProjectDir($projectBaseDir);
    }

    $sourceFile = $directory.$fileName;
    if(!$this->deleteFile($sourceFile)) {
      return false;
    } else {
      $query1 = "EXECUTE delete_starter_project_by_id('$id');";
      $result1 = @pg_query($query1) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
      
      $query = "EXECUTE delete_project_by_id('$id');";
      $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());     
      return true;
    }
  }

  private function removeProjectDir($dir) {
    $dh = opendir($dir);
    while (($file = readdir($dh)) !== false) {
      if ($file != "." && $file != "..")
        @unlink($dir."/".$file);
    }
    closedir($dh);
    rmdir($dir);
  }

  public function hideProject($id) {
    $query = "EXECUTE hide_project('$id');";
    $query1 = "EXECUTE hide_starter_project('$id');";
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    if($result) {
      pg_free_result($result);
    }
    
    $result1 = @pg_query($query1) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    if($result1) {
      pg_free_result($result1);
    }
    
    return $result;
  }

  public function showProject($id) {
    $query = "EXECUTE show_project('$id');";
    $query1 = "EXECUTE show_starter_project('$id');";
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    if($result) {
      pg_free_result($result);
    }
    
    $result1 = @pg_query($query1) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    if($result1) {
      pg_free_result($result1);
    }
    
    return $result;
  }

  private function hideProjectsContainingInsultingWords($word_id) {
    $query = "EXECUTE get_project_list_containing_insulting_words('$word_id');";
    $result = @pg_query($query) or $this->errorHandler->showError('db', 'query_failed', pg_last_error());
    $projects =  pg_fetch_all($result);
    pg_free_result($result);

    if($projects) {
      foreach($projects as $project) {
        if(!$this->hideProject($project['project_id'])) {
          return false;
        }
      }
    }
     
    return true;
  }

  private function deleteWordFromUnapprovedWordList($id) {
    $query = "EXECUTE delete_word_from_list('$id');";
    $result = @pg_query($query) or $this->errorHandler->showError('db', 'query_failed', pg_last_error());
    if($result) {
      pg_free_result($result);
    }
    return $result;
  }

  private function setWordMeaning($meaning, $id) {
    $query = "EXECUTE set_word_meaning('$meaning', '$id');";
    $result = @pg_query($query) or $this->errorHandler->showError('db', 'query_failed', pg_last_error());
    pg_free_result($result);
    return $result;
  }

  private function deleteWord($id) {
    $query = "EXECUTE delete_word_by_id('$id');";
    $result = @pg_query($query) or $this->errorHandler->showError('db', 'query_failed', pg_last_error());
    pg_free_result($result);
    return $result;
  }

  private function isProjectInDatabase($projectId) {
    $query = "EXECUTE get_project_by_id('$projectId');";
    $result = pg_query($query) or die($this->errorHandler->showError('db', 'query_failed', pg_last_error()));
    if(pg_num_rows($result)) {
      return true;
    } else {
      return false;
    }
  }

  private function deleteFile($file) {
    if(!is_file($file)) {
      return true;
    }
    if(@unlink($file)) {
      return true;
    } else {
      return false;
    }
  }
  

  public function updateProjectFilesizeInDatabase($id, $filesize) {
    $query = "EXECUTE update_project_filesize('$id', '$filesize');";
    $result = pg_query($query) or die($this->errorHandler->showError('db', 'query_failed', pg_last_error()));
    return;
  }

  public function blockUser($user_id, $user_name) {
    if ($user_id === null)
      $query = "EXECUTE admin_block_username('$user_name');";
    else
      $query = "EXECUTE admin_block_user('$user_id', '$user_name');";
    $result = pg_query($query) or die($this->errorHandler->showError('db', 'query_failed', pg_last_error()));
  }

  public function blockUserID($user_id) {
    $query = "EXECUTE admin_block_user_id('$user_id');";
    $result = pg_query($query) or die($this->errorHandler->showError('db', 'query_failed', pg_last_error()));
    return $this->getUsernameById($user_id);
  }

  public function unblockUser($user_id, $user_name) {
    if ($user_id === null)
      $query = "EXECUTE admin_unblock_username('$user_name');";
    else
      $query = "EXECUTE admin_unblock_user('$user_id', '$user_name');";
    $result = pg_query($query) or die($this->errorHandler->showError('db', 'query_failed', pg_last_error()));
  }

  public function unblockUserID($user_id) {
    $query = "EXECUTE admin_unblock_user_id('$user_id');";
    $result = pg_query($query) or die($this->errorHandler->showError('db', 'query_failed', pg_last_error()));
    return $this->getUsernameById($user_id);
  }

  private function getUsernameById($user_id) {
    $query = "EXECUTE get_user_by_id ('$user_id');";
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $users = pg_fetch_all($result);
    if($users) {
      foreach($users as $user) {
        $username = $user["username"];
        return $username;
      }
    }
    return "UserID: ".$user_id;
  }

  public function isBlockedUser($user_id, $user_name) {
    $query = "EXECUTE admin_is_blocked_user('$user_id', '$user_name');";
    $result = pg_query($query) or die($this->errorHandler->showError('db', 'query_failed', pg_last_error()));
    if(pg_num_rows($result)) {
      return true;
    } else {
      return false;
    }
  }

  public function blockIp($ip) {
    if (strlen($ip) >= 1) {
      $query = "EXECUTE admin_block_ip('$ip');";
      $result = pg_query($query) or die($this->errorHandler->showError('db', 'query_failed', pg_last_error()));
    }
  }

  public function unblockIp($ip) {
    if (strlen($ip) >= 1) {
      $query = "EXECUTE admin_unblock_ip('$ip');";
      $result = pg_query($query) or die($this->errorHandler->showError('db', 'query_failed', pg_last_error()));
    }
  }

  public function isBlockedIp($ip) {
    $query = "EXECUTE admin_is_blocked_ip('$ip%');";
    $result = pg_query($query) or die($this->errorHandler->showError('db', 'query_failed', pg_last_error()));
    if(pg_num_rows($result)) {
      return true;
    } else {
      return false;
    }
  }

  public function removeAllBlockedIps() {
    $query = "EXECUTE admin_remove_all_blocked_ips;";
    $result = pg_query($query) or die($this->errorHandler->showError('db', 'query_failed', pg_last_error()));
  }

  public function updateMobileBrowserDetectionCode($currentCode, $updateCode) {
    $partCode = preg_split("/\/\/ <[\/]*isMobile>/", $currentCode);
    $newUpdateCode = $this->extractRegexCode($updateCode, "url");
    if (sizeof($partCode) >= 2) {
      $newCode = $partCode[0];
      $newCode.= "// <isMobile>\n";
      $newCode.= "\t\t\t\t".$newUpdateCode;
      $newCode.= "\t\t\t\t// </isMobile>";
      $newCode.= $partCode[2];
      return $newCode;
    } else {
      return $currentCode; // on error, do not change the code
    }
  }

  public function extractRegexCode($code, $mode) {
    if ($mode == "class") {
      $partCode = preg_split("/\/\/ <[\/]*isMobile>/", $code);
      return $partCode[1];
    }
    if ($mode == "url") {
      $newUpdateCodeTmp = preg_split("/if\(/", $code);
      if (is_array($newUpdateCodeTmp) && sizeof($newUpdateCodeTmp) >= 2) {
        $newUpdateCodePart = preg_split("/header/", $newUpdateCodeTmp[1]);
        $newUpdateCode = "if (".$newUpdateCodePart[0];
        return $newUpdateCode;
      }
    }
    return $code;
  }

  public function updateBrowserDetectionRegexPattern() {
    $currentData = "";
    $updateData = "";
    $clientDetectionClass = CORE_BASE_PATH.'classes/CoreClientDetection.php';
    $clientDetectionUpdateUrl = MOBILE_BROWSERDETECTION_URL_FOR_UPDATE;

    $mcurrentData = $this->loadCurrentClientDetectionClass($clientDetectionClass);
    $mupdateData = $this->loadNewRegexPatternFromUrl($clientDetectionUpdateUrl);
    $newData = $this->updateMobileBrowserDetectionCode($mcurrentData, $mupdateData);

    // and save changes back to class
    if (($mcurrentData != $newData)) {
      $fp = fopen($clientDetectionClass, "w+");
      if ($fp) {
        fwrite($fp, $newData);
        fclose($fp);
        return true;
      }
    }
    return false;
  }

  function loadCurrentClientDetectionClass($clientDetectionClass) {
    $fp = fopen($clientDetectionClass, "r");
    if ($fp) {
      $currentData = fread($fp, filesize($clientDetectionClass));
      fclose($fp);
      return $currentData;
    } else {
      return null; // not found - so do not update
    }
  }

  function loadNewRegexPatternFromUrl($clientDetectionUpdateUrl) {
    $fp = fopen($clientDetectionUpdateUrl, "rb");
    if ($fp) {
      $updateData = fread($fp, 32000);
      fclose($fp);
      return $updateData;
    } else {
      return null; // not found - so do not update
    }
  }

  public function updateBrowserDetection() {
    if(isset($_POST['action'])) {
      /*if($this->blockUser($_POST['blockuser'])) {
       $answer = "The username <b>".$_POST["blockuser"]."</b> was added to blacklist.";
      } else {
      $answer = "Error: could NOT add username <b>".$_POST["blockuser"]."</b> to blacklist!";
      }
      */
      $info = $this->updateBrowserDetectionRegexPattern();
      if ($info)
        $answer = "<div style=\"color: green;\">The regex-pattern was updated successfully.</div>";
      else
        $answer = "<div style=\"color: red;\">The regex-pattern could not be updated.<br />Something went wrong!</div>";
      $this->answer = $answer;
    }
    $this->htmlFile = "updateBrowserDetection.php";
    $this->currentRegEx = $this->getCurrentMobileBrowserDetectionRegEx();
    $this->newRegEx = $this->getNewMobileBrowserDetectionRegEx();
  }

  public function getCurrentMobileBrowserDetectionRegEx() {
    $clientDetectionClass = CORE_BASE_PATH.'classes/CoreClientDetection.php';
    $code = $this->loadCurrentClientDetectionClass($clientDetectionClass);
    return trim($this->extractRegexCode($code, "class"));
  }

  public function getNewMobileBrowserDetectionRegEx() {
    $clientDetectionUpdateUrl = MOBILE_BROWSERDETECTION_URL_FOR_UPDATE;
    $code = $this->loadNewRegexPatternFromUrl($clientDetectionUpdateUrl);
    return trim($this->extractRegexCode($code, "url"));
  }
  
  public function uploadNotificationsList() {
    if(isset($_POST['uploadNotificationsAdd'])) {
      if($this->addUploadNotificationsEMail($_POST['email'])) {
        $answer = "E-Mail added successfully!";
      } else {
        $answer = "Error: could NOT add E-Mail!";
      }
      $this->answer = $answer;
    }
    
    if(isset($_POST['uploadNotificationsRemove'])) {
      if($this->removeUploadNotificationsEMail($_POST['id'])) {
        $answer = "E-Mail removed successfully!";
      } else {
        $answer = "Error: could NOT remove E-Mail!";
      }
      $this->answer = $answer;
    }    
    
    $this->htmlFile = "uploadNotificationsList.php";
    $this->emailList = $this->getUploadNotificationsEMailList();
  }
  
  public function addUploadNotificationsEMail($email) {
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return false;
    }
    $query = "EXECUTE insert_uploadnotifications_email('$email');";
    return @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
  }
  
  public function removeUploadNotificationsEMail($id) {
    $query = "EXECUTE remove_uploadnotifications_email('$id');";
    return @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
  }
  
  public function getUploadNotificationsEMailList() {
    
    $result = pg_execute($this->dbConnection, "get_uploadnotifications_email_list", array());
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
          STATUS_CODE_SQL_QUERY_FAILED);
    }
    
    return pg_fetch_all($result);
  }
  
  public function approveProjects() {
    if(isset($_POST['toggle'])) {
      if ($_POST['toggle'] == "visible") {
        if($this->showProject($_POST['projectId'])) {
          $answer = "The project was succesfully set to state visible!";
        } else {
          $answer = "Error could NOT change project to state visible!";
        }
        $this->answer = $answer;
      }
      if ($_POST['toggle'] == "invisible") {
        if($this->hideProject($_POST['projectId'])) {
          $answer = "The project was succesfully set to state invisible!";
        } else {
          $answer = "Error could NOT change project to state invisible!";
        }
        $this->answer = $answer;
      }
    }
    if(isset($_POST['delete'])) {
      if($this->deleteProject($_POST['projectId'])) {
        $answer = "The project was succesfully deleted!";
      } else {
        $answer = "Error: could NOT delete the project!";
      }
      $this->answer = $answer;
    }
    if(isset($_POST['approve'])) {
      if($this->approveProject($_POST['projectId'])) {
        $answer = "The project was successfully approved!";
      } else {
        $answer = "Error: could NOT approve the project!";
      }
      $this->answer = $answer;
    }
    
    $this->htmlFile = "approveProjectsList.php";
    $this->unapprovedProjects = $this->retrieveAllUnapprovedProjectsFromDatabase();
  }
  
  private function getProjectXmlFile($unzipDir) {
    if(!$dirHandler = opendir($unzipDir)){
      echo '<a id="aAdminToolsBackToCatroidweb" href="' . BASE_PATH . 'admin/tools/approveProjects">&lt;- back</a><br /><br />';
      
      echo '<form id="nextform" class="admin" action="showProjectsContent" method="POST">
      <input type="hidden" name="projectId" value="' . ($this->projectId) . '"/>
      <input  type =  "submit" value="Next" name="nextProject" id="next' . $project['id'] . '" >
        </form>';
      echo "unzipped directory: " . $unzipDir;
      die();
    }
      
    while(($file = readdir($dirHandler)) !== false) {
      $details = pathinfo($file);
      if(isset($details['extension']) && file_exists($unzipDir . $file) && (strcmp($details['extension'], 'spf') == 0 ||
          strcmp($details['extension'], 'xml') == 0 || strcmp($details['extension'], 'catroid') == 0)) {
        return $unzipDir.$file;
      }
    }
    throw new Exception($this->errorHandler->getError('tools', 'project_xml_not_found'), STATUS_CODE_UPLOAD_MISSING_XML);
  }  
  
  public function showProjectsContent() {
    
    function cmp($a, $b) {
      if (strlen($a) == strlen($b)) {
        return 0;
      }
      return (strlen($a) < strlen($b)) ? 1 : -1;
    }    
    $this->projectId = $_POST['projectId'];
//     $this->projectTitle = $_POST['title']; 

    if(isset($_POST['approve'])) {
      if($this->approveProject($_POST['projectId'])) {
        $answer = "The project was successfully approved!";
      } else {
        $answer = "Error: could NOT approve the project!";
      }
      $this->answer = $answer;
    }
    $this->unapprovedProjects = $this->retrieveAllUnapprovedProjectsFromDatabase(); 
    if (!$this->unapprovedProjects) {
      $this->htmlFile = "approveProjectsList.php";
      return;
    }   
    $endOfList = false;
    if (isset($_POST['nextProject']) || isset($_POST['approve'])) {
      foreach($this->unapprovedProjects as $project) {
        if($project['id'] < $this->projectId) {        
          $this->projectId = $project['id'];
          $endOfList = true;
          break;
        } 
      }
      if(!$endOfList) {
        $project = $this->unapprovedProjects[0];
        $this->projectId = $project['id'];
      }
    }
    $recource = PROJECTS_UNZIPPED_DIRECTORY . $this->projectId . "/";
    $xml = fopen( $this->getProjectXmlFile($recource), r);
    if(!$xml)
      die("Sorry, couldn't open xml-File ".$this->getProjectXmlFile($recource));
    $data = '';
    while ($input = fread($xml, 1024)) {
      $data .= $input;
    }  
    if (preg_match_all("#>(.*[A-Za-z]+.*)<#", $data, $match, PREG_PATTERN_ORDER)) {
      $match = array_unique($match[1]);
    }
    fclose($xml);
    uasort($match, 'cmp');    
    $this->parsedStrings = $match; 
    $xmlstr = simplexml_load_file($this->getProjectXmlFile($recource));
    
    $node = $xmlstr->children();
    $this->projectTitle = current($node[0]->programName);
    $site = $this->htmlFile = "showProjectContent.php";
    $this->addCss('showProjectContent.css'); 
  }
  
  
  public function retrieveAllUnapprovedProjectsFromDatabase() {
    $query = 'EXECUTE get_unapproved_projects;';
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $words =  pg_fetch_all($result);
    pg_free_result($result);
    return($words);
  }
  
  public function approveProject($projectId) {
    $query = "EXECUTE approve_project(true, '$projectId');";
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    if($result) {
      pg_free_result($result);
    }
    
    return $result;
  }
  
  public function unapproveProject($projectId) {
    $query = "EXECUTE approve_project(false, '$projectId');";
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    if($result) {
      pg_free_result($result);
    }
    
    return $result;
  }
  
  public function getResource()
  {
    $file = CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY . $_GET['project_id'] ."/".$_GET['file_name'].".".$_GET["view"];
    if(is_file($file)) {
      $resource = file_get_contents($file);
    } else {
      $file = CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY . $_GET['project_id'] ."/".$_GET['file_name'];
      if(is_file($file)) {
        $resource = file_get_contents($file);
      }else{
        header("HTTP/1.0 404 Not Found");
        die();
      }
    }
    $this->__set(1, $resource);
  }
  
  public function addInternTaggingName($tag_name) {
    $query = "EXECUTE insert_intern_tagging_name('$tag_name');";
    return @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
  }
  
  public function removeInternTaggingName($id) {
    $query = "EXECUTE remove_intern_tagging_name('$id');";
    return @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
  }
  
  public function removeInternTaggingReferencesById($id) {
    $query = "EXECUTE remove_intern_tagging_references_by_id('$id');";
    return @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
  }
  
  public function getInternTaggingList() {
    $result = pg_execute($this->dbConnection, "get_intern_tagging_list", array());
    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error($this->dbConnection)),
          STATUS_CODE_SQL_QUERY_FAILED);
    }
  
    return pg_fetch_all($result);
  }
  
  public function internTagging() {
    if(isset($_POST['internTaggingAdd'])) {
      if($this->addInternTaggingName($_POST['tag'])) {
        $answer = "Tag added successfully!";
      } else {
        $answer = "Error: could NOT add Tag!";
      }
      $this->answer = $answer;
    }
    
    if(isset($_POST['internTaggingRemove'])) {
      if($this->removeInternTaggingName($_POST['id'])) {
        $this->removeInternTaggingReferencesById($_POST['id']);
        $answer = "Tag removed successfully!";
      } else {
        $answer = "Error: could NOT remove Tag!";
      }
      $this->answer = $answer;
    }
    
    $this->htmlFile = "internTagging.php";
    $this->tagList = $this->getInternTaggingList();
  }
  
  public function updateTagRef()
  {
    if(!isset($_POST['checked']))
      return;
    
    if($_POST['checked'] == "true") {
      $query = "EXECUTE insert_intern_tagging_ref('".$_POST["tag_id"]."','".$_POST['project_id']."');";
      return @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    }else {
      $query = "EXECUTE remove_intern_tagging_ref('".$_POST["tag_id"]."','".$_POST['project_id']."');";
      return @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    }
    $this->answer = "OK";
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
