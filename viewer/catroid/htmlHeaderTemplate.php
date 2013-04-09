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
              <a href="<?php echo BASE_PATH; ?>">Catroid</a>
            </div>
            
            <div class="largeSearchBarLeft">
             <img src="<?php echo BASE_PATH; ?>images/symbols/placeholder.png" />
            </div>

            <div class="largeSearchBarMiddle">
             <input type="search" placeholder="Projekt suchen..." />
            </div>
            
            <div class="largeSearchBarRight">
             <img src="<?php echo BASE_PATH; ?>images/symbols/placeholder.png" />
            </div>
            <div class="marginSpacer"></div>
          </div>
          <div id="smallMenu">
            <div class="marginSpacer"></div>
            
            <div id="smallMenuBar" class="catroidLink">
              <a href="<?php echo BASE_PATH; ?>">Catroid</a>
            </div>

            <div id="smallSearchBar">
              <input type="search" placeholder="Projekt suchen..." />
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
          new Header();
        });
      </script>