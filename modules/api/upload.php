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

  //Strict Standards: Redefining already defined constructor for class
  public function _upload() {
    $this->versionName = "";
    $this->versionCode = 0;
    $newId = $this->doUpload($_POST, $_FILES, $_SERVER);
    if($newId > 0) {
      $this->answer = 'Upload successfull!';
    } else {
      $this->sendUploadFailAdminEmail($_POST, $_FILES, $_SERVER);
    }
  }

  public function checkValidProjectTitle($title) {
    if(strcmp($title, PROJECT_DEFAULT_SAVEFILE_NAME) == 0) {
      return false;
    } else {
      return true;
    }
  }

  public function doUpload($formData, $fileData, $serverData) {
    $fileInfo = array("", "");
    $statusCode = 500;
    $fileChecksum = null;
    $newId = 0;
    $answer = null;
    $projectDescription = '';
    if(isset($formData['projectTitle']) && $formData['projectTitle'] && isset($fileData['upload']['tmp_name']) && $fileData['upload']['error'] == 0) {
      if(intval($fileData['upload']['size']) <= PROJECTS_MAX_SIZE) {
        $projectTitle = pg_escape_string($formData['projectTitle']);
        if(($this->checkValidProjectTitle($projectTitle))){
          if(!$this->badWordsFilter->areThereInsultingWords($projectTitle)) {
            if(isset($formData['projectDescription'])) {
              $projectDescription = pg_escape_string($formData['projectDescription']);
            }
            if(!$this->badWordsFilter->areThereInsultingWords($formData['projectDescription'])) {
              $projectName = md5(uniqid(time()));
              $upfile = $projectName.PROJECTS_EXTENTION;
              $updir = CORE_BASE_PATH.'/'.PROJECTS_DIRECTORY.$upfile;
              $uploadIp = $serverData['REMOTE_ADDR'];
              isset($formData['deviceIMEI']) ? $uploadImei = $formData['deviceIMEI'] : $uploadImei = '';
              isset($formData['userEmail']) ? $uploadEmail = $formData['userEmail'] : $uploadEmail = '';
              isset($formData['userLanguage']) ? $uploadLanguage = $formData['userLanguage'] : $uploadLanguage = '';

              if($fileSize = $this->copyProjectToDirectory($fileData['upload']['tmp_name'], $updir)) {
                $this->session->userLogin_userId ? $userId = $this->session->userLogin_userId : $userId = 0;
                $query = "EXECUTE insert_new_project('$projectTitle', '$projectDescription', '$upfile', '$uploadIp', '$uploadImei', '$uploadEmail', '$uploadLanguage', '$fileSize', '$userId');";
                $result = pg_query($query);
                if($result) {
                  $line = pg_fetch_assoc($result);
                  $newId = $line['id'];
                  if($this->renameProjectFile($updir, $newId)) {
                    $projectFile = CORE_BASE_PATH.'/'.PROJECTS_DIRECTORY.$newId.PROJECTS_EXTENTION;
                    $projectDir = CORE_BASE_PATH.'/'.PROJECTS_DIRECTORY;
                    $fileChecksum = md5_file($projectFile);
                    if($formData['fileChecksum']) {
                      if($this->checkFileChecksum($fileChecksum, $formData['fileChecksum'])) {
                        $statusCode = 200;
                        if($this->getQRCode($newId, $projectTitle)) {
                          $answer = 'Upload successfull!';
                        } else {
                          $answer = 'Upload successfull! QR-Code failed!';
                        }

                        $this->unzipUploadedFile($fileData['upload']['tmp_name'], $projectDir, $newId);
                        $this->unzipThumbnailFromUploadedFile($fileData['upload']['tmp_name'], $projectDir, $newId);
                        
                        $unapprovedWords = $this->badWordsFilter->getUnapprovedWords();
                        if($unapprovedWords) {
                          $this->badWordsFilter->mapUnapprovedWordsToProject($newId);
                          $this->sendUnapprovedWordlistPerEmail();
                        }
                      } else {
                        //Error: file checksum incorrect
                        $statusCode = 501;
                        $this->removeProjectFromDatabase($newId);
                        $this->removeProjectFromFilesystem($projectFile, $newId);
                        $newId = 0;
                        $answer = $this->errorHandler->getError('upload', 'invalid_file_checksum');
                      }
                    } else {
                      //Client did not send file checksum; <= release 5
                      $statusCode = 510;
                      $this->removeProjectFromDatabase($newId);
                      $this->removeProjectFromFilesystem($projectFile);
                      $newId = 0;
                      $answer = $this->errorHandler->getError('upload', 'missing_post_file_checksum');
                    }
                  } else {
                    //Error during rename
                    $projectFile = CORE_BASE_PATH.'/'.PROJECTS_DIRECTORY.$newId.PROJECTS_EXTENTION;
                    $statusCode = 502;
                    $newId = 0;
                    $this->removeProjectFromDatabase($newId);
                    $this->removeProjectFromFilesystem($projectFile, $newId);
                    $this->removeProjectFromFilesystem($updir, $newId);
                    $answer = $this->errorHandler->getError('upload', 'rename_failed');
                  }
                } else {
                  //DB INSERT Error
                  $statusCode = 503;
                  $this->removeProjectFromFilesystem($updir, $newId);
                  $answer = $this->errorHandler->getError('upload', 'sql_insert_failed');
                }
          						@pg_free_result($result);
              } else {
                //Error during copy
                $statusCode = 504;
                $answer = $this->errorHandler->getError('upload', 'copy_failed');
              }
            } else {
              //Error: insulting words in project description
              $statusCode = 505;
              $answer = $this->errorHandler->getError('upload', 'insulting_words_in_project_description');
            }
          } else {
            //Error: insulting words in project title
            $statusCode = 506;
            $answer = $this->errorHandler->getError('upload', 'insulting_words_in_project_title');
          }
        } else {
          // project title == "defaultSaveFile"
          $statusCode= 507;
          $answer = $this->errorHandler->getError('upload', 'project_title_default');
        }
      } else {
        //Error: project file is too big
        $statusCode = 508;
        $answer = $this->errorHandler->getError('upload', 'project_exceed_filesize_limit');
      }
    } else {
      //Error: POST-Data not correct or missing
      $statusCode = 509;
      $answer = $this->errorHandler->getError('upload', 'missing_post_data');
    }
    $this->statusCode = $statusCode;
    $this->fileChecksum = $fileChecksum;
    $this->projectId = $newId;
    $this->answer = $answer;
    // $this->versionName = $fileInfo[0];
    // $this->versionCode = $fileInfo[1];
    return $newId;
  }

  public function unzipUploadedFile($filename, $projectDir, $projectId) { // unzips thumbnail only
    //$versionName = "";
    //$versionCode = 0;
    
    $unzipDir = CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY.$projectId;    
    // print "Unzip FILE ".$projectDir.$projectId.PROJECTS_EXTENTION.$unzipDir;
    mkdir(CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY.$projectId);
    mkdir(CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY.$projectId."/images");
    mkdir(CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY.$projectId."/sounds");
    // change rights
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
      	   $this->versionCode = $this->extractVersionCode($spf);
      	   $this->versionName = $this->extractVersionName($spf);
      	   $this->saveVersionInfo($projectId, $this->versionCode, $this->versionName);
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
  
  public function unzipThumbnailFromUploadedFile($filename, $projectDir, $projectId) { // unzips thumbnail only
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

  public function saveThumbnail($filename, $thumbnail) {
    $thumbnailDir = CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY;

    $thumbImage = imagecreatefromstring($thumbnail);
    if ($thumbImage) {
      $w = imagesx($thumbImage);
      $h = imagesy($thumbImage);

      $wsmall = 0; $hsmall = 0; $hsmallopt = intval(240*$h/$w);
      $wlarge = 0; $hlarge = 0; $hlargeopt = intval(480*$h/$w);
       
      // thumbnail with original filesize
      imagejpeg($thumbImage, $thumbnailDir.$filename.PROJECTS_THUMBNAIL_EXTENTION_ORIG, 100);
      // chmod($thumbnailDir.$filename.PROJECTS_THUMBNAIL_EXTENTION_ORIG, 0666);
      
      // small thumbnail for preview 240x400
      $smallImage = imagecreatetruecolor(240, $hsmallopt);
      imagecopyresampled($smallImage, $thumbImage, 0, 0, 0, 0, 240, $hsmallopt, $w, $h);
      imagejpeg($smallImage, $thumbnailDir.$filename.PROJECTS_THUMBNAIL_EXTENTION_SMALL, 50);
      // chmod($thumbnailDir.$filename.PROJECTS_THUMBNAIL_EXTENTION_SMALL, 0666);
      
      // large thumbnail for details-view 480x800
      $newImage = imagecreatetruecolor(480, $hlargeopt);
      imagecopyresampled($newImage, $thumbImage, 0, 0, 0, 0, 480, $hlargeopt, $w, $h);
      imagejpeg($newImage, $thumbnailDir.$filename.PROJECTS_THUMBNAIL_EXTENTION_LARGE, 50);
      // chmod($thumbnailDir.$filename.PROJECTS_THUMBNAIL_EXTENTION_LARGE, 0666);
    }
  }

  public function saveFile($targetDir, $filename, $filecontent, $filesize) {
    $fp = fopen($targetDir."/".$filename, "wb+");
    if ($fp) {
      fwrite($fp, $filecontent, $filesize);
      fclose($fp);
      // chmod($targetDir."/".$filename, 0666);
    }
  }

  public function renameProjectFile($oldName, $newId) {
    $newFileName = $newId.PROJECTS_EXTENTION;
    $newName = CORE_BASE_PATH.'/'.PROJECTS_DIRECTORY.$newFileName;
    if(@rename($oldName, $newName)) {
      $query = "EXECUTE set_project_new_filename('$newFileName', '$newId');";
      $result = @pg_query($query);
      if($result) {
        @pg_free_result($result);
        return true;
      } else {
        return false;
      }
    }
    return false;
  }

  public function copyProjectToDirectory($tmpFile, $updir) {
    if(@copy($tmpFile, $updir)) {
      return filesize($updir);
    } else {
      return false;
    }
  }

  public function checkFileChecksum($uploadChecksum, $clientChecksum) {
    if(strcmp(strtolower($uploadChecksum), strtolower($clientChecksum)) == 0) {
      return true;
    } else {
      return false;
    }
  }

  public function removeProjectFromDatabase($projectId) {
    $query = "EXECUTE delete_project_by_id('$projectId');";
    @pg_query($query);
    return;
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
  
  public function removeProjectFromFilesystem($projectFile, $projectId=-1) {
    @unlink($projectFile);
    if($projectId > 0) {
      $projectBaseDir = CORE_BASE_PATH.'/'.PROJECTS_UNZIPPED_DIRECTORY.$projectId;
      $projectSoundDir = $projectBaseDir.'/sounds';
      $projectImageDir = $projectBaseDir.'/images';
      
      if(file_exists(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$projectId.PROJECTS_THUMBNAIL_EXTENTION_SMALL))
        @unlink(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$projectId.PROJECTS_THUMBNAIL_EXTENTION_SMALL);
      if(file_exists(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$projectId.PROJECTS_THUMBNAIL_EXTENTION_LARGE))
        @unlink(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$projectId.PROJECTS_THUMBNAIL_EXTENTION_LARGE);
      if(file_exists(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$projectId.PROJECTS_THUMBNAIL_EXTENTION_ORIG))
        @unlink(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$projectId.PROJECTS_THUMBNAIL_EXTENTION_ORIG);
      if(file_exists(CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$projectId.PROJECTS_QR_EXTENTION))
        @unlink(CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$projectId.PROJECTS_QR_EXTENTION);
      
      if(is_dir($projectSoundDir)) $this->removeProjectDir($projectSoundDir);
      if(is_dir($projectImageDir)) $this->removeProjectDir($projectImageDir);
      if(is_dir($projectBaseDir)) $this->removeProjectDir($projectBaseDir);
    }
    return;
  }

  public function getQRCode($projectId, $projectTitle) {
    if(!$projectId || !$projectTitle) {
      $this->sendQRFailNotificationEmail($projectId, $projectTitle);
      return false;
    }
    $urlToEncode = urlencode(BASE_PATH.'catroid/download/'.$projectId.PROJECTS_EXTENTION.'?fname='.urlencode($projectTitle));
    $serviceUrl = PROJECTS_QR_SERVICE_URL.$urlToEncode;
    $destinationPath = CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$projectId.PROJECTS_QR_EXTENTION;
    $qrImageHandle = @imagecreatefrompng($serviceUrl);
    if(!$qrImageHandle) {
      $this->sendQRFailNotificationEmail($projectId, $projectTitle);
      return false;
    }
    if(@imagepng($qrImageHandle, $destinationPath, 9)) {
      @imagedestroy($qrImageHandle);
      chmod($destinationPath, 0666);
      return true;
    } else {
      $this->sendQRFailNotificationEmail($projectId, $projectTitle);
      return false;
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

  public function areThereInsultingWords($text) {
    switch($this->badWordsFilter->areThereInsultingWords($text)) {
      case -1:
        $statusCode = 510;
        $this->answer = $this->errorHandler->getError('upload', 'bad_words_filter_error');
        break;
      case 0:
        return false;
        break;
      case 1:
        return true;
        break;
      default:
        return true;
    }
  }

  public function sendUnapprovedWordlistPerEmail() {
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

  public function sendUploadFailAdminEmail($formData, $fileData, $serverData) {
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
    if(isset($formData['deviceIMEI']))
    $mailText .= "Device IMEI: ".$formData['deviceIMEI']."\n";
    if(isset($formData['userEmail']))
    $mailText .= "User Email: ".$formData['userEmail']."\n";
    if(isset($formData['userLanguage']))
    $mailText .= "User Language: ".$formData['userLanguage']."\n";
    if(isset($serverData['REMOTE_ADDR']))
    $mailText .= "User IP: ".$serverData['REMOTE_ADDR']."\n";

    $mailText .= "You should check this!";

    return($this->mailHandler->sendAdministrationMail($mailSubject, $mailText));
  }
  
function extractVersionCode($xml) {
    $version = 0;
    if (!preg_match("/versionCode/", $xml))
      return $version;
      
    if (preg_match("/<versionCode/", $xml)) {
      $stag = "<versionCode>";
      $etag = "<";
      $version = 5;
    } else {
      $stag = "<project ";
      $etag = ">";
      $stag2 = "versionCode=\"";
      $etag2 = "\"";
      $version = 4;
    }
    $starttag = preg_split("/".$stag."/", $xml);
    $endtag = preg_split("/".$etag."/", $starttag[1]);
    $content = $endtag[0];
    if ($version >= 5)
      return $content;
    
    $starttag = preg_split("/".$stag2."/", $content);
    $endtag = preg_split("/".$etag2."/", $starttag[1]);
    return $endtag[0];
  }

 function extractVersionName($xml) {
    $version = 0;
    if (!preg_match("/versionName/", $xml))
      return '';
      
    if (preg_match("/<versionName/", $xml)) {
      $stag = "<versionName>";
      $etag = "<";
      $version = 5;
    } else {
      $stag = "<project ";
      $etag = ">";
      $stag2 = "versionName=\"";
      $etag2 = "\"";
      $version = 4;
    }
    $starttag = preg_split("/".$stag."/", $xml);
    $endtag = preg_split("/".$etag."/", $starttag[1]);
    $content = $endtag[0];
    if ($version >= 5)
      return $content;
    
    $starttag = preg_split("/".$stag2."/", $content);
    $endtag = preg_split("/".$etag2."/", $starttag[1]);
    return $endtag[0];
  }

  public function saveVersionInfo($projectId, $versionCode, $versionName) {
    $query = "EXECUTE save_catroid_version_info('$projectId', '$versionCode', '$versionName');";
    $result = @pg_query($query);
    if($result) {
      @pg_free_result($result);
      return true;
    } else {
      return false;
    }
    return false;
  }
  
  
  public function __destruct() {
    parent::__destruct();
  }
}
?>
