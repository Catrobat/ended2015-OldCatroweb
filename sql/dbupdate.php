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
 *    An additional term exception under section 7 of the GNU Affero
 *    General Public License, version 3, is available at
 *    http://www.catroid.org/catroid/licenseadditionalterm
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
//require_once '../config.php';
require_once '../passwords.php';

$connection_web = null;
if($connection_web === null) {
      $connection_web = pg_connect("host=".DB_HOST." dbname=".DB_NAME." user=".DB_USER." password=".DB_PASS)
      or die('Connection to Database failed: ' . pg_last_error());
}

set_time_limit(0);

walkThroughDirectory(dirname(__FILE__).'/'."catroweb/updates/", $connection_web);

$connection_board = null;
if($connection_board === null) {
      $connection_board = pg_connect("host=".DB_HOST_BOARD." dbname=".DB_NAME_BOARD." user=".DB_USER_BOARD." password=".DB_PASS_BOARD)
      or die('Connection to Database failed: ' . pg_last_error());
}

walkThroughDirectory(dirname(__FILE__).'/'."catroboard/updates/", $connection_board);

function walkThroughDirectory($directory, $connection) {
    $filearray = array();
    if(is_dir($directory)) {
      if($directory_handler = opendir($directory)) {
        while(($file = readdir($directory_handler)) !== false) {
          if(stristr($file, ".sql")) {
            // print "reading file: ".$directory."/".$file."\n";
            array_push($filearray,$file);
          }
        }
        closedir($directory_handler);
      }
    }
   if($filearray) {
     executeFiles($directory,$connection,$filearray);
   }
}

function executeFiles($directory,$connection,$filearray)
{
  sort($filearray);
  foreach ($filearray as $file) {
    executeQueryInFile($directory, $file, $connection);     
  }
}

function executeQueryInFile($directory, $file, $connection) {
  $sql_string = file_get_contents($directory.$file);
  $result = @pg_query($connection, $sql_string);
  if($result) {
    print "file ".$directory.$file.": query ok!\n";
  } else {
    print "file ".$directory.$file.": ERROR: ".pg_last_error($connection)."\n";
  }
}

?>
