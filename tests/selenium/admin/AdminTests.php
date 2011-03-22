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

/* Set TESTS_BASE_PATH to your catroid www-root */
require_once 'testsBootstrap.php';

class AdminTests extends PHPUnit_Framework_TestCase
{
  private $selenium;

  public function setUp()
  {

    $path= 'http://'.ADMIN_AREA_USER.':'.DB_PASS.'@'.str_replace('http://', '', TESTS_BASE_PATH).'admin/';
    $this->selenium = new Testing_Selenium("*firefox", $path);
    if (TESTS_SLOW_MODE==TRUE)
    $this->selenium->setSpeed(TESTS_SLOW_MODE_SPEED);
    else
    $this->selenium->setSpeed(1);
    $this->selenium->start();
  }

  public function tearDown()
  {
    $this->selenium->stop();
  }

  public function goBack()
  {
    $this->selenium->click("aAdminToolsBackToCatroidweb");
    $this->selenium->waitForPageToLoad(10000);
  }

  public function testSuccessfulLogin()
  {
    $path= 'http://'.ADMIN_AREA_USER.':'.DB_PASS.'@'.str_replace('http://', '', TESTS_BASE_PATH).'admin';
    $this->selenium->open($path);
    $this->assertRegExp("/Administration - Catroid Website/", $this->selenium->getTitle());
    $this->assertTrue($this->selenium->isTextPresent("Administration Tools"));
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->click("xpath=//a[2]");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/Catroid Website/", $this->selenium->getTitle());
    $this->selenium->goBack();
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Catroid Administration Site"));
  }

  public function testClickAllLinks()
  {
    $path= 'http://'.ADMIN_AREA_USER.':'.DB_PASS.'@'.str_replace('http://', '', TESTS_BASE_PATH).'admin';
    $this->selenium->open($path);
    $this->selenium->waitForPageToLoad(10000);
    $this->assertRegExp("/Administration - Catroid Website/", $this->selenium->getTitle());
    $this->assertTrue($this->selenium->isTextPresent("Catroid Administration Site"));

    $this->selenium->click("xpath=//a[1]");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Administration Tools"));
    $this->assertTrue($this->selenium->isTextPresent("remove inconsistant project files"));
    $this->assertTrue($this->selenium->isTextPresent("edit projects"));
    $this->assertTrue($this->selenium->isTextPresent("thumbnail uploader"));
    $this->assertTrue($this->selenium->isTextPresent("inappropriate projects"));
    $this->assertTrue($this->selenium->isTextPresent("approve unapproved words"));    
    
    $this->assertRegExp("/Administration - Catroid Website/", $this->selenium->getTitle());

    $this->selenium->click("xpath=//a[1]");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Answer"));
    $this->selenium->goBack();
    $this->selenium->waitForPageToLoad(10000);

    $this->selenium->click("xpath=//a[2]");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Administration Tools - List of available projects"));
    $this->goBack();
    $this->selenium->waitForPageToLoad(10000);

    $this->selenium->click("xpath=//a[3]");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Administration Tools - Thumbnail Uploader"));
    $this->goBack();
    $this->selenium->waitForPageToLoad(10000);

    $this->selenium->click("xpath=//a[4]");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Administration Tools - List of inappropriate projects"));
    $this->goBack();
    $this->selenium->waitForPageToLoad(10000);
    
    $this->selenium->click("xpath=//a[5]");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Administration Tools - List of unapproved Words"));
    $this->goBack();
    $this->selenium->waitForPageToLoad(10000);

    $this->assertTrue($this->selenium->isTextPresent("- back"));
    $this->selenium->click("xpath=//a[6]");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("Catroid Administration Site"));
  }

  /**
   * @dataProvider randomIds
   */
  public function testInappropriateProjects($id, $title) {
    $this->selenium->open(TESTS_BASE_PATH.'catroid/details/'.$id);
    $this->selenium->waitForPageToLoad(10000);
    $this->selenium->click("reportAsInappropriateButton");
    $this->selenium->type("reportInappropriateReason", "my selenium reason");
    $this->selenium->click("reportInappropriateReportButton");
    $this->selenium->waitForPageToLoad(2000);
    $this->assertTrue($this->selenium->isTextPresent("You reported this project as inappropriate!"));
    $path= 'http://'.ADMIN_AREA_USER.':'.DB_PASS.'@'.str_replace('http://', '', TESTS_BASE_PATH).'admin/tools/inappropriateProjects';
    $this->selenium->open($path);
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent($id));
    $this->selenium->click("detailsLink".$id);
    $this->selenium->selectWindow("_blank");
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent($title));
    $this->selenium->close();
    $this->selenium->selectWindow(null);
    $this->selenium->chooseOkOnNextConfirmation();
    $this->selenium->click("resolve".$id);
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isTextPresent("The project was succesfully restored and set to visible!"));
    $this->assertFalse($this->selenium->isTextPresent($id));
  }

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

}
?>

