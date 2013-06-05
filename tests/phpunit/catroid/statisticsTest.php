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

require_once('testsBootstrap.php');

class statisticsTest extends PHPUnit_Framework_TestCase
{
    protected $statistics;
    protected function setUp() {
      require_once CORE_BASE_PATH.'modules/catroid/statistics.php';
      $this->statistics = new statistics();
    }

    /**
     * @dataProvider data
     */
    public function testRetrieveProjectById($users, $projects, $expected)
    {
      $average = $this->statistics->getAverageProjectsPerUser($users, $projects);
      $this->assertEquals($expected, $average);
    }


    /* *** DATA PROVIDERS *** */
    public function data()
    {
        return array(
          array(2, 4, 2.0),
          array(2, 0, 0.0),
          array(0, 4, 0.0),
          array(123, 21532, 175.06)
        );
    }
}
?>
