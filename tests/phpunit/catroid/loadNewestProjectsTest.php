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

class loadNewestProjectsTest extends PHPUnit_Framework_TestCase
{
  protected $obj;
  protected $upload;    
  protected $insertIDArray = array();
  protected function setUp() {
    require_once CORE_BASE_PATH.'modules/catroid/loadNewestProjects.php';
    $this->obj = new loadNewestProjects();
    require_once CORE_BASE_PATH.'modules/catroid/upload.php';
    $this->upload = new upload();        
  } 
  

  public function testRetrievePageNrFromDatabase()
  {  
    $this->doUpload();
    
    // retrieve first page from database
    $projects = $this->obj->retrievePageNrFromDatabase(0);    
    foreach($projects as $project) {
      $this->assertEquals('t', $project['visible']);
    }

    $query = 'SELECT * FROM projects WHERE visible=true LIMIT '.(PROJECT_PAGE_MAX_PROJECTS).' OFFSET 0';
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
    $this->deleteUploadedProjects();
  }
  
  public function testEncodePageContent()
  {
    $this->doUpload();
    $pageContent = array();
       
    $pageContent['current'] = $this->obj->retrievePageNrFromDatabase(0);
    $pageContent['next'] = $this->obj->retrievePageNrFromDatabase(1);
            
    $pageContent = json_encode($pageContent);
    $pageContent2 = $this->obj->encodePageContent();    
    $this->assertEquals($pageContent,$pageContent2);
    $this->deleteUploadedProjects();
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

  
  public function testGetTimeInWords() {
    $fromTime = time() - 10;
    $timeInWords = $this->obj->getTimeInWords($fromTime, time());
    $this->assertTrue(is_string($timeInWords));
    $this->assertTrue(is_int(strpos($timeInWords, 'less')) && is_int(strpos($timeInWords, 'minute')));

    $fromTime = time() - 66;
    $timeInWords = $this->obj->getTimeInWords($fromTime, time());
    $this->assertFalse(strpos($timeInWords, 'less'));
    $this->assertTrue(is_int(strpos($timeInWords, 'minute')));

    $fromTime = time() - 60*60*24-1;
    $timeInWords = $this->obj->getTimeInWords($fromTime, time());
    $this->assertFalse(strpos($timeInWords, 'minute'));
    $this->assertTrue(is_int(strpos($timeInWords, 'day')));

    $fromTime = time() - 60*60*24*31-1;
    $timeInWords = $this->obj->getTimeInWords($fromTime, time());
    $this->assertFalse(strpos($timeInWords, 'day'));
    $this->assertTrue(is_int(strpos($timeInWords, 'month')));

    $fromTime = time() - 60*60*24*32*12-1;
    $timeInWords = $this->obj->getTimeInWords($fromTime, time());
    $this->assertFalse(strpos($timeInWords, 'month'));
    $this->assertTrue(is_int(strpos($timeInWords, 'year')));
  }
  
  /**
   * @dataProvider randomLongStrings
   */
  public function testShortenTitle($string) {
    $short = $this->obj->shortenTitle($string);

    $this->assertEquals(PROJECT_TITLE_MAX_DISPLAY_LENGTH, strlen($short));
    $this->assertEquals(0, strcmp(substr($string, 0, strlen($short)), $short));
  }

   public function doUpload() {    
     for($i=1; $i< 15; $i++)
     {
       $fileName = 'test.zip';
       $testFile = dirname(__FILE__).'/testdata/'.$fileName;
       $fileChecksum = md5_file($testFile);
       $fileSize = filesize($testFile);
       $fileType = 'application/x-zip-compressed';
      
       $formData = array('projectTitle'=>'unitTest'.$i, 'projectDescription'=>'unitTestDescription'.$i, 'fileChecksum'=>$fileChecksum);
       $fileData = array('upload'=>array('name'=>$fileName, 'type'=>$fileType,
                          'tmp_name'=>$testFile, 'error'=>0, 'size'=>$fileSize));
       $serverData = array('REMOTE_ADDR'=>'127.0.0.1');
       $insertId = $this->upload->doUpload($formData, $fileData, $serverData);
       $filePath = CORE_BASE_PATH.PROJECTS_DIRECTORY.$insertId.PROJECTS_EXTENTION;
      
       //test qrcode image generation
       $this->assertTrue(is_file(CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$insertId.PROJECTS_QR_EXTENTION));
       $this->assertNotEquals(0, $insertId);
       $this->assertTrue(is_file($filePath));
       $this->assertEquals(200, $this->upload->statusCode);
       $this->assertTrue($this->upload->projectId > 0);
       $this->assertTrue($this->upload->fileChecksum != null);
       $this->assertEquals(md5_file($testFile), $this->upload->fileChecksum);
       $this->assertTrue(is_string($this->upload->answer));
       array_push($this->insertIDArray, $insertId);
    }
    
  }    

  public function deleteUploadedProjects()
  {
     foreach ($this->insertIDArray as $insertId)
     {
       $filePath = CORE_BASE_PATH.PROJECTS_DIRECTORY.$insertId.PROJECTS_EXTENTION;
       // test deleting from database
       $this->upload->removeProjectFromFilesystem($filePath);    
       $this->assertFalse(is_file($filePath));
       //test deleting from filesystem
       $this->upload->removeProjectFromDatabase($insertId);
       $query = "SELECT * FROM projects WHERE id='$insertId'";
       $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
       $this->assertEquals(0, pg_num_rows($result));
    }
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
