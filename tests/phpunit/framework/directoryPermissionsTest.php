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

require_once('frameworkTestsBootstrap.php');

class directoryPermissionsTest extends PHPUnit_Framework_TestCase {

  public function isWriteable($dir) {
    $writeable = true;
    if (stristr(PHP_OS, 'WIN'))
    {
      $writeable= is_writeable($dir);
    } else {
      if (substr(sprintf('%o', fileperms($dir)), -4) != '0777') {
        $writeable = false;
      }
    }
    if (!$writeable)  {
      echo $dir;
    }
    return $writeable;
  }
  /**
   * @dataProvider writeableDirectories
   */
  public function testCheckPermissions($directory,$recursive) {
    $directory = '../../'.$directory.'/';
    if (!$recursive) {
      $this->assertTrue($this->isWriteable($directory));
      return;
    }else {
      if($directory_handler = opendir($directory)) {
        while(($file = readdir($directory_handler)) !== false) {
          if($file != "." && $file != "..") {
            if(is_dir($directory . $file)) {
              $this->assertTrue($this->isWriteable($directory . $file));
            }
          }
        }
      }
    }
  }

  /* *** DATA PROVIDERS *** */
  public function writeableDirectories() {
    $dataArray = array(
     array('addons/board/cache',false),
     array('addons/board/images/avatars/upload',false),
     array('resources/catroid',false),
     array('resources/projects',false),
     array('resources/thumbnails',false),
    array('include/xml/lang',true),
    array('tests/phpunit/framework/testdata',true),
    );
    return $dataArray;
  }
}
?>
