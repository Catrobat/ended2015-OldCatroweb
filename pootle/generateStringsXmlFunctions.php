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
  $filesWhitelist = array("aliveCheckerDB.php", "aliveCheckerHost.php", "qrCodeGenerator.php");
  $modulesWhitelist = array("test");

  $file_listing = walkThroughDirectory($coreBasePath.'modules/', $filesWhitelist, $modulesWhitelist);
  $modArr = array();
  $fileArr = array();
  $i = 0;
  foreach($file_listing as $mod => $file) {
    $modArr[$i] = $mod;
    $fileArr[$i] = $file;
    $i++;
  }
  array_multisort($file_listing, $modArr, SORT_ASC, $fileArr, SORT_ASC);

  $mergedXmlObject = mergeStringsXmlFiles($file_listing, $coreBasePath);
  if(!is_dir($stringsXmlDestination.SITE_DEFAULT_LANGUAGE)) {
    mkdir($stringsXmlDestination.SITE_DEFAULT_LANGUAGE, 0777, true);
  }
  if($mergedXmlObject->asXML($stringsXmlDestination.SITE_DEFAULT_LANGUAGE.'/strings.xml')) {
    return true;
  } else {
    print "\nERROR: Error while generating XML: ".$stringsXmlDestination.SITE_DEFAULT_LANGUAGE.'/strings.xml'."\n";
  }
}

function mergeStringsXmlFiles($file_listing, $coreBasePath) {
  $uniqueStrings = array();
  addStringNodes($file_listing, $coreBasePath, $uniqueStrings);
  addTemplateStringNodes($file_listing, $coreBasePath, $uniqueStrings);
  addErrorsStringNodes($coreBasePath, $uniqueStrings);
  
  $stringsXml = buildXml($uniqueStrings);
  return $stringsXml;
}

function buildXml($uniqueStrings) {
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
  $destXml = new SimpleXMLElement($license."<resources></resources>");
  foreach($uniqueStrings as $strings) {
    $clearedString = $strings['string'];
    $destStringName = $strings['stringName'];
    $destString = $destXml->addChild('string', $clearedString);
    $destString->addAttribute('name', $destStringName);
  }
  return $destXml;
}

function addTemplateStringNodes($file_listing, $coreBasePath, &$uniqueStrings) {
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
        $clearedString = preg_replace('/\s+/', ' ', trim(strval($string)));
        $destStringName = $module.'$'.$templateNodeName.'$'.strval($attributes['name']);
        if(isset($uniqueStrings[md5($clearedString)]['stringName'])) {
          $uniqueStrings[md5($clearedString)]['stringName'] .= '%'.$destStringName;
        } else {
          $uniqueStrings[md5($clearedString)]['stringName'] = $destStringName;
          $uniqueStrings[md5($clearedString)]['string'] = $clearedString;
        }
      }
    }
  }
}

function addStringNodes($file_listing, $coreBasePath, &$uniqueStrings) {
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
          $clearedString = preg_replace('/\s+/', ' ', trim(strval($string)));
          $destStringName = $module.'$'.$class.'$'.strval($attributes['name']);
          if(isset($uniqueStrings[md5($clearedString)]['stringName'])) {
            $uniqueStrings[md5($clearedString)]['stringName'] .= '%'.$destStringName;
          } else {
            $uniqueStrings[md5($clearedString)]['stringName'] = $destStringName;
            $uniqueStrings[md5($clearedString)]['string'] = $clearedString;
          }
        }
      }
    }
  }
}

function addErrorsStringNodes($coreBasePath, &$uniqueStrings) {
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
        $clearedString = preg_replace('/\s+/', ' ', trim(strval($error)));
        $destStringName = $errorsModuleName.'$'.$errorsDevNodeName.'$'.strval($error_type->getName()).'$'.strval($attributes['name']);
        if(isset($uniqueStrings[md5($clearedString)]['stringName'])) {
          $uniqueStrings[md5($clearedString)]['stringName'] .= '%'.$destStringName;
        } else {
          $uniqueStrings[md5($clearedString)]['stringName'] = $destStringName;
          $uniqueStrings[md5($clearedString)]['string'] = $clearedString;
        }
      }
    }
  }
  $xml = simplexml_load_file($errorsPub);
  foreach($xml->children() as $error_type) {
    foreach($error_type as $error) {
      $attributes = $error->attributes();
      if($error_type->getName() && $attributes['name']) {
        $clearedString = preg_replace('/\s+/', ' ', trim(strval($error)));
        $destStringName = $errorsModuleName.'$'.$errorsPubNodeName.'$'.strval($error_type->getName()).'$'.strval($attributes['name']);
        if(isset($uniqueStrings[md5($clearedString)]['stringName'])) {
          $uniqueStrings[md5($clearedString)]['stringName'] .= '%'.$destStringName;
        } else {
          $uniqueStrings[md5($clearedString)]['stringName'] = $destStringName;
          $uniqueStrings[md5($clearedString)]['string'] = $clearedString;
        }
      }
    }
  }
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
