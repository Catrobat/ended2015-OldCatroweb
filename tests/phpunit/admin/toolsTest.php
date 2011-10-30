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

require_once('testsBootstrap.php');

class toolsTest extends PHPUnit_Framework_TestCase
{
  protected $tools;
  protected $upload;

  protected function setUp() {
    require_once CORE_BASE_PATH.'modules/admin/tools.php';
    require_once CORE_BASE_PATH.'modules/api/upload.php';
    $this->tools = new tools();
    $this->upload = new upload();
    @unlink(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.'test_thumbnail.png');
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
    $thumbName = 'test_thumbnail.png';
    $fileData = array('upload'=>array('name'=>$thumbName, 'type'=>'image/png',
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

  /**
   * @dataProvider blockUser
   */
  public function testBlockUser($user_id, $user_name, $check_user_id, $check_user_name) {
    $this->tools->blockUser($user_id, $user_name);
    $this->assertTrue($this->tools->isBlockedUser($check_user_id, $check_user_name));
    $this->tools->unblockUser($user_id, $user_name);
    $this->assertFalse($this->tools->isBlockedUser($check_user_id, $check_user_name));
  }

  /**
   * @dataProvider unblockedUser
   */
  public function testUnblockedUser($user_id, $user_name, $check_user_id, $check_user_name) {
    $this->tools->blockUser($user_id, $user_name);
    $this->assertFalse($this->tools->isBlockedUser($check_user_id, $check_user_name));
    $this->tools->unblockUser($user_id, $user_name);
  }

  /**
   * @dataProvider blockIp
   */
  public function testBlockIp($ip, $check_ip) {
    $this->tools->removeAllBlockedIps();
    $this->tools->blockIp($ip);
    $this->assertTrue($this->tools->isBlockedIp($check_ip));
    $this->tools->unblockIp($ip);
    $this->assertFalse($this->tools->isBlockedIp($check_ip));
  }

  /**
   * @dataProvider unblockedIp
   */
  public function testUnblockedIp($ip, $check_ip) {
    $this->tools->blockIp($ip);
    $this->assertFalse($this->tools->isBlockedIp($check_ip));
    $this->tools->unblockIp($ip);
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

  public function blockUser() {
    $dataArray = array(
    array(0, "anonymous", 0, "anonymous"),
    array(1, "catroweb", 1, "catroweb")
    );
    return $dataArray;
  }

  public function unblockedUser() {
    $dataArray = array(
    array(0, "anonymous", 1, "catroweb"),
    array(1, "catroweb", 0, "anonymous")
    );
    return $dataArray;
  }

  public function blockIp() {
    $dataArray = array(
    array("127.0.0.1", "127.0.0.1"),
    array("127.0.0.", "127.0.0.9"),
    array("127.0.0.2", "127.0.0.2"),
    array("127.", "127.0.0.1"),
    array("127.", "127.0.0.2"),
    array("127.", "127.29.12.33")
    );
    return $dataArray;
  }

  public function unblockedIp() {
    $dataArray = array(
    array("127.0.0.1", "127.0.0.2"),
    array("127.0.0.", "127.12.0.1"),
    array("127.0.0.2", "127.0.0.1")
    );
    return $dataArray;
  }

  private function deleteWord($word) {
    $query = "DELETE FROM wordlist WHERE word='$word'";
    $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
    if($result) {
      pg_free_result($result);
    }
  }

  private function getWordId($word) {
    $query = "SELECT * FROM wordlist WHERE word='$word'";
    $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
    if($result) {
      $word =  pg_fetch_all($result);
      pg_free_result($result);

      if($word) {
        return $word[0]['id'];
      }
    }
    return -1;
  }

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
