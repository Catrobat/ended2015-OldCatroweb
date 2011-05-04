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

class loadSearchProjectsTest extends PHPUnit_Framework_TestCase
{
  protected $obj;
  protected $upload;    
  protected $uniqueString;
  protected $insertIDArray = array();
  protected function setUp() {
    require_once CORE_BASE_PATH.'modules/catroid/loadSearchProjects.php';
    $this->obj = new loadSearchProjects();
    require_once CORE_BASE_PATH.'modules/catroid/upload.php';
    $this->upload = new upload();        
  } 
  
  public function testCheckLabels() {
    $this->assertEquals($this->obj->labels['websitetitle'], "Catroid Website");
    $this->assertEquals($this->obj->labels['title'], "Search Results");
    $this->assertEquals($this->obj->labels['prevButton'], "&laquo; Previous");
    $this->assertEquals($this->obj->labels['nextButton'], "Next &raquo;");
    $this->assertEquals($this->obj->labels['loadingButton'], "<img src='".BASE_PATH."images/symbols/ajax-loader.gif' /> loading...");
  }  

  public function testRetrieveSearchResultsFromDatabaseSuccess() {
    $this->uniqueString = "";
    $randomStrings =  $this->randomLongStrings();
    $this->uniqueString = $randomStrings[0][0];
    $this->doUpload($this->uniqueString,PROJECT_PAGE_LOAD_MAX_PROJECTS);
    
    // retrieve first page from database
    $projects = $this->obj->retrieveSearchResultsFromDatabase($this->uniqueString, 0);
    $i = PROJECT_PAGE_LOAD_MAX_PROJECTS;
    foreach($projects as $project) {
      $this->assertEquals('unitTest'.($i--), $project['title']);
    }

    $query = 'SELECT * FROM projects WHERE title ILIKE \''.$this->uniqueString.'\' OR description ILIKE \''.$this->uniqueString.'\' ORDER BY upload_time DESC  LIMIT '.(PROJECT_PAGE_LOAD_MAX_PROJECTS).' OFFSET 0';
    $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
    $numDbEntries =  pg_num_rows($result);
    
    // test that projects is$numDbEntries a valid db serach result
    if ($numDbEntries > 0) {
      $this->assertEquals(true, is_array($projects));
    } else {
      $this->assertEquals(false, is_array($projects));
    }

    //test if all projects are fetched
    $this->assertEquals($numDbEntries, count($projects));        
    $this->deleteUploadedProjects();
  }
  
  public function testRetrieveSearchResultsFromDatabaseFail() {  
    $this->uniqueString = "";  
    $randomStrings =  $this->randomLongStrings();
    $this->uniqueString = $randomStrings[0][0];  
    
    // retrieve first page from database
    $projects = $this->obj->retrieveSearchResultsFromDatabase($this->uniqueString, 0);
    $i = PROJECT_PAGE_LOAD_MAX_PROJECTS - 1; 

    $query = 'SELECT * FROM projects WHERE title ILIKE \''.$this->uniqueString.'\' OR description ILIKE \''.$this->uniqueString.'\' ORDER BY upload_time DESC  LIMIT '.(PROJECT_PAGE_LOAD_MAX_PROJECTS).' OFFSET 0';
            
    $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
    $this->assertEquals(false, is_array($result));
    
    // test that projects is$numDbEntries a valid db serach result    
    $this->assertEquals(true, is_array($projects));
    $this->assertEquals(1, count($projects));
    $this->assertEquals("Your search returned no results",$projects[0]['title']);
    $this->assertEquals("Your search returned no results",$projects[0]['title_short']);
    $this->assertRegExp("/images\/symbols\/thumbnail_gray\.png/",$projects[0]['thumbnail']);    
  }
  
  public function testSpecialCharSearch() {        
    $specialStrings = $this->specialCharStrings();
    foreach($specialStrings as $specialString) {
      $this->doUpload($specialString,1);
      $this->doUpload("",2);

      for($i=0; $i<mb_strlen($specialString,'UTF-8'); $i++) {
        $char = mb_substr($specialString, $i, 1,'UTF-8');
        $projects = $this->obj->retrieveSearchResultsFromDatabase($char, 0);
        //echo $char." - ".$projects[0]['title']."\n";
        $this->assertEquals("unitTest1", $projects[0]['title']);
      }   
      $this->deleteUploadedProjects();
    }   
  }
  
  /**
   * @dataProvider randomLongStrings
   */
  public function testShortenTitle($string) {   
    $short = $this->obj->shortenTitle($string);
    $this->assertEquals(PROJECT_TITLE_MAX_DISPLAY_LENGTH, strlen($short));
    $this->assertEquals(0, strcmp(substr($string, 0, strlen($short)), $short));
  }

   public function doUpload($description,$projectcount) {
     for($i=1; $i<= $projectcount; $i++)
     {
       $fileName = 'test.zip';       
       $testFile = dirname(__FILE__).'/testdata/'.$fileName;
       $fileChecksum = md5_file($testFile);
       $fileSize = filesize($testFile);
       $fileType = 'application/x-zip-compressed';      
       
       $formData = array('projectTitle'=>'unitTest'.$i, 'projectDescription'=>$description, 'fileChecksum'=>$fileChecksum);
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

  public function deleteUploadedProjects()  {
     foreach ($this->insertIDArray as $insertId)
     {
       $filePath = CORE_BASE_PATH.PROJECTS_DIRECTORY.$insertId.PROJECTS_EXTENTION;
       // test deleting from database
       $this->upload->removeProjectFromFilesystem($filePath);    
       $this->assertFalse(is_file($filePath));
       @unlink(CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$insertId.PROJECTS_QR_EXTENTION);
       $this->assertFalse(is_file(CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$insertId.PROJECTS_QR_EXTENTION));
       //test deleting from filesystem
       $this->upload->removeProjectFromDatabase($insertId);
       $query = "SELECT * FROM projects WHERE id='$insertId'";
       $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
       $this->assertEquals(0, pg_num_rows($result));
    }
  }  
  
  public function specialCharStrings() {
    $returnArray = array(      
    'ÄÖÜäöüß'.'!"§$%&/()=?`°^²³'.'+*~#_-.:,;<>|µ€@',
    'ČčŠšžŽÅĆÀóŬĉĄĘęåáéŐőâãÇöΛ'.'ÁáçÉéÍíÑñÓóÚúüªº¡¿'.'ÀàÁáÂâÆæÇçÈèÉéÊêËëÎîÏïÔôŒœÙùÛûŸÿ'.''.'ሀሏจ',           
    '德国', '德意志联邦共和國.'.'ܢܐܠܝܬܚܝܠܐܪܒܡܢܚܝܠܐܕܐܡܪܝܩܐ,ܘܐܝܬܠܗ'.'মার্কিনযুক্তরাষ্ট্র'.'Гісторыя'.'Соединённые_Штаты_Америки',
    );          
    return $returnArray;
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
