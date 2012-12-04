<?php
/**
 *    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2012 The Catroid Team 
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
        new PasswordRecovery();
      });
    </script>

    <div class="webMainMiddle">
      <div class="blueBoxMain">
  		   	<div class="webMainContent">
              <div class="webMainContentTitle"><?php echo $this->languageHandler->getString('title'); ?></div>
                <div class="loginMain">            	
            	  <div class ="whiteBoxMain">
            	    <div class="loginText">
                    <div class="loginHeader"><?php echo $this->languageHandler->getString('headline'); ?></div>
                    <input type="hidden" id="passwordRecoveryHash" value="<?php echo htmlentities($_GET['c']); ?>" />
                    <input id="passwordSavePassword" type="text" class="catroid webHeadLoginBox" required="required" /><br />
                    <input id="passwordSaveSubmit" type="button" class="catroidSubmit button orange loginSubmitButton" value="<?php echo $this->languageHandler->getString('change_password'); ?>" /><br />
                    <br /> <br /> <br /> <br />
                    
                    <div class="otherOptions"><?php echo $this->languageHandler->getString('additional_options'); ?></div>
                    <ul class="loginOptions">
                      <li><a id="signUp" href="<?php echo BASE_PATH?>catroid/registration"><?php echo $this->languageHandler->getString('account_link')?></a></li>
                    </ul>
                </div> <!-- login Text -->
              </div> <!--  White Box -->            	
           </div> <!--  license Main -->  		   		
  		  </div> <!-- mainContent close //-->
  		</div> <!-- blueBoxMain close //-->
  	</div>
