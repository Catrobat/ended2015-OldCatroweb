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

function generateLanguagePack($lang, $stringsXlfDestination = '', $languagePackDestination = '', $sourceXlfFile = 'catweb.xlf') {
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
  $file = $stringsXlfDestination.$lang.'/'.$sourceXlfFile;
  if(!is_file($file)) {
    print "\nERROR: $sourceXlfFile not found: $stringsXlfDestination.$file\n";
    exit();
  }

  $xml = simplexml_load_file($file);

  $stringsXmlArray = array();
  $errorsXmlArray = array();

  foreach($xml->children()->children()->children() as $unit) {
    foreach($unit->children() as $tag) {
      if(strcmp($tag->getName(), 'target') == 0) {
        $message = strval($tag);
      }
      if(strcmp($tag->getName(), 'note') == 0) {
        $tmp = strval($tag);
        $note = substr($tmp, strpos($tmp, '"')+1, strpos($tmp, '"', strpos($tmp, '"')+1)-(strpos($tmp, '"')+1));
      }
    }
    $uniqueParts = explode('%', $note);
    foreach($uniqueParts as $nameAttribute) {
      $nameParts = explode('$', $nameAttribute);
      if(count($nameParts) < 3 || count($nameParts) > 4 || (count($nameParts) != 4 && strcmp($nameParts[0], 'errors') == 0)) {
        print "ERROR: invalid stringName: ".$nameAttribute;
        exit();
      }
      if(count($nameParts) == 3) {
        $module = $nameParts[0];
        $class = $nameParts[1];
        $stringName = $nameParts[2];
        $stringsXmlArray[$module][$class][$stringName] = $message;
      } elseif(count($nameParts) == 4) {
        $class = $nameParts[1];
        $type = $nameParts[2];
        $errorName = $nameParts[3];
        $errorsXmlArray[$class][$type][$errorName] = $message;
      }
    }
  }

  generateStringXmlFiles($lang, $stringsXmlArray, $license, $languagePackDestination);
  generateErrorXmlFiles($lang, $errorsXmlArray, $license, $languagePackDestination);
}

function generateStringXmlFiles($lang, $array, $license, $languagePackDestination) {
  foreach($array as $module => $classes) {
    $folder = $languagePackDestination.$lang.'/'.$module;
    if(!is_dir($folder)) {
      mkdir($folder, 0777, true);
    }
    foreach($classes as $class => $stringNames) {
      $xml = new SimpleXMLElement($license."<strings></strings>");
      foreach($stringNames as $stringName => $string) {
        $destString = $xml->addChild('string', strval($string));
        $destString->addAttribute('name', $stringName);
      }
      $xmlFile = $folder.'/'.$class.'.xml';
      writeXmlFile($xml, $xmlFile);
    }
  }
}

function generateErrorXmlFiles($lang, $array, $license, $languagePackDestination) {
  $folder = $languagePackDestination.$lang.'/errors';
  if(!is_dir($folder)) {
    mkdir($folder, 0777, true);
  }
  foreach($array as $class => $types) {
    $xml = new SimpleXMLElement($license."<errors></errors>");
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
    return true;
  } else {
    print_r("ERROR: Error while generating XML: $xmlFile\n");
  }
}

?>
