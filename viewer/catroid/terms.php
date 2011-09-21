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
                <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_use_catroid_community_list_head')?></p>
                <ul>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_catroid_community_list_element1')?></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_catroid_community_list_element2')?></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_catroid_community_list_element3')?></li>
                </ul>
                <p class ="licenseText"><?php echo $this->languageHandler->getString('terms_of_use_catroid_help_list_head')?></p>
                <ul>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_catroid_help_list_element1')?></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_catroid_help_list_element2')?></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_catroid_help_list_element3')?></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_catroid_help_list_element4')?></li>
                  <li><?php echo $this->languageHandler->getString('terms_of_use_catroid_help_list_element5')?></li>
                </ul>
                <p class ="licenseText">
                  <?php echo $this->languageHandler->getString('terms_of_use_project_licence',
                    '<a class = "nolink" href="http://creativecommons.org/licenses/by-sa/2.0/" target="_blank">'.$this->languageHandler->getString('share_alike_licence_link').'</a>')?><br/><br/>
                  <?php echo $this->languageHandler->getString('terms_of_use_software_licence', 
					  		    '<a href="http://www.gnu.org/licenses/gpl.html" target="_blank">'.$this->languageHandler->getString('gnu_licence_link').'</a>',
						        '<a href="http://www.gnu.org/licenses/agpl.html" target="_blank">'.$this->languageHandler->getString('gnu_affero_licence_link').'</a>')?><br/><br/>
                  <?php echo $this->languageHandler->getString('terms_of_use_google_code', 
                    '<a href="http://code.google.com/p/catroid" target="_blank">'.$this->languageHandler->getString('google_code_link').'</a>')?><br/><br/>
                  <?php echo $this->languageHandler->getString('terms_of_use_check_back', 
                    '<a href="mailto:'.CONTACT_EMAIL.'?subject='.rawurlencode($this->languageHandler->getString('title')).'">'.CONTACT_EMAIL.'</a>')?><br/><br/>
                  <?php echo $this->languageHandler->getString('terms_of_use_thanks')?><br/><br/>
                  <?php echo $this->languageHandler->getString('terms_of_use_team')?> 
                </p>
              </div> <!-- License Text -->
            </div> <!--  White Box -->            	
          </div> <!--  license Main -->
        </div> <!-- mainContent close //-->
      </div> <!-- blueBoxMain close //-->
    </div>