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

require_once '../../../config.php';

$filesWhitelist = array("aliveCheckerDB.php", "aliveCheckerHost.php");
$modulesWhitelist = array("test");

$file_listing = walkThroughDirectory(CORE_BASE_PATH.'modules/', $filesWhitelist, $modulesWhitelist);
$mergedXmlObject = mergeStringsXmlFiles($file_listing);
$mergedXmlObject->asXML('strings.xml');

function mergeStringsXmlFiles($file_listing) {
  $stringsXml = new SimpleXMLElement("<resources></resources>");
  $stringsXml = addStringNodes($stringsXml, $file_listing);
  $stringsXml = addTemplateStringNodes($stringsXml, $file_listing);
  
  return $stringsXml;
}

function addTemplateStringNodes($destXml, $file_listing) {
  foreach($file_listing as $module=>$files) {
    $template = CORE_BASE_PATH.LANGUAGE_PATH.SITE_DEFAULT_LANGUAGE.'/'.$module.'/'.DEFAULT_TEMPLATE_LANGUAGE_FILE;
    if(!file_exists($template)) {
      print "Template XML File missing:\n$template\n";
    }
    $xml = simplexml_load_file($template);
    foreach($xml->children() as $string) {
      $attributes = $string->attributes();
      if($string->getName() && $attributes['name']) {
        $destStringName = $module.'$template$'.strval($attributes['name']);
        $destString = $destXml->addChild('string', strval($string));
        $destString->addAttribute('name', $destStringName);
      }
    }
  }
  return $destXml;
}

function addStringNodes($destXml, $file_listing) {
  foreach($file_listing as $module=>$files) {
    foreach($files as $file) {
      $class = substr($file, 0, strpos($file, '.'));
      $languageFile = CORE_BASE_PATH.LANGUAGE_PATH.SITE_DEFAULT_LANGUAGE.'/'.$module.'/'.$class.'.xml';
      if(!file_exists($languageFile)) {
        print "Language XML File missing:\n$languageFile\n";
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
