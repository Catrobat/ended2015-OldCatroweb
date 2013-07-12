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

class uploadTest extends PHPUnit_Framework_TestCase
{
  protected $upload;
  protected $dbConnection;

  protected function setUp() {
    require_once CORE_BASE_PATH.'modules/api/upload.php';
    $this->upload = new upload();
    $this->dbConnection = pg_connect("host=".DB_HOST." dbname=".DB_NAME." user=".DB_USER." password=".DB_PASS)
    or die('Connection to Database failed: ' . pg_last_error());
  }

  protected function tearDown() {
    $this->upload->cleanup();
  }
  
  protected function cleanUserInput($projectTitle) {
    $cleanedProjectTitle = $projectTitle;
    $cleanedProjectTitle = html_entity_decode($cleanedProjectTitle);
    $cleanedProjectTitle = preg_replace("/&#?[a-z0-9]{2,8}/i", "", $cleanedProjectTitle);
    $cleanedProjectTitle = strip_tags($cleanedProjectTitle);
    $cleanedProjectTitle = htmlspecialchars($cleanedProjectTitle);
    $cleanedProjectTitle = trim($cleanedProjectTitle, " ");
    
    return $cleanedProjectTitle;
  }
  

  /**
   * @dataProvider correctPostData
   */
  public function testDoUpload($projectTitle, $projectDescription, $testFile, $fileName, $fileChecksum, $fileSize, $fileType, $uploadLanguage = '') {
    $formData = array(
        'projectTitle' => $projectTitle,
        'projectDescription' => $projectDescription,
        'fileChecksum' => $fileChecksum,
        'userLanguage'=>$uploadLanguage
    );
    $fileData = array(
        'upload' => array(
            'name' => $fileName,
            'type' => $fileType,
            'tmp_name' => $testFile,
            'error' => 0,
            'size'=>$fileSize
        )
    );
    $serverData = array('REMOTE_ADDR'=>'127.0.0.1');
    $fileSize = filesize($testFile);

    $this->upload->doUpload($formData, $fileData, $serverData);
    $insertId = $this->upload->projectId;
    $filePath = CORE_BASE_PATH . PROJECTS_DIRECTORY . $insertId . PROJECTS_EXTENSION;
    $projectPath = CORE_BASE_PATH . PROJECTS_UNZIPPED_DIRECTORY . $insertId;

    $this->assertEquals(200, $this->upload->statusCode);
    $this->assertNotEquals(0, $insertId);
    $this->assertTrue(is_file($filePath));

    $this->assertTrue(is_dir($projectPath));
    $this->assertTrue(is_dir($projectPath . "/images"));
    $this->assertTrue(is_dir($projectPath . "/sounds"));

    $this->assertTrue($this->upload->projectId > 0);

    if($uploadLanguage) {
      $query = "SELECT upload_language FROM projects WHERE id='$insertId'";
      $result = pg_query($this->dbConnection, $query);
      $row = pg_fetch_row($result);
      $this->assertEquals($uploadLanguage, $row[0]);
      pg_free_result($result);
    }
    if($fileSize) {
      $query = "SELECT filesize_bytes FROM projects WHERE id='$insertId'";
      $result = pg_query($this->dbConnection, $query);
      $row = pg_fetch_row($result);
      $this->assertEquals($fileSize, $row[0]);
      pg_free_result($result);
    }
    
    $query = "SELECT title, description FROM projects WHERE id='$insertId'";
    $result = pg_query($this->dbConnection, $query);
    $pg_result = pg_fetch_assoc($result);
    
    $cleanedProjectTitle = $this->cleanUserInput($projectTitle);
    $cleanedProjectDescription = $this->cleanUserInput($projectDescription);
    $this->assertEquals($pg_result['title'], $cleanedProjectTitle);
    $this->assertEquals($pg_result['description'], $cleanedProjectDescription);
    
    pg_free_result($result);

    //test deleting from filesystem
    $this->upload->cleanup();
    $this->assertFalse(is_file($filePath));
    $this->assertFalse(is_file(CORE_BASE_PATH . PROJECTS_THUMBNAIL_DIRECTORY . $insertId . PROJECTS_THUMBNAIL_EXTENSION_SMALL));
    $this->assertFalse(is_file(CORE_BASE_PATH . PROJECTS_THUMBNAIL_DIRECTORY . $insertId . PROJECTS_THUMBNAIL_EXTENSION_LARGE));
    $this->assertFalse(is_file(CORE_BASE_PATH . PROJECTS_THUMBNAIL_DIRECTORY . $insertId . PROJECTS_THUMBNAIL_EXTENSION_ORIG));

    //test deleting from database
    $query = "SELECT * FROM projects WHERE id='$insertId'";
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $this->assertEquals(0, pg_num_rows($result));
  }
  
