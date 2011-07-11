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
        new HeaderMenu();
        new Login();
        new Menu(<?php echo "'".$this->module->session->userLogin_userId."'"; ?>);
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
            <button type="button" class="webHeadButtons button orange  medium" id="headerHomeButton"><img class="webHeadSymbolOnButton" src="<?php echo BASE_PATH?>images/symbols/home.png" alt="<?php echo $this->languageHandler->getString('template_header_home')?>" /></button>
            <button type="button" class="webHeadButtons button orange medium" id="headerProfileButton"><img class="webHeadSymbolOnButton" src="<?php echo BASE_PATH?>images/symbols/menu_profile.png" alt="<?php echo $this->languageHandler->getString('template_header_search')?>" /></button>
          </div>
          <div id="cancelHeaderButton" class="webHeadButtons">
            <button type="button" class="webHeadCancelButton button orange medium" id="headerCancelButton">
              <span class="webHeadCancelButtonText"><?php echo $this->languageHandler->getString('template_header_cancel')?></span>
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
              <div class="loginInfoText" id="loginInfoText">
           		<div class="loginErrorMsg" id="loginErrorMsg">
            	   <!-- errorMsg -->
              	</div>
              </div>
              <form id="loginForm">
              	<div id="headerProfileBoxLeft" class="headerProfileBoxLeft" >
              	  <?php if($this->module->clientDetection->isMobile()) {?>
              		<?php echo $this->languageHandler->getString('template_header_nick')?><br><input id="loginUsername" type="text" class="webHeadLoginBox" placeholder="<?php echo $this->languageHandler->getString('template_header_enter_nick')?>"  /><br>
                	<?php echo $this->languageHandler->getString('template_header_password')?><br><input id="loginPassword" type="text" class="webHeadLoginBox" placeholder="<?php echo $this->languageHandler->getString('template_header_enter_password')?>"  /><br>
                    <input id="loginSubmitButton" type="button" class="button orange webHeadLoginSubmit" value="<?php echo $this->languageHandler->getString('template_header_login')?>" />
              	  <?php } else {?>
                    <?php echo $this->languageHandler->getString('template_header_nick')?> <input id="loginUsername" type="text" class="webHeadLoginBox" placeholder="<?php echo $this->languageHandler->getString('template_header_enter_nick')?>"  />
                    <?php echo $this->languageHandler->getString('template_header_password')?> <input id="loginPassword" type="text" class="webHeadLoginBox" placeholder="<?php echo $this->languageHandler->getString('template_header_enter_password')?>"  />
                    <input id="loginSubmitButton" type="button" class="button orange webHeadLoginSubmit" value="<?php echo $this->languageHandler->getString('template_header_login')?>" />
              	  <?php }?>
                </div>         
              </form>
            <?php } else { ?>
              <div id="headerProfileBoxLeft" class="headerProfileBoxLeft">
               <?php echo $this->languageHandler->getString('template_header_logged_in_as')?>
               <a href="<?php echo BASE_PATH; ?>catroid/profile" class="profileText"><?php echo $this->module->session->userLogin_userNickname; ?></a>!<br>
               <div class="headerProfileBoxSubmitDiv" >
                <input id="logoutSubmitButton" type="button" class="button orange webHeadLogoutSubmit" value="<?php echo $this->languageHandler->getString('template_header_logout')?>" />
               </div>
             </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div> <!--  WEBMAINTOP -->
