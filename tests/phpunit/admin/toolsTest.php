<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010  Catroid development team
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once('testsBootstrap.php');

class toolsTest extends PHPUnit_Framework_TestCase
{
  protected $tools;
	protected $upload;

	protected function setUp() {
		require_once CORE_BASE_PATH.'modules/admin/tools.php';
		require_once CORE_BASE_PATH.'modules/catroid/upload.php';
		$this->tools = new tools();
		$this->upload = new upload();
		@unlink(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.'test_thumbnail.jpg');
	}

  public function testRemoveInconsistantProjectFiles() {
    $projectDirectory = CORE_BASE_PATH.PROJECTS_DIRECTORY;
    $testFileName = "99999999.zip";
    $testFile = $projectDirectory.$testFileName;
    $testFileHandle = fopen($testFile, 'w') or die("can't create file");
    fclose($testFileHandle);

    $fileExistsBefore = is_file($testFile);
    $this->tools->removeInconsistantProjectFiles();
    $fileExistsAfter = is_file($testFile);

    $this->assertTrue($fileExistsBefore && !$fileExistsAfter);
  }

  public function testUploadThumbnail() {
    $thumbName = 'test_thumbnail.jpg';
    $fileData = array('upload'=>array('name'=>$thumbName, 'type'=>'image/jpeg',
                        'tmp_name'=>dirname(__FILE__).'/testdata/'.$thumbName, 'error'=>0, 'size'=>4482));
    $this->assertTrue($this->tools->uploadThumbnail($fileData));
    $this->assertTrue(is_file(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$thumbName));
  }

  /**
   * @dataProvider randomIds
   */
  public function testResolveInappropriateProject($id) {
    $this->assertTrue($this->tools->resolveInappropriateProject($id));
    $query = "SELECT * FROM projects WHERE id='$id' AND visible=false";
    $result = @pg_query($query);
    $this->assertEquals(0, pg_num_rows($result));
    pg_free_result($result);
    $query = "SELECT * FROM flagged_projects WHERE project_id='$id' AND resolved=false";
    $result = @pg_query($query);
    $this->assertEquals(0, pg_num_rows($result));
    pg_free_result($result);
  }

