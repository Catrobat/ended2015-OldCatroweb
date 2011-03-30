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

/* Set TESTS_BASE_PATH in testsBootstrap.php to your catroid www-root */
require_once 'testsBootstrap.php';

class DetailsTests extends PHPUnit_Framework_TestCase
{
  private $selenium;

  public function setUp()
  {
    $path= 'http://'.str_replace('http://', '', TESTS_BASE_PATH).'catroid/';
    $this->selenium = new Testing_Selenium("*firefox", $path);
    if (TESTS_SLOW_MODE==TRUE) {
      $this->selenium->setSpeed(TESTS_SLOW_MODE_SPEED);
    } else {
      $this->selenium->setSpeed(1);
    }
    $this->selenium->start();
  }

  public function tearDown()
  {
    $this->selenium->stop();
  }

  public function ajaxWait($waitfor)
  {
    // Loop initialization.
    for ($second = 0; $second <=600;$second++) {
     // If loop is reached 60 seconds then break the loop.
     if ($second >= 600) break;
     // Search for element "link=ajaxLink" and if available then break loop.
     try 
     {        
       if (($this->selenium->isElementPresent($waitfor))&&(!($this->selenium->isTextPresent("loading..."))))
       break; 
     } catch (Exception $e) {}
     sleep(1);
    }
  }
  
  /**
   * @dataProvider randomIds
   */
  public function testDetailsPage($id, $title, $description)
  {
    $this->selenium->open(TESTS_BASE_PATH.'catroid/details/'.$id);
    $this->selenium->waitForPageToLoad(10000);

    //project title
    $this->assertEquals($title, $this->selenium->getText("xpath=//div[@class='detailsProjectTitle']"));

    //test the view counter
    $numOfViews = intval($this->selenium->getText("xpath=//p[@class='detailsStats']/b"));
    $this->selenium->refresh();
    $this->selenium->waitForPageToLoad(10000);
    $numOfViewsAfter = intval($this->selenium->getText("xpath=//p[@class='detailsStats']/b"));
    $this->assertEquals($numOfViews+1, $numOfViewsAfter);

    //test the download counter
    $numOfDownloads = intval($this->selenium->getText("xpath=//p[@class='detailsStats'][2]/b"));
    $this->selenium->click("xpath=//div[@class='detailsDownloadButton']/a[1]");
    $this->selenium->waitForPageToLoad(2000);
    $this->selenium->keyPressNative("27"); //press escape key
    $this->selenium->refresh();
    $this->selenium->waitForPageToLoad(10000);
    $numOfDownloadsAfter = intval($this->selenium->getText("xpath=//p[@class='detailsStats'][2]/b"));
    $this->assertEquals($numOfDownloads+1, $numOfDownloadsAfter);
    $this->selenium->click("xpath=//div[@class='detailsMainImage']/a[1]");
    $this->selenium->waitForPageToLoad(2000);
    $this->selenium->keyPressNative("27"); //press escape key
    $this->selenium->refresh();
    $this->selenium->waitForPageToLoad(10000);
    $numOfDownloadsAfter = intval($this->selenium->getText("xpath=//p[@class='detailsStats'][2]/b"));
    $this->assertEquals($numOfDownloads+2, $numOfDownloadsAfter);

    //test the home link
    $this->selenium->click("xpath=//div[@class='webHeadTitleName']/a");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/catroid\/index/", $this->selenium->getLocation());
    $this->selenium->goBack();
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->click("xpath=//div[@class='webHeadLogo']/a");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/catroid\/index/", $this->selenium->getLocation());
    $this->selenium->goBack();
    $this->selenium->waitForPageToLoad(10000);
  }

