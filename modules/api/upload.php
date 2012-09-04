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

class upload extends CoreAuthenticationDevice {
  
  private $uploadState;

  public function __construct() {
    parent::__construct();
    $thumbnailDir = CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY;
    $this->uploadState = array('remove_files' => array(),
        'remove_dirs' => array(),
        'remove_project_from_db' => array());
  }

  public function __default() {
    $this->_upload();
  }

  public function __authenticationFailed() {
    $this->statusCode = 601;
    $this->answer = $this->errorHandler->getError('auth', 'device_auth_invalid_token');
  }

  public function _upload() {
    $this->doUpload($_POST, $_FILES);
  }

  public function doUpload($formData, $fileData) {
    try {
      $fileData = $this->checkForAndPrepareFTPUpload($formData, $fileData);
    
      $this->checkFileData($formData, $fileData);
      $this->checkProjectSize($fileData);
    
      $tempFilenameUnique = md5(uniqid(time()));
      while(file_exists(CORE_BASE_PATH.PROJECTS_DIRECTORY.$tempFilenameUnique)) {
        $tempFilenameUnique = md5(uniqid(time()));
      }
      
      $this->checkFileChecksum($formData, $fileData['upload']['tmp_name']);
      $fileSize = $this->copyProjectToDirectory($fileData['upload']['tmp_name'], CORE_BASE_PATH.PROJECTS_DIRECTORY.$tempFilenameUnique);
      $this->unzipProjectFiles(CORE_BASE_PATH.PROJECTS_DIRECTORY.$tempFilenameUnique, CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY.$tempFilenameUnique);
      
      $xmlFile = $this->getProjectXmlFile(CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY.$tempFilenameUnique.'/');
      $projectInformation = $this->getProjectInformation($xmlFile, $formData);
      $this->checkValidProjectTitle($projectInformation['projectTitle']);
      $this->checkTitleForInsultingWords($projectInformation['projectTitle']);
      $this->checkDescriptionForInsultingWords($projectInformation['projectDescription']);
      
      $projectId = $this->updateOrInsertProjectIntoDatabase($projectInformation['projectTitle'], $projectInformation['projectDescription'], 
          $projectInformation['uploadIp'], $projectInformation['uploadEmail'], $projectInformation['uploadLanguage'], 
          $fileSize, $projectInformation['versionName'], $projectInformation['versionCode']);
      
      $this->renameProjectFile(CORE_BASE_PATH.PROJECTS_DIRECTORY.$tempFilenameUnique, $projectId);
      $this->renameUnzipDirectory(CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY.$tempFilenameUnique,
          CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY.$projectId);
      $this->extractThumbnail(CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY.$projectId.'/', $projectId);
    
      $this->getQRCode($projectId, $projectInformation['projectTitle']);
      
      $unapprovedWords = $this->badWordsFilter->getUnapprovedWords();
      if($unapprovedWords) {
        $this->badWordsFilter->mapUnapprovedWordsToProject($projectId);
        $this->sendUnapprovedWordlistPerEmail();
      }
      
      $this->buildNativeApp($projectId);
      
      $this->projectId = $projectId;
      $this->statusCode = 200;
      $this->answer = $this->languageHandler->getString('upload_successfull');
    } catch(Exception $e) {
      $this->sendUploadFailAdminEmail($_POST, $_FILES);
      $this->cleanup();
      $this->statusCode = $e->getCode();
      $this->answer = $e->getMessage();
    }
  }
  
  private function setState($type, $new, $old=0) {
    switch($type) {
      case 'remove_files':
      case 'remove_dirs':
        if($old === 0) {
          array_push($this->uploadState[$type], $new);
        } else {
          foreach($this->uploadState[$type] as $key => $value) {
            if($value === $old) {
              $this->uploadState[$type][$key] = $new;
              break;
            }
          }
        }
        break;
      case 'remove_project_from_db':
        array_push($this->uploadState[$type], $new);
        break;
    }
  }
  
