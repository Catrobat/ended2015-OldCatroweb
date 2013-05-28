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

class languageManagement extends CoreAuthenticationAdmin {
  private $licenseString;

  public function __construct() {
    parent::__construct();
    $this->addJs('adminLanguageManagement.js');
    $this->licenseString = "<!--
Catroid: An on-device visual programming system for Android devices
Copyright (C) 2010-2013 The Catrobat Team
(<http://developer.catrobat.org/credits>)\n
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.\n
An additional term exception under section 7 of the GNU Affero
General Public License, version 3, is available at
http://developer.catrobat.org/license_additional_term\n
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU Affero General Public License for more details.\n
You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
-->";
  }

  public function __default() {

  }

  public function generateLanguagePack() {
    try {
      $answer = $this->generateLanguagePackFromXlf($_REQUEST);
      $this->statusCode = STATUS_CODE_OK;
      $this->answer = $answer;
    } catch(Exception $e) {
      $this->statusCode = STATUS_CODE_INTERNAL_SERVER_ERROR;
      $this->answer = $e->getMessage();
      return false;
    }
  }

  public function generateLanguagePackFromXlf($requestData) {
    if(isset($requestData['lang'])) {
      $lang = $requestData['lang'];
      if(isset($requestData['dest'])) {
        $dest = $requestData['dest'];
      } else {
        $dest = CORE_BASE_PATH.LANGUAGE_PATH;
      }
      if(isset($requestData['source'])) {
        $source = $requestData['source'];
      } else {
        $pootleFriendlyLang = preg_replace("^-^", "_", $lang);
        $source = ADMIN_POOTLE_ROOT_URL.$pootleFriendlyLang.'/catweb/catweb.po/export/xlf';
      }
      try {
        $xmlArrays = $this->getXmlArrays($this->getXmlObject($this->loadXlfFile($source)));
        $this->generateStringXmlFiles($lang, $xmlArrays[0], $dest);
        $this->generateErrorXmlFiles($lang, $xmlArrays[1], $dest);
      } catch(Exception $e) {
        throw new Exception($e->getMessage());
      }
    } else {
      throw new Exception($this->errorHandler->getError('admin', 'pootle_post_data_missing'));
    }
    return($this->languageHandler->getString('pootle_language_successfully_created', $lang));
  }

  private function loadXlfFile($source) {
    $data = @fopen($source, "r");
    if(!$data) {
      throw new Exception($this->errorHandler->getError('admin', 'pootle_load_xlf_failed', '', $source));
    }
    return stream_get_contents($data);
  }

  private function getXmlObject($xlfString) {
    $xml = @simplexml_load_string($xlfString);
    if(!$xml) {
      throw new Exception($this->errorHandler->getError('admin', 'pootle_not_valid_xml'));
    }
    return $xml;
  }

  private function getXmlArrays($xml) {
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
          throw new Exception($this->errorHandler->getError('admin', 'pootle_invalid_stringname_in_xml', '', $nameAttribute));
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
    return(array($stringsXmlArray, $errorsXmlArray));
  }

  private function generateStringXmlFiles($lang, $array, $languagePackDestination) {
    foreach($array as $module => $classes) {
      $folder = $languagePackDestination.$lang.'/'.$module;
      if(!is_dir($folder)) {
        mkdir($folder, 0777, true);
      }
      foreach($classes as $class => $stringNames) {
        $xml = new SimpleXMLElement($this->licenseString."<strings></strings>");
        foreach($stringNames as $stringName => $string) {
          if(strval($string)) {
            $destString = $xml->addChild('string', strval($string));
            $destString->addAttribute('name', $stringName);
          }
        }
        $xmlFile = $folder.'/'.$class.'.xml';
        try {
          $this->writeXmlFile($xml, $xmlFile);
        } catch(Exception $e) {
          throw new Exception($e->getMessage());
        }
      }
    }
  }

  private function generateErrorXmlFiles($lang, $array, $languagePackDestination) {
    $folder = $languagePackDestination.$lang.'/errors';
    if(!is_dir($folder)) {
      mkdir($folder, 0777, true);
    }
    foreach($array as $class => $types) {
      $xml = new SimpleXMLElement($this->licenseString."<errors></errors>");
      foreach($types as $type => $stringNames) {
        $destType = $xml->addChild($type);
        foreach($stringNames as $stringName => $string) {
          $destString = $destType->addChild('string', strval($string));
          $destString->addAttribute('name', $stringName);
        }
      }
      $xmlFile = $folder.'/'.$class.'.xml';
      try {
        $this->writeXmlFile($xml, $xmlFile);
      } catch(Exception $e) {
        throw new Exception($e->getMessage());
      }
    }
  }

  private function writeXmlFile($xml, $xmlFile) {
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    if(@$dom->save($xmlFile)) {
      return true;
    } else {
      throw new Exception($this->errorHandler->getError('admin', 'pootle_could_not_write_xml_file', '', $xmlFile));
    }
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
