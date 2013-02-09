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
            <div class="webMainContentTitle"><?php echo $this->languageHandler->getString('userTitle'); ?></div> 
            <div class="profileMain">                
              <div class ="whiteBoxMain">
                <div class="profileFormContainer">
                    
                  <div class="avatarContainer">
                    <img src="<?php echo $this->userData['avatar']; ?>" class="avatar" />
                  </div>
  
                  <div class="profileItem">
                    <div class="label"><?php echo $this->languageHandler->getString('name'); ?></div>
                    <div><?php echo $this->userData['username']; ?></div>
                  </div>
  
                  <div class="profileItem">
                    <div class="label"><?php echo $this->languageHandler->getString('location'); ?></div>
                    <div>
                      <?php 
                        $countries = getCountryArray($this->languageHandler);
                        if($this->userData['country'] != "") {
                          echo $countries[$this->userData['country']];
                        }
                      ?>
                    </div>
                  </div>
  
                  <div class="profileItem">
                    <div class="label"><?php echo $this->languageHandler->getString('projects'); ?></div>
                    <div>
                      <div><?php echo $this->userData['project_count']; ?></div>
                    </div>
                  </div>
  
                </div> <!-- profileFormContainer -->
              </div> <!--  White Box -->                
            </div> <!--  license Main -->                     
          </div> <!-- mainContent close //-->
        </div> <!-- blueBoxMain close //-->
      </div>