  private function checkForAndPrepareFTPUpload($formData, $fileData) {
    if(isset($formData['catroidFileName'])) {
      $fileData['upload']['tmp_name'] = PROJECTS_FTP_UPLOAD_DIRECTORY . $formData['catroidFileName'];
    
      if(file_exists($fileData['upload']['tmp_name'])) {
        $fileData['upload']['error'] = UPLOAD_ERR_OK;
      } else {
        $fileData['upload']['error'] = UPLOAD_ERR_NO_FILE;
      }
    }
    return $fileData;
  }

  public function copyProjectToDirectory($tmpFile, $uploadDir) {
    if(copy($tmpFile, $uploadDir)) {
      chmod($uploadDir, 0666);
      $this->setState('remove_files', $uploadDir);
      return filesize($uploadDir);
    } else {
      throw new Exception($this->errorHandler->getError('upload', 'copy_failed'), 504);
    }
  }

  public function unzipProjectFiles($zipFile, $destDir) {
    if(!unzipFile($zipFile, $destDir)) {
      throw new Exception($this->errorHandler->getError('upload', 'invalid_project_zip'), 511);
    }
    chmodDir($destDir, 0666, 0777);
    $this->setState('remove_dirs', $destDir);
  }

  public function renameProjectFile($oldName, $projectId) {
    $newName = CORE_BASE_PATH.PROJECTS_DIRECTORY.$projectId.PROJECTS_EXTENSION;
    
    if(file_exists($newName)) {
      unlink($newName);
    }
    if(!rename($oldName, $newName)) {
      throw new Exception($this->errorHandler->getError('upload', 'rename_failed'), 502);
    }
    $this->setState('remove_files', $newName, $oldName);
    
    $result = pg_execute($this->dbConnection, "set_project_new_filename", array($projectId.PROJECTS_EXTENSION, $projectId)) or
              $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    if(!$result) {
      throw new Exception($this->errorHandler->getError('upload', 'rename_failed', pg_last_error($this->dbConnection)), 502);
    }
    return true;
  }
  
  public function renameUnzipDirectory($oldName, $newName) {
    removeDir($newName);
    if(!rename($oldName, $newName)) {
      throw new Exception($this->errorHandler->getError('upload', 'rename_failed'), 502);
    }
    $this->setState('remove_dirs', $newName, $oldName);
    
    return true;
  }

  public function checkFileChecksum($formData, $uploadedFile) {
    if(!isset($formData['fileChecksum']) || !$formData['fileChecksum']) {
      throw new Exception($this->errorHandler->getError('upload', 'missing_post_file_checksum'), 510);
    }
    
    $fileChecksum = md5_file($uploadedFile);
    if(strcmp(strtolower($formData['fileChecksum']), strtolower($fileChecksum)) != 0) {
      throw new Exception($this->errorHandler->getError('upload', 'invalid_file_checksum'), 501);
    }

    return true;
  }

  private function getProjectXmlFile($unzipDir) {
    $dirHandler = opendir($unzipDir);
    while(($file = readdir($dirHandler)) !== false) {
      $details = pathinfo($file);
      if(isset($details['extension']) && (strcmp($details['extension'], 'spf') == 0 || strcmp($details['extension'], 'xml') == 0 || strcmp($details['extension'], 'catroid') == 0)) {
        if(file_exists($unzipDir.$file)) {
          return $unzipDir.$file;
        }
      }
    }
    throw new Exception($this->errorHandler->getError('upload', 'project_xml_not_found'), 512);
  }

