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

class tools extends CoreAuthenticationAdmin {
	public function __construct() {
		parent::__construct();
		$this->addCss('adminLayout.css?'.VERSION);
	}

	public function __default() {

	}

	public function removeInconsistantProjectFiles() {
		$directory = CORE_BASE_PATH.PROJECTS_DIRECTORY;
		$files = scandir($directory);
		$answer = '';
		foreach($files as $fileName) {
			if($fileName != '.' && $fileName != '..' && pathinfo($directory.$fileName, PATHINFO_EXTENSION) == 'zip') {
				$projectId = substr($fileName, 0, strpos($fileName, '.'));
				if(is_numeric($projectId)) {
					if($this->isProjectInDatabase(intval($projectId))) {
						$answer .= 'FOUND: ';
					} else {
						$this->deleteFile($directory.$fileName);
						$answer .= 'NOT FOUND: ';
					}
					$answer .= $fileName.'<br />';
				}
			}
		}
		$this->answer = $answer;
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

	private function retrieveAllProjectsFromDatabase() {
		$query = 'EXECUTE get_projects_ordered_by_uploadtime;';
		$result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
		$projects =  pg_fetch_all($result);
		if($projects) {
			for($i=0;$i<count($projects);$i++) {
				$projects[$i]['num_flags'] = $this->countFlags($projects[$i]['id']);
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
			}
		}
		pg_free_result($result);
		return($projects);
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

	public function deleteProject($id) {
		$directory = CORE_BASE_PATH.PROJECTS_DIRECTORY;
		$thumbnailDirectory = CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY;

		$query = "EXECUTE get_project_by_id('$id');";
		$result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
		$project =  pg_fetch_assoc($result);
		$fileName = $project['source'];
		$thumbnailSmallName = $project['id'].PROJECTS_THUMBNAIL_EXTENTION_SMALL;
		$thumbnailLargeName = $project['id'].PROJECTS_THUMBNAIL_EXTENTION_LARGE;

		$sourceFile = $directory.$fileName;
		$thumbnailSmallFile = $thumbnailDirectory.$thumbnailSmallName;
		$thumbnailLargeFile = $thumbnailDirectory.$thumbnailLargeName;
		$qrCodeFile = CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$project['id'].PROJECTS_QR_EXTENTION;
		if(!$this->deleteFile($sourceFile) ||
		!$this->deleteFile($thumbnailSmallFile) ||
		!$this->deleteFile($thumbnailLargeFile) ||
		!$this->deleteFile($qrCodeFile)) {
			return false;
		} else {
			$query = "EXECUTE delete_project_by_id('$id');";
			$result = @pg_query($query) or $this->errorHandler->showError('db', 'query_failed', pg_last_error());
			return true;
		}
	}

	public function hideProject($id) {
		$query = "EXECUTE hide_project('$id');";
		$result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
		if($result) {
			pg_free_result($result);
		}
		return $result;
	}

	public function showProject($id) {
		$query = "EXECUTE show_project('$id');";
		$result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
		if($result) {
			pg_free_result($result);
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

	public function __destruct() {
		parent::__destruct();
	}
}
?>
