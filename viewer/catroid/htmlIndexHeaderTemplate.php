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
    <script type="text/javascript">
      $(document).ready(function() {
        new Index(<?php echo "'".BASE_PATH."', '".PROJECT_PAGE_LOAD_MAX_PROJECTS."', '".PROJECT_PAGE_SHOW_MAX_PAGES."', '".$this->module->session->pageNr."', '".$this->module->session->searchQuery."', '".$this->module->session->task."', { loading: 'loading...' }"; ?>);
        new HeaderMenu(<?php echo "'".BASE_PATH."'"; ?>);
      });
    </script>
    <div class="webMainTop">
      <div class="blueBoxMain">
        <div class="webMainHead">
          <div id="aIndexWebLogoLeft" class="webHeadLogo">
            <img id="aIndexWebLogoLeft" class="catroidLogo" src="<?php echo BASE_PATH?>images/logo/logo_head.png" alt="head logo" />
          </div>
          <div class="webHeadTitle">
            <div class="webHeadTitleName">
                <a class="noLink" id="aIndexWebLogoMiddle" href="http://code.google.com/p/catroid/downloads/list" target="_blank">
                  <img class="catroidLettering" src="<?php echo BASE_PATH?>images/logo/logo_lettering_dl.png" alt="catroid [beta] download" />
                </a>			      			
            </div>
          </div>
          <div id="normalHeaderButtons" class="webHeadButtons">
            <button type="button" class="webHeadButtons button orange medium" id="headerSearchButton"><img class="webHeadSymbolOnButton" src="<?php echo BASE_PATH?>images/symbols/search.png" alt="Search" /></button>
            <button type="button" class="webHeadButtons button orange medium" id="headerMenuButton"><img class="webHeadSymbolOnButton" src="<?php echo BASE_PATH?>images/symbols/wall.png" alt="Menu" /></button>
            <button type="button" class="webHeadButtons button orange medium" id="headerProfileButton"><img class="webHeadSymbolOnButton" src="<?php echo BASE_PATH?>images/symbols/menu_profile.png" alt="Profile" /></button>
          </div>
          <div id="cancelHeaderButton" class="webHeadButtons">
            <button type="button" class="webHeadCancelButton button orange medium" id="headerCancelButton">
              <span class="webHeadCancelButtonText">Cancel</span>
            </button>
          </div>
          <div style="clear:both;"></div>
        </div>
      </div>
      <div id="headerSearchBox" style="display:none;">
        <div class="webHeadBoxSpacer"></div>
        <div class="blueBoxMain">
          <div class="webMainHead">
            <form id="searchForm">
              <input id="searchQuery" type="text" class="webHeadSearchBox" placeholder="Search for projects" autofocus  />             
              <input type="submit" class="webHeadSearchSubmit" value="Search" />
            </form>
          </div>
        </div>
      </div>
      <div id="headerProfileBox" class="headerProfileBox" style="display:none;">
        <div class="webHeadBoxSpacer"></div>
        <div class="blueBoxMain">
          <div class="webMainHead">
            <?php if($this->module->session->userLogin_userId <= 0) { ?>
              <form id="loginForm">
              	<div id="headerProfileBoxLeft" class="headerProfileBoxLeft" >
              		Nick: <input id="loginUsername" type="text" class="webHeadLoginBox" placeholder="your nickname"  />
                	Password: <input id="loginPassword" type="text" class="webHeadLoginBox" placeholder="your password"  />
                </div>         
                <div class="headerProfileBoxSubmitDiv" ><input id="loginSubmitButton" type="submit" class="button orange webHeadSubmitButton" value="Login" /></div>
              	<div style="clear:both;"></div>
              </form>
            <?php } else { ?>
              <form id="logoutForm">
								<div id="headerProfileBoxLeft" class="headerProfileBoxLeft" >You are logged in as <a href="/catroid/profile" class="profileText" id="profileChangeEmailText"><?php echo $this->module->session->userLogin_userNickname; ?></a>!</div>
								<div class="headerProfileBoxSubmitDiv" ><input id="logoutSubmitButton" type="submit" class="button orange webHeadSubmitButton" value="Logout" /></div>
              	<div style="clear:both;"></div>
              </form>
            <?php } ?>
          </div>
        </div>
      </div>
    </div> <!--  WEBMAINTOP -->