  /* *** DATA PROVIDERS *** */
  //choose random ids from database
  public function randomIds() {
    $returnArray = array();

    $query = 'SELECT * FROM projects ORDER BY random() LIMIT 3';
    $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
    $projects = pg_fetch_all($result);
    pg_free_result($result);
    $i=0;
    foreach($projects as $project) {
      $returnArray[$i] = array($project['id']);
      $i++;
    }

    return $returnArray;
  }

//  public function testApproveWordGood() {
//		$unapprovedWord = "donaudampfschiffahrtselektrizitaetenhauptbetriebswerkbauunterbeamtengesellschaft";
//		$this->deleteWord($unapprovedWord);
//		$unapprovedWordCount = count($this->badWordsFilter->getUnapprovedWords());
//		$this->badWordsFilter->addWord($unapprovedWord, 'false', 'false');
//		$this->badWordsFilter->checkWord($unapprovedWord);
//		$this->assertTrue((count($this->badWordsFilter->getUnapprovedWords()) == $unapprovedWordCount+1));
//
//		$fileName = 'test.zip';
//		$testFile = dirname(__FILE__).'/testdata/'.$fileName;
//		$fileChecksum = md5_file($testFile);
//		$fileSize = filesize($testFile);
//		$fileType = 'application/x-zip-compressed';
//
//		$formData = array('projectTitle'=>'A Testproject that contains an unapproved word',
//                      'projectDescription'=>$unapprovedWord, 'fileChecksum'=>$fileChecksum);
//		$fileData = array('upload'=>array('name'=>$fileName, 'type'=>$fileType,
//                        'tmp_name'=>$testFile, 'error'=>0, 'size'=>$fileSize));
//		$serverData = array('REMOTE_ADDR'=>'127.0.0.1');
//		$insertId = $this->upload->doUpload($formData, $fileData, $serverData);
//		$this->assertTrue($this->isProjectInDatabase($insertId));
//		$this->assertTrue(in_array($unapprovedWord, $this->getUnapprovedWords()));
//		$this->assertTrue($this->isProjectVisible($insertId));
//
//		$_POST['approve'] = true;
//		$_POST['meaning'] = 1;
//		$_POST['wordId'] = $this->getWordId($unapprovedWord);
//		$this->tools->approveWords();
//		$this->assertFalse(in_array($unapprovedWord, $this->getUnapprovedWords()));
//		$this->assertTrue($this->isProjectVisible($insertId));
//
//		$this->deleteWord($unapprovedWord);
//		$this->tools->deleteProject($insertId);
//		$this->assertFalse($this->isProjectInDatabase($insertId));
//		$this->assertTrue($this->getWordId($unapprovedWord) == -1);
//	}

//	public function testApproveWordBad() {
//		$unapprovedWord = "donaudampfschiffahrtselektrizitaetenhauptbetriebswerkbauunterbeamtengesellschaft";
//		$unapprovedWordCount = count($this->badWordsFilter->getUnapprovedWords());
//    $this->deleteWord($unapprovedWord);
//		$this->badWordsFilter->addWord($unapprovedWord, 'false', 'false');
//		$this->badWordsFilter->checkWord($unapprovedWord);
//		$this->assertTrue((count($this->badWordsFilter->getUnapprovedWords()) == $unapprovedWordCount+1));
//
//		$fileName = 'test.zip';
//		$testFile = dirname(__FILE__).'/testdata/'.$fileName;
//		$fileChecksum = md5_file($testFile);
//		$fileSize = filesize($testFile);
//		$fileType = 'application/x-zip-compressed';
//
//		$formData = array('projectTitle'=>'A Testproject that contains an unapproved word',
//                      'projectDescription'=>$unapprovedWord, 'fileChecksum'=>$fileChecksum);
//		$fileData = array('upload'=>array('name'=>$fileName, 'type'=>$fileType,
//                        'tmp_name'=>$testFile, 'error'=>0, 'size'=>$fileSize));
//		$serverData = array('REMOTE_ADDR'=>'127.0.0.1');
//		$insertId = $this->upload->doUpload($formData, $fileData, $serverData);
//		$this->assertTrue($this->isProjectInDatabase($insertId));
//		$this->assertTrue(in_array($unapprovedWord, $this->getUnapprovedWords()));
//		$this->assertTrue($this->isProjectVisible($insertId));
//
//		$_POST['approve'] = true;
//		$_POST['meaning'] = 0;
//		$_POST['wordId'] = $this->getWordId($unapprovedWord);
//		$this->tools->approveWords();
//		$this->assertFalse(in_array($unapprovedWord, $this->getUnapprovedWords()));
//		$this->assertFalse($this->isProjectVisible($insertId));
//
//		$this->deleteWord($unapprovedWord);
//		$this->tools->deleteProject($insertId);
//		$this->assertFalse($this->isProjectInDatabase($insertId));
//		$this->assertTrue($this->getWordId($unapprovedWord) == -1);
//	}
//
//	public function testApproveWordNoSelection() {
//		$unapprovedWord = "donaudampfschiffahrtselektrizitaetenhauptbetriebswerkbauunterbeamtengesellschaft";
//		$unapprovedWordCount = count($this->badWordsFilter->getUnapprovedWords());
//    $this->deleteWord($unapprovedWord);
//		$this->badWordsFilter->addWord($unapprovedWord, 'false', 'false');
//		$this->badWordsFilter->checkWord($unapprovedWord);
//		$this->assertTrue((count($this->badWordsFilter->getUnapprovedWords()) == $unapprovedWordCount+1));
//
//		$fileName = 'test.zip';
//		$testFile = dirname(__FILE__).'/testdata/'.$fileName;
//		$fileChecksum = md5_file($testFile);
//		$fileSize = filesize($testFile);
//		$fileType = 'application/x-zip-compressed';
//
//		$formData = array('projectTitle'=>'A Testproject that contains an unapproved word',
//                      'projectDescription'=>$unapprovedWord, 'fileChecksum'=>$fileChecksum);
//		$fileData = array('upload'=>array('name'=>$fileName, 'type'=>$fileType,
//                        'tmp_name'=>$testFile, 'error'=>0, 'size'=>$fileSize));
//		$serverData = array('REMOTE_ADDR'=>'127.0.0.1');
//		$insertId = $this->upload->doUpload($formData, $fileData, $serverData);
//		$this->assertTrue($this->isProjectInDatabase($insertId));
//		$this->assertTrue(in_array($unapprovedWord, $this->getUnapprovedWords()));
//		$this->assertTrue($this->isProjectVisible($insertId));
//
//		$_POST['approve'] = true;
//		$_POST['meaning'] = -1;
//		$_POST['wordId'] = $this->getWordId($unapprovedWord);
//		$this->tools->approveWords();
//		$this->assertTrue(in_array($unapprovedWord, $this->getUnapprovedWords()));
//		$this->assertTrue($this->isProjectVisible($insertId));
//
//		$this->deleteWord($unapprovedWord);
//		$this->tools->deleteProject($insertId);
//		$this->assertFalse($this->isProjectInDatabase($insertId));
//		$this->assertTrue($this->getWordId($unapprovedWord) == -1);
//	}
//
//	public function testApproveWordDelete() {
//		$unapprovedWord = "donaudampfschiffahrtselektrizitaetenhauptbetriebswerkbauunterbeamtengesellschaft";
//		$unapprovedWordCount = count($this->badWordsFilter->getUnapprovedWords());
//    $this->deleteWord($unapprovedWord);
//		$this->badWordsFilter->addWord($unapprovedWord, 'false', 'false');
//		$this->badWordsFilter->checkWord($unapprovedWord);
//		$this->assertTrue((count($this->badWordsFilter->getUnapprovedWords()) == $unapprovedWordCount+1));
//
//		$fileName = 'test.zip';
//		$testFile = dirname(__FILE__).'/testdata/'.$fileName;
//		$fileChecksum = md5_file($testFile);
//		$fileSize = filesize($testFile);
//		$fileType = 'application/x-zip-compressed';
//
//		$formData = array('projectTitle'=>'A Testproject that contains an unapproved word',
//                      'projectDescription'=>$unapprovedWord, 'fileChecksum'=>$fileChecksum);
//		$fileData = array('upload'=>array('name'=>$fileName, 'type'=>$fileType,
//                        'tmp_name'=>$testFile, 'error'=>0, 'size'=>$fileSize));
//		$serverData = array('REMOTE_ADDR'=>'127.0.0.1');
//		$insertId = $this->upload->doUpload($formData, $fileData, $serverData);
//		$this->assertTrue($this->isProjectInDatabase($insertId));
//		$this->assertTrue(in_array($unapprovedWord, $this->getUnapprovedWords()));
//		$this->assertTrue($this->isProjectVisible($insertId));
//
//		$_POST['delete'] = true;
//		$_POST['wordId'] = $this->getWordId($unapprovedWord);
//		$this->tools->approveWords();
//		$this->assertFalse(in_array($unapprovedWord, $this->getUnapprovedWords()));
//		$this->assertTrue($this->isProjectVisible($insertId));
//
//		$this->tools->deleteProject($insertId);
//		$this->assertFalse($this->isProjectInDatabase($insertId));
//		$this->assertTrue($this->getWordId($unapprovedWord) == -1);
//	}
//
//	public function testUnapprovedWordsInProjectsTable() {
//		$unapprovedTableLength = $this->getUnapprovedTableLength();
//		$unapprovedWord = "donaudampfschiffahrtselektrizitaetenhauptbetriebswerkbauunterbeamtengesellschaft";
//		$unapprovedWordCount = count($this->badWordsFilter->getUnapprovedWords());
//    $this->deleteWord($unapprovedWord);
//		$this->badWordsFilter->addWord($unapprovedWord, 'false', 'false');
//		$this->badWordsFilter->checkWord($unapprovedWord);
//		$this->assertTrue((count($this->badWordsFilter->getUnapprovedWords()) == $unapprovedWordCount+1));
//
//		$fileName = 'test.zip';
//		$testFile = dirname(__FILE__).'/testdata/'.$fileName;
//		$fileChecksum = md5_file($testFile);
//		$fileSize = filesize($testFile);
//		$fileType = 'application/x-zip-compressed';
//
//		$formData = array('projectTitle'=>'A Testproject that contains an unapproved word',
//                      'projectDescription'=>$unapprovedWord, 'fileChecksum'=>$fileChecksum);
//		$fileData = array('upload'=>array('name'=>$fileName, 'type'=>$fileType,
//                        'tmp_name'=>$testFile, 'error'=>0, 'size'=>$fileSize));
//		$serverData = array('REMOTE_ADDR'=>'127.0.0.1');
//		$insertId = $this->upload->doUpload($formData, $fileData, $serverData);
//		$this->assertTrue($this->isProjectInDatabase($insertId));
//		$this->assertTrue(in_array($unapprovedWord, $this->getUnapprovedWords()));
//		$this->assertTrue($this->isProjectVisible($insertId));
//		$this->assertTrue($unapprovedTableLength+1 == $this->getUnapprovedTableLength());
//
//		$_POST['delete'] = true;
//		$_POST['wordId'] = $this->getWordId($unapprovedWord);
//		$this->tools->approveWords();
//		$this->assertFalse(in_array($unapprovedWord, $this->getUnapprovedWords()));
//		$this->assertTrue($this->isProjectVisible($insertId));
//		$this->assertTrue($unapprovedTableLength == $this->getUnapprovedTableLength());
//
//		$this->tools->deleteProject($insertId);
//		$this->assertFalse($this->isProjectInDatabase($insertId));
//		$this->assertTrue($this->getWordId($unapprovedWord) == -1);
//	}
//
//	private function deleteWord($word) {
//		$query = "DELETE FROM wordlist WHERE word='$word'";
//		$result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
//		if($result) {
//			pg_free_result($result);
//		}
//	}
//
//	private function getWordId($word) {
//		$query = "SELECT * FROM wordlist WHERE word='$word'";
//		$result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
//		if($result) {
//			$word =  pg_fetch_all($result);
//			pg_free_result($result);
//
//			if($word) {
//				return $word[0]['id'];
//			}
//		}
//		return -1;
//	}

