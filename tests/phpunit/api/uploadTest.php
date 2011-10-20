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

  /**
   * @dataProvider correctPostData
   */
  public function testDoUpload($projectTitle, $projectDescription, $testFile, $fileName, $fileChecksum, $fileSize, $fileType, $uploadEmail = '', $uploadLanguage = '') {
    $formData = array('projectTitle'=>$projectTitle, 'projectDescription'=>$projectDescription, 'fileChecksum'=>$fileChecksum, 'userEmail'=>$uploadEmail, 'userLanguage'=>$uploadLanguage);
    $fileData = array('upload'=>array('name'=>$fileName, 'type'=>$fileType, 'tmp_name'=>$testFile, 'error'=>0, 'size'=>$fileSize));
    $serverData = array('REMOTE_ADDR'=>'127.0.0.1');
    $fileSize = filesize($testFile);
    $insertId = $this->upload->doUpload($formData, $fileData, $serverData);
    $filePath = CORE_BASE_PATH.PROJECTS_DIRECTORY.$insertId.PROJECTS_EXTENSION;
    $projectPath = CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY.$insertId;

    $this->assertEquals(200, $this->upload->statusCode);
    $this->assertNotEquals(0, $insertId);
    $this->assertTrue(is_file($filePath));

    $this->assertTrue(is_dir($projectPath));
    $this->assertTrue(is_dir($projectPath."/images"));
    $this->assertTrue(is_dir($projectPath."/sounds"));

    $this->assertTrue($this->upload->projectId > 0);
    $this->assertTrue($this->upload->fileChecksum != null);
    $this->assertEquals(md5_file($testFile), $this->upload->fileChecksum);
    //$this->assertTrue(is_string($this->upload->answer));

    if($uploadEmail) {
      $query = "SELECT upload_email FROM projects WHERE id='$insertId'";
      $result = pg_query($this->dbConnection, $query);
      $row = pg_fetch_row($result);
      $this->assertEquals($uploadEmail, $row[0]);
      pg_free_result($result);
    }
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

    //test qrcode image generation
    $this->assertTrue(is_file(CORE_BASE_PATH.PROJECTS_QR_DIRECTORY.$insertId.PROJECTS_QR_EXTENSION));

    //test renaming
    $return = $this->upload->renameProjectFile($filePath, $insertId);
    $this->assertTrue($return);

    //test deleting from filesystem
    $this->upload->removeProjectFromFilesystem($filePath, $insertId);
    $this->assertFalse(is_file($filePath));
    $this->assertFalse(is_file(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$insertId.PROJECTS_THUMBNAIL_EXTENSION_SMALL));
    $this->assertFalse(is_file(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$insertId.PROJECTS_THUMBNAIL_EXTENSION_LARGE));
    $this->assertFalse(is_file(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$insertId.PROJECTS_THUMBNAIL_EXTENSION_ORIG));

    //$this->assertFalse(is_dir($projectPath));
    //$this->assertFalse(is_dir($projectPath."/images"));
    //$this->assertFalse(is_dir($projectPath."/sounds"));

    //test deleting from database
    $this->upload->removeProjectFromDatabase($insertId);
    $query = "SELECT * FROM projects WHERE id='$insertId'";
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $this->assertEquals(0, pg_num_rows($result));
  }

  /**
   * @dataProvider incorrectPostData
   */
  public function testDoUploadFail($projectTitle, $projectDescription, $testFile, $fileName, $fileChecksum, $fileSize, $fileType, $expectedStatusCode, $uploadEmail = '', $uploadLanguage = '') {
    $formData = array('projectTitle'=>$projectTitle, 'projectDescription'=>$projectDescription, 'fileChecksum'=>$fileChecksum, 'userEmail'=>$uploadEmail, 'userLanguage'=>$uploadLanguage);
    $fileData = array('upload'=>array('name'=>$fileName, 'type'=>$fileType, 'tmp_name'=>$testFile, 'error'=>0, 'size'=>$fileSize));
    $serverData = array('REMOTE_ADDR'=>'127.0.0.1');
    try {
      $insertId = $this->upload->doUpload($formData, $fileData, $serverData);
    } catch(Exception $e) {
      $this->assertNotEquals(200, $this->upload->statusCode);
      $this->assertEquals($expectedStatusCode, $this->upload->statusCode);
      $this->assertFalse($this->upload->projectId > 0);
      return;
    }
    $this->fail('EXPECTED EXCEPTION NOT RAISED!');
  }

  /**
   * @dataProvider correctVersionData
   */
  public function testDoUploadCorrectVersion($projectTitle, $projectDescription, $fileName, $fileType, $versionCode, $versionName, $uploadEmail = '', $uploadLanguage = '') {
    $testFile = dirname(__FILE__).'/testdata/'.$fileName;
    $fileChecksum = md5_file($testFile);
    $fileSize = filesize($testFile);

    $formData = array('projectTitle'=>$projectTitle, 'projectDescription'=>$projectDescription, 'fileChecksum'=>$fileChecksum, 'userEmail'=>$uploadEmail, 'userLanguage'=>$uploadLanguage);
    $fileData = array('upload'=>array('name'=>$fileName, 'type'=>$fileType, 'tmp_name'=>$testFile, 'error'=>0, 'size'=>$fileSize));
    $serverData = array('REMOTE_ADDR'=>'127.0.0.1');
    $fileSize = filesize($testFile);
    $insertId = $this->upload->doUpload($formData, $fileData, $serverData);
    $filePath = CORE_BASE_PATH.PROJECTS_DIRECTORY.$insertId.PROJECTS_EXTENSION;
    $projectPath = CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY.$insertId;

    $this->assertEquals(200, $this->upload->statusCode);
    $this->assertNotEquals(0, $insertId);
    $this->assertTrue(is_file($filePath));
    $this->assertTrue($this->upload->projectId > 0);
    $this->assertTrue($this->upload->fileChecksum != null);
    $this->assertEquals(md5_file($testFile), $this->upload->fileChecksum);
    
    $this->assertEquals($versionName, $this->getVersionInfo($insertId, "versionName"));
    $this->assertEquals($versionCode, $this->getVersionInfo($insertId, "versionCode"));

    // cleanup
    $this->upload->removeProjectFromFilesystem($filePath, $insertId);
    $this->upload->removeProjectFromDatabase($insertId);
  }

  /**
   * @dataProvider incorrectVersionData
   */
  public function testDoUploadWrongVersion($projectTitle, $projectDescription, $fileName, $fileType, $versionCode, $versionName, $uploadEmail = '', $uploadLanguage = '') {
    $testFile = dirname(__FILE__).'/testdata/'.$fileName;
    $fileChecksum = md5_file($testFile);
    $fileSize = filesize($testFile);
    $formData = array('projectTitle'=>$projectTitle, 'projectDescription'=>$projectDescription, 'fileChecksum'=>$fileChecksum, 'userEmail'=>$uploadEmail, 'userLanguage'=>$uploadLanguage);
    $fileData = array('upload'=>array('name'=>$fileName, 'type'=>$fileType, 'tmp_name'=>$testFile, 'error'=>0, 'size'=>$fileSize));
    $serverData = array('REMOTE_ADDR'=>'127.0.0.1');
    $fileSize = filesize($testFile);
    $insertId = $this->upload->doUpload($formData, $fileData, $serverData);
    $filePath = CORE_BASE_PATH.PROJECTS_DIRECTORY.$insertId.PROJECTS_EXTENSION;
    $projectPath = CORE_BASE_PATH.PROJECTS_UNZIPPED_DIRECTORY.$insertId;

    $this->assertEquals(200, $this->upload->statusCode);
    $this->assertNotEquals(0, $insertId);
    $this->assertTrue(is_file($filePath));
    $this->assertTrue($this->upload->projectId > 0);
    $this->assertTrue($this->upload->fileChecksum != null);
    $this->assertEquals(md5_file($testFile), $this->upload->fileChecksum);
    
    $this->assertEquals($versionName, $this->getVersionInfo($insertId, "versionName"));
    $this->assertEquals($versionCode, $this->getVersionInfo($insertId, "versionCode"));
    
    // cleanup
    $this->upload->removeProjectFromFilesystem($filePath, $insertId);
    $this->upload->removeProjectFromDatabase($insertId);
  }

  /**
   * @dataProvider versionInfo
   */
  public function testSaveVersionInfo($projectId, $versionCode, $versionName) {
    $this->assertTrue($this->upload->saveVersionInfo($projectId, $versionCode, $versionName));
    $this->assertEquals($this->getVersionInfo($projectId, "versionName"), $versionName);
    $this->assertEquals($this->getVersionInfo($projectId, "versionCode"), $versionCode);
  }

  /**
   * @dataProvider testVersion
   */
  public function testExtractVersionInfo($xml, $code, $name) {
    $catroidVersion = $this->upload->extractCatroidVersion(dirname(__FILE__).'/testdata/'.$xml);
    $this->assertEquals($code, $catroidVersion['versionCode']);
    $this->assertEquals($name, $catroidVersion['versionName']);
  }

  public function testCheckFileChecksum() {
    $csOne = '12abc';
    $csTwo = '12abc';
    try {
      $this->assertTrue($this->upload->checkFileChecksum($csOne, $csTwo));
    } catch(Exception $e) {
      $this->fail('EXCEPTION RAISED: '.$e->getMessage());
    }
    $csOne = '12abc';
    $csTwo = '21cba';
    try {
      $this->upload->checkFileChecksum($csOne, $csTwo);
    } catch(Exception $e) {
      return;
    }
    $this->fail('EXPECTED EXCEPTION NOT RAISED!');
  }

  public function testCopyProjectToDirectory() {
    $dest = CORE_BASE_PATH.PROJECTS_DIRECTORY.'copyTest'.PROJECTS_EXTENSION;
    $src = dirname(__FILE__).'/testdata/test.zip';
    @unlink($dest);
    $this->assertEquals(filesize($src), $this->upload->copyProjectToDirectory($src, $dest));
    $this->assertTrue(is_file($dest));
    @unlink($dest);
  }

  public function testCopyProjectWithThumbnailToDirectory() {
    $dest = CORE_BASE_PATH.PROJECTS_DIRECTORY.'copyTest'.PROJECTS_EXTENSION;
    $src = dirname(__FILE__).'/testdata/test2.zip';
    @unlink($dest);
    $this->assertEquals(filesize($src), $this->upload->copyProjectToDirectory($src, $dest));
    $this->assertTrue(is_file($dest));
    @unlink($dest);
  }

  /**
   * @dataProvider correctPostDataThumbailInRootFolderPNG
   */
  public function testDoUploadWithThumbnailInRootFolderPNG($projectTitle, $projectDescription, $testFile, $fileName, $fileChecksum, $fileSize, $fileType, $uploadEmail = '', $uploadLanguage = '') {
    $formData = array('projectTitle'=>$projectTitle, 'projectDescription'=>$projectDescription, 'fileChecksum'=>$fileChecksum, 'userEmail'=>$uploadEmail, 'userLanguage'=>$uploadLanguage);
    $fileData = array('upload'=>array('name'=>$fileName, 'type'=>$fileType, 'tmp_name'=>$testFile, 'error'=>0, 'size'=>$fileSize));
    $serverData = array('REMOTE_ADDR'=>'127.0.0.1');
    $insertId = $this->upload->doUpload($formData, $fileData, $serverData);
    $filePath = CORE_BASE_PATH.PROJECTS_DIRECTORY.$insertId.PROJECTS_EXTENSION;

    $this->assertEquals(200, $this->upload->statusCode);
    $this->assertNotEquals(0, $insertId);
    $this->assertTrue(is_file($filePath));
    $this->assertTrue($this->upload->projectId > 0);
    $this->assertTrue($this->upload->fileChecksum != null);
    $this->assertEquals(md5_file($testFile), $this->upload->fileChecksum);

    // check thumbnails
    $this->assertTrue(is_file(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$insertId.PROJECTS_THUMBNAIL_EXTENSION_SMALL));
    $this->assertTrue(is_file(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$insertId.PROJECTS_THUMBNAIL_EXTENSION_LARGE));
    $this->assertTrue(is_file(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$insertId.PROJECTS_THUMBNAIL_EXTENSION_ORIG));

    //test deleting from filesystem
    $this->upload->removeProjectFromFilesystem($filePath, $insertId);
    $this->assertFalse(is_file($filePath));
    $this->assertFalse(is_file(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$insertId.PROJECTS_THUMBNAIL_EXTENSION_SMALL));
    $this->assertFalse(is_file(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$insertId.PROJECTS_THUMBNAIL_EXTENSION_LARGE));
    $this->assertFalse(is_file(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$insertId.PROJECTS_THUMBNAIL_EXTENSION_ORIG));

    //test deleting from database
    $this->upload->removeProjectFromDatabase($insertId);
    $query = "SELECT * FROM projects WHERE id='$insertId'";
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $this->assertEquals(0, pg_num_rows($result));
  }

  /**
   * @dataProvider incorrectPostDataWithThumbnail
   */
  public function testDoUploadWithThumbnailFail($projectTitle, $projectDescription, $testFile, $fileName, $fileChecksum, $fileSize, $fileType, $uploadEmail = '', $uploadLanguage = '') {
    $formData = array('projectTitle'=>$projectTitle, 'projectDescription'=>$projectDescription, 'fileChecksum'=>$fileChecksum, 'userEmail'=>$uploadEmail, 'userLanguage'=>$uploadLanguage);
    $fileData = array('upload'=>array('name'=>$fileName, 'type'=>$fileType, 'tmp_name'=>$testFile, 'error'=>0, 'size'=>$fileSize));
    $serverData = array('REMOTE_ADDR'=>'127.0.0.1');
    $insertId = $this->upload->doUpload($formData, $fileData, $serverData);
    $filePath = CORE_BASE_PATH.PROJECTS_DIRECTORY.$insertId.PROJECTS_EXTENSION;

    $this->assertEquals(200, $this->upload->statusCode);
    $this->assertNotEquals(0, $insertId);
    $this->assertTrue(is_file($filePath));
    $this->assertTrue($this->upload->projectId > 0);
    $this->assertTrue($this->upload->fileChecksum != null);
    $this->assertEquals(md5_file($testFile), $this->upload->fileChecksum);

    // check thumbnails
    $this->assertFalse(is_file(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$insertId.PROJECTS_THUMBNAIL_EXTENSION_SMALL));
    $this->assertFalse(is_file(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$insertId.PROJECTS_THUMBNAIL_EXTENSION_LARGE));
    $this->assertFalse(is_file(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$insertId.PROJECTS_THUMBNAIL_EXTENSION_ORIG));

    //test deleting from filesystem
    $this->upload->removeProjectFromFilesystem($filePath, $insertId);
    $this->assertFalse(is_file($filePath));

    //test deleting from database
    $this->upload->removeProjectFromDatabase($insertId);
    $query = "SELECT * FROM projects WHERE id='$insertId'";
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $this->assertEquals(0, pg_num_rows($result));
  }

  /* *** DATA PROVIDERS *** */
  public function correctPostData() {
    $fileName = 'test.zip';
    $fileNameWithThumbnail = 'test2.zip';
    $testFile = dirname(__FILE__).'/testdata/'.$fileName;
    $testFileWithThumbnail = dirname(__FILE__).'/testdata/'.$fileNameWithThumbnail;
    $fileChecksum = md5_file($testFile);
    $fileChecksumWithThumbnail = md5_file($testFileWithThumbnail);
    //echo "File Checksum $testFile = $fileChecksum\n";
    //echo "File Checksum2 $testFileWithThumbnail = $fileChecksumWithThumbnail\n";
    
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
    array('unitTest with Email and Language', 'description', $testFile, $fileName, $fileChecksum, $fileSize, $fileType, 'catroid_unittest@gmail.com', 'en'),
    array('unitTest', 'my project description with thumbnail in root folder.', $testFile, 'test2.zip', $fileChecksum, $fileSize, $fileType),
    array('unitTest', 'my project description with thumbnail in images folder.', $testFile, 'test3.zip', $fileChecksum, $fileSize, $fileType),
    array('unitTest', 'project with new extention "catroid".', dirname(__FILE__).'/testdata/test.catroid', 'test.catroid', $fileChecksum, $fileSize, $fileType),
    );
    return $dataArray;
  }

  public function incorrectPostData() {
    $validFileName = 'test.zip';
    $invalidFileName = 'nonExistingFile.zip';
    $corruptFileName = 'test_invalid_projectfile.zip';
    $validTestFile = dirname(__FILE__).'/testdata/'.$validFileName;
    $invalidTestFile = dirname(__FILE__).'/testdata/'.$invalidFileName;
    $corruptTestFile = dirname(__FILE__).'/testdata/'.$corruptFileName;
    $validFileChecksum = md5_file($validTestFile);
    $corruptFileChecksum = md5_file($corruptTestFile);
    $invalidFileChecksum = 'invalidfilechecksum';
    $validFileSize = filesize($validTestFile);
    $corruptFileSize = filesize($corruptTestFile);
    $fileType = 'application/x-zip-compressed';
    $dataArray = array(
    array('unitTestFail1', 'this project uses a non existing file for upload', $invalidTestFile, $invalidFileName, $validFileChecksum, 0, $fileType, 504),
    array('unitTestFail9', 'no file checksum is send together with this project', $validTestFile, $validFileName, '', $validFileSize, $fileType, 510),
    array('', 'this project has an empty projectTitle', $validTestFile, $validFileName, $validFileChecksum, $validFileSize, $fileType, 509),
    array('defaultProject', 'this project is named defaultProject', $validTestFile, $validFileName, $validFileChecksum, $validFileSize, $fileType, 507),
    array('unitTestFail2', 'this project has an invalid fileChecksum', $validTestFile, $validFileName, $invalidFileChecksum, $validFileSize, $fileType, 501),
    array('unitTestFail3', 'this project has a too large project file', $validTestFile, $validFileName, $validFileChecksum, 200000000, $fileType, 508),
    array('defaultProject', 'this project has the default save file set.', $validTestFile, $validFileName, $validFileChecksum, $validFileSize, $fileType, 507),
    array('my fucking project title', 'this project has an insulting projectTitle', $validTestFile, $validFileName, $validFileChecksum, $validFileSize, $fileType, 506),
    array('insulting description', 'this project has an insulting projectDescription - Fuck!', $validTestFile, $validFileName, $validFileChecksum, $validFileSize, $fileType, 505),
    array('invalid project xml', 'this project contains an corrupt spf xml file', $corruptTestFile, $corruptFileName, $corruptFileChecksum, $corruptFileSize, $fileType, 512)
    );
    return $dataArray;
  }

  public function correctVersionData() {
    $fileType = 'application/x-zip-compressed';
    $dataArray = array(
    array('unitTest for correct version info 4', 'my project description for correct version info.', 'test_version4.zip', $fileType, 4, '0.4.3d'),
    array('unitTest for correct version info 5', 'my project description for correct version info.', 'test_version5.zip', $fileType, 5, '0.5.1')
    );
    return $dataArray;
  }

  public function incorrectVersionData() {
    $fileType = 'application/x-zip-compressed';
    $dataArray = array(
    array('unitTest for incorrect version info 4', 'my project description for incorrect version info.', 'test.zip', $fileType, 4, '&lt; 0.4.3d'),
    array('unitTest for incorrect version info 5', 'my project description for incorrect version info.', 'test2.zip', $fileType, 4, '&lt; 0.4.3d')
    );
    return $dataArray;
  }

  public function correctPostDataThumbailInRootFolderPNG() {
    $fileName = 'test_thumbnail.zip';
    $testFile = dirname(__FILE__).'/testdata/'.$fileName;
    $testFileDir = dirname(__FILE__).'/testdata/';
    $fileChecksum = md5_file($testFile);
    $fileSize = filesize($testFile);
    $fileType = 'application/x-zip-compressed';

    $testFile1 = 'test_thumbnail_240x400.zip'; $testFileDir1 = $testFileDir.'test_thumbnail_240x400.zip';
    $testFile2 = 'test_thumbnail_480x800.zip'; $testFileDir2 = $testFileDir.'test_thumbnail_480x800.zip';
    $testFile3 = 'test_thumbnail_240x240.zip'; $testFileDir3 = $testFileDir.'test_thumbnail_240x240.zip';
    $testFile4 = 'test_thumbnail_480x480.zip'; $testFileDir4 = $testFileDir.'test_thumbnail_480x480.zip';
    $testFile5 = 'test_thumbnail_400x400.zip'; $testFileDir5 = $testFileDir.'test_thumbnail_400x400.zip';
    $testFile6 = 'test_thumbnail_800x800.zip'; $testFileDir6 = $testFileDir.'test_thumbnail_800x800.zip';
    $testFile7 = 'test_thumbnail_960x1600.zip'; $testFileDir7 = $testFileDir.'test_thumbnail_960x1600.zip';
    $testFile8 = 'test_thumbnail_400x240.zip'; $testFileDir8 = $testFileDir.'test_thumbnail_400x240.zip';
    $testFile9 = 'test_thumbnail_800x480.zip'; $testFileDir9 = $testFileDir.'test_thumbnail_800x480.zip';

    $dataArray = array(
    array('unitTest', 'my project description with thumbnail in root folder and default thumbnail.', $testFile, $fileName, $fileChecksum, $fileSize, $fileType),
    array('unitTest', 'my project description with thumbnail in root folder and thumbnail 240x400.', $testFileDir1, $testFile1, md5_file($testFileDir1), filesize($testFileDir1), $fileType),
    array('unitTest', 'my project description with thumbnail in root folder and thumbnail 480x800.', $testFileDir2, $testFile2, md5_file($testFileDir2), filesize($testFileDir2), $fileType),
    array('unitTest', 'my project description with thumbnail in root folder and thumbnail 240x240.', $testFileDir3, $testFile3, md5_file($testFileDir3), filesize($testFileDir3), $fileType),
    array('unitTest', 'my project description with thumbnail in root folder and thumbnail 480x480.', $testFileDir4, $testFile4, md5_file($testFileDir4), filesize($testFileDir4), $fileType),
    array('unitTest', 'my project description with thumbnail in root folder and thumbnail 400x400.', $testFileDir5, $testFile5, md5_file($testFileDir5), filesize($testFileDir5), $fileType),
    array('unitTest', 'my project description with thumbnail in root folder and thumbnail 800x800.', $testFileDir6, $testFile6, md5_file($testFileDir6), filesize($testFileDir6), $fileType),
    array('unitTest', 'my project description with thumbnail in root folder and thumbnail 960x1600.', $testFileDir7, $testFile7, md5_file($testFileDir7), filesize($testFileDir7), $fileType),
    array('unitTest', 'my project description with thumbnail in root folder and thumbnail 400x240.', $testFileDir8, $testFile8, md5_file($testFileDir8), filesize($testFileDir8), $fileType),
    array('unitTest', 'my project description with thumbnail in root folder and thumbnail 800x480.', $testFileDir9, $testFile9, md5_file($testFileDir9), filesize($testFileDir9), $fileType)
    );
    return $dataArray;
  }

  public function incorrectPostDataWithThumbnail() {
    $fileName = 'test_missing_thumbnail.zip';
    $testFile = dirname(__FILE__).'/testdata/'.$fileName;
    $fileChecksum = md5_file($testFile);
    $fileSize = filesize($testFile);
    $fileType = 'application/x-zip-compressed';
    $dataArray = array(
    array('unitTest', 'my project description without screenshot of type PNG.', $testFile, $fileName, $fileChecksum, $fileSize, $fileType)
    );
    return $dataArray;
  }

  public function testVersion() {
    $dataArray = array(
    /*
    array("<project versionCode=\"4\" versionName=\"0.4.3d\"><stage><brick id=\"13\" type=\"0\"></brick></stage></project>", 4, "0.4.3d"),
    array("<project><stage><brick id=\"13\" type=\"0\"></brick></stage></project>", 4, "&lt; 0.4.3d"),
    array("<project><versionName>0.5.1</versionName><versionCode>5</versionCode></project>", 5, "0.5.1"),
    array("<project><version>0.5.1</version><code>5</code></project>", 4, "&lt; 0.4.3d")
    */
    array("test_v4.spf", 4, "0.4.3d"),
    array("test_no_version.spf", 4, "&lt; 0.4.3d"),
    array("test_v5.spf", 5, "0.5.1"),
    array("test_v5_invalid_tag.spf", 4, "&lt; 0.4.3d")
    );
    return $dataArray;
  }

  public function versionInfo() {
    $dataArray = array(
    array(1, 4, "0.5.1"),
    array(1, 5, "0.4.3d"),
    array(1, 6, "1.0"),
    array(1, 0, ""),
    array(1, 4, "0.4.3d")
    );
    return $dataArray;
  }

  private function getVersionInfo($projectId, $col) {
    $query = "SELECT projects.*, cusers.username AS uploaded_by FROM projects, cusers WHERE projects.id=$projectId AND cusers.id=projects.user_id LIMIT 1";
    $result = pg_query($this->dbConnection, $query) or die('DB operation failed: ' . pg_last_error());
    $projects = pg_fetch_all($result);
    pg_free_result($result);
    foreach($projects as $project) {
      if ($col == "versionName") return $project['version_name'];
      if ($col == "versionCode") return $project['version_code'];
    }
    return null;
  }
}

?>
