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

require_once('testsBootstrap.php');

class downloadTest extends PHPUnit_Framework_TestCase
{
    protected $download;

    protected function setUp() {
      require_once CORE_BASE_PATH.'modules/catroid/download.php';
      $this->download = new download();
    }

    /**
     * @dataProvider validIds
     */
    public function testRetrieveProjectById($id)
    {
      $ret = $this->download->retrieveProjectById($id);
      $this->assertNotEquals(-1, $ret);
    }

    /**
     * @dataProvider invalidIds
     */
    public function testRetrieveProjectByIdFail($id)
    {
      $ret = $this->download->retrieveProjectById($id);
      $this->assertEquals(-1, $ret);
    }

    /**
    * @dataProvider realIds
    */
    public function testIncrementDownloadCounter($id) {
      $testProject = $this->download->retrieveProjectById($id);
      $downloadCounterOld = $testProject['download_count'];

      $this->download->session->projectsCurrentlyLoading = array();
      $testProject = $this->download->retrieveProjectById($id);
      $downloadCounterNew = $testProject['download_count'];

      $this->assertEquals($downloadCounterOld + 1, $downloadCounterNew);
    }

    /* *** DATA PROVIDERS *** */
    public function validIds()
    {
        return array(
          array(0),
          array(1),
          array(2),
          array(30000)
        );
    }

    public function invalidIds()
    {
        return array(
          array('x'),
          array(-10)
        );
    }

    //chooses random ids from database
    public function realIds() {
      $returnArray = array();

      $query = 'SELECT * FROM projects ORDER BY random() LIMIT 3';
      $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
      $projects = pg_fetch_all($result);
      pg_free_result($result);
      $i=0;
      foreach($projects as $project) {
        $returnArray[$i] = array($project['id']);
        $i++;
      }

      return $returnArray;
    }
}
?>