  /**
   * @dataProvider correctPostDataWithSpecialChars
   */
  public function testDoUploadWithSpecialChars($projectTitle, $projectDescription, $testFile, $fileName, $fileChecksum, $fileSize, $fileType, $uploadLanguage = '') {
    $formData = array(
        'projectTitle' => $projectTitle,
        'projectDescription' => $projectDescription,
        'fileChecksum' => $fileChecksum,
        'userLanguage'=>$uploadLanguage
    );
    $fileData = array(
        'upload' => array(
            'name' => $fileName,
            'type' => $fileType,
            'tmp_name' => $testFile,
            'error' => 0,
            'size'=>$fileSize
        )
    );
    
    $serverData = array('REMOTE_ADDR'=>'127.0.0.1');
    $fileSize = filesize($testFile);
  
    $this->upload->doUpload($formData, $fileData, $serverData);
    $insertId = $this->upload->projectId;
    $filePath = CORE_BASE_PATH . PROJECTS_DIRECTORY . $insertId . PROJECTS_EXTENSION;
    $projectPath = CORE_BASE_PATH . PROJECTS_UNZIPPED_DIRECTORY . $insertId;
  
    $this->assertEquals(200, $this->upload->statusCode);
    $this->assertNotEquals(0, $insertId);
    $this->assertTrue(is_file($filePath));
  
    $this->assertTrue(is_dir($projectPath));
    $this->assertTrue(is_dir($projectPath . "/images"));
    $this->assertTrue(is_dir($projectPath . "/sounds"));
  
    $this->assertTrue($this->upload->projectId > 0);
  
    $query = "SELECT title, description FROM projects WHERE id='$insertId'";
    $result = pg_query($this->dbConnection, $query);
    $pg_result = pg_fetch_assoc($result);

    $cleanedProjectTitle = $this->cleanUserInput($projectTitle);
    $cleanedProjectDescription = $this->cleanUserInput($projectDescription);
    $this->assertEquals($pg_result['title'], $cleanedProjectTitle);
    $this->assertEquals($pg_result['description'], $cleanedProjectDescription);
    
    pg_free_result($result);
  
    //test deleting from filesystem
    $this->upload->cleanup();
    $this->assertFalse(is_file($filePath));
    $this->assertFalse(is_file(CORE_BASE_PATH . PROJECTS_THUMBNAIL_DIRECTORY . $insertId . PROJECTS_THUMBNAIL_EXTENSION_SMALL));
    $this->assertFalse(is_file(CORE_BASE_PATH . PROJECTS_THUMBNAIL_DIRECTORY . $insertId . PROJECTS_THUMBNAIL_EXTENSION_LARGE));
    $this->assertFalse(is_file(CORE_BASE_PATH . PROJECTS_THUMBNAIL_DIRECTORY . $insertId . PROJECTS_THUMBNAIL_EXTENSION_ORIG));
  
    //test deleting from database
    $query = "SELECT * FROM projects WHERE id='$insertId'";
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $this->assertEquals(0, pg_num_rows($result));
  }

  /**
   * @dataProvider incorrectPostData
   */
  public function testDoUploadFail($projectTitle, $projectDescription, $testFile, $fileName, $fileChecksum, $fileSize, $fileType, $expectedStatusCode, $uploadEmail = '', $uploadLanguage = '') {
    $formData = array(
        'projectTitle' => $projectTitle,
        'projectDescription' => $projectDescription,
        'fileChecksum' => $fileChecksum,
        'userEmail' => $uploadEmail,
        'userLanguage' => $uploadLanguage
    );
    $fileData = array(
        'upload' => array(
            'name' => $fileName,
            'type' => $fileType,
            'tmp_name' => $testFile,
            'error' => 0,
            'size'=>$fileSize
        )
    );
    $serverData = array('REMOTE_ADDR' => '127.0.0.1');

    $insertId = intval($this->upload->doUpload($formData, $fileData, $serverData));
    
    $this->assertNotEquals(200, $this->upload->statusCode);
    $this->assertEquals($expectedStatusCode, $this->upload->statusCode);
    $this->assertFalse($this->upload->projectId > 0);
  }

  /**
   * @dataProvider correctVersionData
   */
  public function testDoUploadCorrectVersion($projectTitle, $projectDescription, $fileName, $fileType, $versionCode, $versionName, $uploadEmail = '', $uploadLanguage = '') {
    $testFile = dirname(__FILE__) . '/testdata/' . $fileName;
    $fileChecksum = md5_file($testFile);
    $fileSize = filesize($testFile);

    $formData = array(
        'projectTitle' => $projectTitle,
        'projectDescription' => $projectDescription,
        'fileChecksum' => $fileChecksum,
        'userEmail' => $uploadEmail,
        'userLanguage' => $uploadLanguage
    );
    $fileData = array(
        'upload' => array(
            'name' => $fileName,
            'type' => $fileType,
            'tmp_name' => $testFile,
            'error' => 0,
            'size' => $fileSize
        )
    );
    $serverData = array('REMOTE_ADDR' => '127.0.0.1');
    $fileSize = filesize($testFile);

    $this->upload->doUpload($formData, $fileData, $serverData);
    $insertId = $this->upload->projectId;
    $filePath = CORE_BASE_PATH . PROJECTS_DIRECTORY . $insertId . PROJECTS_EXTENSION;
    $projectPath = CORE_BASE_PATH . PROJECTS_UNZIPPED_DIRECTORY . $insertId;

    $this->assertEquals(200, $this->upload->statusCode);
    $this->assertNotEquals(0, $insertId);
    $this->assertTrue(is_file($filePath));
    $this->assertTrue($this->upload->projectId > 0);

    $this->assertEquals($versionName, $this->getVersionInfo($insertId, "versionName"));
    $this->assertEquals($versionCode, $this->getVersionInfo($insertId, "versionCode"));

    // cleanup
    $this->upload->cleanup();
  }

