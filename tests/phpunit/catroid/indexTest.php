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

class indexTest extends PHPUnit_Framework_TestCase
{
  protected $obj;

  protected function setUp() {
    require_once CORE_BASE_PATH.'modules/catroid/index.php';
    $this->obj = new index();
    @unlink(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.'test_large.jpg');
  }

  public function testRetrieveAllProjectsFromDatabase()
  {
    $projects = $this->obj->retrieveAllProjectsFromDatabase();
    foreach($projects as $project) {
      $this->assertEquals('t', $project['visible']);
    }

    $query = 'SELECT * FROM projects WHERE visible=true';
    $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
    $numDbEntries =  pg_num_rows($result);
    // test that projects is a valid db serach result
    if ($numDbEntries > 0) {
      $this->assertEquals(true, is_array($projects));
    } else {
      $this->assertEquals(false, is_array($projects));
    }

    //test if all projects are fetched
    $this->assertEquals($numDbEntries, count($projects));
    //test that newest projects are first
    if($numDbEntries > 1) {
      $this->assertGreaterThanOrEqual(strtotime($projects[$numDbEntries-1]['upload_time']), strtotime($projects[0]['upload_time']));
    }
  }

  public function testGetThumbnail() {
    $thumbSourceName = 'test_thumbnail.jpg';
    $thumbDestName = 'test_small.jpg';
    $thumb = $this->obj->getThumbnail('test');
    $this->assertFalse(strpos($thumb, $thumbDestName));
    copy(dirname(__FILE__).'/testdata/'.$thumbSourceName, CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$thumbDestName);
    $thumb = $this->obj->getThumbnail('test');
    $this->assertTrue(is_int(strpos($thumb, $thumbDestName)));
  }

  /**
   * @dataProvider randomLongStrings
   */
  public function testShortenTitle($string) {
    $short = $this->obj->shortenTitle($string);

    $this->assertEquals(PROJECT_TITLE_MAX_DISPLAY_LENGTH, strlen($short));
    $this->assertEquals(0, strcmp(substr($string, 0, strlen($short)), $short));
  }

  /* *** DATA PROVIDERS *** */
  public function randomLongStrings() {
    $returnArray = array();
    $strLen = 200;
    $chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

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
    @unlink(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.'test_small.jpg');
  }
}
?>
