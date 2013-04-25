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

require_once('testsBootstrap.php');

class detailsTest extends PHPUnit_Framework_TestCase
{
  protected $obj;

  protected function setUp() {
    require_once CORE_BASE_PATH.'modules/catroid/details.php';
    $this->obj = new details();
    @unlink(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.'test_large.png');
  }

  /**
   * @dataProvider randomIds
   */
  public function testGetProjectDetails($id)
  {
    $project = $this->obj->getProjectDetails($id);
    $this->assertTrue(is_array($project));
  }
  
  public function testGetProjectImage()
  {
    $thumbSourceName = 'test_thumbnail.png';
    $thumbDestName = 'test_large.png';
    $thumb = getProjectImageUrl('test');
    $this->assertFalse(strpos($thumb, $thumbDestName));
    copy(dirname(__FILE__).'/testdata/'.$thumbSourceName, CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$thumbDestName);
    $thumb = getProjectImageUrl('test');
    $this->assertTrue(is_int(strpos($thumb, $thumbDestName)));
  }

  /**
   * @dataProvider projectVersionInfo
   */
  public function testGetProjectInfo($id, $version_code, $version_name) {
    $project = $this->obj->getProjectDetails($id);
    $this->assertEquals($project['version_name'], $version_name);
    $this->assertEquals($project['language_code'], $version_code);
  }
  
  /**
   * @dataProvider randomLongStrings
   */
  public function testShortenDescription($string) {
    $short = makeShortString($string, PROJECT_SHORT_DESCRIPTION_MAX_LENGTH, '...'); //$this->obj->shortenDescription($string);

    $this->assertEquals(PROJECT_SHORT_DESCRIPTION_MAX_LENGTH, strlen($short));
    $this->assertEquals(substr($string, 0, strlen($short)-3).'...', $short);
  }

  /**
   * @dataProvider randomIds
   */
  public function testIncrementViewCounter($id)
  {
    $project = $this->obj->getProjectDetails($id);
    $viewCounterInitial = $project['view_count'];
    $project = $this->obj->getProjectDetails($id);
    $viewCounterNew = $project['view_count'];
    $this->assertEquals($viewCounterInitial+1, $viewCounterNew);
  }
  
  public function testGetFilesizeInMegabytes() {
    $bytes = 1234567890;
    $megabytes = round($bytes/1048576, 1);
    $this->assertEquals($megabytes, convertBytesToMegabytes($bytes));
  }

  /* *** DATA PROVIDERS *** */
  //choose random ids from database
  public function randomIds() {
    $returnArray = array();

    $query = 'SELECT * FROM projects WHERE visible=true ORDER BY random() LIMIT 3';
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

  public function projectVersionInfo() {
    $dataArray = array(
        array(1, 500, "0.7.0beta")
      );
    return $dataArray;
  }
    
  public function randomLongStrings() {
    $returnArray = array();
    $strLen = 400;
    $chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',' ');

    for($i=0;$i<5;$i++) {
      $str = '';
      for($j=0;$j<$strLen;$j++) {
        $str .= $chars[rand(0, count($chars)-1)];
      }
      $returnArray[$i] = array($str);
    }

    return $returnArray;
  }

  protected function tearDown() {
    @unlink(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.'test_large.png');
  }
}
?>
