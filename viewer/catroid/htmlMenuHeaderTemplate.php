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
        new HeaderMenu(<?php echo "'".BASE_PATH."'"; ?>);
        new Login(<?php echo "'".BASE_PATH."'"; ?>);
        new Menu(<?php echo "'".BASE_PATH."', '".$this->module->session->userLogin_userId."'"; ?>);
      });
    </script>
    <div class="webMainTop">
      <div class="blueBoxMain">
        <div class="webMainHead">
          <div class="webHeadLogo">
            <a id="aIndexWebLogoLeft" href="<?php echo BASE_PATH?>catroid/index/1">
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
            <button type="button" class="webHeadButtons button orange  medium" id="headerHomeButton"><img class="webHeadSymbolOnButton" src="<?php echo BASE_PATH?>images/symbols/home.png" alt="Home" /></button>
            <button type="button" class="webHeadButtons button orange medium" id="headerProfileButton"><img class="webHeadSymbolOnButton" src="<?php echo BASE_PATH?>images/symbols/menu_profile.png" alt="Search" /></button>
          </div>
          <div id="cancelHeaderButton" class="webHeadButtons">
            <button type="button" class="webHeadCancelButton button orange medium" id="headerCancelButton">
              <span class="webHeadCancelButtonText">Cancel</span>
            </button>
          </div>
          <div style="clear:both;"></div>
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
