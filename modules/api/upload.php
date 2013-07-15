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

class upload extends CoreAuthenticationDevice {
  private $uploadState;

  public function __construct() {
    parent::__construct();

    $this->uploadState = array('remove_files' => array(),
        'remove_dirs' => array(),
        'remove_project_from_db' => array());
  }

  public function __default() {
    $this->doUpload($_POST, $_FILES);
  }

  public function __authenticationFailed() {
    $this->statusCode = STATUS_CODE_AUTHENTICATION_FAILED;
    $this->answer = $this->errorHandler->getError('auth', 'device_auth_invalid_token');
  }

  public function doUpload($formData, $fileData) {
    try {
      $fileData = $this->checkForAndPrepareFTPUpload($formData, $fileData);

      $this->checkFileData($formData, $fileData);
      $this->checkProjectSize($fileData);
      $this->checkFileChecksum($formData, $fileData['upload']['tmp_name']);

      $tempFilenameUnique = $this->getUniqueFilename();
      $fileSize = $this->copyProjectToDirectory($fileData['upload']['tmp_name'],
          CORE_BASE_PATH . PROJECTS_DIRECTORY . $tempFilenameUnique);
      $this->unzipProjectFiles(CORE_BASE_PATH . PROJECTS_DIRECTORY . $tempFilenameUnique,
          CORE_BASE_PATH . PROJECTS_UNZIPPED_DIRECTORY . $tempFilenameUnique);

      $xmlFile = $this->getProjectXmlFile(CORE_BASE_PATH . PROJECTS_UNZIPPED_DIRECTORY . $tempFilenameUnique . '/');
      $projectInformation = $this->getProjectInformation($xmlFile, $formData);
      $this->checkValidCatrobatVersion($projectInformation['versionName']);
      $this->checkValidCatrobatCode($projectInformation['versionCode']);
      $this->checkValidProjectTitle($projectInformation['projectTitle']);
      $this->checkTitleForInsultingWords($projectInformation['projectTitle']);
      $this->checkDescriptionForInsultingWords($projectInformation['projectDescription']);
      
      
      $projectIdArray = $this->updateOrInsertProjectIntoDatabase($projectInformation['projectTitle'],
          $projectInformation['projectDescription'], $projectInformation['uploadIp'],
          $projectInformation['uploadLanguage'], $fileSize, $projectInformation['versionName'],
          $projectInformation['versionCode']);
      
      $projectId = $projectIdArray['id'];
      
      $this->checkAndSetLicensesAndRemixInfo(CORE_BASE_PATH . PROJECTS_DIRECTORY . $tempFilenameUnique, $xmlFile, $projectId, $projectIdArray['update']);
      
      if(!$projectIdArray['update'])
        $this->insertRemixOfIDIntoDatabase($projectId, $xmlFile);

      $this->renameProjectFile(CORE_BASE_PATH . PROJECTS_DIRECTORY . $tempFilenameUnique, $projectId);
      $this->renameUnzipDirectory(CORE_BASE_PATH . PROJECTS_UNZIPPED_DIRECTORY . $tempFilenameUnique,
          CORE_BASE_PATH . PROJECTS_UNZIPPED_DIRECTORY . $projectId);
      $this->extractThumbnail(CORE_BASE_PATH . PROJECTS_UNZIPPED_DIRECTORY . $projectId . '/', $projectId);

      $unapprovedWords = $this->badWordsFilter->getUnapprovedWords();
      if($unapprovedWords) {
        $this->badWordsFilter->mapUnapprovedWordsToProject($projectId);
        $this->sendUnapprovedWordlistPerEmail();
      }

      $this->buildNativeApp($projectId);
      
      $this->projectId = $projectId;
      $this->statusCode = STATUS_CODE_OK;
      $this->answer = $this->languageHandler->getString('upload_successfull');

      $this->loadModule('common/userFunctions');
      $this->token = $this->userFunctions->updateAuthenticationToken();
    } catch(Exception $e) {
      $this->sendUploadFailAdminEmail($_POST, $_FILES);
      $this->cleanup();
      $this->statusCode = $e->getCode();
      $this->answer = $e->getMessage();
    }
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
      $this->query("delete_project_by_id", array($projectId));
    }
  }

  public function __destruct() {
    parent::__destruct();
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

  private function checkFileData($formData, $fileData) {
    if(!isset($fileData['upload']['tmp_name']) ||
        !file_exists($fileData['upload']['tmp_name']) ||
        ($fileData['upload']['error'] !== UPLOAD_ERR_OK)) {
      throw new Exception($this->errorHandler->getError('upload', 'missing_file_data'), STATUS_CODE_UPLOAD_MISSING_DATA);
    }
    return true;
  }

  private function checkProjectSize($fileData) {
    if(isset($fileData['upload']['size']) && intval($fileData['upload']['size']) > PROJECTS_MAX_SIZE) {
      throw new Exception($this->errorHandler->getError('upload', 'project_exceed_filesize_limit'), STATUS_CODE_UPLOAD_EXCEEDING_FILESIZE);
    }
    return true;
  }

  private function checkFileChecksum($formData, $uploadedFile) {
    if(!isset($formData['fileChecksum']) || !$formData['fileChecksum']) {
      throw new Exception($this->errorHandler->getError('upload', 'missing_post_file_checksum'), STATUS_CODE_UPLOAD_MISSING_CHECKSUM);
    }

    $fileChecksum = md5_file($uploadedFile);
    if(strcmp(strtolower($formData['fileChecksum']), strtolower($fileChecksum)) != 0) {
      throw new Exception($this->errorHandler->getError('upload', 'invalid_file_checksum'), STATUS_CODE_UPLOAD_INVALID_CHECKSUM);
    }
  }

  private function getUniqueFilename() {
    $filename = md5(uniqid(time()));
    while(file_exists(CORE_BASE_PATH . PROJECTS_DIRECTORY . $filename)) {
      $filename = md5(uniqid(time()));
    }
    return $filename;
  }

  private function copyProjectToDirectory($tmpFile, $uploadDir) {
    if(copy($tmpFile, $uploadDir)) {
      chmod($uploadDir, 0666);
      $this->setState('remove_files', $uploadDir);
      return filesize($uploadDir);
    } else {
      throw new Exception($this->errorHandler->getError('upload', 'copy_failed'), STATUS_CODE_UPLOAD_COPY_FAILED);
    }
  }

  private function unzipProjectFiles($zipFile, $destDir) {
    if(!unzipFile($zipFile, $destDir)) {
      throw new Exception($this->errorHandler->getError('upload', 'invalid_project_zip'), STATUS_CODE_UPLOAD_UNZIP_FAILED);
    }
    chmodDir($destDir, 0666, 0777);
    $this->setState('remove_dirs', $destDir);
  }

  private function getProjectXmlFile($unzipDir) {
    $dirHandler = opendir($unzipDir);
    while(($file = readdir($dirHandler)) !== false) {
      $details = pathinfo($file);
      if(isset($details['extension']) && file_exists($unzipDir . $file) && (strcmp($details['extension'], 'spf') == 0 ||
          strcmp($details['extension'], 'xml') == 0 || strcmp($details['extension'], 'catroid') == 0)) {
        return $unzipDir.$file;
      }
    }
    throw new Exception($this->errorHandler->getError('upload', 'project_xml_not_found'), STATUS_CODE_UPLOAD_MISSING_XML);
  }

  private function getProjectInformation($xmlFile, $formData) {
    libxml_use_internal_errors(true);
    $xml = simplexml_load_file($xmlFile);
    if(!$xml) {
      throw new Exception($this->errorHandler->getError('upload', 'invalid_project_xml'), STATUS_CODE_UPLOAD_INVALID_XML);
    }

    $node = $xml->children();
    $versionName = current($node[0]->applicationVersion);
    $versionCode = current($node[0]->catrobatLanguageVersion);
    $projectTitle = current($node[0]->programName);
    $projectDescription = current($node[0]->description);
    
    // workaround for temporary xml file
    if(!$versionName) {
      $versionName = current($node->applicationVersion);
    }
    if(!$versionCode) {
      $versionCode = current($node->catrobatLanguageVersion);
    }
    if(!$projectTitle) {
      $projectTitle = current($node->programName);
    }
    if(!$projectDescription) {
      $projectDescription = current($node->description);
    }
    
    if(!$versionName || !$versionCode) {
      $versionCode = 0.0;
      $versionName = "0.0.0beta";
    } else if(stristr($versionName, "-")) {
      $versionName = substr($versionName, 0, strpos($versionName, "-"));
    }

    if(!$projectTitle) {
      $projectTitle = pg_escape_string($projectTitle); 
      $projectTitle = ((isset($formData['projectTitle']) && $formData['projectTitle'] != "") ? checkUserInput($formData['projectTitle']) : "");      
      if($projectTitle == "") {
        throw new Exception($this->errorHandler->getError('upload', 'missing_project_title'), STATUS_CODE_UPLOAD_MISSING_PROJECT_TITLE);
      }
    }
    if(!$projectDescription) {
      $projectDescription = pg_escape_string($projectDescription); 
      $projectDescription = ((isset($formData['projectDescription'])) ? checkUserInput($formData['projectDescription']) : "");
    }

    $uploadIp = (isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'');
    $uploadLanguage = ((isset($formData['userLanguage'])) ? checkUserInput($formData['userLanguage']) : 'en');

    return(array(
        "projectTitle" => $projectTitle,
        "projectDescription" => $projectDescription,
        "versionName" => $versionName,
        "versionCode" => $versionCode,
        "uploadIp" => $uploadIp,
        "uploadLanguage" => $uploadLanguage
    ));
  }

  private function checkValidCatrobatVersion($versionName) {
    if(version_compare($versionName, MIN_CATROBAT_VERSION) < 0) {
      throw new Exception($this->errorHandler->getError('upload', 'old_catrobat_version'), STATUS_CODE_UPLOAD_OLD_CATROBAT_VERSION);
    }
  }
  
  private function checkValidCatrobatCode($versionCode) {
    if(floatval($versionCode) < floatval(MIN_CATROBAT_LANGUAGE_VERSION)) {
      throw new Exception($this->errorHandler->getError('upload', 'old_catrobat_language'), STATUS_CODE_UPLOAD_OLD_CATROBAT_LANGUAGE);
    }
  }

  private function checkValidProjectTitle($title) {
    if(strcmp($title, $this->languageHandler->getString('default_project_name')) == 0) {
      throw new Exception($this->errorHandler->getError('upload', 'project_title_default'), STATUS_CODE_UPLOAD_DEFAULT_PROJECT_TITLE);
    }
  }

  private function checkTitleForInsultingWords($title) {
    if($this->badWordsFilter->areThereInsultingWords($title)) {
      throw new Exception($this->errorHandler->getError('upload', 'insulting_words_in_project_title'), STATUS_CODE_UPLOAD_RUDE_PROJECT_TITLE);
    }
  }

  private function checkDescriptionForInsultingWords($description) {
    if($description && $this->badWordsFilter->areThereInsultingWords($description)) {
      throw new Exception($this->errorHandler->getError('upload', 'insulting_words_in_project_description'), STATUS_CODE_UPLOAD_RUDE_PROJECT_DESCRIPTION);
    }
  }

  private function updateOrInsertProjectIntoDatabase($projectTitle, $projectDescription, $uploadIp, $uploadLanguage, $fileSize, $versionName, $versionCode) {
    $userId = (($this->session->userLogin_userId) ? $this->session->userLogin_userId : 0);

    $result = $this->query("does_project_already_exist", array($projectTitle, $userId));
    if(pg_num_rows($result) == 1) {
      $row = pg_fetch_assoc($result);
      $updateId = $row['id'];
      pg_free_result($result);

      $this->query("update_project", array($projectDescription, $uploadIp, $fileSize, $versionName, $versionCode, $updateId));
   
      return(array(
          "id" => $updateId,
          "update" => true
      ));
    } else {
      pg_free_result($result);

      $result = $this->query("insert_new_project", array($projectTitle, $projectDescription, $uploadIp, $uploadLanguage, $fileSize, $versionName, $versionCode, $userId));
      $row = pg_fetch_assoc($result);
      $insertId = $row['id'];
      pg_free_result($result);

      $this->setState('remove_project_from_db', $insertId);
      
      return(array(
          "id" => $insertId,
          "update" => false
      ));
    }
  }
  
  private function checkAndSetLicensesAndRemixInfo($zipFile, $xmlFile, $projectId, $update) {
    $zip = new ZipArchive();
    $zip->open($zipFile);
  
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->load($xmlFile);
  
  
    if(!$this->checkHeaderTag($dom)) {
      $nodes = $dom->getElementsByTagName('*');
      $header = $dom->createElement('header');
      $dom->firstChild->insertBefore($header, $nodes->item(1));
  
      $mediaLicense = $dom->createElement('mediaLicense');
      $programLicense = $dom->createElement('programLicense');
      $remixOf = $dom->createElement('remixOf');
      $url = $dom->createElement('url');
      $userHandle = $dom->createElement('userHandle');
  
      $header->appendChild($mediaLicense);
      $header->appendChild($programLicense);
      $header->appendChild($remixOf);
      $header->appendChild($url);
      $header->appendChild($userHandle);
    }
  
    $header = $dom->getElementsByTagName('header');
    $mediaLicense = $dom->getElementsByTagName('mediaLicense');
    $programLicense = $dom->getElementsByTagName('programLicense');
    $remixOf = $dom->getElementsByTagName('remixOf');
    $url = $dom->getElementsByTagName('url');
    $userHandle = $dom->getElementsByTagName('userHandle');
  
    if($header->length == 0) {
      $mediaLicense = $dom->getElementsByTagName('MediaLicense');
      $programLicense = $dom->getElementsByTagName('ProgramLicense');
      $remixOf = $dom->getElementsByTagName('RemixOf');
      $url = $dom->getElementsByTagName('URL');
      $userHandle = $dom->getElementsByTagName('UserHandle');
    }
  
    foreach($mediaLicense as $value) {
      if($value->nodeValue != '' && $value->nodeValue != PROJECT_MEDIA_LICENSE)
        throw new Exception($this->errorHandler->getError('upload', 'invalid_media_license'), STATUS_CODE_UPLOAD_INVALID_MEDIA_LICENSE);
      $value->nodeValue = PROJECT_MEDIA_LICENSE;
    }
  
    foreach($programLicense as $value) {
      if($value->nodeValue != '' && $value->nodeValue != PROJECT_PROGRAM_LICENSE)
        throw new Exception($this->errorHandler->getError('upload', 'invalid_program_license'), STATUS_CODE_UPLOAD_INVALID_PROGRAM_LICENSE);
      $value->nodeValue = PROJECT_PROGRAM_LICENSE;
    }
  
    foreach($url as $url_value) {
      if($url_value->nodeValue == '') {
        $url_value->nodeValue = PROJECT_URL_TEXT . $projectId;
        foreach($userHandle as $user)
          $user->nodeValue = $this->session->userLogin_userNickname;
      }
      else {
        foreach($remixOf as $remix_value) {
          if(!$update)
            $remix_value->nodeValue = $url_value->nodeValue;
          else {
            $result = $this->query("get_remixof_id", array($projectId));
            while($row = pg_fetch_assoc($result)) {
              $remix_value->nodeValue = PROJECT_URL_TEXT . $row['remixof'];
            }            
          }
          $url_value->nodeValue = PROJECT_URL_TEXT . $projectId;
          foreach($userHandle as $user)
            $user->nodeValue = $this->session->userLogin_userNickname;
        }
      }
    }
  
    $dom->save($xmlFile);
    $filename = pathinfo($xmlFile);
    $zip->addFile($xmlFile, $filename['basename']);
    $zip->close();
    chmod($zipFile, 0777);
  }
  
  private function checkHeaderTag($dom) {
    $header = $dom->getElementsByTagName('header');
    $Header = $dom->getElementsByTagName('Header');
  
    if($header->length == 0 && $Header->length == 0) {
      return false;
    }
  
    return true;
  }
  
  private function insertRemixOfIDIntoDatabase($projectId, $xmlFile) {
    $dom = new DOMDocument();
    $dom->load($xmlFile);
    
    $remixOf = $dom->getElementsByTagName('remixOf');
    if($remixOf->length == 0)
      $remixOf = $dom->getElementsByTagName('RemixOf');
    
    foreach($remixOf as $value) {
      if($value->nodeValue != '') {
        $remixID = intval(str_replace(PROJECT_URL_TEXT, "", $value->nodeValue));
        $this->query("set_remixof_id", array($remixID, $projectId));
      }
    }
  }

  private function renameProjectFile($oldName, $projectId) {
    $newName = CORE_BASE_PATH.PROJECTS_DIRECTORY . $projectId . PROJECTS_EXTENSION;

    if(file_exists($newName)) {
      unlink($newName);
    }
    if(!rename($oldName, $newName)) {
      throw new Exception($this->errorHandler->getError('upload', 'rename_failed'), STATUS_CODE_UPLOAD_RENAME_FAILED);
    }
    $this->setState('remove_files', $newName, $oldName);

    $this->query("set_project_new_filename", array($projectId.PROJECTS_EXTENSION, $projectId));
  }

  private function renameUnzipDirectory($oldName, $newName) {
    removeDir($newName);
    if(!rename($oldName, $newName)) {
      throw new Exception($this->errorHandler->getError('upload', 'rename_failed'), STATUS_CODE_UPLOAD_RENAME_FAILED);
    }
    $this->setState('remove_dirs', $newName, $oldName);
  }

  private function extractThumbnail($unzipDir, $projectId) {
    $automaticThumbnail = $unzipDir . 'automatic_screenshot.png';
    $manualThumbnail = $unzipDir . 'manual_screenshot.png';
    
    $thumbnail = $automaticThumbnail;
    if(file_exists($manualThumbnail)) {
      $thumbnail = $manualThumbnail;
    }

    if(is_file($thumbnail)) {
      $thumbImage = imagecreatefrompng($thumbnail);
      if($thumbImage) {
        if(!$this->saveThumbnail($thumbImage, $projectId, 0, PROJECTS_THUMBNAIL_EXTENSION_ORIG)) {
          throw new Exception($this->errorHandler->getError('upload', 'save_thumbnail_failed'), STATUS_CODE_UPLOAD_SAVE_THUMBNAIL_FAILED);
        }
        if(!$this->saveThumbnail($thumbImage, $projectId, 160, PROJECTS_THUMBNAIL_EXTENSION_SMALL)) {
          throw new Exception($this->errorHandler->getError('upload', 'save_thumbnail_failed'), STATUS_CODE_UPLOAD_SAVE_THUMBNAIL_FAILED);
        }
        if(!$this->saveThumbnail($thumbImage, $projectId, 480, PROJECTS_THUMBNAIL_EXTENSION_LARGE)) {
          throw new Exception($this->errorHandler->getError('upload', 'save_thumbnail_failed'), STATUS_CODE_UPLOAD_SAVE_THUMBNAIL_FAILED);
        }
      } else {
        throw new Exception($this->errorHandler->getError('upload', 'save_thumbnail_failed'), STATUS_CODE_UPLOAD_SAVE_THUMBNAIL_FAILED);
      }
    }
  }

  private function saveThumbnail($thumbImage, $projectId, $desiredWidth, $extension) {
    if($thumbImage) {
      $width = imagesx($thumbImage);
      $height = imagesy($thumbImage);

      if($desiredWidth == 0) {
        $desiredWidth = $width;
      }
      $desiredHeight = intval($desiredWidth * $height / $width);

      $thumbnail = imagecreatetruecolor($desiredWidth, $desiredHeight);
      if(!$thumbnail) {
        return false;
      }
      if(!imagecopyresampled($thumbnail, $thumbImage, 0, 0, 0, 0, $desiredWidth, $desiredHeight, $width, $height)) {
        return false;
      }
      if(!imagepng($thumbnail, CORE_BASE_PATH . PROJECTS_THUMBNAIL_DIRECTORY . $projectId . $extension, 7)) {
        return false;
      }

      $this->setState('remove_files', CORE_BASE_PATH . PROJECTS_THUMBNAIL_DIRECTORY . $projectId . $extension);
      return true;
    }
    return false;
  }

  private function sendUnapprovedWordlistPerEmail() {
    $unapprovedWords = $this->badWordsFilter->getUnapprovedWords();
    $mailSubject = '';
    $unapprovedWordCount = count($unapprovedWords);
    if($unapprovedWordCount > 1) {
      $mailSubject = 'There are ' . $unapprovedWordCount . ' new unapproved words!';
    } else {
      $mailSubject = 'There is ' . $unapprovedWordCount . ' new unapproved word!';
    }

    $mailText = "Hello ".APPLICATION_URL_TEXT." Administrator!\n\n";
    $mailText .= "New word(s):\n";
    for($i = 0; $i < $unapprovedWordCount; $i++) {
      $mailText .= $unapprovedWords[$i].(($unapprovedWordCount-1 == $i) ? "" : ", ");
    }
    $mailText .= "\n\nYou should check this!\n" . BASE_PATH . "admin/tools/approveWords";

    return($this->mailHandler->sendAdministrationMail($mailSubject, $mailText));
  }

  private function buildNativeApp($projectId) {
    $pythonHandler = CORE_BASE_PATH . PROJECTS_APP_BUILDING_SRC . "nativeAppBuilding/src/handle_project.py";
    $projectFile = CORE_BASE_PATH . PROJECTS_DIRECTORY . $projectId . PROJECTS_EXTENSION;
    $catroidSource = CORE_BASE_PATH . PROJECTS_APP_BUILDING_SRC;
    $outputFolder = CORE_BASE_PATH . PROJECTS_DIRECTORY;

    if(is_dir(CORE_BASE_PATH . PROJECTS_APP_BUILDING_SRC)) {
      shell_exec("python2.6 $pythonHandler $projectFile $catroidSource $projectId $outputFolder > /dev/null 2>/dev/null &");
    }
  }

  private function sendUploadFailAdminEmail($formData, $fileData) {
    $mailSubject = 'Upload of a project failed!';
    $mailText = "Hello ".APPLICATION_URL_TEXT." Administrator!\n\n";
    $mailText .= "The Upload of a project failed:\n\n";
    $mailText .= "---PROJECT DETAILS---\n";
    $mailText .= "Upload Error Code: " . $this->statusCode . "\n";
    $mailText .= "Upload Error Message: " . $this->answer . "\n";
    $mailText .= (isset($formData['projectTitle']) ? "Project Title: " . $formData['projectTitle'] . "\n" : "");
    $mailText .= (isset($formData['projectDescription']) ? "Project Description: " . $formData['projectDescription'] . "\n" : "");
    $mailText .= (isset($fileData['upload']) ? "Project Size: " . intval($fileData['upload']['size']) . " Byte\n" : "");
    $mailText .= (isset($formData['userEmail']) ? "User Email: " . $formData['userEmail'] . "\n" : "");
    $mailText .= (isset($formData['userLanguage']) ? "User Language: " . $formData['userLanguage'] . "\n" : "");
    $mailText .= (isset($_SERVER['REMOTE_ADDR']) ? "User IP: " . $_SERVER['REMOTE_ADDR'] . "\n" : "");
    $mailText .= "You should check this!";

    return($this->mailHandler->sendAdministrationMail($mailSubject, $mailText));
  }

  private function query($query, $parameter=null) {
    $result = null;
    if(is_array($parameter)) {
      $result = pg_execute($this->dbConnection, $query, $parameter);
    } else {
      $result = pg_execute($this->dbConnection, $query);
    }

    if(!$result) {
      throw new Exception($this->errorHandler->getError('db', 'query_failed', pg_last_error()), STATUS_CODE_SQL_QUERY_FAILED);
    }
    return $result;
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
}
?>
