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
require_once CORE_BASE_PATH . 'modules/common/userFunctions.php';

class monitoringTests extends PHPUnit_Framework_TestCase {
  
  protected function setUp() {
  } 
  
  
  public function testCheckCatrowebUsage() {
    exec('../../monitoring/check_catroweb.sh',$output,$return_var);

    $this->assertStringStartsWith('usage:', $output[0]);
    $this->assertEquals($return_var,3);
  }  

  public function testCheckCatrowebIsOnline() {
    exec('../../monitoring/check_catroweb.sh -h ' . BASE_PATH,$output,$return_var);
    
    $this->assertEquals($output[0], 'OK: ' . BASE_PATH . ' is online');
    $this->assertEquals($return_var,0);
  }
  
  public function testCheckCatrowebError() {
    exec('../../monitoring/check_catroweb.sh -h Error',$output,$return_var);
  
    $this->assertStringStartsWith('CRITICAL:',$output[0]);
    $this->assertEquals($return_var,2);
  }

 
  protected function tearDown() {
  }
 
}
?>