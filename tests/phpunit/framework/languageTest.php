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

class languageTest extends PHPUnit_Framework_TestCase {
  protected $file_listing;
  protected $modules;
  protected $blacklist;
  protected $blacklist_folders;

  protected function setUp() {
    $this->file_listing = array();
    $this->modules = array("admin", "api", "catroid");
    $this->blacklist = array("aliveCheckerDB.php", "aliveCheckerHost.php", "qrCodeGenerator.php");
    $this->blacklist_folders = array();
    $this->walkThroughDirectory(CORE_BASE_PATH.'modules/');
  }

  protected function tearDown() {
    $testdata = dirname(__FILE__).'/testdata/languageTestData/';
    $runtimeFolder1 = $testdata.'testOutput1/';
    $runtimeFolder2 = $testdata.'testOutput2/';
    removeDir($runtimeFolder1);
    removeDir($runtimeFolder2);
  }

  public function testLanguageFolders() {
    foreach($this->file_listing as $module=>$files) {
      $folder = CORE_BASE_PATH.LANGUAGE_PATH.'/'.SITE_DEFAULT_LANGUAGE.'/'.$module;
      if(!is_dir($folder)) {
        print "\nLanguage folder missing:\n$folder\n";
      }
      $this->assertTrue(is_dir($folder));
    }
  }

  public function testErrorXmlFiles() {
    $errorDevFile = CORE_BASE_PATH.LANGUAGE_PATH.SITE_DEFAULT_LANGUAGE.'/errors/'.DEFAULT_DEV_ERRORS_FILE;
    $errorPubFile = CORE_BASE_PATH.LANGUAGE_PATH.SITE_DEFAULT_LANGUAGE.'/errors/'.DEFAULT_PUB_ERRORS_FILE;
    if(!file_exists($errorDevFile)) {
      print "\nError XML File missing:\n$errorDevFile\n";
    }
    if(!file_exists($errorPubFile)) {
      print "\nError XML File missing:\n$errorPubFile\n";
    }
    $this->assertTrue(file_exists($errorDevFile));
    $this->assertTrue(file_exists($errorPubFile));

    $errorsDevXml = simplexml_load_file($errorDevFile);
    $errorsPubXml = simplexml_load_file($errorPubFile);
    if(count($errorsDevXml->children()) != count($errorsPubXml->children())) {
      print "\nError XMLs for DEV and PUB differ in ErrorType Nodes!";
    }
    $this->assertEquals(count($errorsDevXml->children()), count($errorsPubXml->children()));

    $errorsDevNodeCount = array();
    foreach($errorsDevXml->children() as $error_type) {
      $errorsDevNodeCount[strval($error_type->getName())] = count($error_type->children());
    }
    foreach($errorsPubXml->children() as $error_type) {
      if($errorsDevNodeCount[strval($error_type->getName())] != count($error_type->children())) {
        print "\nErrorTypeNode ".strval($error_type->getName()).": children Nodes differ in number!";
      }
      $this->assertEquals($errorsDevNodeCount[strval($error_type->getName())], count($error_type->children()));
    }
  }

  public function testTemplateXmlFiles() {
    foreach($this->file_listing as $module=>$files) {
      $template = CORE_BASE_PATH.LANGUAGE_PATH.SITE_DEFAULT_LANGUAGE.'/'.$module.'/'.DEFAULT_TEMPLATE_LANGUAGE_FILE;
      if(!file_exists($template)) {
        print "\nTemplate XML File missing:\n$template\n";
      }
      $this->assertTrue(file_exists($template));

      $xml = simplexml_load_file($template);
      foreach($xml->children() as $string) {
        $attributes = $string->attributes();
        if($string->getName() && $attributes['name']) {
          $name = strval($attributes['name']);
          if(strcmp(substr($name, 0, strpos($name, '_')+1), 'template_') != 0) {
            print "\n'template_' - prefix missing in stringname '$name' in file $template.\n";
            print "change the stringname to 'template_$name' instead!\n";
          }
          $this->assertEquals(0, strcmp(substr($name, 0, strpos($name, '_')+1), 'template_'));
        }
      }
    }
  }

