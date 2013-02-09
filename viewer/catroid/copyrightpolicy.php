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

?>
    <div class="webMainMiddle">
      <div class="blueBoxMain">
        <div class="webMainContent">
          <div class="webMainContentTitle"><?php echo $this->languageHandler->getString('title')?></div>
          <div class="licenseMain">            	
            <div class ="whiteBoxMain">
              <div class="licenseText">
                <p class="licenseText"> 
                  <?php echo $this->languageHandler->getString('copyright_policy_part1',
                    '<a href="'.impedeCrawling("mailto:".CONTACT_EMAIL).'?subject='.rawurlencode($this->languageHandler->getString('title')).'">'.$this->languageHandler->getString('email_link').'</a>')?><br/><br/>
                  <?php echo $this->languageHandler->getString('copyright_policy_part2',
                    '<a class="nolink" href="http://eur-lex.europa.eu/LexUriServ/LexUriServ.do?uri=CELEX:32001L0029:EN:HTML" target="_blank">'.$this->languageHandler->getString('directive_link').'</a>')?><br/><br/>
                  <?php echo $this->languageHandler->getString('copyright_policy_part3')?><br/><br/>
                  <?php echo $this->languageHandler->getString('copyright_policy_part4')?><br/><br/>
                  <?php echo $this->languageHandler->getString('copyright_policy_part5',
                    '<a class="nolink" href="http://chillingeffects.org" target="_blank">'.$this->languageHandler->getString('chilling_link').'</a>')?>           	   					
                </p>
              </div> <!-- License Text -->
            </div> <!--  White Box -->            	
          </div> <!--  license Main -->
        </div> <!-- mainContent close //-->
      </div> <!-- blueBoxMain close //-->
    </div>