  /**
   * @dataProvider incorrectVersionData
   */
  public function testDoUploadWrongVersion($projectTitle, $projectDescription, $fileName, $fileType, $versionCode, $versionName, $uploadEmail = '', $uploadLanguage = '') {
    $testFile = dirname(__FILE__).'/testdata/'.$fileName;
    $fileChecksum = md5_file($testFile);
    $fileSize = filesize($testFile);
    $formData = array(
        'projectTitle' => $projectTitle,
        'projectDescription' => $projectDescription,
        'fileChecksum' => $fileChecksum,
        'userEmail' => $uploadEmail,
        'userLanguage' => $uploadLanguage
    );
    $fileData = array(
        'upload' => array(
            'name' => $fileName,
            'type' => $fileType,
            'tmp_name' => $testFile,
            'error' => 0,
            'size' => $fileSize
        )
    );
    $serverData = array('REMOTE_ADDR' => '127.0.0.1');
    $fileSize = filesize($testFile);

    $this->upload->doUpload($formData, $fileData, $serverData);
    $insertId = $this->upload->projectId;
    $filePath = CORE_BASE_PATH . PROJECTS_DIRECTORY . $insertId . PROJECTS_EXTENSION;
    $projectPath = CORE_BASE_PATH . PROJECTS_UNZIPPED_DIRECTORY . $insertId;

    $this->assertEquals(200, $this->upload->statusCode);
    $this->assertNotEquals(0, $insertId);
    $this->assertTrue(is_file($filePath));
    $this->assertTrue($this->upload->projectId > 0);

    $this->assertNotEquals($versionName, $this->getVersionInfo($insertId, "versionName"));
    $this->assertNotEquals($versionCode, $this->getVersionInfo($insertId, "versionCode"));

    // cleanup
    $this->upload->cleanup();
  }
  
  /**
   * @dataProvider correctLicenses
   */
  public function testDoUploadCorrectLicenses($projectTitle, $projectDescription, $fileName, $fileType, $versionCode, $versionName, $uploadEmail = '', $uploadLanguage = '') {
    $testFile = dirname(__FILE__) . '/testdata/' . $fileName;
    $fileChecksum = md5_file($testFile);
    $fileSize = filesize($testFile);
  
    $formData = array(
        'projectTitle' => $projectTitle,
        'projectDescription' => $projectDescription,
        'fileChecksum' => $fileChecksum,
        'userEmail' => $uploadEmail,
        'userLanguage' => $uploadLanguage
    );
    $fileData = array(
        'upload' => array(
            'name' => $fileName,
            'type' => $fileType,
            'tmp_name' => $testFile,
            'error' => 0,
            'size' => $fileSize
        )
    );
    $serverData = array('REMOTE_ADDR' => '127.0.0.1');
    $fileSize = filesize($testFile);
  
    $this->upload->doUpload($formData, $fileData, $serverData);
    $insertId = $this->upload->projectId;
    $filePath = CORE_BASE_PATH . PROJECTS_DIRECTORY . $insertId . PROJECTS_EXTENSION;
    $projectPath = CORE_BASE_PATH . PROJECTS_UNZIPPED_DIRECTORY . $insertId;
  
    $this->assertEquals(200, $this->upload->statusCode);
  
    $xmlFile = $this->getProjectXmlFile($projectPath . '/');
  
    $dom = new DOMDocument();
    $dom->load($xmlFile);
  
    $mediaLicense = $dom->getElementsByTagName('mediaLicense');
    $programLicense = $dom->getElementsByTagName('programLicense');
  
    foreach($mediaLicense as $value)
      $this->assertEquals($value->nodeValue, PROJECT_MEDIA_LICENSE);
  
    foreach($programLicense as $value)
      $this->assertEquals($value->nodeValue, PROJECT_PROGRAM_LICENSE);
  
    // cleanup
    $this->upload->cleanup();
  }
  
  /**
   * @dataProvider incorrectLicenses
   */
  public function testDoUploadInvalidMediaLicenses($projectTitle, $projectDescription, $fileName, $fileType, $versionCode, $versionName, $uploadEmail = '', $uploadLanguage = '') {
    $testFile = dirname(__FILE__).'/testdata/'.$fileName;
    $fileChecksum = md5_file($testFile);
    $fileSize = filesize($testFile);
    $formData = array(
        'projectTitle' => $projectTitle,
        'projectDescription' => $projectDescription,
        'fileChecksum' => $fileChecksum,
        'userEmail' => $uploadEmail,
        'userLanguage' => $uploadLanguage
    );
    $fileData = array(
        'upload' => array(
            'name' => $fileName,
            'type' => $fileType,
            'tmp_name' => $testFile,
            'error' => 0,
            'size' => $fileSize
        )
    );
    $serverData = array('REMOTE_ADDR' => '127.0.0.1');
    $fileSize = filesize($testFile);
  
    $this->upload->doUpload($formData, $fileData, $serverData);
    $insertId = $this->upload->projectId;
    $filePath = CORE_BASE_PATH . PROJECTS_DIRECTORY . $insertId . PROJECTS_EXTENSION;
    $projectPath = CORE_BASE_PATH . PROJECTS_UNZIPPED_DIRECTORY . $insertId;
  
    $this->assertTrue($this->upload->statusCode >= 520);
    $this->assertTrue($this->upload->statusCode <= 521);
  
    // cleanup
    $this->upload->cleanup();
  }
  
