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
  <input type="hidden" id="basePath" value="<?php echo BASE_PATH?>">
  <script type="text/javascript">
  	$(document).ready(function() {
  	  var __hm = new HeaderMenu();
      __hm.toggleProfileBox();
  	});
  </script>

  	<div class="webMainMiddle">
  		<div class="blueBoxMain">
  		   	<div class="webMainContent">
              <div class="webMainContentTitle"><?php echo $this->languageHandler->getString('title')?></div>
                <div class="loginMain">
            	  <div class ="whiteBoxMain">
            	    <div class="loginText">
            	    <div class="loginFormContainer">
     	  		   		<?php if($this->module->session->userLogin_userId <= 0) {?>
							<div class="loginHelper">
								<a id="signUp" target="_self" href="<?php echo BASE_PATH?>catroid/registration"><?php echo $this->languageHandler->getString('account_link')?></a>
								<br><?php echo $this->languageHandler->getString('or')?><br>
								<a id="forgotPassword" target="_self" href="<?php echo BASE_PATH?>catroid/passwordrecovery"><?php echo $this->languageHandler->getString('password_link')?></a>
							</div>
						<?php }?>
                     </div> <!-- loginFormContainer -->
					</div> <!-- login Text -->
              </div> <!--  White Box -->
           </div> <!--  license Main -->
  		  </div> <!-- mainContent close //-->
  		</div> <!-- blueBoxMain close //-->
  	</div>
