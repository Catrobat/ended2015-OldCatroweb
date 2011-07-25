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
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class upload extends CoreAuthenticationDevice {

  public function __construct() {
    parent::__construct();
    $thumbnailDir = CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY;
  }

  public function __default() {
    $this->_upload();
  }

  public function __authenticationFailed() {
    $this->statusCode = 601;
    $this->answer = $this->errorHandler->getError('auth', 'device_auth_invalid_token');
  }

  public function _upload() {
    try {
      $newId = $this->doUpload($_POST, $_FILES, $_SERVER);
      $this->statusCode = 200;
      $this->answer = $this->languageHandler->getString('upload_successfull');
    } catch(Exception $e) {
      $this->sendUploadFailAdminEmail($_POST, $_FILES, $_SERVER);
      $this->answer = $e->getMessage();
    }
  }

  public function doUpload($formData, $fileData, $serverData) {
    try {
      $this->checkPostData($formData, $fileData);
    } catch(Exception $e) {
      $this->statusCode = 509;
      throw new Exception($e->getMessage());
    }

    try {
      $this->checkProjectSize($fileData);
    } catch(Exception $e) {
      $this->statusCode = 508;
      throw new Exception($e->getMessage());
    }

    $projectTitle = pg_escape_string($formData['projectTitle']);
    isset($formData['projectDescription']) ? $projectDescription=pg_escape_string($formData['projectDescription']) : $projectDescription=null;

    try {
      $this->checkValidProjectTitle($projectTitle);
    } catch(Exception $e) {
      $this->statusCode= 507;
      throw new Exception($e->getMessage());
    }

    try {
      $this->checkTitleForInsultingWords($projectTitle);
    } catch(Exception $e) {
      $this->statusCode = 506;
      throw new Exception($e->getMessage());
    }

    try {
      $this->checkDescriptionForInsultingWords($projectDescription);
    } catch(Exception $e) {
      $this->statusCode = 505;
      throw new Exception($e->getMessage());
    }

    $projectName = md5(uniqid(time()));
    $uploadFile = $projectName.PROJECTS_EXTENTION;
    $uploadDir = CORE_BASE_PATH.'/'.PROJECTS_DIRECTORY.$uploadFile;
    $uploadIp = $serverData['REMOTE_ADDR'];
    isset($formData['userEmail']) ? $uploadEmail=$formData['userEmail'] : $uploadEmail=null;
    isset($formData['userLanguage']) ? $uploadLanguage=$formData['userLanguage'] : $uploadLanguage=null;

    try {
      $fileSize = $this->copyProjectToDirectory($fileData['upload']['tmp_name'], $uploadDir);
    } catch(Exception $e) {
      $this->statusCode = 504;
      throw new Exception($e->getMessage());
    }

    try {
      $newId = $this->insertProjectIntoDatabase($projectTitle, $projectDescription, $uploadFile, $uploadIp, $uploadEmail, $uploadLanguage, $fileSize);
    } catch(Exception $e) {
      $this->statusCode = 503;
      $this->removeProjectFromFilesystem($uploadDir);
      throw new Exception($e->getMessage());
    }

    try {
      $this->renameProjectFile($uploadDir, $newId);
    } catch(Exception $e) {
      $this->statusCode = 502;
      $projectFile = CORE_BASE_PATH.PROJECTS_DIRECTORY.$newId.PROJECTS_EXTENTION;
      $this->removeProjectFromDatabase($newId);
      $this->removeProjectFromFilesystem($uploadDir);
      $this->removeProjectFromFilesystem(CORE_BASE_PATH.PROJECTS_DIRECTORY.$newId.PROJECTS_EXTENTION, $newId);
      throw new Exception($e->getMessage());
    }

    $projectDir = CORE_BASE_PATH.PROJECTS_DIRECTORY;
    $projectFile = $projectDir.$newId.PROJECTS_EXTENTION;
    if(!isset($formData['fileChecksum']) || !$formData['fileChecksum']) {
      $this->statusCode = 510;
      $this->removeProjectFromDatabase($newId);
      $this->removeProjectFromFilesystem($projectFile, $newId);
      throw new Exception($this->errorHandler->getError('upload', 'missing_post_file_checksum'));
    }
    $fileChecksum = md5_file($projectFile);

    try {
      $this->checkFileChecksum($fileChecksum, $formData['fileChecksum']);
    } catch(Exception $e) {
      $this->statusCode = 501;
      $this->removeProjectFromDatabase($newId);
      $this->removeProjectFromFilesystem($projectFile, $newId);
      throw new Exception($e->getMessage());
    }

    try {
      $this->getQRCode($newId, $projectTitle);
    } catch(Exception $e) {
      $this->sendQRFailNotificationEmail($newId, $projectTitle);
    }
    
    try {
      $this->unzipUploadedFile($fileData['upload']['tmp_name'], $projectDir, $newId);
    } catch(Exception $e) {
      $this->statusCode = 511;
      $this->removeProjectFromDatabase($newId);
      $this->removeProjectFromFilesystem($projectFile, $newId);
      throw new Exception($e->getMessage());
    }
    
    $this->unzipThumbnailFromUploadedFile($fileData['upload']['tmp_name'], $projectDir, $newId);

    $unapprovedWords = $this->badWordsFilter->getUnapprovedWords();
    if($unapprovedWords) {
      $this->badWordsFilter->mapUnapprovedWordsToProject($newId);
      $this->sendUnapprovedWordlistPerEmail();
    }

    $this->statusCode = 200;
    $this->projectId = $newId;
    $this->fileChecksum = $fileChecksum;
    return $newId;
  }

  public function copyProjectToDirectory($tmpFile, $uploadDir) {
    if(@copy($tmpFile, $uploadDir)) {
      return filesize($uploadDir);
    } else {
      throw new Exception($this->errorHandler->getError('upload', 'copy_failed'));
    }
  }

  public function removeProjectFromFilesystem($projectFile, $projectId = -1) {
    @unlink($projectFile);
    if($projectId > 0) {
      @unlink(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$projectId.PROJECTS_THUMBNAIL_EXTENTION_SMALL);
      @unlink(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$projectId.PROJECTS_THUMBNAIL_EXTENTION_LARGE);
      @unlink(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$projectId.PROJECTS_THUMBNAIL_EXTENTION_ORIG);
      @unlink(CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$projectId.PROJECTS_QR_EXTENTION);
      removeDir(CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY.$projectId);
    }
    return true;
  }

  public function renameProjectFile($oldName, $projectId) {
    $newFileName = $projectId.PROJECTS_EXTENTION;
    $newName = CORE_BASE_PATH.PROJECTS_DIRECTORY.$newFileName;
    if(!@rename($oldName, $newName)) {
      throw new Exception($this->errorHandler->getError('upload', 'rename_failed'));
    }
    $query = "EXECUTE set_project_new_filename('$newFileName', '$projectId');";
    if(!@pg_query($this->dbConnection, $query)) {
      throw new Exception($this->errorHandler->getError('upload', 'rename_failed', pg_last_error($this->dbConnection)));
    }
    return true;
  }

  public function removeProjectFromDatabase($projectId) {
    $query = "EXECUTE delete_project_by_id('$projectId');";
    @pg_query($this->dbConnection, $query);
    return true;
  }

  public function checkFileChecksum($uploadChecksum, $clientChecksum) {
    if(strcmp(strtolower($uploadChecksum), strtolower($clientChecksum)) != 0) {
      throw new Exception($this->errorHandler->getError('upload', 'invalid_file_checksum'));
    }
    return true;
  }

  public function extractCatroidVersion($xmlString) {
    $xml = simplexml_load_string($xmlString);
    if(!$xml) {
      throw new Exception($this->errorHandler->getError('upload', 'invalid_project_xml'));
    }
    $attributes = $xml->attributes();
    isset($attributes["versionName"]) && $attributes["versionName"] ? $versionName = strval($attributes["versionName"]) : $versionName = null;
    isset($attributes["versionCode"]) && $attributes["versionCode"] ? $versionCode = strval($attributes["versionCode"]) : $versionCode = null;

    if(!$versionName || !$versionCode) {
      $versionCode = null;
      $versionName = null;
      foreach($xml->children() as $child) {
        if(strcmp(strval($child->getName()), 'versionName') == 0) {
          $versionName = strval($child);
        } elseif(strcmp(strval($child->getName()), 'versionCode') == 0) {
          $versionCode = strval($child);
        }
      }
    }
    
    if(!$versionName || !$versionCode) {
      $versionCode = 4;
      $versionName = '&lt; 0.4.3d';
    }
    
    return(array("versionName"=>$versionName, "versionCode"=>$versionCode));
  }

  public function saveVersionInfo($projectId, $versionCode, $versionName) {
    $query = "EXECUTE save_catroid_version_info('$projectId', '$versionCode', '$versionName');";
    $result = @pg_query($this->dbConnection, $query);
    if($result) {
      @pg_free_result($result);
      return true;
    } else {
      return false;
    }
    return false;
  }

  private function insertProjectIntoDatabase($projectTitle, $projectDescription, $uploadFile, $uploadIp, $uploadEmail, $uploadLanguage, $fileSize) {
    $this->session->userLogin_userId ? $userId=$this->session->userLogin_userId : $userId=0;
    $query = "EXECUTE insert_new_project('$projectTitle', '$projectDescription', '$uploadFile', '$uploadIp', '$uploadEmail', '$uploadLanguage', '$fileSize', '$userId');";
    $result = @pg_query($this->dbConnection, $query);
    if(!$result) {
      throw new Exception($this->errorHandler->getError('upload', 'sql_insert_failed', pg_last_error($this->dbConnection)));
    }
    $row = pg_fetch_assoc($result);
    $insertId = $row['id'];
    @pg_free_result($result);
    return $insertId;
  }

  private function checkPostData($formData, $fileData) {
    if(!isset($formData['projectTitle']) || !$formData['projectTitle'] || !isset($fileData['upload']['tmp_name']) || $fileData['upload']['error'] != 0) {
      throw new Exception($this->errorHandler->getError('upload', 'missing_post_data'));
    }
    return true;
  }

  private function checkProjectSize($fileData) {
    if(intval($fileData['upload']['size']) > PROJECTS_MAX_SIZE) {
      throw new Exception($this->errorHandler->getError('upload', 'project_exceed_filesize_limit'));
    }
    return true;
  }

  private function checkValidProjectTitle($title) {
    if(strcmp($title, $this->languageHandler->getString('default_project_name')) == 0) {
      throw new Exception($this->errorHandler->getError('upload', 'project_title_default'));
    }
    return true;
  }

  private function checkTitleForInsultingWords($title) {
    if($this->badWordsFilter->areThereInsultingWords($title)) {
      throw new Exception($this->errorHandler->getError('upload', 'insulting_words_in_project_title'));
    }
    return true;
  }

  private function checkDescriptionForInsultingWords($description) {
    if($description && $this->badWordsFilter->areThereInsultingWords($description)) {
      throw new Exception($this->errorHandler->getError('upload', 'insulting_words_in_project_description'));
    }
    return true;
  }

  private function getQRCode($projectId, $projectTitle) {
    $urlToEncode = urlencode(BASE_PATH.'catroid/download/'.$projectId.PROJECTS_EXTENTION.'?fname='.urlencode($projectTitle));
    $serviceUrl = PROJECTS_QR_SERVICE_URL.$urlToEncode;
    $destinationPath = CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$projectId.PROJECTS_QR_EXTENTION;
    $qrImageHandle = @imagecreatefrompng($serviceUrl);
    if(!$qrImageHandle) {
      throw new Exception();
    }
    if(!@imagepng($qrImageHandle, $destinationPath, 9)) {
      throw new Exception();
    }
    @imagedestroy($qrImageHandle);
    chmod($destinationPath, 0666);
    return true;
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
    $mailText .= "You should check this! <a href='".BASE_PATH."admin/tools/approveWords'>follow me!</a>";

    return($this->mailHandler->sendAdministrationMail($mailSubject, $mailText));
  }

  private function sendUploadFailAdminEmail($formData, $fileData, $serverData) {
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
    if(isset($serverData['REMOTE_ADDR']))
    $mailText .= "User IP: ".$serverData['REMOTE_ADDR']."\n";

    $mailText .= "You should check this!";

    return($this->mailHandler->sendAdministrationMail($mailSubject, $mailText));
  }

  private function unzipUploadedFile($filename, $projectDir, $projectId) { // unzips thumbnail only
    $unzipDir = CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY.$projectId;
    mkdir(CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY.$projectId);
    mkdir(CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY.$projectId."/images");
    mkdir(CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY.$projectId."/sounds");
    chmod(CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY.$projectId, 0777);
    chmod(CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY.$projectId."/images", 0777);
    chmod(CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY.$projectId."/sounds", 0777);

    $zip = zip_open($projectDir.$projectId.PROJECTS_EXTENTION);
    while ($zip_entry = zip_read($zip)) {
      $filename = zip_entry_name($zip_entry);
      if (preg_match("/images\//", $filename)) {
        $image = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
        if ($image)
        $this->saveFile($unzipDir, $filename, $image, zip_entry_filesize($zip_entry));
      }
      if (preg_match("/sounds\//", $filename)) {
        $sound = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
        if ($sound)
        $this->saveFile($unzipDir, $filename, $sound, zip_entry_filesize($zip_entry));
      }
      if (preg_match("/\.spf/", $filename)) {
        $spf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
        if ($spf) {
          $this->saveFile($unzipDir, $filename, $spf, zip_entry_filesize($zip_entry));
          try {
            $catroidVersion = $this->extractCatroidVersion($spf);
          } catch(Exception $e) {
            throw new Exception($e->getMessage());
          }
          $this->saveVersionInfo($projectId, $catroidVersion['versionCode'], $catroidVersion['versionName']);
        }
      }
      if ($filename == "thumbnail.jpg" || $filename == "thumbnail.png") {
        $spf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
        if ($spf)
        $this->saveFile($unzipDir, $filename, $spf, zip_entry_filesize($zip_entry));
      }
    }
    zip_close($zip);
  }

  private function unzipThumbnailFromUploadedFile($filename, $projectDir, $projectId) { // unzips thumbnail only
    $unzipDir = CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY;
    $zip = zip_open($projectDir.$projectId.PROJECTS_EXTENTION);
    while ($zip_entry = zip_read($zip)) {
      $filename = zip_entry_name($zip_entry);
      if (preg_match("/thumbnail\./", $filename) || preg_match("/images\/thumbnail\./", $filename)) {
        $thumbnail = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
        if ($thumbnail) {
          $this->saveThumbnail($projectId, $thumbnail);
        }
      }
    }
    zip_close($zip);
  }

  private function saveThumbnail($filename, $thumbnail) {
    $thumbnailDir = CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY;

    $thumbImage = imagecreatefromstring($thumbnail);
    if ($thumbImage) {
      $w = imagesx($thumbImage);
      $h = imagesy($thumbImage);

      $wsmall = 0; $hsmall = 0; $hsmallopt = intval(240*$h/$w);
      $wlarge = 0; $hlarge = 0; $hlargeopt = intval(480*$h/$w);
       
      // thumbnail with original filesize
      imagejpeg($thumbImage, $thumbnailDir.$filename.PROJECTS_THUMBNAIL_EXTENTION_ORIG, 100);

      // small thumbnail for preview 240x400
      $smallImage = imagecreatetruecolor(240, $hsmallopt);
      imagecopyresampled($smallImage, $thumbImage, 0, 0, 0, 0, 240, $hsmallopt, $w, $h);
      imagejpeg($smallImage, $thumbnailDir.$filename.PROJECTS_THUMBNAIL_EXTENTION_SMALL, 50);

      // large thumbnail for details-view 480x800
      $newImage = imagecreatetruecolor(480, $hlargeopt);
      imagecopyresampled($newImage, $thumbImage, 0, 0, 0, 0, 480, $hlargeopt, $w, $h);
      imagejpeg($newImage, $thumbnailDir.$filename.PROJECTS_THUMBNAIL_EXTENTION_LARGE, 50);
    }
  }

  private function saveFile($targetDir, $filename, $filecontent, $filesize) {
    $fp = fopen($targetDir."/".$filename, "wb+");
    if ($fp) {
      fwrite($fp, $filecontent, $filesize);
      fclose($fp);
    }
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