  /**
   * @dataProvider correctRemixInfo
   */
  public function testDoUploadCorrectRemixInfo($projectTitle, $projectDescription, $fileName, $fileType, $versionCode, $versionName, $uploadEmail = '', $uploadLanguage = '') {
    $testFile = dirname(__FILE__).'/testdata/'.$fileName;
    $fileChecksum = md5_file($testFile);
    $fileSize = filesize($testFile);
    $formData = array(
        'projectTitle' => $projectTitle,
        'projectDescription' => $projectDescription,
        'fileChecksum' => $fileChecksum,
        'userEmail' => $uploadEmail,
        'userLanguage' => $uploadLanguage
    );
    $fileData = array(
        'upload' => array(
            'name' => $fileName,
            'type' => $fileType,
            'tmp_name' => $testFile,
            'error' => 0,
            'size' => $fileSize
        )
    );
    $serverData = array('REMOTE_ADDR' => '127.0.0.1');
    $fileSize = filesize($testFile);
  
    $this->upload->doUpload($formData, $fileData, $serverData);
    $insertId = $this->upload->projectId;
    $filePath = CORE_BASE_PATH . PROJECTS_DIRECTORY . $insertId . PROJECTS_EXTENSION;
    $projectPath = CORE_BASE_PATH . PROJECTS_UNZIPPED_DIRECTORY . $insertId;
  
    $this->assertEquals(200, $this->upload->statusCode);
  
    $xmlFile = $this->getProjectXmlFile($projectPath . '/');
  
    $dom = new DOMDocument();
    $dom->load($xmlFile);
  
    $url = $dom->getElementsByTagName('url');
    $userHandle = $dom->getElementsByTagName('userHandle');
  
    foreach($url as $value)
      $this->assertTrue(isset($value->nodeValue));
    foreach($userHandle as $value)
      $this->assertTrue(isset($value->nodeValue));
  
    // cleanup
    $this->upload->cleanup();
  }
  
  /**
   * @dataProvider correctRemixUpdate
   */
  public function testDoUploadCorrectRemixUpdate($projectTitle, $projectDescription, $fileName, $fileType, $versionCode, $versionName, $uploadEmail = '', $uploadLanguage = '') {
    $testFile = dirname(__FILE__).'/testdata/'.$fileName;
    $fileChecksum = md5_file($testFile);
    $fileSize = filesize($testFile);
    $formData = array(
        'projectTitle' => $projectTitle,
        'projectDescription' => $projectDescription,
        'fileChecksum' => $fileChecksum,
        'userEmail' => $uploadEmail,
        'userLanguage' => $uploadLanguage
    );
    $fileData = array(
        'upload' => array(
            'name' => $fileName,
            'type' => $fileType,
            'tmp_name' => $testFile,
            'error' => 0,
            'size' => $fileSize
        )
    );
    $serverData = array('REMOTE_ADDR' => '127.0.0.1');
    $fileSize = filesize($testFile);
  
    $this->upload->doUpload($formData, $fileData, $serverData);
    $this->assertEquals(200, $this->upload->statusCode);
    
    $this->upload->doUpload($formData, $fileData, $serverData);
    
    $insertId = $this->upload->projectId;
    $filePath = CORE_BASE_PATH . PROJECTS_DIRECTORY . $insertId . PROJECTS_EXTENSION;
    $projectPath = CORE_BASE_PATH . PROJECTS_UNZIPPED_DIRECTORY . $insertId;
  
    $this->assertEquals(200, $this->upload->statusCode);
  
    $xmlFile = $this->getProjectXmlFile($projectPath . '/');
  
    $dom = new DOMDocument();
    $dom->load($xmlFile);
  
    $remixOf = $dom->getElementsByTagName('remixOf');
    $url = $dom->getElementsByTagName('url');
    $userHandle = $dom->getElementsByTagName('userHandle');
    $projectName = $dom->getElementsByTagName('programName');
  
    
    foreach($remixOf as $val_remix) {
      foreach($url as $val_url) {
         $this->assertNotEquals($val_remix->nodeValue, $val_url->nodeValue);
      }
    }
    
    foreach($userHandle as $value)
      $this->assertTrue(isset($value->nodeValue));
    foreach($url as $value)
      $this->assertTrue(isset($value->nodeValue));
  
    // cleanup
    $this->upload->cleanup();
  }
  
  /**
   * @dataProvider correctRemixofIdInDatabase
   */
  public function testDoUploadWithCorrectRemixofIdInDatabase($projectTitle, $projectDescription, $fileName, $fileType, $versionCode, $versionName, $uploadEmail = '', $uploadLanguage = '') {
    $testFile = dirname(__FILE__).'/testdata/'.$fileName;
    $fileChecksum = md5_file($testFile);
    $fileSize = filesize($testFile);
    $formData = array(
        'projectTitle' => $projectTitle,
        'projectDescription' => $projectDescription,
        'fileChecksum' => $fileChecksum,
        'userEmail' => $uploadEmail,
        'userLanguage' => $uploadLanguage
    );
    $fileData = array(
        'upload' => array(
            'name' => $fileName,
            'type' => $fileType,
            'tmp_name' => $testFile,
            'error' => 0,
            'size' => $fileSize
        )
    );
    $serverData = array('REMOTE_ADDR' => '127.0.0.1');
    $fileSize = filesize($testFile);
  
    $this->upload->doUpload($formData, $fileData, $serverData);
    $this->assertEquals(200, $this->upload->statusCode);
    
    $insertId = $this->upload->projectId;
    $projectPath = CORE_BASE_PATH . PROJECTS_UNZIPPED_DIRECTORY . $insertId;
    
    // get remixOf ID from XML
    $xmlFile = $this->getProjectXmlFile($projectPath . '/');
    $dom = new DOMDocument();
    $dom->load($xmlFile);
    $remixOf = $dom->getElementsByTagName('remixOf');
    foreach($remixOf as $value)
      $id_xml = intval(str_replace(PROJECT_URL_TEXT, "", $value->nodeValue));
    
    $this->assertTrue(isset($id_xml));
    
    // get remixOf ID from Database
    $query = "SELECT * FROM projects WHERE remixof='$id_xml'";
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $this->assertEquals(1, pg_num_rows($result));    
    while($row = pg_fetch_array($result))
      $id_db = $row['remixof'];
    
    // remixOf ID must be same in XML and Database!
    $this->assertEquals($id_xml, $id_db);
    
    // cleanup
    $this->upload->cleanup();    
  }

