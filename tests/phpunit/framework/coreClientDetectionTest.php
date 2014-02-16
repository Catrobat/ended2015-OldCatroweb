<?php
/*
 * Catroid: An on-device visual programming system for Android devices
 * Copyright (C) 2010-2014 The Catrobat Team
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

class coreClientDetectionTest extends PHPUnit_Framework_TestCase
{

  protected function setUp() {

  }

  /**
   * @dataProvider userAgents
   */
  public function testClientDetection($userAgentString, $shouldBeMobile = false) {
    // $_SERVER['HTTP_USER_AGENT'] = $userAgentString;
    $obj = new CoreClientDetection($userAgentString);
    
    if (0) {
      print "\n* UserAgent: ".$obj->getUserAgent();
  
      if ($obj->isMobile()) 
        print " \n* isMobile: TRUE";
      else 
        print "\n* isMobile: FALSE";
      print "\n";
      exit();
    }
    $this->assertEquals($shouldBeMobile, $obj->isMobile());
  }
  
  /* DATA PROVIDERS */
  public function userAgents() {
    /* You can add more userAgentStrings to test from http://www.useragentstring.com/pages/Browserlist/ */

    $userAgents = array();
    $userAgentFiles = array(
    array('fileName'=>'useragentsOperaMobile.txt', 'shouldBeMobile'=>true),
    array('fileName'=>'useragentsOperaMini.txt', 'shouldBeMobile'=>true),
    array('fileName'=>'useragentsOpera11.txt', 'shouldBeMobile'=>false),
    array('fileName'=>'useragentsOpera10.txt', 'shouldBeMobile'=>false),
    array('fileName'=>'useragentsFirefoxMin3.6.txt', 'shouldBeMobile'=>false),
    array('fileName'=>'useragentsFirefox4.txt', 'shouldBeMobile'=>false),
    array('fileName'=>'useragentsFirefoxMobile.txt', 'shouldBeMobile'=>true),
    array('fileName'=>'useragentsAndroid2.1.txt', 'shouldBeMobile'=>true),
     array('fileName'=>'useragentsAndroid2.2.txt', 'shouldBeMobile'=>true),
    array('fileName'=>'useragentsInternetExplorer8.txt', 'shouldBeMobile'=>false),
    array('fileName'=>'useragentsInternetExplorer9.txt', 'shouldBeMobile'=>false)
    );
    $i = 0;
    foreach($userAgentFiles as $params) {
      $file = file(dirname(__FILE__).'/testdata/'.$params['fileName']);
      foreach($file as $row) {
        $userAgents[$i] = array(trim($row), $params['shouldBeMobile']);
        $i++;
      }
    }

    return $userAgents;
  }
}
?>
