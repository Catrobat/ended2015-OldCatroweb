<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2012 The Catroid Team
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

class updateAllSourceCodeLicenseHeaders extends PHPUnit_Framework_TestCase
{
  protected $file_listing;
  protected $allowed_extensions;
  protected $blacklist;
  protected $blacklist_folders;
  protected $license;

  protected function setUp() {
    $this->file_listing = array();
    $this->allowed_extensions = array("php", "xml", "css", "html", "htm", "js", "java");
    $this->blacklist = array("CoreClientDetection.php", "classy.js", "jquery.js", "Snoopy.php", "strings.xml", ".ant-targets-build.xml");
    $this->blacklist_folders = array(".metadata", "addons", "app-building", "pear", "target", "resources", "pootle", "phpPgAdmin");

    $this->replaceFromLicense = " \*    License, or \(at your option\) any later version.".chr(13).chr(10)
                               ." \*".chr(13).chr(10)
                               ." \*    This program is distributed in the hope that it will be useful,".chr(13).chr(10);
                               
    $this->replaceToLicense  =  " *    License, or (at your option) any later version.\n"
                               ." *\n"
                               ." *    An additional term exception under section 7 of the GNU Affero\n"
                               ." *    General Public License, version 3, is available at\n"
                               ." *    http://www.catroid.org/catroid/licenseadditionalterm\n"
                               ." *\n"
                               ." *    This program is distributed in the hope that it will be useful,\n";

    $this->replaceFromLicense2 = "      License, or License, or \(at your option\) any later version.".chr(13).chr(10)
                                ."  ".chr(13).chr(10)
                                ."      This program is distributed in the hope that it will be useful,".chr(13).chr(10);
                               
    $this->replaceToLicense2  =  "      License, or (at your option) any later version.\n"
                                ."  \n"
                                ."      An additional term exception under section 7 of the GNU Affero\n"
                                ."      General Public License, version 3, is available at\n"
                                ."      http://www.catroid.org/catroid/licenseadditionalterm\n"
                                ."  \n"
                                ."      This program is distributed in the hope that it will be useful,\n";

    $this->replaceFromLicense2a ="      License, or License, or \(at your option\) any later version.".chr(10)
                                ."  ".chr(10)
                                ."      This program is distributed in the hope that it will be useful,".chr(10);
                               
    $this->replaceToLicense2a  = "      License, or (at your option) any later version.".chr(10)
                                ."  ".chr(10)
                                ."      An additional term exception under section 7 of the GNU Affero".chr(10)
                                ."      General Public License, version 3, is available at".chr(10)
                                ."      http://www.catroid.org/catroid/licenseadditionalterm".chr(10)
                                ."  ".chr(10)
                                ."      This program is distributed in the hope that it will be useful,".chr(10);
                                
    $this->replaceFromLicense3 = "License, or \(at your option\) any later version.".chr(13).chr(10)
                                ."".chr(13).chr(10)
                                ."This program is distributed in the hope that it will be useful,".chr(13).chr(10);
                               
    $this->replaceToLicense3  =  "License, or (at your option) any later version.\n"
                                ."\n"
                                ."An additional term exception under section 7 of the GNU Affero\n"
                                ."General Public License, version 3, is available at\n"
                                ."http://www.catroid.org/catroid/licenseadditionalterm\n"
                                ."\n"
                                ."This program is distributed in the hope that it will be useful,\n";

    $this->replaceFromLicense3a = "License, or \(at your option\) any later version.".chr(10)
                                ."".chr(10)
                                ."This program is distributed in the hope that it will be useful,".chr(10);
    $this->replaceToLicense3a  = "License, or (at your option) any later version.".chr(10)
                                ."".chr(10)
                                ."An additional term exception under section 7 of the GNU Affero".chr(10)
                                ."General Public License, version 3, is available at".chr(10)
                                ."http://www.catroid.org/catroid/licenseadditionalterm".chr(10)
                                ."".chr(10)
                                ."This program is distributed in the hope that it will be useful,".chr(10);

    $this->replaceFromLicense4a = "      published by the Free Software Foundation, either version 3 of the".chr(13).chr(10)
                                 ." \*    License, or \(at your option\) any later version.".chr(10)
                                 ." \*".chr(10)
                                 ." \*    An additional term exception under section 7 of the GNU Affero".chr(10)
                                 ." \*    General Public License, version 3, is available at".chr(10)
                                 ." \*    http:\/\/www.catroid.org\/catroid\/licenseadditionalterm".chr(10)
                                 ." \*".chr(10)
                                 ." \*    This program is distributed in the hope that it will be useful,".chr(10);
    $this->replaceToLicense4a  = "      published by the Free Software Foundation, either version 3 of the".chr(10)
    														."      License, or (at your option) any later version.".chr(10)
                                ."".chr(10)
                                ."      An additional term exception under section 7 of the GNU Affero".chr(10)
                                ."      General Public License, version 3, is available at".chr(10)
                                ."      http://www.catroid.org/catroid/licenseadditionalterm".chr(10)
                                ."".chr(10)
                                ."      This program is distributed in the hope that it will be useful,".chr(10);
                                
    $this->walkThroughDirectory(CORE_BASE_PATH);
  }

  public function testLicenseUpdate() {
    foreach($this->file_listing as $current_file) {
      $contents = $this->getFileContent($current_file);
      // to see what files are checked ... 
      // but you NEVER change THIS file
      if (preg_match("/xml\/lang\/en\/catroid/copyrightpolicy.xml/", $current_file)) {
      //if (preg_match("/updateAllSourceCodeLicenseHeaders/", $current_file)) {
      // nix
      //} else { 
        print "processing file: ".$current_file;
        if (preg_match("'".$this->replaceFromLicense4a."'", $contents)) print "\nOK (4)";
        else print "\nNOT FOUND (4)";
//        if (preg_match("'".$this->replaceFromLicense3a."'", $contents)) print "\nOK (3a)";
//        else print "\nNOT FOUND (3a)";
        
        // OK -- $newcontents = preg_replace("'".$this->replaceFromLicense."'", $this->replaceToLicense, $contents);
        // OK -- $newcontents = preg_replace("'".$this->replaceFromLicense2."'", $this->replaceToLicense2, $contents);
        // OK -- $newcontents = preg_replace("'".$this->replaceFromLicense3."'", $this->replaceToLicense3, $contents);
        // OK -- $newcontents = preg_replace("'".$this->replaceFromLicense3a."'", $this->replaceToLicense3a, $contents);
        // $newcontents = preg_replace("'".$this->replaceFromLicense2a."'", $this->replaceToLicense2a, $contents);
        $newcontents = preg_replace("'".$this->replaceFromLicense4a."'", $this->replaceToLicense4a, $contents);
        if ($newcontents) {
          $fp = fopen($current_file,"rb+");
          if ($fp) {
            fwrite($fp, $newcontents);
            fclose($fp);
            print "... changed successfully.\n";
          } else { 
            print "... error writing new contents.\n";
          }
        } else {
            print "... no replacement done.\n";
        }
        $this->assertTrue(true === true);
        // exit();
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