  public function getProjectInformation($xmlFile, $formData) {
    libxml_use_internal_errors(true);
  	$xml = simplexml_load_file($xmlFile);
    if(!$xml) {
      throw new Exception($this->errorHandler->getError('upload', 'invalid_project_xml'), 513);
    }
    $attributes = $xml->attributes();
    $versionName = (isset($attributes["catroidVersionName"]) && $attributes["catroidVersionName"]) ? strval($attributes["catroidVersionName"]) : null;
    $versionCode = (isset($attributes["catroidVersionCode"]) && $attributes["catroidVersionCode"]) ? strval($attributes["catroidVersionCode"]) : null;
    $projectTitle = isset($attributes["projectName"]) && $attributes["projectName"] ? strval($attributes["projectName"]) : null;
    $projectDescription = isset($attributes["description"]) && $attributes["description"] ? strval($attributes["description"]) : null;
    
    
    if(!$versionName || !$versionCode) {
      $versionCode = null;
      $versionName = null;
      $projectTitle = null;
      $projectDescription = null;
      foreach($xml->children() as $child) {
        if(strcmp(strval($child->getName()), 'catroidVersionName') == 0) {
          $versionName = strval($child);
        } elseif(strcmp(strval($child->getName()), 'catroidVersionCode') == 0) {
          $versionCode = strval($child);
        } elseif(strcmp(strval($child->getName()), 'projectName') == 0) {
          $projectTitle = strval($child);
        } elseif(strcmp(strval($child->getName()), 'description') == 0) {
          $projectDescription = strval($child);
        }
      }
    }

    if(!$versionName || !$versionCode) {
      $versionCode = 499;
      $versionName = '&lt; 0.6.0beta';
    } else if(stristr($versionName, "-")) {
      $versionName = substr($versionName, 0, strpos($versionName, "-"));
    }
    
    if(!$projectTitle) {
      $projectTitle = ((isset($formData['projectTitle'])) ? checkUserInput($formData['projectTitle']) : "default_project_name");
    }
    if(!$projectDescription) {
      $projectDescription = ((isset($formData['projectDescription'])) ? checkUserInput($formData['projectDescription']) : "");
    }
    
    $uploadIp = ((isset($_SERVER['REMOTE_ADDR']))?$_SERVER['REMOTE_ADDR']:'');
    $uploadEmail = ((isset($formData['userEmail'])) ? checkUserInput($formData['userEmail']) : null);
    $uploadLanguage = ((isset($formData['userLanguage'])) ? checkUserInput($formData['userLanguage']) : null);
    
    return(array(
        "projectTitle" => pg_escape_string($projectTitle),
        "projectDescription" => pg_escape_string($projectDescription),
        "versionName" => $versionName,
        "versionCode" => $versionCode,
        "uploadIp" => $uploadIp,
        "uploadEmail" => $uploadEmail,
        "uploadLanguage" => $uploadLanguage
        ));
  }

  private function updateOrInsertProjectIntoDatabase($projectTitle, $projectDescription, $uploadIp, $uploadEmail, $uploadLanguage, $fileSize, $versionName, $versionCode) {
    $userId = (($this->session->userLogin_userId )? $this->session->userLogin_userId : 0);
    
    $result = pg_execute($this->dbConnection, "does_project_already_exist", array($projectTitle, $userId)) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    if($result && pg_num_rows($result) == 1) {
      $row = pg_fetch_assoc($result);
      $updateId = $row['id'];
      @pg_free_result($result);

      $result = pg_execute($this->dbConnection, "update_project", array($projectDescription, $uploadIp, $fileSize, $versionName, $versionCode, $updateId)) or
                $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
      if(!$result) {
        throw new Exception($this->errorHandler->getError('upload', 'sql_update_failed', pg_last_error($this->dbConnection)), 503);
      }

      return $updateId;
    } else {
      if(!$result) {
        throw new Exception($this->errorHandler->getError('upload', 'sql_insert_failed', pg_last_error($this->dbConnection)), 503);
      }
      @pg_free_result($result);

      $result = pg_execute($this->dbConnection, "insert_new_project", array($projectTitle, $projectDescription, $uploadIp, $uploadEmail, $uploadLanguage, $fileSize, $versionName, $versionCode, $userId)) or
                $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
      if(!$result) {
        throw new Exception($this->errorHandler->getError('upload', 'sql_insert_failed', pg_last_error($this->dbConnection)), 503);
      }
      $row = pg_fetch_assoc($result);
      $insertId = $row['id'];
      
      @pg_free_result($result);
      
      $this->setState('remove_project_from_db', $insertId);
      return $insertId;
    }
    return 0;
  }

  private function checkFileData($formData, $fileData) {
    if(!isset($fileData['upload']['tmp_name']) ||
        $fileData['upload']['error'] !== UPLOAD_ERR_OK ||
        !file_exists($fileData['upload']['tmp_name'])) {
      throw new Exception($this->errorHandler->getError('upload', 'missing_file_data'), 509);
    }
    return true;
  }

