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

/* Set TESTS_BASE_PATH to your catroid www-root */
require_once 'testsBootstrap.php';

class AdminBadWordsFilterTest extends PHPUnit_Framework_TestCase
{
	private $selenium;
	private $unapprovedWord;
	private $adminpath;

	public function setUp() {
		$this->unapprovedWord = "donaudampfschiffahrtselektrizitaetenhauptbetriebswerkbauunterbeamtengesellschaft";
		$this->adminpath='http://'.ADMIN_AREA_USER.':'.DB_PASS.'@'.str_replace('http://', '', TESTS_BASE_PATH).'admin';
			
		$path= 'http://'.ADMIN_AREA_USER.':'.DB_PASS.'@'.str_replace('http://', '', TESTS_BASE_PATH).'admin/';
		$this->selenium = new Testing_Selenium(TESTS_BROWSER, $path);
		if(TESTS_SLOW_MODE) {
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

	public function testApproveButtonGood()
	{
		$this->uploadProjectWithAnUnapprovedWord();

		$this->selenium->open($this->adminpath);
		$this->selenium->click("aAdministrationTools");
		$this->selenium->waitForPageToLoad(10000);
		$this->selenium->click("aAdminToolsApproveWords");
		$this->selenium->waitForPageToLoad(10000);
		$this->assertTrue($this->selenium->isTextPresent($this->unapprovedWord));
		$this->selenium->select("meaning", "label=good");
    $this->selenium->chooseOkOnNextConfirmation();
		$this->selenium->click("xpath=//input[@name='approveButton']");
    $this->selenium->waitForPageToLoad(10000);
		$this->assertTrue($this->selenium->isTextPresent("The word was succesfully approved!"));

		$this->deletePrivouslyUploadedProjectAndUnapporvedWord();
	}

	public function testApproveButtonBad()
	{
		$this->uploadProjectWithAnUnapprovedWord();

		$this->selenium->open($this->adminpath);
		$this->selenium->click("aAdministrationTools");
		$this->selenium->waitForPageToLoad(10000);
		$this->selenium->click("aAdminToolsApproveWords");
		$this->selenium->waitForPageToLoad(10000);
		$this->assertTrue($this->selenium->isTextPresent($this->unapprovedWord));
		$this->selenium->select("meaning", "label=bad");
    $this->selenium->chooseOkOnNextConfirmation();
		$this->selenium->click("xpath=//input[@name='approveButton']");
    $this->selenium->waitForPageToLoad(10000);
		$this->assertTrue($this->selenium->isTextPresent("The word was succesfully approved!"));

		$this->deletePrivouslyUploadedProjectAndUnapporvedWord();
	}

	public function testApproveButtonNoSelection()
	{
		$this->uploadProjectWithAnUnapprovedWord();

		$this->selenium->open($this->adminpath);
		$this->selenium->click("aAdministrationTools");
		$this->selenium->waitForPageToLoad(10000);
		$this->selenium->click("aAdminToolsApproveWords");
		$this->selenium->waitForPageToLoad(10000);
		$this->assertTrue($this->selenium->isTextPresent($this->unapprovedWord));
    $this->selenium->chooseOkOnNextConfirmation();
		$this->selenium->click("xpath=//input[@name='approveButton']");
		$this->selenium->waitForPageToLoad(10000);
		$this->assertTrue($this->selenium->isTextPresent("Error: no word meaning selected!"));

		$this->deletePrivouslyUploadedProjectAndUnapporvedWord();
	}

	public function testDeletButton()
	{
		$this->uploadProjectWithAnUnapprovedWord();

		$this->selenium->open($this->adminpath);
		$this->selenium->click("aAdministrationTools");
		$this->selenium->waitForPageToLoad(10000);
		$this->selenium->click("aAdminToolsApproveWords");
		$this->selenium->waitForPageToLoad(10000);
		$this->assertTrue($this->selenium->isTextPresent($this->unapprovedWord));
    $this->selenium->chooseOkOnNextConfirmation();
		$this->selenium->click("xpath=//input[@name='deleteButton']");
    $this->selenium->waitForPageToLoad(10000);
		$this->assertTrue($this->selenium->isTextPresent("The word was succesfully deleted!"));
		$this->assertFalse($this->selenium->isTextPresent($this->unapprovedWord));

		$this->deletePrivouslyUploadedProjectAndUnapporvedWord();
	}

	private function uploadProjectWithAnUnapprovedWord() {

		$uploadpath= TESTS_BASE_PATH.'catroid/upload/';

		$this->deleteWord($this->unapprovedWord);
		$this->selenium->open($uploadpath);
		$this->selenium->waitForPageToLoad("10000");
		$uploadpath = dirname(__FILE__);
		if(strpos($uploadpath, '\\') >= 0) {
			$uploadpath .= "testdata\test.zip";
		} else {
			$uploadpath .= "testdata/test.zip";
		}

		$this->selenium->type("upload",$uploadpath);
		$this->selenium->type("projectTitle",$this->unapprovedWord);
		$this->selenium->click("submit_upload");
		$this->selenium->waitForPageToload("10000");
	}

	private function deletePrivouslyUploadedProjectAndUnapporvedWord() {
		$this->selenium->selectWindow(null);
	  $this->selenium->open($this->adminpath);
		// $this->selenium->click("xpath=//a[@id='aAdminToolsBackToCatroidweb']");
		$this->selenium->waitForPageToLoad(10000);
		$this->selenium->click("aAdministrationTools");
		$this->selenium->waitForPageToLoad(10000);
		$this->selenium->click("aAdminToolsEditProjects");
		$this->selenium->waitForPageToLoad(10000);
		$this->assertTrue($this->selenium->isTextPresent($this->unapprovedWord));
    $this->selenium->chooseOkOnNextConfirmation();
		$this->selenium->click("xpath=//input[@name='deleteButton']");
		$this->selenium->waitForPageToLoad(10000);
		$this->assertFalse($this->selenium->isTextPresent($this->unapprovedWord));

		$this->deleteWord($this->unapprovedWord);
	}

	private function deleteWord($word) {
		$query = "DELETE FROM wordlist WHERE word='$word'";
		$result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
		if($result) {
			pg_free_result($result);
		}
	}
}
?>

