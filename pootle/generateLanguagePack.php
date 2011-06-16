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

if(!isset($_SERVER['argv'][1])) {
  print "usage: php generateLanguagePack.php <languageShortcut>";
  exit();
}

generateLanguagePack($_SERVER['argv'][1]);

function generateLanguagePack($lang) {
  $file = $lang.'/strings.xml';
  if(!is_file($file)) {
    print "ERROR: Strings.xml not found: $file";
    exit();
  }

  $xml = simplexml_load_file($file);

  $stringsXmlArray = array();
  $errorsXmlArray = array();
  foreach($xml->children() as $string) {
    $attributes = $string->attributes();
    if($string->getName() && $attributes['name']) {
      $nameParts = explode('$', strval($attributes['name']));
      if(count($nameParts) < 3 || count($nameParts) > 4 || (count($nameParts) != 4 && strcmp($nameParts[0], 'errors') == 0)) {
        print "ERROR: invalid stringName: ".strval($attributes['name']);
        exit();
      }
      if(count($nameParts) == 3) {
        $module = $nameParts[0];
        $class = $nameParts[1];
        $stringName = $nameParts[2];
        $stringsXmlArray[$module][$class][$stringName] = strval($string);
      } elseif(count($nameParts) == 4) {
        $class = $nameParts[1];
        $type = $nameParts[2];
        $errorName = $nameParts[3];
        $errorsXmlArray[$class][$type][$errorName] = strval($string);
      }
    }
  }

  generateStringXmlFiles($lang, $stringsXmlArray);
  generateErrorXmlFiles($lang, $errorsXmlArray);
}

function generateStringXmlFiles($lang, $array) {
  foreach($array as $module => $classes) {
    $folder = $lang.'/'.$module;
    if(!is_dir($folder)) {
      mkdir($folder);
    }
    foreach($classes as $class => $stringNames) {
      $xml = new SimpleXMLElement("<strings></strings>");
      foreach($stringNames as $stringName => $string) {
        $destString = $xml->addChild('string', strval($string));
        $destString->addAttribute('name', $stringName);
      }
      $xmlFile = $folder.'/'.$class.'.xml';
      writeXmlFile($xml, $xmlFile);
    }
  }
}

function generateErrorXmlFiles($lang, $array) {
  $folder = $lang.'/errors';
  if(!is_dir($folder)) {
    mkdir($folder);
  }
  foreach($array as $class => $types) {
    $xml = new SimpleXMLElement("<errors></errors>");
    foreach($types as $type => $stringNames) {
      $destType = $xml->addChild($type);
      foreach($stringNames as $stringName => $string) {
        $destString = $destType->addChild('string', strval($string));
        $destString->addAttribute('name', $stringName);
      }
    }
    $xmlFile = $folder.'/'.$class.'.xml';
    writeXmlFile($xml, $xmlFile);
  }
}

function writeXmlFile($xml, $xmlFile) {
  $dom = new DOMDocument('1.0');
  $dom->preserveWhiteSpace = false;
  $dom->formatOutput = true;
  $dom->loadXML($xml->asXML());
  if($dom->save($xmlFile)) {
    print_r("XML successfully generated: $xmlFile\n");
  } else {
    print_r("ERROR: Error while generating XML: $xmlFile\n");
  }
}

?>
