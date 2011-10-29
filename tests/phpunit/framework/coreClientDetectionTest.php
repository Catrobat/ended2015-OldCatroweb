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
 *    An additional term exception under section 7 of the GNU Affero
 *    General Public License, version 3, is available at
 *    http://www.catroid.org/catroid/licenseadditionalterm
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once('frameworkTestsBootstrap.php');

class coreClientDetectionTest extends PHPUnit_Framework_TestCase
{

  protected function setUp() {

  }

  /**
   * @dataProvider userAgents
   */
  public function testClientDetection($userAgentString, $expectedBrowser, $expectedMinVersion, $shouldBeMobile = false) {
    $_SERVER['HTTP_USER_AGENT'] = $userAgentString;
    $obj = new CoreClientDetection();
    $versionString = $obj->getVersion();
    $aVersion = explode('.', $versionString);
    if(count($aVersion) == 1) {
      $versionNumber = intval($aVersion[0]);
    } else {
      $versionNumber = intval($aVersion[0]) + (0.1*intval($aVersion[1]));
    }

    $this->assertEquals($userAgentString, $obj->getUserAgent());
    $this->assertTrue($obj->isBrowser($expectedBrowser));
    $this->assertGreaterThanOrEqual($expectedMinVersion, $versionNumber);
    $this->assertEquals($shouldBeMobile, $obj->isMobile());
  }

  /* DATA PROVIDERS */
  public function userAgents() {
    /* You can add more userAgentStrings to test from http://www.useragentstring.com/pages/Browserlist/ */

    $userAgents = array();
    $userAgentFiles = array(
    array('fileName'=>'useragentsOperaMobile.txt', 'expectedBrowser'=>CoreClientDetection::BROWSER_OPERA_MOBILE, 'expectedMinVersion'=>10.0, 'shouldBeMobile'=>true),
    array('fileName'=>'useragentsOperaMini.txt', 'expectedBrowser'=>CoreClientDetection::BROWSER_OPERA_MINI, 'expectedMinVersion'=>5, 'shouldBeMobile'=>true),
    array('fileName'=>'useragentsOpera11.txt', 'expectedBrowser'=>CoreClientDetection::BROWSER_OPERA, 'expectedMinVersion'=>11, 'shouldBeMobile'=>false),
    array('fileName'=>'useragentsOpera10.txt', 'expectedBrowser'=>CoreClientDetection::BROWSER_OPERA, 'expectedMinVersion'=>10, 'shouldBeMobile'=>false),
    array('fileName'=>'useragentsFirefoxMin3.6.txt', 'expectedBrowser'=>CoreClientDetection::BROWSER_FIREFOX, 'expectedMinVersion'=>3.6, 'shouldBeMobile'=>false),
    array('fileName'=>'useragentsFirefox4.txt', 'expectedBrowser'=>CoreClientDetection::BROWSER_FIREFOX, 'expectedMinVersion'=>4, 'shouldBeMobile'=>false),
    array('fileName'=>'useragentsFirefoxMobile.txt', 'expectedBrowser'=>CoreClientDetection::BROWSER_FIREFOX_MOBILE, 'expectedMinVersion'=>4, 'shouldBeMobile'=>true),
    array('fileName'=>'useragentsAndroid2.1.txt', 'expectedBrowser'=>CoreClientDetection::BROWSER_ANDROID, 'expectedMinVersion'=>2.1, 'shouldBeMobile'=>true),
    array('fileName'=>'useragentsAndroid2.2.txt', 'expectedBrowser'=>CoreClientDetection::BROWSER_ANDROID, 'expectedMinVersion'=>2.2, 'shouldBeMobile'=>true),
    array('fileName'=>'useragentsInternetExplorer8.txt', 'expectedBrowser'=>CoreClientDetection::BROWSER_IE, 'expectedMinVersion'=>8, 'shouldBeMobile'=>false),
    array('fileName'=>'useragentsInternetExplorer9.txt', 'expectedBrowser'=>CoreClientDetection::BROWSER_IE, 'expectedMinVersion'=>9, 'shouldBeMobile'=>false)
    );
    $i = 0;
    foreach($userAgentFiles as $params) {
      $file = file(dirname(__FILE__).'/testdata/'.$params['fileName']);
      foreach($file as $row) {
        $userAgents[$i] = array(trim($row), $params['expectedBrowser'], $params['expectedMinVersion'], $params['shouldBeMobile']);
        $i++;
      }
    }

    return $userAgents;
  }
}
?>
