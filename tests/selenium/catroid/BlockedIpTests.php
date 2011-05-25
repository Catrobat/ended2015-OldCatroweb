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

/* Set TESTS_BASE_PATH in testsBootstrap.php to your catroid www-root */
require_once 'testsBootstrap.php';

class BlockedIpTests extends PHPUnit_Framework_TestCase
{
  private $selenium;
  private $adminpath;

  public function setUp()
  {
    $path= 'http://'.str_replace('http://', '', TESTS_BASE_PATH).'catroid/';
    $this->adminpath = 'http://'.ADMIN_AREA_USER.':'.DB_PASS.'@'.str_replace('http://', '', TESTS_BASE_PATH).'admin';

    $this->selenium = new Testing_Selenium(TESTS_BROWSER, $path);
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

  public function ajaxWait()
  {
    for($second = 0; $second <= 600; $second++) {
      if($second >= 600) break;
      try {
        if($this->selenium->isElementPresent("xpath=//input[@id='ajax-loader'][@value='off']")) {
          break;
        }
      } catch (Exception $e) {}
      sleep(1);
    }
  }

  /**
   * @dataProvider blockedIps
   */
  public function testBlockedIps($project_id, $blocked_ip) {
    $this->removeAllBlockedIps();
    $this->blockIp($blocked_ip);
    $this->selenium->open(TESTS_BASE_PATH.'catroid/details/'.$project_id);
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@class='errorMessage']"));
    $this->assertTrue($this->selenium->isTextPresent("Your IP-Address has been blocked."));
    
    $this->selenium->open(TESTS_BASE_PATH.'catroid/index');
    $this->selenium->waitForPageToLoad(10000);
    $this->assertTrue($this->selenium->isElementPresent("xpath=//div[@class='errorMessage']"));
    $this->assertTrue($this->selenium->isTextPresent("Your IP-Address has been blocked."));
    $this->unblockIp($blocked_ip);
  }

  /**
   * @dataProvider unblockedIps
   */
  public function testUnblockedIps($project_id, $unblocked_ip) {
    $this->removeAllBlockedIps();
    $this->blockIp($unblocked_ip);
    $this->selenium->open(TESTS_BASE_PATH.'catroid/details/'.$project_id);
    $this->selenium->waitForPageToLoad(10000);
    $this->assertFalse($this->selenium->isElementPresent("xpath=//div[@class='errorMessage']"));
    $this->assertFalse($this->selenium->isTextPresent("Your IP-Address has been blocked."));
    
    $this->selenium->open(TESTS_BASE_PATH.'catroid/index');
    $this->selenium->waitForPageToLoad(10000);
    $this->assertFalse($this->selenium->isElementPresent("xpath=//div[@class='errorMessage']"));
    $this->assertFalse($this->selenium->isTextPresent("Your IP-Address has been blocked."));
    $this->unblockIp($unblocked_ip);
  }
  
  /* *** DATA PROVIDERS *** */
  public function blockedIps() {
    $returnArray = array(
      array(1, "127.0.0.1"),
      array(1, "127.0.0."),
      array(1, "127.0."),
      array(1, "127.")
    );
    return $returnArray;
  }

  /* *** DATA PROVIDERS *** */
  public function unblockedIps() {
    $returnArray = array(
      array(1, "127.0.0.2"),
      array(1, "127.12.0."),
      array(1, "127.12."),
      array(1, "129.0.0.1")
    );
    return $returnArray;
  }
  
  private function blockIp($ip) {
    $query = "INSERT INTO blocked_ips (ip_address) values ('$ip');";
    $result = pg_query($query) or die($this->errorHandler->showError('db', 'query_failed', pg_last_error()));
  }

  private function unblockIp($ip) {
    $query = "DELETE FROM blocked_ips WHERE ip_address = '$ip';";
    $result = pg_query($query) or die($this->errorHandler->showError('db', 'query_failed', pg_last_error()));
  }

  private function removeAllBlockedIps() {
    $query = "DELETE FROM blocked_ips;";
    $result = pg_query($query) or die($this->errorHandler->showError('db', 'query_failed', pg_last_error()));
  }
  
}
?>