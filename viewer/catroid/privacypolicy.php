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
?>
    <div class="webMainMiddle">
      <div class="blueBoxMain">
        <div class="webMainContent">
          <div class="webMainContentTitle"><?php echo $this->languageHandler->getString('title')?></div>
          <div class="licenseMain">            	
            <div class ="whiteBoxMain">
              <div class="licenseText">
                <p class="licenseText">
                  <?php echo $this->languageHandler->getString('privacy_policy_intro')?><br/><br/>
                  <span class="licenseHeader"><?php echo $this->languageHandler->getString('privacy_policy_using_software_head')?></span><br/><br/>
                  <?php echo $this->languageHandler->getString('privacy_policy_using_software')?> <br/><br/>
                  <span class="licenseHeader"><?php echo $this->languageHandler->getString('privacy_policy_using_website_head')?></span><br/><br/>
                  <?php echo $this->languageHandler->getString('privacy_policy_using_website')?><br/><br/>
                  <span class="licenseHeader"><?php echo $this->languageHandler->getString('privacy_policy_contact_head')?></span><br/><br/>
                  <?php echo $this->languageHandler->getString('privacy_policy_contact', '<a class="downloadLink" href="'.impedeCrawling("mailto:".CONTACT_EMAIL).'?subject='.rawurlencode($this->languageHandler->getString('privacy_policy_email_subject')).'">'.impedeCrawling(CONTACT_EMAIL).'</a>')?> 
                </p>
              </div> <!-- License Text -->
            </div> <!--  White Box -->            	
          </div> <!--  license Main -->
        </div> <!-- mainContent close //-->
      </div> <!-- blueBoxMain close //-->
    </div>
