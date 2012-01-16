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
                <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_use_welcome')?></p>
                <ul>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_1')?></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_2')?></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_3')?></li>
                </ul>
                <br />
                <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_use_your_support')?></p>
                <ul>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_4')?></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_5')?></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_6')?></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_7')?></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_8')?></li>
                </ul>
                <br />
                <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_use_become_a_member')?></p>
                <ul>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_9')?></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_10')?></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_11')?></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_12', '<a href="'.BASE_PATH.'catroid/licenseofuploadedprojects">'.BASE_PATH.'catroid/licenseofuploadedprojects</a>')?></li>
                </ul>
                <br />
                <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_use_our_gift')?></p>
                <ul>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_13')?></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_14')?></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_15', '<a href="'.BASE_PATH.'catroid/licenseofsystem">'.BASE_PATH.'catroid/licenseofsystem</a>', '<a href="http://code.google.com/p/catroid" target="_blank">http://code.google.com/p/catroid</a>')?></li>
                </ul>
                <br />
                <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_use_our_terms')?></p>
                <ul>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_16')?></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_17')?></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_18', '<a href="'.BASE_PATH.'catroid/termsofservice">'.BASE_PATH.'catroid/termsofservice</a>')?></li>
                </ul>
                <br />
                <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_use_check_back')?></p>
                <br />
                <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_use_mail_us', '<a href="'.impedeCrawling("mailto:".CONTACT_EMAIL).'?subject='.rawurlencode($this->languageHandler->getString('title')).'">'.impedeCrawling(CONTACT_EMAIL).'</a>')?></p>
                <br />
                <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_use_dated')?></p>
                <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_use_copy', '<a href="'.BASE_PATH.'">&lt;'.BASE_PATH.'&gt;</a>')?></p>
              </div> <!-- License Text -->
            </div> <!--  White Box -->            	
          </div> <!--  license Main -->
        </div> <!-- mainContent close //-->
      </div> <!-- blueBoxMain close //-->
    </div>