  public function testLanguageXmlFiles() {
    foreach($this->file_listing as $module=>$files) {
      foreach($files as $file) {
        $class = substr($file, 0, strpos($file, '.'));
        $languageFile = CORE_BASE_PATH.LANGUAGE_PATH.SITE_DEFAULT_LANGUAGE.'/'.$module.'/'.$class.'.xml';
        if(!file_exists($languageFile)) {
          print "\nLanguage XML File missing:\n$languageFile\n";
        }
        $this->assertTrue(file_exists($languageFile));
      }
    }
  }

  public function testLanguageScripts() {
    require_once CORE_BASE_PATH.'pootle/generateStringsXmlFunctions.php';
    require_once CORE_BASE_PATH.'pootle/generatePootleFileFunctions.php';
    require_once CORE_BASE_PATH.'modules/admin/languageManagement.php';
    $languageManagement = new languageManagement();
    $testdata = dirname(__FILE__).'/testdata/languageTestData/';
    $runtimeFolder1 = $testdata.'testOutput1/';
    $runtimeFolder2 = $testdata.'testOutput2/';
    removeDir($runtimeFolder1);
    removeDir($runtimeFolder2);
    generateStringsXml($testdata, $runtimeFolder1);
    generatePootleFile($runtimeFolder1);    
    $requestData = array('lang'=>SITE_DEFAULT_LANGUAGE, 'dest'=>$runtimeFolder1.'include/xml/lang/', 'source'=>$testdata.'catweb.xlf');
    $languageManagement->generateLanguagePackFromXlf($requestData);
    copyDir($testdata.'modules/', $runtimeFolder1.'modules/');
    generateStringsXml($runtimeFolder1, $runtimeFolder2);
    generatePootleFile($runtimeFolder2);
    $stringsXmlFile1 = $runtimeFolder1.SITE_DEFAULT_LANGUAGE.'/strings.xml';
    $stringsXmlFile2 = $runtimeFolder2.SITE_DEFAULT_LANGUAGE.'/strings.xml';
    $pootleFile1 = $runtimeFolder1.SITE_DEFAULT_LANGUAGE.'/catweb.pot';
    $pootleFile2 = $runtimeFolder2.SITE_DEFAULT_LANGUAGE.'/catweb.pot';
    $this->assertEquals(md5_file($stringsXmlFile1), md5_file($stringsXmlFile2));
    $this->assertEquals(md5_file($pootleFile1), md5_file($pootleFile2));
    removeDir($runtimeFolder1);
    removeDir($runtimeFolder2);
  }
  
  public function testErrorStringUsage() {
    $errorDevFile = CORE_BASE_PATH.LANGUAGE_PATH.SITE_DEFAULT_LANGUAGE.'/errors/'.DEFAULT_DEV_ERRORS_FILE;
    $classFileListing = $this->getClassesFileListing();
    
    $foundError = array();
    $xml = simplexml_load_file($errorDevFile);
    foreach($xml->children() as $errorType) {
      $errorTypeName = $errorType->getName();
      foreach($errorType as $error) {
        $attributes = $error->attributes();
        array_push($foundError, array($errorTypeName, $attributes['name'], false));
      }
    }
    
    foreach($foundError as $key => $tuple) {
      $errorType = $tuple[0];
      $errorName = $tuple[1];
      
      $this->walkThroughDirectory(CORE_BASE_PATH.'viewer/');

      foreach($this->file_listing as $module=>$files) {
        foreach($files as $file) {
          $moduleFile = CORE_BASE_PATH.MODULE_PATH.$module.'/'.$file;
          $viewerFile = CORE_BASE_PATH.VIEWER_PATH.$module.'/'.$file;

          if(preg_match("/'".$errorType."', '".$errorName."'/i", $this->getFileContent($moduleFile))) {
            $foundError[$key][2] = true;
            break;
          } else if(preg_match("/'".$errorType."', '".$errorName."'/i", $this->getFileContent($viewerFile))) {
            $foundError[$key][2] = true;
            break;
          }
        }
        if($foundError[$key][2]) {
          break;
        }
      }
      
      if(!$foundError[$key][2]) {
        foreach($classFileListing as $file) {
          $classFile = CORE_BASE_PATH.CLASS_PATH.$file;
          if(preg_match("/'".$errorType."', '".$errorName."'/i", $this->getFileContent($classFile))) {
            $foundError[$key][2] = true;
            break;
          }
        }
      }
    }
    
    foreach($foundError as $tuple) {
      if(!$tuple[2]) {
        echo "\nThe error type '".$tuple[0]."' message '".$tuple[1]."' was never used from:\n".$errorDevFile."\n";  
      }
      $this->assertTrue($tuple[2]);
    }
  }