	private function isProjectInDatabase($projectId) {
		$query = "EXECUTE get_project_by_id('$projectId');";
		$result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
		if($result) {
			if(pg_num_rows($result)) {
				pg_free_result($result);
				return true;
			}
		}
		return false;
	}

	private function isProjectVisible($projectId) {
		$query = "SELECT * FROM projects WHERE id='$projectId';";
		$result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
		if($result) {
			$project =  pg_fetch_all($result);
			pg_free_result($result);
			if($project[0]['visible'] == 't') {
				return true;
			}
		}
		return false;
	}

	private function getUnapprovedWords() {
		$datbaseWords = $this->tools->retrieveAllUnapprovedWordsFromDatabase();
		$unapprovedWords = array();

		if($datbaseWords) {
			foreach($datbaseWords as $wordEntry) {
				array_push($unapprovedWords, $wordEntry['word']);
			}
		}
		return $unapprovedWords;
	}

	private function getUnapprovedTableLength() {
		$query = "SELECT * FROM unapproved_words_in_projects;";
		$result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
		if($result) {
			$count =  pg_num_rows($result);
			pg_free_result($result);

			return $count;
		}
		return 0;
	}
  
  protected function tearDown() {
    @unlink(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.'test_thumbnail.jpg');
  }
}
?>
