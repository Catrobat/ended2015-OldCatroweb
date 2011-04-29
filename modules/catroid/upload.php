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

class upload extends CoreAuthenticationNone { 

	public function __construct() {
		parent::__construct();
    $thumbnailDir = CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY;
	}

	public function __default() {
		if(isset($_FILES['upload']['tmp_name']) && $_FILES['upload']['error'] == 0) {
			$this->upload();
		}
	}

	public function upload() {
		$newId = $this->doUpload($_POST, $_FILES, $_SERVER);
		if($newId > 0) {
			$this->answer = 'Upload successfull!';
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
		$statusCode = 500;
		$fileChecksum = null;
		$newId = 0;
		$answer = null;
		$projectDescription = '';
		if($formData['projectTitle'] && isset($fileData['upload']['tmp_name']) && $fileData['upload']['error'] == 0) {
			if(intval($fileData['upload']['size']) <= PROJECTS_MAX_SIZE) {
        		$projectTitle = $formData['projectTitle'];
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

							if($this->copyProjectToDirectory($fileData['upload']['tmp_name'], $updir)) {
								$query = "EXECUTE insert_new_project('$projectTitle', '$projectDescription', '$upfile', '$uploadIp', '$uploadImei', '$uploadEmail', '$uploadLanguage');";
								$result = pg_query($query);
								if($result) {
									$line = pg_fetch_assoc($result);
									$newId = $line['id'];
									if($this->renameProjectFile($updir, $newId)) {
										$projectFile = CORE_BASE_PATH.'/'.PROJECTS_DIRECTORY.$newId.PROJECTS_EXTENTION;
										$projectDir = CORE_BASE_PATH.'/'.PROJECTS_DIRECTORY;
										//$statusCode = 200;
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
		return $newId;
	}
	
	public function unzipUploadedFile($filename, $projectDir, $projectId) { // unzips thumbnail only
	  $unzipDir = CORE_BASE_PATH.'/'.PROJECTS_UNZIPPED_DIRECTORY;
    $zip = zip_open($projectDir.$projectId.".zip");
    while ($zip_entry = zip_read($zip)) {
      $filename = zip_entry_name($zip_entry);
      if (preg_match("/thumbnail\./", $filename) || preg_match("/images\/thumbnail\./", $filename)) {
      	 $thumbnail = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
         $thumbFilename = zip_entry_name($zip_entry);
         $thumbnailExtension = substr($thumbFilename, -3);
         if ($thumbnail) {
           $this->saveThumbnail($projectId, $thumbnailExtension, $thumbnail, "tmp");
           $this->saveThumbnail($projectId, $thumbnailExtension, $thumbnail, "small");
           $this->saveThumbnail($projectId, $thumbnailExtension, $thumbnail, "large");
           $this->removeTempThumbnail($projectId, $thumbnailExtension, $thumbnail, "tmp");
         }
      }
    }
    zip_close($zip);
	}
	
  private function saveThumbnail($filename, $extension, $thumbnail, $addon) {
	  $thumbnailDir = CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY;
    $savedThumbnail = $thumbnailDir.$filename."_".$addon.".".$extension; 
    if ($addon == "tmp") {
      $fp = fopen($savedThumbnail, "wb+");
      if ($fp && $thumbnail) {
        fwrite($fp, $thumbnail);
        fclose($fp);
      }
    }
    
    if ($extension != "jpg" && $extension != "png")
      return;
      
    if ($addon == "large") {
      if ($extension == "jpg") {      
        $thumbImage = imagecreatefromjpeg($thumbnailDir.$filename."_tmp.".$extension);
        $w = imagesx($thumbImage);
        $h = imagesy($thumbImage);
        $newImage = imagecreatetruecolor(480, 800);
        imagecopyresampled($newImage, $thumbImage, 0, 0, 0, 0, 480, 800, max(480, $w), max(800, $h)); 
        imagejpeg($newImage, $thumbnailDir.$filename."_".$addon.".".$extension, 50);
      }        
      if ($extension == "png") {      
        $thumbImage = imagecreatefrompng($thumbnailDir.$filename."_tmp.".$extension);
        $w = imagesx($thumbImage);
        $h = imagesy($thumbImage);
        $newImage = imagecreatetruecolor(480, 800);
        imagecopyresampled($newImage, $thumbImage, 0, 0, 0, 0, 480, 800, max(480, $w), max(800, $h)); 
        imagepng($newImage, $thumbnailDir.$filename."_".$addon.".".$extension, 5);
      }        
    }
    
    if ($addon == "small") {
      if ($extension == "jpg") {      
        $thumbImage = imagecreatefromjpeg($thumbnailDir.$filename."_tmp.".$extension);
        $w = imagesx($thumbImage);
        $h = imagesy($thumbImage);
        $smallImage = imagecreatetruecolor(240, 400);
        imagecopyresampled($smallImage, $thumbImage, 0, 0, 0, 0, 240, 400, max(240, $w), max(400, $h)); 
        imagejpeg($smallImage, $thumbnailDir.$filename."_".$addon.".".$extension, 50);
      }        
      if ($extension == "png") {      
        $thumbImage = imagecreatefrompng($thumbnailDir.$filename."_tmp.".$extension);
        $w = imagesx($thumbImage);
        $h = imagesy($thumbImage);
        $smallImage = imagecreatetruecolor(240, 400);
        imagecopyresampled($smallImage, $thumbImage, 0, 0, 0, 0, 240, 400, max(240, $w), max(400, $h)); 
        imagepng($smallImage, $thumbnailDir.$filename."_".$addon.".".$extension, 5);
      }        
    }
    
  } 

  private function removeTempThumbnail($filename, $extension, $thumbnail, $addon) {
	  $thumbnailDir = CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY;
    @unlink($thumbnailDir.$filename."_".$addon.".".$extension);
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
			return true;
		} else {
			return false;
		}
	}

	public function checkFileChecksum($uploadChecksum, $clientChecksum) {
		if(strcmp($uploadChecksum, $clientChecksum) == 0) {
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

	public function removeProjectFromFilesystem($projectFile, $projectId=-1) {
		@unlink($projectFile);
		if($projectId > 0) {
		  if(file_exists(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$projectId.PROJECTS_THUMBNAIL_EXTENTION_SMALL.'.png'))
		    @unlink(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$projectId.PROJECTS_THUMBNAIL_EXTENTION_SMALL.'.png');
		  if(file_exists(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$projectId.PROJECTS_THUMBNAIL_EXTENTION_LARGE.'.png'))
		    @unlink(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$projectId.PROJECTS_THUMBNAIL_EXTENTION_LARGE.'.png');
		  if(file_exists(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$projectId.PROJECTS_THUMBNAIL_EXTENTION_SMALL.'.jpg'))
		    @unlink(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$projectId.PROJECTS_THUMBNAIL_EXTENTION_SMALL.'.jpg');
		  if(file_exists(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$projectId.PROJECTS_THUMBNAIL_EXTENTION_LARGE.'.jpg'))
		    @unlink(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$projectId.PROJECTS_THUMBNAIL_EXTENTION_LARGE.'.jpg');
		}
		return;
	}

	public function getQRCode($projectId, $projectTitle) {
		if(!$projectId || !$projectTitle) {
			$this->sendQRFailNotificationEmail($projectId, $projectTitle);	
			return false;
		}
		$urlToEncode = urlencode(BASE_PATH.'catroid/download/'.$projectId.'.zip?fname='.urlencode($projectTitle));
		$serviceUrl = PROJECTS_QR_SERVICE_URL.$urlToEncode;
		$destinationPath = CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$projectId.PROJECTS_QR_EXTENTION;
		$qrImageHandle = @imagecreatefrompng($serviceUrl);
		if(!$qrImageHandle) {
			$this->sendQRFailNotificationEmail($projectId, $projectTitle);
			return false;
		}
		if(@imagepng($qrImageHandle, $destinationPath, 9)) {
			@imagedestroy($qrImageHandle);
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

	public function __destruct() {
		parent::__destruct();
	}
}
?>
