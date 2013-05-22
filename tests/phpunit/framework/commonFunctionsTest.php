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

require_once('frameworkTestsBootstrap.php');

class commonFunctionsTest extends PHPUnit_Framework_TestCase {

  public function testGetUsernameBlacklistArray() {
    $bl = getUsernameBlacklistArray();
    $this->assertTrue(is_array($bl));
    $this->assertTrue(in_array('admin', $bl));
    $this->assertTrue(in_array('catroid', $bl));
    $this->assertTrue(in_array('kittyroid', $bl));
  }

  public function testGetIpBlockClassWhitelistArray() {
    $wl = getIpBlockClassWhitelistArray();
    $this->assertTrue(is_array($wl));
    $this->assertTrue(in_array('contactus', $wl));
    $this->assertTrue(in_array('error', $wl));
  }

  public function testConvertBytesToMegabytes() {
    $this->assertEquals('&lt; 0.1', convertBytesToMegabytes(1));
    $this->assertEquals('&lt; 0.1', convertBytesToMegabytes(1000));
    $this->assertEquals('&lt; 0.1', convertBytesToMegabytes(10000));
    $this->assertEquals('&lt; 0.1', convertBytesToMegabytes(100000));
    $this->assertEquals(0.2, convertBytesToMegabytes(104900));    
    $this->assertEquals(1.2 , convertBytesToMegabytes(1234567));
    $this->assertEquals(9.6 , convertBytesToMegabytes(9999999));
  }
  
  public function testUnzipFile() {
    $zipFile = dirname(__FILE__).'/testdata/commonFunctionsTestData/test.zip';
    $destDir = dirname(__FILE__).'/testdata/commonFunctionsTestData/zipTest/';
    removeDir($destDir);
    $this->assertTrue(unzipFile($zipFile, $destDir));
    $this->assertTrue(is_dir($destDir));
    $this->assertTrue(is_dir($destDir.'images/'));
    $this->assertTrue(is_dir($destDir.'sounds/'));
    $this->assertTrue(is_file($destDir.'introducing catroid.spf'));
    $this->assertTrue(is_file($destDir.'images/1284732477347cat1-b.png'));
    removeDir($destDir);
    $zipFile = dirname(__FILE__).'/testdata/commonFunctionsTestData/not_a_zip.zip';
    $this->assertFalse(unzipFile($zipFile, $destDir));
    $this->assertFalse(is_dir($destDir));
    $this->assertFalse(is_dir($destDir.'images/'));
    $this->assertFalse(is_dir($destDir.'sounds/'));
    $this->assertFalse(is_file($destDir.'introducing catroid.spf'));
    $this->assertFalse(is_file($destDir.'images/1284732477347cat1-b.png'));
  }

  /**
   * @dataProvider randomLongStrings
   */
  public function testMakeShortString($string) {
    $this->assertEquals(20, strlen(makeShortString($string, 20)));
    $this->assertEquals(35, strlen(makeShortString($string, 35, '...mysuffix')));
  }

  public function testGetProjectThumbnailUrl() {
    $thumbSourceName = 'test_thumbnail.png';
    $thumbDestName = 'test_small.png';
    @unlink(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$thumbDestName);
    $thumb = getProjectThumbnailUrl('test');
    $this->assertFalse(strpos($thumb, $thumbDestName));
    $this->assertTrue(is_int(strpos($thumb, PROJECTS_THUMBNAIL_DEFAULT.PROJECTS_THUMBNAIL_EXTENSION_SMALL)));
    copy(dirname(__FILE__).'/testdata/'.$thumbSourceName, CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$thumbDestName);
    $thumb = getProjectThumbnailUrl('test');
    $this->assertTrue(is_int(strpos($thumb, $thumbDestName)));
    @unlink(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$thumbDestName);
  }

  public function testGetProjectImageUrl() {
    $thumbSourceName = 'test_thumbnail.png';
    $thumbDestName = 'test_large.png';
    @unlink(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$thumbDestName);
    $thumb = getProjectImageUrl('test');
    $this->assertFalse(strpos($thumb, $thumbDestName));
    $this->assertTrue(is_int(strpos($thumb, PROJECTS_THUMBNAIL_DEFAULT.PROJECTS_THUMBNAIL_EXTENSION_LARGE)));
    copy(dirname(__FILE__).'/testdata/'.$thumbSourceName, CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$thumbDestName);
    $thumb = getProjectImageUrl('test');
    $this->assertTrue(is_int(strpos($thumb, $thumbDestName)));
    @unlink(CORE_BASE_PATH.PROJECTS_THUMBNAIL_DIRECTORY.$thumbDestName);
  }

