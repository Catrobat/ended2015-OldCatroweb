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
require_once '../../../config.php';
require_once '../../../passwords.php';

$connection = null;
if($connection === null) {
      $connection = pg_connect("host=".DB_HOST." dbname=".DB_NAME." user=".DB_USER." password=".DB_PASS)
      or die('Connection to Database failed: ' . pg_last_error());
}

walkThroughDirectory(CORE_BASE_PATH."/sql/catroweb/updates");

function walkThroughDirectory($directory) {
    if(is_dir($directory)) {
      if($directory_handler = opendir($directory)) {
        while(($file = readdir($directory_handler)) !== false) {
          if(stristr($file, ".sql")) {
            // print "reading file: ".$directory."/".$file."\n";
            executeUpdateQueryInFile($file);
          }
        }
        closedir($directory_handler);
      }
    }
}

function executeUpdateQueryInFile($file) {
  $fresult=-1;
  $fp = fopen($file, "rb+");
  if ($fp) {
    while (!feof($fp)) {
      $statement = chop(fgets($fp));
      // print "$statement\n";
      if (stristr($statement, "-- (+) ")) {
        $fresult = executeQueryCommand($statement, "-- (+)");
      } elseif (!stristr($statement, "--")) {
        $fresult = executeQueryCommand($statement, "");
      }
    }
    return $fresult;
  }
  return -1;
}

function executeQueryCommand($statement, $trigger) {
  if ($trigger) {
    $query = preg_split("/-- \(\+\) /", $statement);
    if (stristr($query[1], ".")) {
      $cquery = preg_split("/\./", $query[1]);
      $cquerystring = "select ".$cquery[1]." from ".$cquery[0];
    } else { 
      $cquerystring = $query[1];
    }
    $result = pg_query($cquerystring);
    if (!$result) {
      $qresult = pg_query("$query[0]");
      if ($qresult) { 
        print "executed: ".$query[0]."\n";
        return 1;
      } else {
        print "error: ".pg_last_error();
        return -1;
      }
    } else {
      print "existing: $query[1] - no update!\n";
      return 0;
    }
  } else {
    if (stristr($statement, ";")) {
      $result = pg_query($statement);
      if ($result)
      print "executed: ".$statement."\n";
      return 1;
    } else {
      return 0;
    }
  }
}

?>