  public function testLanguageStringUsage() {
    foreach($this->modules as $module) {
      $directory = CORE_BASE_PATH.LANGUAGE_PATH.SITE_DEFAULT_LANGUAGE.'/'.$module;
      if(is_dir($directory)) {
        if($directory_handler = opendir($directory)) {
          while(($file = readdir($directory_handler)) !== false) {
            if($file != "." && $file != ".." && $file != "template.xml") {
              $info = pathinfo($file);
              $moduleFile = CORE_BASE_PATH.MODULE_PATH.$module.'/'.$info['filename'].'.php';
              $viewerFile = CORE_BASE_PATH.VIEWER_PATH.$module.'/'.$info['filename'].'.php';
              
              $correspondingFileFound = false;
              if(file_exists($moduleFile)) {
                $correspondingFileFound = true;
              } elseif(file_exists($viewerFile)) {
                $correspondingFileFound = true;
              } else {
                echo "\nCould not find a module and a viewer for: ".$file."\n";
              }
              $this->assertTrue($correspondingFileFound);
              
              $foundString = array();
              $xml = simplexml_load_file($directory.'/'.$file);
              foreach($xml->children() as $query) {
                $attributes = $query->attributes();
                array_push($foundString, array($attributes['name'], false));
              }
              
              foreach($foundString as $key => $pair) {
                $string = $pair[0];
                if(preg_match("/getString\('".$string."'/i", $this->getFileContent($moduleFile))) {
                  $foundString[$key][1] = true;
                } else {
                  $viewerPath = CORE_BASE_PATH . VIEWER_PATH . $module . '/';
                  if(is_dir($viewerPath) && $viewer_handle = opendir($viewerPath)) {
                    while(($viewerFile = readdir($viewer_handle)) !== false) {
                      if(is_file($viewerPath . $viewerFile) && preg_match("/getString\('".$string."'/i", $this->getFileContent($viewerPath . $viewerFile))) {
                        $foundString[$key][1] = true;
                        break;
                      }
                    }
                  }
                }
              }
              
              foreach($foundString as $pair) {
                if(!$pair[1]) {
                  echo "\nThe string '".$pair[0]."' was never used from:\n".$directory.'/'.$file."\n";  
                }
                $this->assertTrue($pair[1]);
              }
            }
          }
          closedir($directory_handler);
        }
      }
    }
  }

  public function walkThroughDirectory($directory, $module = null) {
    if(is_dir($directory)) {
      if($directory_handler = opendir($directory)) {
        while(($file = readdir($directory_handler)) !== false) {
          if($file != "." && $file != "..") {
            if(is_dir($directory.$file)) {
              if(!in_array($file, $this->blacklist_folders)) {
                if(!isset($this->file_listing[$file])) {
                  $this->file_listing[$file] = array();
                }
                $this->walkThroughDirectory($directory.$file . "/", $file);
              }
            }

            if(!in_array($file, $this->blacklist)) {
              if($module)
              array_push($this->file_listing[$module], $file);
            }
          }
        }
        closedir($directory_handler);
      }
    }
  }
  
  public function getClassesFileListing() {
    $directory = CORE_BASE_PATH.CLASS_PATH;
    $fileListing = array();
    
    if(is_dir($directory)) {
      if($directory_handler = opendir($directory)) {
        while(($file = readdir($directory_handler)) !== false) {
          if($file != "." && $file != "..") {
            array_push($fileListing, $file);
          }
        }
      }
    }
    return $fileListing;
  }

  public function getFileContent($filename) {
    $contents = "";
    
    if(file_exists($filename)) {
      $handle = fopen($filename, "r");
      $contents = fread($handle, filesize($filename));
      fclose($handle);
    }

    return $contents;
  }
}
?>
