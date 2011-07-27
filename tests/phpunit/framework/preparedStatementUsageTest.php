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

require_once('frameworkTestsBootstrap.php');

class preparedStatementUsageTest extends PHPUnit_Framework_TestCase
{
  protected $file_listing;
  protected $allowed_extensions;
  private $statements = array();

  protected function setUp() {
    $this->file_listing = array();
    $this->allowed_extensions = array("php");
    $this->walkThroughDirectory(CORE_BASE_PATH);
    $this->setStatements(CORE_BASE_PATH.XML_PATH.'prepared_statements.xml');
  }

  public function testPrepairedStatementUsage() {
    $foundKey = array();
    foreach($this->statements as $key => $value) {
      $foundKey[$key] = false;
    }

    foreach($this->file_listing as $current_file) {
      $contents = $this->getFileContent($current_file);

      if(preg_match("/execute/i", $contents)) {
        foreach($this->statements as $key => $value) {
          if(!$foundKey[$key] && preg_match("/execute ".$key."/i", $contents)) {
            $foundKey[$key] = true;
          }
        }
      }
    }

    foreach($this->statements as $key => $value) {
      $this->assertTrue($foundKey[$key]);
    }
  }

  public function walkThroughDirectory($directory) {
    if(is_dir($directory)) {
      if($directory_handler = opendir($directory)) {
        while(($file = readdir($directory_handler)) !== false) {
          if($file != "." && $file != "..") {
            if(is_dir($directory . $file)) {
              $this->walkThroughDirectory($directory . $file . "/");
            }

            $file_extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if(in_array($file_extension, $this->allowed_extensions)) {
              array_push($this->file_listing, $directory . $file);
            }
          }
        }
        closedir($directory_handler);
      }
    }
  }

  public function setStatements($file) {
    if(file_exists($file)) {
      $xml = simplexml_load_file($file);
    } else {
      return false;
    }

    foreach($xml->children() as $query) {
      $attributes = $query->attributes();
      if($query->getName() && $attributes['name']) {
        $this->statements[strval($attributes['name'])] = strval($query);
      }
    }
    return true;
  }

  public function getFileContent($filename) {
    $handle = fopen($filename, "r");
    $contents = fread($handle, filesize($filename));
    fclose($handle);

    return $contents;
  }
}
?>