  public function testGetTimeInWords() {
    require_once('frameworkTestModel.php');
    $testModel = new frameworkTestModel();

    $fromTime = time() - 10;
    $timeInWords = getTimeInWords($fromTime, $testModel->languageHandler, time());
    $this->assertTrue(is_string($timeInWords));
    $this->assertTrue(is_int(strpos($timeInWords, 'just')) && is_int(strpos($timeInWords, 'now')));

    $fromTime = time() - 66;
    $timeInWords = getTimeInWords($fromTime, $testModel->languageHandler, time());
    $this->assertFalse(strpos($timeInWords, 'just'));
    $this->assertTrue(is_int(strpos($timeInWords, 'minute')));

    $fromTime = time() - 60*60*24-1;
    $timeInWords = getTimeInWords($fromTime, $testModel->languageHandler, time());
    $this->assertFalse(strpos($timeInWords, 'minute'));
    $this->assertTrue(is_int(strpos($timeInWords, 'day')));

    $fromTime = time() - 60*60*24*31-1;
    $timeInWords = getTimeInWords($fromTime, $testModel->languageHandler, time());
    $this->assertFalse(strpos($timeInWords, 'day'));
    $this->assertTrue(is_int(strpos($timeInWords, 'month')));

    $fromTime = time() - 60*60*24*32*12-1;
    $timeInWords = getTimeInWords($fromTime, $testModel->languageHandler, time());
    $this->assertFalse(strpos($timeInWords, 'month'));
    $this->assertTrue(is_int(strpos($timeInWords, 'year')));
  }
  
  public function testGetSupportedLanguagesArray() {
    require_once('frameworkTestModel.php');
    $testModel = new frameworkTestModel();
    $supportedLanguages = getSupportedLanguagesArray($testModel->languageHandler);
    $this->assertTrue(isset($supportedLanguages[SITE_DEFAULT_LANGUAGE]));
    $this->assertTrue($supportedLanguages[SITE_DEFAULT_LANGUAGE]['supported']);
  }
  
  public function testCopyAndRemoveDir() {
    $testFolder = dirname(__FILE__).'/testdata/commonFunctionsTestData/testfolder/';
    $runtimeFolder = dirname(__FILE__).'/testdata/commonFunctionsTestData/runtimeFolder/';
    copyDir($testFolder, $runtimeFolder);
    $this->assertTrue(is_dir($runtimeFolder));
    $this->assertTrue(is_dir($runtimeFolder.'subfolder1'));
    $this->assertTrue(is_dir($runtimeFolder.'subfolder2'));
    $this->assertTrue(is_dir($runtimeFolder.'subfolder2/subfolder1'));
    $this->assertTrue(is_file($runtimeFolder.'subfolder2/subfolder1/test.txt'));
    $this->assertEquals(md5_file($runtimeFolder.'subfolder2/subfolder1/test.txt'), md5_file($testFolder.'subfolder2/subfolder1/test.txt'));
    removeDir($runtimeFolder);
    $this->assertFalse(is_dir($runtimeFolder));
    $this->assertFalse(is_dir($runtimeFolder.'subfolder1'));
    $this->assertFalse(is_dir($runtimeFolder.'subfolder2'));
    $this->assertFalse(is_dir($runtimeFolder.'subfolder2/subfolder1'));
    $this->assertFalse(is_file($runtimeFolder.'subfolder2/subfolder1/test.txt'));     
  }
  
  /**
  * @dataProvider inputTestStrings
  */
  public function testCheckUserInput($input, $output) {
    $this->assertTrue($output == checkUserInput($input));
  }