  private function checkProjectSize($fileData) {
    if(isset($fileData['upload']['size']) && intval($fileData['upload']['size']) > PROJECTS_MAX_SIZE) {
      throw new Exception($this->errorHandler->getError('upload', 'project_exceed_filesize_limit'), 508);
    }
    return true;
  }

  private function checkValidProjectTitle($title) {
    if(strcmp($title, $this->languageHandler->getString('default_project_name')) == 0) {
      throw new Exception($this->errorHandler->getError('upload', 'project_title_default'), 507);
    }
    return true;
  }

  private function checkTitleForInsultingWords($title) {
    if($this->badWordsFilter->areThereInsultingWords($title)) {
      throw new Exception($this->errorHandler->getError('upload', 'insulting_words_in_project_title'), 506);
    }
    return true;
  }

  private function checkDescriptionForInsultingWords($description) {
    if($description && $this->badWordsFilter->areThereInsultingWords($description)) {
      throw new Exception($this->errorHandler->getError('upload', 'insulting_words_in_project_description'), 505);
    }
    return true;
  }

  private function getQRCode($projectId, $projectTitle) {
    $urlToEncode = urlencode(BASE_PATH.'catroid/download/'.$projectId.PROJECTS_EXTENSION.'?fname='.urlencode($projectTitle));
    $destinationPath = CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$projectId.PROJECTS_QR_EXTENSION;
    if(!generateQRCode($urlToEncode, $destinationPath)) {
      throw new Exception($this->errorHandler->getError('upload', 'qr_code_generation_failed'), 514);
    }
    $this->setState('remove_files', $destinationPath);
    return true;
  }

  private function extractThumbnail($unzipDir, $projectId) {
    $thumbFile = null;
    $thumbType = null;
    if(is_file($unzipDir.'screenshot.png')) {
      $thumbFile = $unzipDir.'screenshot.png';
      $thumbType = 'PNG';
    }
    if($thumbFile && $thumbType) {
      $this->saveThumbnail($projectId, $thumbFile, $thumbType);
    }
  }

  private function saveThumbnail($projectId, $thumbnail, $thumbType) {
    $thumbnailDir = CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY;
    if(strcmp($thumbType, 'PNG') == 0) {
      $thumbImage = imagecreatefrompng($thumbnail);
    } else {
      throw new Exception($this->errorHandler->getError('upload', 'save_thumbnail_failed'), 515);
    }
    if ($thumbImage) {
      $w = imagesx($thumbImage);
      $h = imagesy($thumbImage);
      $wsmall = 0; $hsmall = 0; $hsmallopt = intval(240*$h/$w);
      $wlarge = 0; $hlarge = 0; $hlargeopt = intval(480*$h/$w);
      imagejpeg($thumbImage, $thumbnailDir.$projectId.PROJECTS_THUMBNAIL_EXTENSION_ORIG, 100);
      $this->setState('remove_files', $thumbnailDir.$projectId.PROJECTS_THUMBNAIL_EXTENSION_ORIG);
      
      $smallImage = imagecreatetruecolor(240, $hsmallopt);
      imagecopyresampled($smallImage, $thumbImage, 0, 0, 0, 0, 240, $hsmallopt, $w, $h);
      imagejpeg($smallImage, $thumbnailDir.$projectId.PROJECTS_THUMBNAIL_EXTENSION_SMALL, 50);
      $this->setState('remove_files', $thumbnailDir.$projectId.PROJECTS_THUMBNAIL_EXTENSION_SMALL);

      $newImage = imagecreatetruecolor(480, $hlargeopt);
      imagecopyresampled($newImage, $thumbImage, 0, 0, 0, 0, 480, $hlargeopt, $w, $h);
      imagejpeg($newImage, $thumbnailDir.$projectId.PROJECTS_THUMBNAIL_EXTENSION_LARGE, 50);
      $this->setState('remove_files', $thumbnailDir.$projectId.PROJECTS_THUMBNAIL_EXTENSION_LARGE);
    }
  }
  
