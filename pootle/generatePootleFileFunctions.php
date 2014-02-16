<?php
/*
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

function generatePootleFile($stringsXmlDestination = '') {
    $prefix = 'msgid ""
msgstr ""
"Project-Id-Version: PACKAGE VERSION\n"
"Report-Msgid-Bugs-To: translate-devel@lists.sourceforge.net\n"
"POT-Creation-Date: 2008-11-25 10:03+0200\n"
"PO-Revision-Date: 2011-06-19 16:37+0200\n"
"Last-Translator: Tobias <tobijat@sbox.tugraz.at>\n"
"Language-Team: LANGUAGE <LL@li.org>\n"
"Language: de\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
"Plural-Forms: nplurals=2; plural=(n != 1);\n"
"X-Generator: Pootle 2.1.5\n"';
    
  $file = $stringsXmlDestination.SITE_DEFAULT_LANGUAGE.'/strings.xml';
  if(!is_file($file)) {
    print "\nERROR: Strings.xml not found: $stringsXmlDestination.$file\n";
    exit();
  }

  $xml = simplexml_load_file($file);
  $pootle = $prefix."\n\n";
  foreach($xml->children() as $string) {
    $attributes = $string->attributes();
    if($string->getName() && $attributes['name']) {
      $msgattribut = '#msgattribut '.'"'.strval($attributes['name']).'"';
      $msgid = 'msgid '.'"'.strval($string).'"';
      $msgstr = 'msgstr '.'""';
      $pootle .= $msgattribut."\n".$msgid."\n".$msgstr."\n\n";
    }
  }
  
  $handle = fopen($stringsXmlDestination.SITE_DEFAULT_LANGUAGE.'/catweb.pot', 'wb');
  return fwrite($handle, $pootle);
}
?>