  /**
   * @dataProvider inputTestUrlStrings
   */  
  public function testWrapUrlsWithAnchors($input,$output){
    $this->assertSame($output,wrapUrlsWithAnchors($input));
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

  public function inputTestStrings() {
    return array(array("<", ""),
                 array("&lt", ""),
                 array("&lt;", ""),
                 array("&LT", ""),
                 array("&LT;", ";"),
                 array("&#60", ""),
                 array("&#060", ""),
                 array("&#0060", ""),
                 array("&#00060", ""),
                 array("&#000060", ""),
                 array("&#0000060", ""),
                 array("&#60;", ""),
                 array("&#060;", ""),
                 array("&#0060;", ""),
                 array("&#00060;", ""),
                 array("&#000060;", ""),
                 array("&#0000060;", ""),
                 array("&#x3c", ""),
                 array("&#x03c", ""),
                 array("&#x003c", ""),
                 array("&#x0003c", ""),
                 array("&#x00003c", ""),
                 array("&#x000003c", ""),
                 array("&#x3c;", ""),
                 array("&#x03c;", ""),
                 array("&#x003c;", ""),
                 array("&#x0003c;", ""),
                 array("&#x00003c;", ""),
                 array("&#x000003c;", ""),
                 array("&#X3c", ""),
                 array("&#X03c", ""),
                 array("&#X003c", ""),
                 array("&#X0003c", ""),
                 array("&#X00003c", ""),
                 array("&#X000003c", ""),
                 array("&#X3c;", ""),
                 array("&#X03c;", ""),
                 array("&#X003c;", ""),
                 array("&#X0003c;", ""),
                 array("&#X00003c;", ""),
                 array("&#X000003c;", ""),
                 array("&#x3C", ""),
                 array("&#x03C", ""),
                 array("&#x003C", ""),
                 array("&#x0003C", ""),
                 array("&#x00003C", ""),
                 array("&#x000003C", ""),
                 array("&#x3C;", ""),
                 array("&#x03C;", ""),
                 array("&#x003C;", ""),
                 array("&#x0003C;", ""),
                 array("&#x00003C;", ""),
                 array("&#x000003C;", ""),
                 array("&#X3C", ""),
                 array("&#X03C", ""),
                 array("&#X003C", ""),
                 array("&#X0003C", ""),
                 array("&#X00003C", ""),
                 array("&#X000003C", ""),
                 array("&#X3C;", ""),
                 array("&#X03C;", ""),
                 array("&#X003C;", ""),
                 array("&#X0003C;", ""),
                 array("&#X00003C;", ""),
                 array("&#X000003C;", ""));
  }
  
  
  public function inputTestUrlStrings() {
    
    return array(
           array("keine Url","keine Url")
         , array("http","http")
         , array("http://","http://")
         , array("https://","https://")
         , array("https://www.fred.at/",'<a href="https://www.fred.at/" target="_blank">https://www.fred.at/</a>')
         , array("http://www.google.at",'<a href="http://www.google.at" target="_blank">http://www.google.at</a>')
         , array("(Something like http://foo.com/blah_blah)",'(Something like <a href="http://foo.com/blah_blah" target="_blank">http://foo.com/blah_blah</a>)')
         , array("http://foo.com/blah_blah_(wikipedia)",'<a href="http://foo.com/blah_blah_(wikipedia)" target="_blank">http://foo.com/blah_blah_(wikipedia)</a>')
         , array("http://foo.com/more_(than)_one_(parens)",'<a href="http://foo.com/more_(than)_one_(parens)" target="_blank">http://foo.com/more_(than)_one_(parens)</a>')
         , array("(Something like http://foo.com/blah_blah_(wikipedia))",'(Something like <a href="http://foo.com/blah_blah_(wikipedia)" target="_blank">http://foo.com/blah_blah_(wikipedia)</a>)')
         , array("http://foo.com/blah_(wikipedia)#cite-1",'<a href="http://foo.com/blah_(wikipedia)#cite-1" target="_blank">http://foo.com/blah_(wikipedia)#cite-1</a>')
         , array("http://foo.com/unicode_(✪)_in_parens",'<a href="http://foo.com/unicode_(✪)_in_parens" target="_blank">http://foo.com/unicode_(✪)_in_parens</a>')
         , array("http://www.extinguishedscholar.com/wpglob/?p=364.",'<a href="http://www.extinguishedscholar.com/wpglob/?p=364" target="_blank">http://www.extinguishedscholar.com/wpglob/?p=364</a>.')
         , array("http://www.extinguishedscholar.com/wpglob/?p=364",'<a href="http://www.extinguishedscholar.com/wpglob/?p=364" target="_blank">http://www.extinguishedscholar.com/wpglob/?p=364</a>')
         , array("mailto:name@example.com",'mailto:name@example.com')
         , array("http://www.asianewsphoto.com/(S(neugxif4twuizg551ywh3f55))/Web_ENG/View_DetailPhoto.aspx?PicId=752",'<a href="http://www.asianewsphoto.com/(S(neugxif4twuizg551ywh3f55))/Web_ENG/View_DetailPhoto.aspx?PicId=752" target="_blank">http://www.asianewsphoto.com/(S(neugxif4twuizg551ywh3f55))/Web_ENG/View_DetailPhoto.aspx?PicId=752</a>')
        
    );
    
    //The Testcases are from here: http://daringfireball.net/misc/2010/07/url-matching-regex-test-data.text

  }
  
  
}
?>