  private function buildNativeApp($projectId) {
    $pythonHandler = CORE_BASE_PATH.PROJECTS_APP_BUILDING_SRC."nativeAppBuilding/src/handle_project.py";
    $projectFile = CORE_BASE_PATH.PROJECTS_DIRECTORY.$projectId.PROJECTS_EXTENSION;
    $catroidSource = CORE_BASE_PATH.PROJECTS_APP_BUILDING_SRC."catroid/";
    $outputFolder = CORE_BASE_PATH.PROJECTS_DIRECTORY;

    if(is_dir(CORE_BASE_PATH.PROJECTS_APP_BUILDING_SRC)) {
      shell_exec("python2.6 $pythonHandler $projectFile $catroidSource $projectId $outputFolder > /dev/null 2>/dev/null &");
    }
  }

  
  private function sendQRFailNotificationEmail($projectId, $projectTitle) {
    $mailSubject = 'QR-Code generation failed!';
    $mailText = "Hello catroid.org Administrator!\n\n";
    $mailText .= "The generation of the QR-Code for the following project failed:\n\n";
    $mailText .= "---PROJECT DETAILS---\nID: ".$projectId."\nTITLE: ".$projectTitle."\n\n";
    $mailText .= "You should check this!";
  
    return($this->mailHandler->sendAdministrationMail($mailSubject, $mailText));
  }
  
  private function sendUnapprovedWordlistPerEmail() {
    $unapprovedWords = $this->badWordsFilter->getUnapprovedWords();
    $mailSubject = '';
    $unapprovedWordCount = count($unapprovedWords);
    if($unapprovedWordCount > 1) $mailSubject = 'There are '.$unapprovedWordCount.' new unapproved words!';
    else $mailSubject = 'There is '.$unapprovedWordCount.' new unapproved word!';
  
    $mailText = "Hello catroid.org Administrator!\n\n";
    $mailText .= "New word(s):\n";
    for($i = 0; $i < $unapprovedWordCount; $i++) {
      $mailText .= $unapprovedWords[$i].(($unapprovedWordCount-1 == $i) ? "" : ", ");
    }
    $mailText .= "\n\nYou should check this!\n".BASE_PATH."admin/tools/approveWords";
  
    return($this->mailHandler->sendAdministrationMail($mailSubject, $mailText));
  }
  
  private function sendUploadFailAdminEmail($formData, $fileData) {
    $mailSubject = 'Upload of a project failed!';
    $mailText = "Hello catroid.org Administrator!\n\n";
    $mailText .= "The Upload of a project failed:\n\n";
    $mailText .= "---PROJECT DETAILS---\n";
    $mailText .= "Upload Error Code: ".$this->statusCode."\n";
    $mailText .= "Upload Error Message: ".$this->answer."\n";
    if(isset($formData['projectTitle']))
      $mailText .= "Project Title: ".$formData['projectTitle']."\n";
    if(isset($formData['projectDescription']))
      $mailText .= "Project Description: ".$formData['projectDescription']."\n";
    if(isset($fileData['upload']))
      $mailText .= "Project Size: ".intval($fileData['upload']['size'])." Byte\n";
    if(isset($formData['userEmail']))
      $mailText .= "User Email: ".$formData['userEmail']."\n";
    if(isset($formData['userLanguage']))
      $mailText .= "User Language: ".$formData['userLanguage']."\n";
    if(isset($_SERVER['REMOTE_ADDR']))
      $mailText .= "User IP: ".$_SERVER['REMOTE_ADDR']."\n";
  
    $mailText .= "You should check this!";
  
    return($this->mailHandler->sendAdministrationMail($mailSubject, $mailText));
  }
  
  public function cleanup() {
    foreach($this->uploadState['remove_files'] as $file) {
      if(file_exists($file)) {
        unlink($file);
      }
    }
    foreach($this->uploadState['remove_dirs'] as $dir) {
      removeDir($dir);
    }
  
    foreach($this->uploadState['remove_project_from_db'] as $projectId) {
      pg_execute($this->dbConnection, "delete_project_by_id", array($projectId)) or
      $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    }
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
