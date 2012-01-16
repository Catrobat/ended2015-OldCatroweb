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
?>
    <div class="webMainMiddle">
      <div class="blueBoxMain">
        <div class="webMainContent">
          <div class="webMainContentTitle"><?php echo $this->languageHandler->getString('title')?></div>
          <div class="licenseMain">            	
            <div class ="whiteBoxMain">
              <div class="licenseText">
                <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_system_catroid_use')?></p>
                <ul>
                  <li><?php echo $this->languageHandler->getString('terms_of_system_enumeration_1', '<a href="http://www.gnu.org/philosophy/free-sw.html" target="_blank">' . $this->languageHandler->getString('terms_of_system_link_title_1') . '</a>', '<a href="http://www.fsf.org/" target="_blank">Free Software Foundation</a>', '<a href="' . BASE_PATH . 'catroid/agpl3standalone">' . $this->languageHandler->getString('terms_of_system_link_title_2') . '</a>', '<a href="http://www.gnu.org/licenses/agpl.html" target="_blank">' . $this->languageHandler->getString('terms_of_system_link_title_3') . '</a>')?><br/><br/></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_system_enumeration_2_part_1')?> <em><?php echo $this->languageHandler->getString('terms_of_system_enumeration_2_part_2', '<a href="' . BASE_PATH . '">' . BASE_PATH . '</a>')?></em> <?php echo $this->languageHandler->getString('terms_of_system_enumeration_2_part_3')?> <em><?php echo $this->languageHandler->getString('terms_of_system_enumeration_2_part_4', '<a href="' . BASE_PATH . '">' . BASE_PATH . '</a>')?></em> <?php echo $this->languageHandler->getString('terms_of_system_enumeration_2_part_5')?><br/><br/></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_system_enumeration_3_part_1', '<a href="' . BASE_PATH . 'catroid/agpl3standalone">' . $this->languageHandler->getString('terms_of_system_link_title_2') . '</a>')?> <strong><?php echo $this->languageHandler->getString('terms_of_system_enumeration_3_part_2')?></strong> <?php echo $this->languageHandler->getString('terms_of_system_enumeration_3_part_3', '<a href="http://www.gnu.org/licenses/gcc-exception-faq.html" target="_blank"> ' . $this->languageHandler->getString('terms_of_system_link_title_4') . ' </a>', '<a href="' . BASE_PATH . '">' . BASE_PATH . '</a>', '<a href="http://www.fsf.org/" target="_blank">Free Software Foundation</a>', '<a href="' . BASE_PATH . 'catroid/agpl3standalone">' . $this->languageHandler->getString('terms_of_system_link_title_2') . '</a>', '<a href="http://www.gnu.org/licenses/agpl.html" target="_blank">' . $this->languageHandler->getString('terms_of_system_link_title_3') . '</a>', '<a href="' . BASE_PATH . 'catroid/agpl3standalone">' . $this->languageHandler->getString('terms_of_system_link_title_2') . '</a>', '<a href="' . BASE_PATH . 'catroid/licenseadditionalterm">' . $this->languageHandler->getString('terms_of_system_link_title_5') . '</a>')?> <strong><?php echo $this->languageHandler->getString('terms_of_system_enumeration_3_part_4')?></strong> <?php echo $this->languageHandler->getString('terms_of_system_enumeration_3_part_5', '<a href="' . BASE_PATH . 'catroid/termsofuse">' . BASE_PATH . 'catroid/termsofuse</a>', '<a href="' . BASE_PATH . 'catroid/licenseofuploadedprojects">' . BASE_PATH . 'catroid/licenseofuploadedprojects</a>')?><br/><br/></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_system_enumeration_4', '<a href="' . BASE_PATH . 'catroid/ccbysa3">' . $this->languageHandler->getString('terms_of_system_link_title_6') . '</a>', '<a href="http://creativecommons.org/licenses/by-sa/3.0/" target="_blank">http://creativecommons.org/licenses/by-sa/3.0/</a>')?><br/><br/></li>
                </ul>
                <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_system_third_party_code')?></p>
                <ul>
                  <li><?php echo $this->languageHandler->getString('terms_of_system_enumeration_5_part_1', '<a href="http://xstream.codehaus.org/" target="_blank">&lt;http://xstream.codehaus.org&gt;</a>')?><br/><?php echo $this->languageHandler->getString('terms_of_system_enumeration_5_part_2', '<a href="http://xstream.codehaus.org/license.html" target="_blank">&lt;http://xstream.codehaus.org/license.html&gt;</a>')?><br/><?php echo $this->languageHandler->getString('terms_of_system_enumeration_5_part_3')?><br/><?php echo $this->languageHandler->getString('terms_of_system_enumeration_5_part_4')?><br/><?php echo $this->languageHandler->getString('terms_of_system_enumeration_5_part_5')?><br/><br/><?php echo $this->languageHandler->getString('terms_of_system_enumeration_5_part_6')?><br/><br/><?php echo $this->languageHandler->getString('terms_of_system_enumeration_5_part_7')?><br/><br/><?php echo $this->languageHandler->getString('terms_of_system_enumeration_5_part_8')?><br/><br/><?php echo $this->languageHandler->getString('terms_of_system_enumeration_5_part_9')?><br/><br/></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_system_enumeration_6_part_1', '<a href="http://libgdx.badlogicgames.com/" target="_blank">&lt;http://libgdx.badlogicgames.com&gt;</a>')?><br/><?php echo $this->languageHandler->getString('terms_of_system_enumeration_6_part_2', '<a href="http://libgdx.badlogicgames.com/license.php" target="_blank">&lt;http://libgdx.badlogicgames.com/license.php&gt;</a>')?><br/><?php echo $this->languageHandler->getString('terms_of_system_enumeration_6_part_3', '<a href="http://code.google.com/p/libgdx/people/list" target="_blank">&lt;http://code.google.com/p/libgdx/people/list&gt;</a>')?><br/><br/></li>
                </ul>
                <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_system_check_back')?></p>
                <br />
                <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_system_mail_us', '<a href="'.impedeCrawling("mailto:".CONTACT_EMAIL).'?subject='.rawurlencode($this->languageHandler->getString('title')).'">'.impedeCrawling(CONTACT_EMAIL).'</a>')?></p>
                <br />
                <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_system_dated')?></p>
                <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_system_copy', '<a href="'.BASE_PATH.'">&lt;'.BASE_PATH.'&gt;</a>')?></p>
              </div> <!-- License Text -->
            </div> <!--  White Box -->            	
          </div> <!--  license Main -->
        </div> <!-- mainContent close //-->
      </div> <!-- blueBoxMain close //-->
    </div>
