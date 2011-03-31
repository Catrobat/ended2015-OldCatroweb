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

require_once('testsBootstrap.php');

class flagInappropriateTest extends PHPUnit_Framework_TestCase
{
  protected $obj;
  protected $currentProjectId = null;

  protected function setUp() {
    require_once CORE_BASE_PATH.'modules/catroid/flagInappropriate.php';
    $this->obj = new flagInappropriate();
  }
  
  /**
   * @dataProvider randomIds
   */
  public function testHideProject($projectId) {
    $this->currentProjectId = $projectId;
    $this->assertTrue($this->obj->hideProject($projectId));
  }
  
  /**
   * @dataProvider postData
   */
  public function testFlagProject($postData, $serverData) {
    $projectId = $postData['projectId'];
    $this->currentProjectId = $projectId;
    $numFlags = $this->obj->getProjectFlags($projectId);
    $this->obj->flagProject($postData, $serverData, false); //false as 3rd parameter means that no notifiaction-email should be send
    $this->assertEquals(200, $this->obj->statusCode);
    $this->assertEquals($numFlags+1, $this->obj->getProjectFlags($projectId));
    $this->obj->flagProject($postData, $serverData, false); //false as 3rd parameter means that no notifiaction-email should be send
    $this->assertEquals(200, $this->obj->statusCode);
    $this->assertEquals($numFlags+2, $this->obj->getProjectFlags($projectId));
  }
  
  /**
   * @dataProvider randomIds
   */
  public function testGetProjectFlags($projectId) {
    $this->assertEquals(0, $this->obj->getProjectFlags($projectId));
  }

  /* *** DATA PROVIDER *** */
  public function randomIds() {
    $returnArray = array();

    $query = 'SELECT * FROM projects WHERE visible=true ORDER BY random() LIMIT 5';
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
  
  public function postData() {
    $flagReason = 'This project contains inappropriate words!';
    $returnArray = array();

    $query = 'SELECT * FROM projects WHERE visible=true ORDER BY random() LIMIT 5';
    $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
    $projects = pg_fetch_all($result);
    pg_free_result($result);
    $i=0;
    foreach($projects as $project) {
      $returnArray[$i] = array(array("projectId" => $project['id'], "flagReason" => $flagReason), array("REMOTE_ADDR" => "127.0.0.1"));
      $i++;
    }
    return $returnArray;
  }

  protected function tearDown() {
    if($this->currentProjectId) {
      $projectId = $this->currentProjectId;
      $query = "UPDATE projects SET visible=true WHERE id='$projectId'";
      $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
      $query = "DELETE FROM flagged_projects WHERE project_id='$projectId' AND user_ip='127.0.0.1'";
      $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
      pg_free_result($result);
      $this->currentProjectId = null;
    }
  }
}
?>
