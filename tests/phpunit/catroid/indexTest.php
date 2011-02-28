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

require_once('testsBootstrap.php');

class indexTest extends PHPUnit_Framework_TestCase
{
  protected $obj;

  protected function setUp() {
    require_once CORE_BASE_PATH.'modules/catroid/index.php';
    $this->obj = new index();    
  } 
  

  public function testGetNumberOfVisibleProjects()
  {
    $projectscount = $this->obj->getNumberOfVisibleProjects();
    
    $query = 'SELECT * FROM projects WHERE visible=true';
    $result = pg_query($query) or die('DB operation failed: ' . pg_last_error());
    $numDbEntries =  pg_num_rows($result);
    
    // test that projects is a valid db serach result   

    $this->assertEquals($numDbEntries, $projectscount);
    
    
  }

  protected function tearDown() {    
  }
}
?>