  /**
   * @dataProvider correctPostDataThumbailInRootFolderPNG
   */
  public function testDoUploadWithThumbnailInRootFolderPNG($projectTitle, $projectDescription, $testFile, $fileName, $fileChecksum, $fileSize, $fileType, $uploadEmail = '', $uploadLanguage = '') {
    $formData = array(
        'projectTitle' => $projectTitle,
        'projectDescription' => $projectDescription,
        'fileChecksum' => $fileChecksum,
        'userEmail' => $uploadEmail,
        'userLanguage' => $uploadLanguage
    );
    $fileData = array(
        'upload' => array(
            'name' => $fileName,
            'type' => $fileType,
            'tmp_name' => $testFile,
            'error' => 0,
            'size' => $fileSize
        )
    );
    $serverData = array('REMOTE_ADDR' => '127.0.0.1');
    $this->upload->doUpload($formData, $fileData, $serverData);
    $insertId = $this->upload->projectId;
    $filePath = CORE_BASE_PATH . PROJECTS_DIRECTORY . $insertId . PROJECTS_EXTENSION;

    $this->assertEquals(200, $this->upload->statusCode);
    $this->assertNotEquals(0, $insertId);
    $this->assertTrue(is_file($filePath));
    $this->assertTrue($this->upload->projectId > 0);

    // check thumbnails
    $this->assertTrue(is_file(CORE_BASE_PATH . PROJECTS_THUMBNAIL_DIRECTORY . $insertId . PROJECTS_THUMBNAIL_EXTENSION_SMALL));
    $this->assertTrue(is_file(CORE_BASE_PATH . PROJECTS_THUMBNAIL_DIRECTORY . $insertId . PROJECTS_THUMBNAIL_EXTENSION_LARGE));
    $this->assertTrue(is_file(CORE_BASE_PATH . PROJECTS_THUMBNAIL_DIRECTORY . $insertId . PROJECTS_THUMBNAIL_EXTENSION_ORIG));

    //test deleting from filesystem
    $this->upload->cleanup();
    $this->assertFalse(is_file($filePath));
    $this->assertFalse(is_file(CORE_BASE_PATH . PROJECTS_THUMBNAIL_DIRECTORY . $insertId . PROJECTS_THUMBNAIL_EXTENSION_SMALL));
    $this->assertFalse(is_file(CORE_BASE_PATH . PROJECTS_THUMBNAIL_DIRECTORY . $insertId . PROJECTS_THUMBNAIL_EXTENSION_LARGE));
    $this->assertFalse(is_file(CORE_BASE_PATH . PROJECTS_THUMBNAIL_DIRECTORY . $insertId . PROJECTS_THUMBNAIL_EXTENSION_ORIG));

    //test deleting from database
    $query = "SELECT * FROM projects WHERE id='$insertId'";
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $this->assertEquals(1, pg_num_rows($result));
  }

  /**
   * @dataProvider incorrectPostDataWithThumbnail
   */
  public function testDoUploadWithThumbnailFail($projectTitle, $projectDescription, $testFile, $fileName, $fileChecksum, $fileSize, $fileType, $uploadEmail = '', $uploadLanguage = '') {
    $formData = array(
        'projectTitle' => $projectTitle,
        'projectDescription' => $projectDescription,
        'fileChecksum' => $fileChecksum,
        'userEmail' => $uploadEmail,
        'userLanguage' => $uploadLanguage
    );
    $fileData = array(
        'upload' => array(
            'name' => $fileName,
            'type' => $fileType,
            'tmp_name' => $testFile,
            'error' => 0,
            'size' => $fileSize
        )
    );
    
    $serverData = array('REMOTE_ADDR' => '127.0.0.1');
    $this->upload->doUpload($formData, $fileData, $serverData);
    $insertId = $this->upload->projectId;
    $filePath = CORE_BASE_PATH . PROJECTS_DIRECTORY . $insertId . PROJECTS_EXTENSION;

    $this->assertEquals(200, $this->upload->statusCode);
    $this->assertNotEquals(0, $insertId);
    $this->assertTrue(is_file($filePath));
    $this->assertTrue($this->upload->projectId > 0);

    // check thumbnails
    $this->assertFalse(is_file(CORE_BASE_PATH . PROJECTS_THUMBNAIL_DIRECTORY . $insertId . PROJECTS_THUMBNAIL_EXTENSION_SMALL));
    $this->assertFalse(is_file(CORE_BASE_PATH . PROJECTS_THUMBNAIL_DIRECTORY . $insertId . PROJECTS_THUMBNAIL_EXTENSION_LARGE));
    $this->assertFalse(is_file(CORE_BASE_PATH . PROJECTS_THUMBNAIL_DIRECTORY . $insertId . PROJECTS_THUMBNAIL_EXTENSION_ORIG));

    //test deleting from filesystem
    $this->upload->cleanup();
    $this->assertFalse(is_file($filePath));

    //test deleting from database
    $query = "SELECT * FROM projects WHERE id='$insertId'";
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $this->assertEquals(0, pg_num_rows($result));
  }

