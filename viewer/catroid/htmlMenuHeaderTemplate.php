<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010  Catroid development team 
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>
    <script type="text/javascript">
      $(document).ready(function() {
        new HeaderMenu(<?php echo "'".BASE_PATH."'"; ?>);
        new Menu(<?php echo "'".BASE_PATH."', '".$this->module->session->userLogin_userId."'"; ?>);
      });
    </script>
    <div class="webMainTop">
      <div class="blueBoxMain">
        <div class="webMainHead">
          <div class="webHeadLogo">
            <a id="aIndexWebLogoLeft" href="<?php echo BASE_PATH?>catroid/index">
              <img class="catroidLogo" src="<?php echo BASE_PATH?>images/logo/logo_head.png" alt="head logo" />
            </a>
          </div>        
          <div class="webHeadTitle">
            <div class="webHeadTitleName">
                <a class="noLink" id="aIndexWebLogoMiddle" href="catroid/index">
                  <img class="catroidLettering" src="<?php echo BASE_PATH?>images/logo/logo_lettering.png" alt="catroid [beta]" />
                </a>			      			
            </div>
          </div>
          <div id="normalHeaderButtons" class="webHeadButtons">
            <button type="button" class="webHeadButtons button orange medium" id="headerBackButton"><img class="webHeadSymbolOnButton" src="<?php echo BASE_PATH?>images/symbols/back.png" alt="Back" /></button>
            <button type="button" class="webHeadButtons button orange  medium" id="headerHomeButton"><img class="webHeadSymbolOnButton" src="<?php echo BASE_PATH?>images/symbols/home.png" alt="Home" /></button>
          </div>
          <div style="clear:both;"></div>
        </div>
      </div>
  		
      <div id="headerSearchBox">
        <div class="webHeadSearchBoxSpacer"></div>
        <div class="blueBoxMain">
          <div class="webMainHead">
            <form>
              <input type="search" class="webHeadSearchBox" placeholder="Search for projects" />
              <input type="submit" class="webHeadSearchSubmit" value="Search" />
            </form>
          </div>
        </div>
      </div>
    </div> <!--  WEBMAINTOP -->
