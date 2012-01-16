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
                <p class="licenseText"><?php echo $this->languageHandler->getString('license_of_uploaded_projects_accepting')?></p>
                <ul>
                  <li><?php echo $this->languageHandler->getString('license_of_uploaded_projects_enumeration_1', '<a href="http://www.fsf.org/" target="_blank">Free Software Foundation</a>', '<a href="'.BASE_PATH.'catroid/agpl3standalone">' . $this->languageHandler->getString('license_of_uploaded_projects_link_title_1') . '</a>', '<a href="http://www.gnu.org/licenses/agpl.html" target="_blank">' . $this->languageHandler->getString('license_of_uploaded_projects_link_title_2') . '</a>')?><br/><br/></li>
                  <li><?php echo $this->languageHandler->getString('license_of_uploaded_projects_enumeration_2', '<a href="'.BASE_PATH.'catroid/ccbysa3">Creative Commons Attribution-ShareAlike 3.0 License</a>', '<a href="http://creativecommons.org/licenses/by-sa/3.0/" target="_blank">http://creativecommons.org/licenses/by-sa/3.0/</a>')?><br/><br/></li>
                  <li><?php echo $this->languageHandler->getString('license_of_uploaded_projects_enumeration_3', '<a href="'.BASE_PATH.'catroid/termsofservice">'.BASE_PATH.'catroid/termsofservice</a>')?><br/><br/></li>
                </ul>
                <p class="licenseText"><?php echo $this->languageHandler->getString('license_of_uploaded_projects_check_back')?></p>
                <br />
                <p class="licenseText"><?php echo $this->languageHandler->getString('license_of_uploaded_projects_mail_us', '<a href="'.impedeCrawling("mailto:".CONTACT_EMAIL).'?subject='.rawurlencode($this->languageHandler->getString('title')).'">'.impedeCrawling(CONTACT_EMAIL).'</a>')?></p>
                <br />
                <p class="licenseText"><?php echo $this->languageHandler->getString('license_of_uploaded_projects_dated')?></p>
                <p class="licenseText"><?php echo $this->languageHandler->getString('license_of_uploaded_projects_copy', '<a href="'.BASE_PATH.'">&lt;'.BASE_PATH.'&gt;</a>')?></p>
              </div> <!-- License Text -->
            </div> <!--  White Box -->            	
          </div> <!--  license Main -->
        </div> <!-- mainContent close //-->
      </div> <!-- blueBoxMain close //-->
    </div>