  /* *** DATA PROVIDERS *** */
  public function correctPostData() {
    $fileName = 'test.zip';
    $fileNameWithThumbnail = 'test2.zip';
    $testFile = dirname(__FILE__) . '/testdata/' . $fileName;
    $testFileWithThumbnail = dirname(__FILE__) . '/testdata/' . $fileNameWithThumbnail;
    $fileChecksum = md5_file($testFile);
    $fileChecksumWithThumbnail = md5_file($testFileWithThumbnail);

    $testFileCatroid = dirname(__FILE__) . '/testdata/test.catrobat';
    $fileChecksumCatroid = md5_file($testFileCatroid);
    $fileSizeCatroid = filesize($testFileCatroid);

    $fileSize = filesize($testFile);
    $fileSizeWithThumbnail = filesize($testFileWithThumbnail);
    $fileType = 'application/x-zip-compressed';
    $dataArray = array(
        array('unitTest', 'my project description', $testFile, $fileName, $fileChecksum, $fileSize, $fileType),
        array('unitTest with empty description', '', $testFile, $fileName, $fileChecksum, $fileSize, $fileType),
        array('unitTest with a very very very very long title and no description, hopefully not too long', 'description', $testFile, $fileName, $fileChecksum, $fileSize, $fileType),
        array("unitTest with special chars: ä, ü, ö ' ", "jüßt 4 spècia1 char **test** ' %&()[]{}_|~#", $testFile, $fileName, $fileChecksum, $fileSize, $fileType),
        array('unitTest with included Thumbnail', 'this project contains its thumbnail inside the zip file', $testFileWithThumbnail, $fileNameWithThumbnail, $fileChecksumWithThumbnail, $fileSizeWithThumbnail, $fileType),
        array('unitTest with long description and uppercase fileChecksum', 'this is a long description. this is a long description. this is a long description. this is a long description. this is a long description. this is a long description. this is a long description. this is a long description. this is a long description. this is a long description.', $testFile, $fileName, strtoupper($fileChecksum), $fileSize, $fileType),
        array('unitTest with Email and Language', 'description', $testFile, $fileName, $fileChecksum, $fileSize, $fileType, 'en'),
        array('unitTest', 'my project description with thumbnail in root folder.', $testFile, 'test2.zip', $fileChecksum, $fileSize, $fileType),
        array('unitTest', 'my project description with thumbnail in images folder.', $testFile, 'test3.zip', $fileChecksum, $fileSize, $fileType),
        array('unitTest', 'project with new extention "catroid".', dirname(__FILE__).'/testdata/test.catrobat', 'test.catrobat', $fileChecksumCatroid, $fileSizeCatroid, $fileType),        
    );
    return $dataArray;
  }
  
  public function correctPostDataWithSpecialChars() {
    $fileName = 'test.zip';
    $fileNameWithThumbnail = 'test2.zip';
    $testFile = dirname(__FILE__) . '/testdata/' . $fileName;
    $testFileWithThumbnail = dirname(__FILE__) . '/testdata/' . $fileNameWithThumbnail;
    $fileChecksum = md5_file($testFile);
    $fileChecksumWithThumbnail = md5_file($testFileWithThumbnail);
    
    $testFileCatroid = dirname(__FILE__) . '/testdata/test.catrobat';
    $fileChecksumCatroid = md5_file($testFileCatroid);
    $fileSizeCatroid = filesize($testFileCatroid);
    
    $fileSize = filesize($testFile);
    $fileSizeWithThumbnail = filesize($testFileWithThumbnail);
    $fileType = 'application/x-zip-compressed';
    $dataArray = array(
        array("phpunitTest don't escape", 'uploadTest project with quote (\') in title".', dirname(__FILE__).'/testdata/test.catrobat', 'test.catrobat', $fileChecksumCatroid, $fileSizeCatroid, $fileType),
        array('phpunitTest <br/>', '<uploadTest>".', dirname(__FILE__).'/testdata/test.catrobat', 'test.catrobat', $fileChecksumCatroid, $fileSizeCatroid, $fileType),
        array('phpUnitTest <!-- -->', '<uploadTest>".', dirname(__FILE__).'/testdata/test.catrobat', 'test.catrobat', $fileChecksumCatroid, $fileSizeCatroid, $fileType),
        array('   phpunittestspacesbeforeandafter   ', 'project title should be without leading and trailing spaces".', dirname(__FILE__).'/testdata/test.catrobat', 'test.catrobat', $fileChecksumCatroid, $fileSizeCatroid, $fileType),
        array('unitTestWithHtmlTag <!-- abc -->', 'html tags should be cleaned".', dirname(__FILE__).'/testdata/test.catrobat', 'test.catrobat', $fileChecksumCatroid, $fileSizeCatroid, $fileType),
        array('unitTestWithHtmlTag %% abc <html></html>', 'html tags should be cleaned".', dirname(__FILE__).'/testdata/test.catrobat', 'test.catrobat', $fileChecksumCatroid, $fileSizeCatroid, $fileType),
        array('"user@gmail.com"', 'uploadtest  ".', dirname(__FILE__).'/testdata/test.catrobat', 'test.catrobat', $fileChecksumCatroid, $fileSizeCatroid, $fileType),
        array("<html><head><title>MyTitle</title></head><body><a href=\"javascript:alert(\'This is a phpunittest!\')\">Clickme</a></body></html>'", 'uploadtest project title should be MyTitle ClickMe".', dirname(__FILE__).'/testdata/test.catrobat', 'test.catrobat', $fileChecksumCatroid, $fileSizeCatroid, $fileType),
        array("'''''''''''''", 'phpunittest', dirname(__FILE__).'/testdata/test.catrobat', 'test.catrobat', $fileChecksumCatroid, $fileSizeCatroid, $fileType),
    );
    return $dataArray;
  }

