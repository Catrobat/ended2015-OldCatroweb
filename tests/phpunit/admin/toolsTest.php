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

  public function testMobileBrowserDetectionExtractRegexSimple() {
    $currentCode = "";
    $updateCode = "";
  
    $currentCode = "    // <isMobile>\n";
    $currentCode.= "       // isMobileTest\n";
    $currentCode.= "    // </isMobile>\n";
    $currentCode.= "		      return true;\n";
    $currentCode.= "		   else\n";
    $currentCode.= "		      return false;\n";
  
    $updateCode = "<?php\n";
    $updateCode.= "\$useragent=\$_SERVER['HTTP_USER_AGENT'];\n";
    $updateCode.= "if(preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|meego.+mobile|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',\$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr(\$useragent,0,4)))\n";
    $updateCode.= "header('Location: http://detectmobilebrowser.com/mobile');\n";
    $updateCode.= "?>";
  
    $newCode = $this->tools->updateMobileBrowserDetectionCode($currentCode, $updateCode);
    $this->assertFalse(preg_match("/isMobileTest/", $newCode) == 1);
    $this->assertTrue(preg_match("/preg_match\(/", $newCode) == 1);
    $this->assertNotEquals($currentCode, $newCode);
  }

  public function testMobileBrowserDetectionExtractRegexComplex() {
    $currentCode = "";
    $updateCode = "";
  
    $currentCode = "// regular Expression - can be retrieved in admin-mode from http://detectmobilebrowsers.com/download/php\n";
    $currentCode.= "		public function isMobileBrowser(\$useragent) {\n";
		$currentCode.= "    // <isMobile>\n";
		$currentCode.= "       if (preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|meego.+mobile|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',\$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr(\$useragent,0,4)))\n";
		$currentCode.= "    // </isMobile>\n";
		$currentCode.= "		      return true;\n";
		$currentCode.= "		   else\n";
		$currentCode.= "		      return false;\n";
		$currentCode.= "   }";

		$updateCode = "<?php\n";
    $updateCode.= "\$useragent=\$_SERVER['HTTP_USER_AGENT'];\n";
    $updateCode.= "if(preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|meego.+mobile|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',\$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr(\$useragent,0,4)))\n";
    $updateCode.= "header('Location: http://detectmobilebrowser.com/mobile');\n";
    $updateCode.= "?>";
		
		$newCode = $this->tools->updateMobileBrowserDetectionCode($currentCode, $updateCode);
    $this->assertNotEquals($newCode, $currentCode);
  
    //$newCode = $this->tools->updateMobileBrowserDetectionCode($updateCode, $currentCode);
    //$this->assertNotEquals($newCode, $currentCode);
  }
  
  public function testMobileBrowserDetectionUpdate() {
    $updateData = "";
    $clientDetectionClass = CORE_BASE_PATH.'classes/CoreClientDetection.php';
    $clientDetectionUpdateUrl = MOBILE_BROWSERDETECTION_URL_FOR_UPDATE;

    $fileExistsBefore = is_file($clientDetectionClass);
    $fileSizeBefore = filesize($clientDetectionClass);
    
    // update the clientDetectionClass' content
    $this->tools->updateBrowserDetectionRegexPattern();
    
    $fileExistsAfter = is_file($clientDetectionClass);
    $fileSizeAfter = filesize($clientDetectionClass);
    
    // is file still existing?
    $this->assertTrue($fileExistsBefore && $fileExistsAfter);

    // did only minor changes happen?
    $this->assertTrue(($fileSizeAfter/$fileSizeBefore) < 1.05);
    $this->assertTrue(($fileSizeAfter/$fileSizeBefore) > 0.95);
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
  
  /**
   * @dataProvider starterProjectGroups
   */
  public function testStarterProjectGroups($id, $group) {    
    $this->assertEquals($this->tools->getStarterProjectGroupById($id), $group);
  }
  
  /**
   * @dataProvider noStarterProjects
   */
  public function testAddAndRemoveStarterProjects($id, $group, $visibility) {
    $_POST['add'] = 1;
    $_POST['projectId'] = $id;
    $_POST['group'] = $group;
    $_POST['visible'] = $visibility;
    
    $this->assertEquals($this->tools->addStarterProject(), STATUS_CODE_OK);
    $this->assertEquals($this->tools->removeStarterProject(), STATUS_CODE_OK);
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
  
  public function starterProjectGroups() {
    $dataArray = array(
        array(1, "Games"), 
        array(2, "Animations"),
        array(3, "Interactiv Art and Stories"),
        array(4, "Music and Dance"),
        );
    
    return $dataArray;
  }
  
  public function noStarterProjects() {
    $dataArray = array(array(1, "Games"));
    
    $query = 'SELECT * FROM starter_projects';
    $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
    $projects = pg_fetch_all($result);
    
    $condition = null;
    
    if(pg_num_rows($result) != 0)
      $condition = "WHERE ";
    
    pg_free_result($result);
    
    for($i=0;isset($projects[$i]);$i++) {
      if(!isset($projects[$i+1]))
        $condition .= "id!=".$projects[$i]['project_id'];
      else
        $condition .= "id!=".$projects[$i]['project_id']." AND ";
    }
    
    $query = 'SELECT * FROM projects '.$condition.' ORDER BY random() LIMIT 3';
    $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
    $starterProjects = pg_fetch_all($result);
    pg_free_result($result);
    
    for($i=0;isset($starterProjects[$i]);$i++) {
      $dataArray[$i] = array($starterProjects[$i]['id'], 1, $starterProjects[$i]['visible']);
    }
    
    return $dataArray;
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
