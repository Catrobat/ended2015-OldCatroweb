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

class licenseTest extends PHPUnit_Framework_TestCase
{
  protected $file_listing;
  protected $allowed_extensions;
  protected $blacklist;
  protected $blacklist_folders;
  protected $license;

  protected function setUp() {
    $this->file_listing = array();
    $this->allowed_extensions = array("php", "xml", "css", "html", "htm", "js", "java", "py");
    $this->blacklist = array("CoreClientDetection.php", "classy.js", "jquery.js", "Snoopy.php", "strings.xml", ".ant-targets-build.xml", "ga.php", "test_no_version.xml", "test_v5a-199_invalid_tag.xml", "test_v5a-420.xml", "test_v5a-433.xml", "test_v5a.xml", "catroboard.html", "catroweb.html", "catrowiki.html", "normalize.css");
    $this->blacklist_folders = array(".metadata", "addons", "app-building", "pear", "target", "reports", "resources", "pootle", "phpPgAdmin", "cache", "de", "ms", "ro", "ru", "zh-CN", "zh-TW", "ja");

    $this->license = array(
    "Catroid: An on-device visual programming system for Android devices",
    "Copyright \(C\) 2010-2013 The Catrobat Team",
	  "\(<http:\/\/developer.catrobat.org\/credits>\)",
	  "This program is free software: you can redistribute it and\/or modify",
    "it under the terms of the GNU Affero General Public License as",
    "published by the Free Software Foundation, either version 3 of the",
    "License, or \(at your option\) any later version.",
    "An additional term exception under section 7 of the GNU Affero",
    "General Public License, version 3, is available at",
    "http:\/\/developer.catrobat.org\/license_additional_term",
    "This program is distributed in the hope that it will be useful,",
    "but WITHOUT ANY WARRANTY; without even the implied warranty of",
    "MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the",
    "GNU Affero General Public License for more details.",
    "You should have received a copy of the GNU Affero General Public License",
    "along with this program. If not, see <http:\/\/www.gnu.org\/licenses\/>."
    );
    $this->walkThroughDirectory(CORE_BASE_PATH);
  }

  public function testLicense() {
    foreach($this->file_listing as $current_file) {
      $contents = $this->getFileContent($current_file);
        foreach($this->license as $line) {
          $value = preg_match("/" . $line . "/", $contents);
          if(!$value) {
            echo $current_file . "\nis missing following line:\n";
            echo "  " . stripslashes($line) . "\n\n";
          }
          $this->assertTrue($value == 1);
      }
    }
  }

  public function walkThroughDirectory($directory) {
    if(is_dir($directory)) {
      if($directory_handler = opendir($directory)) {
        while(($file = readdir($directory_handler)) !== false) {
          if($file != "." && $file != "..") {
            if(is_dir($directory . $file)) {
              if(!in_array($file, $this->blacklist_folders)) {
                $this->walkThroughDirectory($directory . $file . "/");
              }
            }

            $file_extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if(in_array($file_extension, $this->allowed_extensions) &&
              !in_array($file, $this->blacklist)) {
              array_push($this->file_listing, $directory . $file);
            }
          }
        }
        closedir($directory_handler);
      }
    }
  }

  public function getFileContent($filename) {
    $handle = fopen($filename, "r");
    $contents = fread($handle, filesize($filename));
    fclose($handle);

    return $contents;
  }
  
}
?>
