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

function generateStringsXml($coreBasePath = CORE_BASE_PATH, $stringsXmlDestination = '') {
  $filesWhitelist = array("aliveCheckerDB.php", "aliveCheckerHost.php");
  $modulesWhitelist = array("test");

  $file_listing = walkThroughDirectory($coreBasePath.'modules/', $filesWhitelist, $modulesWhitelist);
  $mergedXmlObject = mergeStringsXmlFiles($file_listing, $coreBasePath);
  if(!is_dir($stringsXmlDestination.SITE_DEFAULT_LANGUAGE)) {
    mkdir($stringsXmlDestination.SITE_DEFAULT_LANGUAGE, 0777, true);
  }
  $dom = new DOMDocument('1.0');
  $dom->preserveWhiteSpace = false;
  $dom->formatOutput = true;
  $dom->loadXML($mergedXmlObject->asXML());
  if($dom->save($stringsXmlDestination.SITE_DEFAULT_LANGUAGE.'/strings.xml')) {
    //print "\nXML successfully generated: ".$stringsXmlDestination.SITE_DEFAULT_LANGUAGE.'/strings.xml'."\n";
    return true;
  } else {
    print "\nERROR: Error while generating XML: ".$stringsXmlDestination.SITE_DEFAULT_LANGUAGE.'/strings.xml'."\n";
  }
}

function mergeStringsXmlFiles($file_listing, $coreBasePath) {
  $license = "<!--
Catroid: An on-device graphical programming language for Android devices
Copyright (C) 2010-2011 The Catroid Team
(<http://code.google.com/p/catroid/wiki/Credits>)\n
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.\n
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.\n
You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
-->";
  $stringsXml = new SimpleXMLElement($license."<resources></resources>");
  $stringsXml = addStringNodes($stringsXml, $file_listing, $coreBasePath);
  $stringsXml = addTemplateStringNodes($stringsXml, $file_listing, $coreBasePath);
  $stringsXml = addErrorsStringNodes($stringsXml, $coreBasePath);
  return $stringsXml;
}

function addTemplateStringNodes($destXml, $file_listing, $coreBasePath) {
  $templateNodeName = substr(DEFAULT_TEMPLATE_LANGUAGE_FILE, 0, strpos(DEFAULT_TEMPLATE_LANGUAGE_FILE, '.'));
  foreach($file_listing as $module=>$files) {
    $template = $coreBasePath.LANGUAGE_PATH.SITE_DEFAULT_LANGUAGE.'/'.$module.'/'.DEFAULT_TEMPLATE_LANGUAGE_FILE;
    if(!file_exists($template)) {
      print "ERROR: Template XML File missing:\n$template\n";
      exit();
    }
    $xml = simplexml_load_file($template);
    foreach($xml->children() as $string) {
      $attributes = $string->attributes();
      if($string->getName() && $attributes['name']) {
        $destStringName = $module.'$'.$templateNodeName.'$'.strval($attributes['name']);
        $destString = $destXml->addChild('string', strval($string));
        $destString->addAttribute('name', $destStringName);
      }
    }
  }
  return $destXml;
}

function addStringNodes($destXml, $file_listing, $coreBasePath) {
  foreach($file_listing as $module=>$files) {
    foreach($files as $file) {
      $class = substr($file, 0, strpos($file, '.'));
      $languageFile = $coreBasePath.LANGUAGE_PATH.SITE_DEFAULT_LANGUAGE.'/'.$module.'/'.$class.'.xml';
      if(!file_exists($languageFile)) {
        print "ERROR: Language XML File missing:\n$languageFile\n";
        exit();
      }
      $xml = simplexml_load_file($languageFile);
      foreach($xml->children() as $string) {
        $attributes = $string->attributes();
        if($string->getName() && $attributes['name']) {
          $destStringName = $module.'$'.$class.'$'.strval($attributes['name']);
          $destString = $destXml->addChild('string', strval($string));
          $destString->addAttribute('name', $destStringName);
        }
      }
    }
  }
  return $destXml;
}

function addErrorsStringNodes($destXml, $coreBasePath) {
  $errorsModuleName = 'errors';
  $errorsDevNodeName = substr(DEFAULT_DEV_ERRORS_FILE, 0, strpos(DEFAULT_DEV_ERRORS_FILE, '.'));
  $errorsPubNodeName = substr(DEFAULT_PUB_ERRORS_FILE, 0, strpos(DEFAULT_PUB_ERRORS_FILE, '.'));
  $errorsDev = $coreBasePath.LANGUAGE_PATH.SITE_DEFAULT_LANGUAGE.'/'.$errorsModuleName.'/'.DEFAULT_DEV_ERRORS_FILE;
  $errorsPub = $coreBasePath.LANGUAGE_PATH.SITE_DEFAULT_LANGUAGE.'/'.$errorsModuleName.'/'.DEFAULT_PUB_ERRORS_FILE;
  if(!file_exists($errorsDev)) {
    print "Error XML File missing:\n$errorsDev\n";
    exit();
  }
  if(!file_exists($errorsPub)) {
    print "Error XML File missing:\n$errorsPub\n";
    exit();
  }

  $xml = simplexml_load_file($errorsDev);
  foreach($xml->children() as $error_type) {
    foreach($error_type as $error) {
      $attributes = $error->attributes();
      if($error_type->getName() && $attributes['name']) {
        $destStringName = $errorsModuleName.'$'.$errorsDevNodeName.'$'.strval($error_type->getName()).'$'.strval($attributes['name']);
        $destString = $destXml->addChild('string', strval($error));
        $destString->addAttribute('name', $destStringName);
      }
    }
  }
  $xml = simplexml_load_file($errorsPub);
  foreach($xml->children() as $error_type) {
    foreach($error_type as $error) {
      $attributes = $error->attributes();
      if($error_type->getName() && $attributes['name']) {
        $destStringName = $errorsModuleName.'$'.$errorsPubNodeName.'$'.strval($error_type->getName()).'$'.strval($attributes['name']);
        $destString = $destXml->addChild('string', strval($error));
        $destString->addAttribute('name', $destStringName);
      }
    }
  }
  return $destXml;
}

function walkThroughDirectory($directory, $whitelist = array(), $whitelist_folders = array(), $module = null) {
  $file_listing = array();
  if(is_dir($directory)) {
    if($directory_handler = opendir($directory)) {
      while(($file = readdir($directory_handler)) !== false) {
        if($file != "." && $file != "..") {
          if(is_dir($directory.$file)) {
            if(!in_array($file, $whitelist_folders)) {
              if(!isset($file_listing[$file])) {
                $file_listing[$file] = array();
              }
              $file_listing = array_merge($file_listing, walkThroughDirectory($directory.$file . "/", $whitelist, $whitelist_folders, $file));
            }
          }
          if(!in_array($file, $whitelist)) {
            if($module) {
              if(!isset($file_listing[$module])) {
                $file_listing[$module] = array();
              }
              array_push($file_listing[$module], $file);
            }
          }
        }
      }
      closedir($directory_handler);
    }
  }
  return $file_listing;
}
?>