  /**
   * @dataProvider randomIds
   */
  public function testInappropriateButton($id) {
    $this->loginAsAdministrator();
    $this->selenium->open(TESTS_BASE_PATH.'catroid/details/'.$id);
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isElementPresent("reportAsInappropriateButton"));
    $this->selenium->click("reportAsInappropriateButton");
    $this->assertTrue($this->selenium->isVisible("reportInappropriateReason"));
    $this->assertTrue($this->selenium->isVisible("reportInappropriateReportButton"));
    $this->assertTrue($this->selenium->isVisible("reportInappropriateCancelButton"));
    $this->selenium->click("reportAsInappropriateButton");
    $this->assertFalse($this->selenium->isVisible("reportInappropriateReason"));
    $this->selenium->click("reportAsInappropriateButton");
    $this->assertTrue($this->selenium->isVisible("reportInappropriateReason"));
    $this->selenium->click("reportInappropriateCancelButton");
    $this->assertFalse($this->selenium->isVisible("reportInappropriateReason"));
    $this->selenium->click("reportAsInappropriateButton");
    $this->selenium->click("reportInappropriateReportButton");
    $this->selenium->waitForPageToLoad(2000);
    $this->assertFalse($this->selenium->isVisible("reportInappropriateReason"));
    $this->assertFalse($this->selenium->isTextPresent("You reported this project as inappropriate!"));
    $this->selenium->click("reportAsInappropriateButton");
    $this->selenium->type("reportInappropriateReason", "my selenium reason");
    $this->selenium->click("reportInappropriateReportButton");
    $this->selenium->waitForPageToLoad(2000);
    $this->assertFalse($this->selenium->isVisible("reportInappropriateReason"));
    $this->assertTrue($this->selenium->isTextPresent("You reported this project as inappropriate!"));
    $this->selenium->refresh();
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->click("reportAsInappropriateButton");
    $this->selenium->type("reportInappropriateReason", "my selenium reason 2");
    $this->selenium->focus("reportInappropriateReason");
    $this->selenium->keyPress("reportInappropriateReason", "\\13");
    $this->selenium->waitForPageToLoad(2000);
    $this->assertFalse($this->selenium->isVisible("reportInappropriateReason"));
    $this->assertTrue($this->selenium->isTextPresent("You reported this project as inappropriate!"));
    //unflag the project again
    $path= 'http://'.ADMIN_AREA_USER.':'.DB_PASS.'@'.str_replace('http://', '', TESTS_BASE_PATH).'admin/tools/inappropriateProjects';
    $this->selenium->open($path);
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent($id));
    $this->selenium->click("resolve".$id);
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("The project was succesfully restored and set to visible!"));
    $this->assertFalse($this->selenium->isTextPresent($id));
  }
  
      $uploadpath .= "\testdata\test.zip";
      $uploadpath .= "/testdataa/test.zip";
    $this->ajaxWait("class=projectListDetailsLink");    
    
    $this->selenium->chooseOkOnNextConfirmation();
    $this->selenium->click("xpath=//input[@name='deleteButton']");
  /* *** DATA PROVIDERS *** */
  //choose random ids from database
  public function randomIds() {
    $returnArray = array();

    $query = 'SELECT * FROM projects WHERE visible=true ORDER BY random() LIMIT 1';
    $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
    $projects = pg_fetch_all($result);
    pg_free_result($result);
    $i=0;
    foreach($projects as $project) {
      $returnArray[$i] = array($project['id'], $project['title'], $project['description']);
      $i++;
    }

    return $returnArray;
  }

  public function titlesAndDescriptions() {
    $returnArray = array(
                    array('more button selenium test', 'This is a description which should have more characters than defined by the threshold in config.php. And once again: This is a description which should have more characters than defined by the threshold in config.php. Thats it!'),
                    array('more button special chars test', utf8_decode('This is a description which has special chars like ", \' or < and > in it and it should have more characters than defined by the threshold in config.php. And once again: This is a description with "special chars" and should have more characters than defined by the threshold in config.php. Thats it!'))
                   );
    
    return $returnArray;
  }

  private function getLastProject() {
    $query = 'SELECT * FROM projects ORDER BY upload_time DESC LIMIT 1';
    $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
    $projects = pg_fetch_all($result);
    pg_free_result($result);
    if(count($projects)) {
      return $projects[0];
    } else {
      return false;
    }
  }

  private function loginAsAdministrator() {
    $adminpath = 'http://'.ADMIN_AREA_USER.':'.DB_PASS.'@'.str_replace('http://', '', TESTS_BASE_PATH).'admin';
    $this->selenium->open($adminpath);
    $this->selenium->waitForPageToLoad(10000);
  }

}
?>

