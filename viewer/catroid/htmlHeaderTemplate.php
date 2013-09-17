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
    
      <header role="banner">
        <nav role="navigation">
          <div id="largeMenu">
            <div class="marginSpacer"></div>
            <div class="catroidLink">
              <a href="<?php echo BASE_PATH; ?>"><img src="<?php echo BASE_PATH;?>images/logo/logo_text.png" alt="<?php echo APPLICATION_NAME;?>" /></a>
            </div>
            
            <div id="largeSearchButton" class="largeSearchBarLeft">
              <button class="img-magnifying-glass"></button>
            </div>

            <div class="largeSearchBarMiddle">
              <input type="search" placeholder="<?php echo $this->languageHandler->getString('template_header_search_placeholder'); ?>" />
            </div>
            
            <div id="largeMenuButton" class="largeSearchBarRight">
              <button class="img-avatar"<?php echo ($this->module->session->userLogin_userAvatar) ? ' style="background-size:cover;background-position:center;background-image:url(' . $this->module->session->userLogin_userAvatar . ');outline:1px solid #FFFFFF;"' : ''; ?>></button>
              <?php echo ($this->module->session->userLogin_userNickname) ? '<button id="userNameButton"><div style="float:left;">' . $this->module->session->userLogin_userNickname . '</div><div class="img-dropdownArrow"></div></button>' : ''; ?>
            </div>
            <div class="marginSpacer"></div>
          </div>
          <div id="smallMenu">
            <div class="marginSpacer"></div>
            
            <div id="smallMenuBar" class="catroidLink">
              <a href="<?php echo BASE_PATH; ?>"><img src="<?php echo BASE_PATH;?>images/logo/logo_text.png" alt="<?php echo APPLICATION_NAME;?>" /></a>
            </div>

            <div id="smallSearchBar">
              <input type="search" placeholder="<?php echo $this->languageHandler->getString('template_header_search_placeholder'); ?>" />
            </div>

            <div id="mobileSearchButton">
              <button class="img-magnifying-glass"></button>
            </div>
            
            <div class="marginSpacer"></div>

            <div id="mobileMenuButton">
              <button class="img-avatar"<?php echo ($this->module->session->userLogin_userAvatar) ? ' style="background-size:cover; background-position:center; background-repeat: norepeat; background-image:url(' . $this->module->session->userLogin_userAvatar . ');outline:1px solid #FFFFFF;"' : ''; ?>></button>
            </div>
            
            <div class="marginSpacer"></div>
          </div>
          
          <div id="navigationMenu">
            <ul>
              <li id="menuProfileButton"><div class="img-author-big">&nbsp;</div><div><?php echo $this->languageHandler->getString('template_header_my_profile'); ?></div></li>
              <li id="menuLogoutButton"><div class="img-logout">&nbsp;</div><div><?php echo $this->languageHandler->getString('template_header_logout'); ?></div></li>
            </ul>
          </div>
        </nav>
      </header>
      <script type="text/javascript">
        $(document).ready(function() {
          Header = new Header(<?php echo $this->module->session->userLogin_userId; ?>);
          SearchBar = new SearchBar(Header);
        });
      </script>
