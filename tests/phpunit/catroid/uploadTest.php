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

  protected function setUp() {
    require_once CORE_BASE_PATH.'modules/catroid/upload.php';
    $this->upload = new upload();
  }

  /**
   * @dataProvider correctPostData
   */
  public function testDoUpload($projectTitle, $projectDescription, $testFile, $fileName, $fileChecksum, $fileSize, $fileType) {
    $formData = array('projectTitle'=>$projectTitle, 'projectDescription'=>$projectDescription, 'fileChecksum'=>$fileChecksum);
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

    // check thumbnails
    $this->assertTrue(is_file(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$insertId.'_small.png'));
    $this->assertTrue(is_file(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$insertId.'_large.png'));
    
    //test renaming
    $return = $this->upload->renameProjectFile($filePath, $insertId);
    $this->assertTrue($return);

    //test deleting from filesystem
    $this->upload->removeProjectFromFilesystem($filePath, $insertId);
    $this->assertFalse(is_file($filePath));
    $this->assertFalse(is_file(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$insertId.'_small.png'));
    $this->assertFalse(is_file(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$insertId.'_large.png'));
    
    //test deleting from database
    $this->upload->removeProjectFromDatabase($insertId);
    $query = "SELECT * FROM projects WHERE id='$insertId'";
    $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
    $this->assertEquals(0, pg_num_rows($result));
  }

  /**
   * @dataProvider incorrectPostData
   */
  public function testDoUploadFail($projectTitle, $projectDescription, $testFile, $fileName, $fileChecksum, $fileSize, $fileType) {
    $formData = array('projectTitle'=>$projectTitle, 'projectDescription'=>$projectDescription, 'fileChecksum'=>$fileChecksum);
    $fileData = array('upload'=>array('name'=>$fileName, 'type'=>$fileType,
                        'tmp_name'=>$testFile, 'error'=>0, 'size'=>$fileSize));
    $serverData = array('REMOTE_ADDR'=>'127.0.0.1');
    $insertId = $this->upload->doUpload($formData, $fileData, $serverData);
    $filePath = CORE_BASE_PATH.PROJECTS_DIRECTORY.$insertId.PROJECTS_EXTENTION;

    $this->assertEquals(0, $insertId);
    $this->assertFalse(is_file($filePath));

    // check thumbnails
    $this->assertFalse(is_file(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$insertId.'_small.png'));
    $this->assertFalse(is_file(CORE_BASE_PATH.'/'.PROJECTS_THUMBNAIL_DIRECTORY.'/'.$insertId.'_large.png'));
    
    $this->assertNotEquals(200, $this->upload->statusCode);
    $this->assertFalse($this->upload->projectId > 0);
    $this->assertTrue(is_string($this->upload->answer));
  }

  public function testCheckFileChecksum() {
    $csOne = '12abc';
    $csTwo = '12abc';
    $this->assertTrue($this->upload->checkFileChecksum($csOne, $csTwo));
    $csOne = '12abc';
    $csTwo = '21cba';
    $this->assertFalse($this->upload->checkFileChecksum($csOne, $csTwo));
  }

  public function testCopyProjectToDirectory() {
    $dest = CORE_BASE_PATH.PROJECTS_DIRECTORY.'copyTest'.PROJECTS_EXTENTION;
    $src = dirname(__FILE__).'/testdata/test.zip';
    
    $this->assertTrue($this->upload->copyProjectToDirectory($src, $dest));
    $this->assertTrue(is_file($dest));
    @unlink($dest);
  }

  public function testCopyProjectWithThumbnailToDirectory() {
    $dest = CORE_BASE_PATH.PROJECTS_DIRECTORY.'copyTest'.PROJECTS_EXTENTION;
    $src = dirname(__FILE__).'/testdata/test2.zip';
    
    $this->assertTrue($this->upload->copyProjectToDirectory($src, $dest));
    $this->assertTrue(is_file($dest));
    @unlink($dest);
  }
  
  /* *** DATA PROVIDERS *** */
  public function correctPostData() {
    $fileName = 'test2.zip';
    $testFile = dirname(__FILE__).'/testdata/'.$fileName;
    $fileChecksum = md5_file($testFile);
    $fileSize = filesize($testFile);
    $fileType = 'application/x-zip-compressed';
    $dataArray = array(
    array('unitTest', 'my project description', $testFile, $fileName, $fileChecksum, $fileSize, $fileType),
    array('unitTest with empty description', '', $testFile, $fileName, $fileChecksum, $fileSize, $fileType),
    array('unitTest with a very very very very long title and no description, hopefully not too long', '', $testFile, $fileName, $fileChecksum, $fileSize, $fileType),
    array('unitTest with special chars: ä, ü, ö', '', $testFile, $fileName, $fileChecksum, $fileSize, $fileType)
    );

    return $dataArray;
  }

  public function incorrectPostData() {
    $validFileName = 'test.zip';
    $invalidFileName = 'nonExistingFile.zip';
    $validTestFile = dirname(__FILE__).'/testdata/'.$validFileName;
    $invalidTestFile = dirname(__FILE__).'/testdata/'.$invalidFileName;
    $validFileChecksum = md5_file($validTestFile);
    $invalidFileChecksum = 'invalidfilechecksum';
    $validFileSize = filesize($validTestFile);
    $fileType = 'application/x-zip-compressed';
    $dataArray = array(
    array('unitTestFail1', 'this project uses a non existing file for upload', $invalidTestFile, $invalidFileName, '', 0, $fileType),
    array('', 'this project has an empty projectTitle', $validTestFile, $validFileName, $validFileChecksum, $validFileSize, $fileType),
    array('unitTestFail2', 'this project has an invalid fileChecksum', $validTestFile, $validFileName, $invalidFileChecksum, $validFileSize, $fileType),
    array('unitTestFail3', 'this project has a too large project file', $validTestFile, $validFileName, $validFileChecksum, 200000000, $fileType),
    array('defaultSaveFile', 'this project has the default save file set.', $validTestFile, $validFileName, $validFileChecksum, $validFileSize, $fileType),
    array('unitTestFail3', 'this project has a too large project file', $validTestFile, $validFileName, $validFileChecksum, 200000000, $fileType),
    array('my fucking project title', 'this project has an insulting projectTitle', $validTestFile, $validFileName, $validFileChecksum, $validFileSize, $fileType),
    array('unitTestFail5', 'this project has an insulting projectDescription - Fuck!', $validTestFile, $validFileName, $validFileChecksum, $validFileSize, $fileType)
    );

    return $dataArray;
  }
}
?>