  public function incorrectPostData() {
    $validFileName = 'test.zip';
    $invalidFileName = 'nonExistingFile.zip';
    $corruptFileName = 'test_invalid_projectfile.zip';
    $oldVersionFileName = 'test0.3.catrobat';
    $validTestFile = dirname(__FILE__) . '/testdata/' . $validFileName;
    $invalidTestFile = dirname(__FILE__) . '/testdata/' . $invalidFileName;
    $corruptTestFile = dirname(__FILE__) . '/testdata/' . $corruptFileName;
    $oldVersionFile = dirname(__FILE__) . '/testdata/' . $oldVersionFileName;
    $validFileChecksum = md5_file($validTestFile);
    $corruptFileChecksum = md5_file($corruptTestFile);
    $invalidFileChecksum = 'invalidfilechecksum';
    $oldVersionFileChecksum = md5_file($oldVersionFile);
    $validFileSize = filesize($validTestFile);
    $corruptFileSize = filesize($corruptTestFile);
    $oldVersionFileSize = filesize($oldVersionFile);
    $fileType = 'application/x-zip-compressed';
    $dataArray = array(
        array('uploadTestFail1', 'this project uses a non existing file for upload', $invalidTestFile, $invalidFileName, $validFileChecksum, 0, $fileType, STATUS_CODE_UPLOAD_MISSING_DATA),
        array('uploadTestFail2', 'this project has a too large project file', $validTestFile, $validFileName, $validFileChecksum, 200000000, $fileType, STATUS_CODE_UPLOAD_EXCEEDING_FILESIZE),
        array('uploadTestFail3', 'no file checksum is send together with this project', $validTestFile, $validFileName, '', $validFileSize, $fileType, STATUS_CODE_UPLOAD_MISSING_CHECKSUM),
        array('uploadTestFail4', 'this project has an invalid fileChecksum', $validTestFile, $validFileName, $invalidFileChecksum, $validFileSize, $fileType, STATUS_CODE_UPLOAD_INVALID_CHECKSUM),
        array('uploadTestFail5', 'this project contains an corrupt spf xml file', $corruptTestFile, $corruptFileName, $corruptFileChecksum, $corruptFileSize, $fileType, STATUS_CODE_UPLOAD_INVALID_XML),
        array('defaultProject', 'this project is named defaultProject', $validTestFile, $validFileName, $validFileChecksum, $validFileSize, $fileType, STATUS_CODE_UPLOAD_DEFAULT_PROJECT_TITLE),
        array('uploadTestFail8 fucking project title', 'this project has an insulting projectTitle', $validTestFile, $validFileName, $validFileChecksum, $validFileSize, $fileType, STATUS_CODE_UPLOAD_RUDE_PROJECT_TITLE),
        array('uploadTestFail9', 'this project has an insulting projectDescription - Fuck!', $validTestFile, $validFileName, $validFileChecksum, $validFileSize, $fileType, STATUS_CODE_UPLOAD_RUDE_PROJECT_DESCRIPTION),
        array('uploadTestFail10', 'this project has an old catrobatLanguageVersion!', $oldVersionFile, $oldVersionFileName, $oldVersionFileChecksum, $oldVersionFileSize, $fileType, STATUS_CODE_UPLOAD_OLD_CATROBAT_LANGUAGE),
        array('', 'this project has no project title', $validTestFile, $validFileName, $validFileChecksum, $validFileSize, $fileType, STATUS_CODE_UPLOAD_MISSING_PROJECT_TITLE),
        array('<!-- --> <br/>', 'this project should also have an empty project title after being cleaned.', $validTestFile, $validFileName, $validFileChecksum, $validFileSize, $fileType, STATUS_CODE_UPLOAD_MISSING_PROJECT_TITLE),
        array('<!-- abc -->', 'this project should also have an empty project title after being cleaned.".', $validTestFile, $validFileName, $validFileChecksum, $validFileSize, $fileType, STATUS_CODE_UPLOAD_MISSING_PROJECT_TITLE),
        array('  ', 'phpuploadtest with tab in project title".', $validTestFile, $validFileName, $validFileChecksum, $validFileSize, $fileType, STATUS_CODE_UPLOAD_MISSING_PROJECT_TITLE),
        array('    ', 'phpuploadtest with tab, LF, CR, in project title".', $validTestFile, $validFileName, $validFileChecksum, $validFileSize, $fileType, STATUS_CODE_UPLOAD_MISSING_PROJECT_TITLE),
    );
    return $dataArray;
  }

  public function correctVersionData() {
    $fileType = 'application/x-zip-compressed';
    $dataArray = array(
        array('unitTest for correct version info 0.6.0b', 'my project description for correct version info.', 'test-0.7.0beta.catrobat', $fileType, 0.8, '0.7.3beta')
    );
    return $dataArray;
  }
  
  public function incorrectVersionData() {
    $fileType = 'application/x-zip-compressed';
    $dataArray = array(
        array('unitTest for incorrect version info 0.5a-xxx', 'my project description for incorrect version info.', 'test.zip', $fileType, 499, '0.6.0beta'),
        array('unitTest for incorrect version info 0.5.1', 'my project description for incorrect version info.', 'test2.zip', $fileType, 399, '0.5.4beta')
    );
    return $dataArray;
  }
  
  public function correctLicenses() {
    $fileType = 'application/x-zip-compressed';
    $dataArray = array(
        array('unitTest for correct media and program license', 'my project with correct media and program liense.', 'test_license.catrobat', $fileType, 0.8, '0.7.3beta')
    );
    return $dataArray;
  }
  
  public function incorrectLicenses() {
    $fileType = 'application/x-zip-compressed';
    $dataArray = array(
        array('unitTest for incorrect media license', 'my project with invalid media license', 'test_invalid_license1.catrobat', $fileType, 0.8, '0.7.3beta'),
        array('unitTest for incorrect program license', 'my project with invalid program license', 'test_invalid_license2.catrobat', $fileType, 0.8, '0.7.3beta')
    );
    return $dataArray;
  }
  
  public function correctRemixInfo() {
    $fileType = 'application/x-zip-compressed';
    $dataArray = array(
        array('unitTest for correct remix info', 'my project with correct remixing information.', 'test_remix.catrobat', $fileType, 0.6, '0.7.3beta')
    );
    return $dataArray;
  }
  
