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

class languageTest extends PHPUnit_Framework_TestCase
{
  protected $file_listing;
  protected $whitelist;
  protected $whitelist_folders;

  protected function setUp() {
    $this->file_listing = array();
    $this->whitelist = array("Snoopy.php", "aliveCheckerDB.php", "aliveCheckerHost.php");
    $this->whitelist_folders = array();
    $this->walkThroughDirectory(CORE_BASE_PATH.'modules/');
  }

  public function testLanguageFolders() {
    foreach($this->file_listing as $module=>$files) {
      $folder = CORE_BASE_PATH.LANGUAGE_PATH.$module.'/'.SITE_DEFAULT_LANGUAGE;
      if(!is_dir($folder)) {
        print "Language folder missing:\n$folder\n";
      }
      $this->assertTrue(is_dir($folder));
    }
  }

  public function testErrorXmlFiles() {
    $errorDevFile = CORE_BASE_PATH.LANGUAGE_PATH.'errors/'.SITE_DEFAULT_LANGUAGE.'/errors_dev.xml';
    $errorPubFile = CORE_BASE_PATH.LANGUAGE_PATH.'errors/'.SITE_DEFAULT_LANGUAGE.'/errors_pub.xml';
    if(!file_exists($errorDevFile)) {
      print "Error XML File missing:\n$errorDevFile\n";
    }
    if(!file_exists($errorPubFile)) {
      print "Error XML File missing:\n$errorPubFile\n";
    }
    $this->assertTrue(file_exists($errorDevFile));
    $this->assertTrue(file_exists($errorPubFile));
  }

  public function testTemplateXmlFiles() {
    foreach($this->file_listing as $module=>$files) {
      $template = CORE_BASE_PATH.LANGUAGE_PATH.$module.'/'.SITE_DEFAULT_LANGUAGE.'/template.xml';
      if(!file_exists($template)) {
        print "Template XML File missing:\n$template\n";
      }
      $this->assertTrue(file_exists($template));
    }
  }

  public function testLanguageXmlFiles() {
    foreach($this->file_listing as $module=>$files) {
      foreach($files as $file) {
        $class = substr($file, 0, strpos($file, '.'));

        $languageFile = CORE_BASE_PATH.LANGUAGE_PATH.$module.'/'.SITE_DEFAULT_LANGUAGE.'/'.$class.'.xml';
        if(!file_exists($languageFile)) {
          print "Language XML File missing:\n$languageFile\n";
        }
        $this->assertTrue(file_exists($languageFile));
      }
    }
  }

  public function walkThroughDirectory($directory, $module = null) {
    if(is_dir($directory)) {
      if($directory_handler = opendir($directory)) {
        while(($file = readdir($directory_handler)) !== false) {
          if($file != "." && $file != "..") {
            if(is_dir($directory.$file)) {
              if(!in_array($file, $this->whitelist_folders)) {
                if(!isset($this->file_listing[$file])) {
                  $this->file_listing[$file] = array();
                }
                $this->walkThroughDirectory($directory.$file . "/", $file);
              }
            }

            if(!in_array($file, $this->whitelist)) {
              if($module)
              array_push($this->file_listing[$module], $file);
            }
          }
        }
        closedir($directory_handler);
      }
    }
  }

}
?>
