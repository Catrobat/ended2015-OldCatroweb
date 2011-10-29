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
 *    An additional term exception under section 7 of the GNU Affero
 *    General Public License, version 3, is available at
 *    http://www.catroid.org/catroid/licenseadditionalterm
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
          <div class="menuListRow">
            <div class="whiteBoxMain" id="firstRow">
              <div class="menuListElementRow">
                <button id="menuProfileButton" type="button" class="menuElementButton button green medium" title="Profile">
                  <span class="menuElementButtonLabel">
                    <img src="<?php echo BASE_PATH; ?>images/symbols/menu_profile.png" alt="Profile" /><br /><?php echo $this->languageHandler->getString('profile')?> 
                  </span>
                </button>
              </div>
              <div class="menuListElementRow">
                <button id="menuForumButton" type="button" class="menuElementButton button blue medium" title="Forum">
                  <span class="menuElementButtonLabel">
                    <img src="<?php echo BASE_PATH; ?>images/symbols/menu_forum.png" alt="Forum" /><br /><?php echo $this->languageHandler->getString('forum')?> 
                  </span>
                </button>
              </div>
              <div class="menuListElementRow">
                <button id="menuWikiButton" type="button" class="menuElementButton button darkorange medium" title="Wiki">
                  <span class="menuElementButtonLabel">
                    <img src="<?php echo BASE_PATH; ?>images/symbols/menu_wiki.png" alt="Wiki" /><br /><?php echo $this->languageHandler->getString('wiki')?> 
                  </span>
                </button>
              </div>              
              <div style="clear:left;"></div>
            </div> <!-- whiteBoxMain close //-->
            <div class="menuListSpacer"></div>
            <div class="whiteBoxMain" id="secondRow">
              <div class="menuListElementRow">
                <button id="menuLoginButton" type="button" class="menuElementButton button green medium" title="Login">
                  <span class="menuElementButtonLabel">
                    <img src="<?php echo BASE_PATH; ?>images/symbols/menu_profile.png" alt="Log in now" /><br /><?php echo $this->languageHandler->getString('login')?> 
                  </span>
                </button>
              </div>
              <div class="menuListElementRow">
                <button id="menuRegistrationButton" type="button" class="menuElementButton button red medium" title="Registration">
                  <span class="menuElementButtonLabel">
                    <img src="<?php echo BASE_PATH; ?>images/symbols/menu_profile.png" alt="Profile" /><br /><?php echo $this->languageHandler->getString('sign_up')?> 
                  </span>
                </button>
              </div>
                
              <div style="clear:left;"></div>
            </div> <!-- whiteBoxMain close //-->
            <div class="menuListSpacer"></div>
            <div class="whiteBoxMain" id="thirdRow">
              <div class="menuListElementRow">
                <button id="menuWallButton" type="button" class="menuElementButton button green medium" title="Wall">
                  <span class="menuElementButtonLabel">
                    <img src="<?php echo BASE_PATH; ?>images/symbols/menu_wall.png" alt="Wall" /><br /><?php echo $this->languageHandler->getString('wall')?> 
                  </span>
                </button>
              </div>
              <div class="menuListElementRow">
                <button id="menuSettingsButton" type="button" class="menuElementButton button rosy medium" title="Settings">
                  <span class="menuElementButtonLabel">
                    <img src="<?php echo BASE_PATH; ?>images/symbols/menu_settings.png" alt="Settings" /><br /><?php echo $this->languageHandler->getString('settings')?> 
                  </span>
                </button>
              </div>
              <div style="clear:left;"></div>
            </div> <!-- whiteBoxMain close //-->
          </div>
        </div> <!-- mainContent close //-->
      </div> <!-- blueBoxMain close //-->
    </div> <!--  WEBMAINMIDDLE -->