  public function correctRemixUpdate() {
    $fileType = 'application/x-zip-compressed';
    $dataArray = array(
        array('unitTest for correct remix update', 'my project with correct remixing update.', 'test_remix_update_1.catrobat', $fileType, 0.8, '0.7.3beta')
    );
    return $dataArray;
  }
  
  public function correctRemixofIdInDatabase() {
    $fileType = 'application/x-zip-compressed';
    $dataArray = array(
        array('unitTest for correct RemixOf ID in database', 'my project with correct remixOf id in db', 'test_remix.catrobat', $fileType, 0.8, '0.7.3beta')
    );
    return $dataArray;
  }

  public function correctPostDataThumbailInRootFolderPNG() {
    $fileName = 'test_thumbnail.zip';
    $testFile = dirname(__FILE__) . '/testdata/' . $fileName;
    $testFileDir = dirname(__FILE__) . '/testdata/';
    $fileChecksum = md5_file($testFile);
    $fileSize = filesize($testFile);
    $fileType = 'application/x-zip-compressed';

    $testFile1 = 'test_thumbnail_240x400.zip';
    $testFileDir1 = $testFileDir . 'test_thumbnail_240x400.zip';
    $testFile2 = 'test_thumbnail_480x800.zip';
    $testFileDir2 = $testFileDir . 'test_thumbnail_480x800.zip';
    $testFile3 = 'test_thumbnail_240x240.zip';
    $testFileDir3 = $testFileDir . 'test_thumbnail_240x240.zip';
    $testFile4 = 'test_thumbnail_480x480.zip';
    $testFileDir4 = $testFileDir . 'test_thumbnail_480x480.zip';
    $testFile5 = 'test_thumbnail_400x400.zip';
    $testFileDir5 = $testFileDir . 'test_thumbnail_400x400.zip';
    $testFile6 = 'test_thumbnail_800x800.zip';
    $testFileDir6 = $testFileDir . 'test_thumbnail_800x800.zip';
    $testFile7 = 'test_thumbnail_960x1600.zip';
    $testFileDir7 = $testFileDir . 'test_thumbnail_960x1600.zip';
    $testFile8 = 'test_thumbnail_400x240.zip';
    $testFileDir8 = $testFileDir . 'test_thumbnail_400x240.zip';
    $testFile9 = 'test_thumbnail_800x480.zip';
    $testFileDir9 = $testFileDir . 'test_thumbnail_800x480.zip';

    $dataArray = array(
        array('unitTest', 'default thumbnail', $testFile, $fileName, $fileChecksum, $fileSize, $fileType),
        array('unitTest', 'thumbnail 240x400.', $testFileDir1, $testFile1, md5_file($testFileDir1), filesize($testFileDir1), $fileType),
        array('unitTest', 'thumbnail 480x800.', $testFileDir2, $testFile2, md5_file($testFileDir2), filesize($testFileDir2), $fileType),
        array('unitTest', 'thumbnail 240x240.', $testFileDir3, $testFile3, md5_file($testFileDir3), filesize($testFileDir3), $fileType),
        array('unitTest', 'thumbnail 480x480.', $testFileDir4, $testFile4, md5_file($testFileDir4), filesize($testFileDir4), $fileType),
        array('unitTest', 'thumbnail 400x400.', $testFileDir5, $testFile5, md5_file($testFileDir5), filesize($testFileDir5), $fileType),
        array('unitTest', 'thumbnail 800x800.', $testFileDir6, $testFile6, md5_file($testFileDir6), filesize($testFileDir6), $fileType),
        array('unitTest', 'thumbnail 960x1600.', $testFileDir7, $testFile7, md5_file($testFileDir7), filesize($testFileDir7), $fileType),
        array('unitTest', 'thumbnail 400x240.', $testFileDir8, $testFile8, md5_file($testFileDir8), filesize($testFileDir8), $fileType),
        array('unitTest', 'thumbnail 800x480.', $testFileDir9, $testFile9, md5_file($testFileDir9), filesize($testFileDir9), $fileType)
    );
    return $dataArray;
  }

  public function incorrectPostDataWithThumbnail() {
    $fileName = 'test_missing_thumbnail.zip';
    $testFile = dirname(__FILE__) . '/testdata/' . $fileName;
    $fileChecksum = md5_file($testFile);
    $fileSize = filesize($testFile);
    $fileType = 'application/x-zip-compressed';
    $dataArray = array(
        array('unitTest', 'my project description without screenshot of type PNG.', $testFile, $fileName, $fileChecksum, $fileSize, $fileType)
    );
    return $dataArray;
  }

  private function getVersionInfo($projectId, $col) {
    $query = "SELECT projects.*, cusers.username AS uploaded_by FROM projects, cusers WHERE projects.id=$projectId AND cusers.id=projects.user_id LIMIT 1";
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $projects = pg_fetch_all($result);
    pg_free_result($result);

    foreach($projects as $project) {
      if($col == "versionName") return $project['version_name'];
      if($col == "versionCode") return floatval($project['language_code']);
    }
    return null;
  }
  
  private function getProjectXmlFile($unzipDir) {
    $dirHandler = opendir($unzipDir);
    while(($file = readdir($dirHandler)) !== false) {
      $details = pathinfo($file);
      if(isset($details['extension']) && file_exists($unzipDir . $file) && (strcmp($details['extension'], 'spf') == 0 ||
          strcmp($details['extension'], 'xml') == 0 || strcmp($details['extension'], 'catroid') == 0)) {
        return $unzipDir.$file;
      }
    }
  }
}
?>
