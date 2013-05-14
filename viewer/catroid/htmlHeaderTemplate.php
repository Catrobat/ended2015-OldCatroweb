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
              <a href="<?php echo BASE_PATH; ?>">Pocket Code</a>
            </div>
            
            <div class="largeSearchBarLeft">
             <img src="<?php echo BASE_PATH; ?>images/symbols/placeholder.png" />
            </div>

            <div class="largeSearchBarMiddle">
              <input type="search" placeholder="<?php echo $this->languageHandler->getString('template_header_search_placeholder'); ?>" />
            </div>
            
            <div class="largeSearchBarRight">

            <?php if ($this->userData['id']) { ?>

              <a id="headerProfileButton" href="#"><img src="<?php echo $this->userData['avatar']; ?>" />
              <span><?php echo $this->userData['username']; ?></span></a>
             <div id="profileMenuNavigation"><div id="profileMenuNavigationContent"><ul>
              <li><a href="#" id="menuProfileButton"><div class="icon">&nbsp;</div>Mein Profil</a></li>
              <li><a href="#" id="menuProfileChangeButton"><div class="icon">&nbsp;</div>Profil bearbeiten</a></li>
              <li><a href="#" id="menuLogoutButton"><div class="icon">&nbsp;</div>Ausloggen</a></li>
             </ul></div></div>              

            <?php } else { ?>
            
                        
             <a id="headerLoginButton" href="#"><img src="<?php echo BASE_PATH; ?>images/symbols/placeholder.png" />
             <span>Login</span></a>

            <?php } ?>

            </div>
            <div class="marginSpacer"></div>
          </div>
          <div id="smallMenu">
            <div class="marginSpacer"></div>
            
            <div id="smallMenuBar" class="catroidLink">
              <a href="<?php echo BASE_PATH; ?>">Pocket Code</a>
            </div>

            <div id="smallSearchBar">
              <input type="search" placeholder="<?php echo $this->languageHandler->getString('template_header_search_placeholder'); ?>" />
            </div>

            <div>
              <img id="mobileSearchButton" src="<?php echo BASE_PATH; ?>images/symbols/placeholder.png" />
            </div>
            
            <div class="marginSpacer"></div>

            <div>
              <img src="<?php echo BASE_PATH; ?>images/symbols/placeholder.png" />
            </div>
            
            <div class="marginSpacer"></div>
          </div>
        </nav>
      </header>
      <script type="text/javascript">
        $(document).ready(function() {
          Header = new Header();
          SearchBar = new SearchBar(Header);
          HeaderMenu = new HeaderMenu();
        });
      </script>
