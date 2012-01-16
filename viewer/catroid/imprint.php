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
              <div class="imprintText">
                <p class="licenseText">
                  <?php echo $this->languageHandler->getString('development_text', '<br><a href="http://www.ist.tugraz.at" target="_blank">'
                    .$this->languageHandler->getString('institute_for_software_technology').'</a><br>')?><br/><br/>            	                 	     
                  <span class="licenseHeader"><?php echo $this->languageHandler->getString('address')?></span><br/>
                  <?php echo $this->languageHandler->getString('institute_for_software_technology')?><br/>
                  <?php echo $this->languageHandler->getString('graz_university_of_technology')?><br/>
                  <?php echo $this->languageHandler->getString('address_street')?><br/>
                  <?php echo $this->languageHandler->getString('address_city')?><br/>
                  <?php echo $this->languageHandler->getString('address_country')?><br/><br/>
                  <span class="imprintHeader"><?php echo $this->languageHandler->getString('contact')?></span><br/>
                  <?php echo $this->languageHandler->getString('contact_text', '<a class="downloadLink" href="'.impedeCrawling("mailto:".CONTACT_EMAIL).'">'.impedeCrawling(CONTACT_EMAIL).'</a>')?> 
                </p>
              </div> <!-- imprintText -->
            </div> <!--  whiteBoxMain -->            	
          </div> <!--  licenseMain -->
        </div> <!-- mainContent close //-->
      </div> <!-- blueBoxMain close //-->
    </div>  