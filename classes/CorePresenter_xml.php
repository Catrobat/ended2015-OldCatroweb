<?php
/**
  * Catroid: An on-device visual programming system for Android devices
  * Copyright (C) 2010-2014 The Catrobat Team
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

  require_once('XML/Serializer.php');

  class CorePresenter_xml extends CorePresenterCommon  {
    private $xmlOptions;
    private $xmlString;
    
    public function __construct(CoreModule $module) {
      parent::__construct($module);
      
      $this->xmlOptions = array('cdata' => true, 'defaultTagName' => 'xmlElement');
      if(is_array($this->xmlSerializerOptions)) {
        $this->xmlOptions = $this->xmlSerializerOptions;
      }

      $this->module->preHeaderMessages = ob_get_contents();
      ob_clean();

      $xml = new XML_Serializer($this->xmlOptions);
      @$xml->serialize($this->module->getData());
      $this->xmlString = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n" . $xml->getSerializedData();
    }

    public function display() {
      header("Cache-Control: no-cache, must-revalidate");
      header("Expires: Sun, 4 Apr 2004 04:04:04 GMT");
      header("Content-Type: text/xml; charset=utf-8");
      echo $this->xmlString;
      return true;
    }

    public function getXmlString() {
      return $this->xmlString;
    }

    public function __destruct() {
      parent::__destruct();
    }
  }
?>
