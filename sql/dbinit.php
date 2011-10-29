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

$connection_common = null;
if($connection_common === null) {
      $connection_common = pg_connect("host=".DB_HOST." dbname=postgres user=".DB_USER." password=".DB_PASS)      
      or die('Connection to Database failed: ' . pg_last_error());
      
}

executeQueryLineByLineInFile(dirname(__FILE__).'/'."catroweb/create/", "01_create_databases.sql", $connection_common);

$connection_web = null;
if($connection_web === null) {
      $connection_web = pg_connect("host=".DB_HOST." dbname=".DB_NAME." user=".DB_USER." password=".DB_PASS)      
      or die('Connection to Database failed: ' . pg_last_error());
}

$connection_board = null;
if($connection_board === null) {
      $connection_board = pg_connect("host=".DB_HOST_BOARD." dbname=".DB_NAME_BOARD." user=".DB_USER_BOARD." password=".DB_PASS_BOARD)
      or die('Connection to Database failed: ' . pg_last_error());
}

$connection_wiki = null;
if($connection_wiki === null) {
      $connection_wiki = pg_connect("host=".DB_HOST_WIKI." dbname=".DB_NAME_WIKI." user=".DB_USER_WIKI." password=".DB_PASS_WIKI)
      or die('Connection to Database failed: ' . pg_last_error());
}

set_time_limit(0);


walkThroughDirectory(dirname(__FILE__).'/'."catroweb/init/", $connection_web);
walkThroughDirectory(dirname(__FILE__).'/'."catroboard/init/", $connection_board);
walkThroughDirectory(dirname(__FILE__).'/'."catrowiki/init/", $connection_wiki);

function walkThroughDirectory($directory, $connection) {
    $filearray = array();
    if(is_dir($directory)) {
      if($directory_handler = opendir($directory)) {
        while(($file = readdir($directory_handler)) !== false) {
          if(stristr($file, ".sql")) {
            array_push($filearray,$file);
            // print "reading file: ".$directory."/".$file."\n";
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

function executeQueryLineByLineInFile($directory, $file, $connection) {
  $handle = fopen($directory.$file, "r");  
  while ( $sql_string = trim(fgets($handle)) ) {    
    if ($sql_string!="") {
      $result = @pg_query($connection, $sql_string);
      if($result) {
        print "query ".$sql_string." successful!\n";
      } else {
        print "query ".$sql_string." failed!\n".pg_last_error($connection)."\n";;     
      }
    }
  } 
  fclose($handle); 
}
?>
