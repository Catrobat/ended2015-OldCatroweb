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
        new NewestProjects(<?php echo "'".BASE_PATH."', '".PROJECT_PAGE_MAX_PROJECTS."', '".$this->module->session->pageNr."', '".$this->numberOfPages."'"; ?>);
       
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
          <div id="normalHeaderButtons" class="webHeadButtons">
            <a title="Menu" href="<?php echo BASE_PATH?>catroid/menu"><button type="button" class="webHeadButtons button white medium" id="headerMenuButton"><img alt="menu" class="webHeadSymbolOnButton" src="<?php echo BASE_PATH?>images/symbols/wall.png" /></button></a>
            <button type="button" class="webHeadButtons button white medium" id="headerSearchButton"><img alt="search" class="webHeadSymbolOnButton" src="<?php echo BASE_PATH?>images/symbols/magnifying-glass.png" /></button>
          </div>
          <div id="cancelHeaderButton" class="webHeadButtons">
            <button type="button" class="webHeadCancelButton button white medium" id="headerCancelSearchButton">
              <span class="webHeadCancelButtonText">Cancel</span>
            </button>
          </div>
          <div class="webHeadTitle">
            <div class="webHeadTitleName">
                <a class="noLink" id="aIndexWebLogoMiddle" href="http://code.google.com/p/catroid/downloads/list" target="_blank">
                  <img class="catroidLettering" src="<?php echo BASE_PATH?>images/logo/logo_lettering_dl.png" alt="catroid [beta] download" />
                </a>			      			
            </div>
          </div>
          <div style="clear:both;"></div>
        </div>
      </div>
      <div id="headerSearchBox" style="display:none;">
        <div class="webHeadSearchBoxSpacer"></div>
        <div class="blueBoxMain">
          <div class="webMainHead">
            <form>
              <input type="search" class="webHeadSearchBox" placeholder="Search for projects" autofocus  />             
              <input type="submit" class="webHeadSearchSubmit" value="Search" />
            </form>
          </div>
        </div>
      </div>
    </div> <!--  WEBMAINTOP -->
