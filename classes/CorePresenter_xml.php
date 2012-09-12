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

  require_once('XML/Serializer.php');

  class CorePresenter_xml extends CorePresenterCommon  {
    private $xmlString;
    public function __construct(CoreModule $module) {
      parent::__construct($module);
      $this->generateXML();
    }

    private function generateXML() {
      $data = $this->module->getData();
      
      $options = array('cdata' => true, 'defaultTagName' => 'xmlElement');
      if(isset($data['xmlSerializerOptions']) && is_array($data['xmlSerializerOptions'])) {
        $options = $data['xmlSerializerOptions'];
        unset($data['xmlSerializerOptions']);
      }
      
      $xml = new XML_Serializer($options);
      @$xml->serialize($data);
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
