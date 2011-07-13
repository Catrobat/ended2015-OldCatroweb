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
  		new PasswordRecovery();
  	});
  </script>
  
  	<div class="webMainMiddle">
  		<div class="blueBoxMain">
  		   	<div class="webMainContent">
              <div class="webMainContentTitle"><?php echo $this->languageHandler->getString('title')?></div>
                <div class="passwordRecoveryMain">            	
            	  <div class ="whiteBoxMain">
            	    <div class="passwordRecoveryText">
            	    <div class="passwordRecoveryFormContainer">
            	    	<div class="passwordRecoveryFormAnswer" id="passwordRecoveryFormAnswer">
            	    		<div class="errorMsg" id="errorMsg">
            	    		<!-- error messages here -->
              		   	</div>
            	    		<div class="okMsg" id="okMsg">
            	    		<!-- ok messages here -->
              		   	</div>
            		   	</div>
  		   						<div id="loginOk">
    		   						<br>
    		   						<input type="submit" id="loginOkForwardSubmit" name="loginOkForwardSubmit" value="<?php echo $this->languageHandler->getString('login_ok_submit')?>" class="button orange compact passwordRecoverySubmitButton">
  									</div>
      	  		   			<?php 
      	  		   			  if(isset($_GET['c'])) {
 
      	  		   			    if($this->showForm == 1) { ?>
          										<div class="passwordRecoveryHeadline"><?php echo $this->languageHandler->getString('headline')?></div>
          										<input type="hidden" id="passwordRecoveryHash" name="passwordRecoveryHash" value="<?php echo $_GET['c']; ?>">
															<input type="text" id="passwordSavePassword" name="passwordSavePassword" >
															<br>
          										<input type="button" id="passwordSaveSubmit" name="passwordSaveSubmit" value="<?php echo $this->languageHandler->getString('change_password')?>" class="button orange compact passwordRecoverySubmitButton">
	      	  		   					<br> 
      	  		   			<?php } // showForm == 1
      	  		   			    else if($this->showForm == 2) {
      	  		   			?>
															<div class="errorMsg"><?php echo $this->languageHandler->getString('expired_url')?></div>
															<br>
															<input type="submit" id="passwordNextSubmit" name="passwordNextSubmit" value="<?php echo $this->languageHandler->getString('next')?>" class="button orange compact passwordRecoverySubmitButton">
	      	  		   					<br>
      	  		   			<?php } // showForm == 2
      	  		   			  } // get['c']
      	  		   			  else if(isset($_POST['passwordSaveSubmit'])) {
 
      	  		   			    //echo ( $this->passwordRecoveryForm );
      	  		   			    if($this->showForm == 1) { ?>
          										<div class="passwordRecoveryHeadline"><?php echo $this->languageHandler->getString('headline')?></div>
          										<input type="hidden" id="c" name="c" value="<?php echo $_POST['c']; ?>">
															<input type="text" id="passwordSavePassword" name="passwordSavePassword" >
															<br>
												      <br>
          										<input type="button" id="passwordSaveSubmit" name="passwordSaveSubmit" value="<?php echo $this->languageHandler->getString('change_password')?>" class="button orange compact passwordRecoverySubmitButton">
        	  		   					<br>
      	  		   			<?php } // showForm == 1
      	  		   			    else if($this->showForm == 2) {
      	  		   			?>
															<div class="errorMsg"><?php echo $this->languageHandler->getString('expired_url')?></div>
															<br>
															<input type="submit" id="passwordNextSubmit" name="passwordNextSubmit" value="<?php echo $this->languageHandler->getString('next')?>" class="button orange compact passwordRecoverySubmitButton">
	      	  		   					<br>
      	  		   			<?php } // showForm == 2
           		   			  } // $_POST['passwordSaveSubmit'])
      	  		   				else {
        		   	      ?>
  												<div class="passwordRecoveryHeadline"><?php echo $this->languageHandler->getString('enter_userdata')?></div>
      	  		   					<input type="text" id="passwordRecoveryUserdata" name="passwordRecoveryUserdata" required="required" placeholder="<?php echo $this->languageHandler->getString('nickname_placeholder')?>" >
      	  		   					<br>
												  <br>
                        	<input type="button" id="passwordRecoverySendLink" name="passwordRecoverySendLink" value="<?php echo $this->languageHandler->getString('send_link')?>" class="button orange compact passwordRecoverySubmitButton">
    	  		   				<br>
	      	  		   		<br>
      	  		   			<?php } // else
                      ?>
											<div class="passwordRecoveryHelper"><a id="forgotPassword" target="_self" href="<?php echo BASE_PATH?>catroid/login"><?php echo $this->languageHandler->getString('login')?></a> <br>or<br><a id="signUp" target="_self" href="<?php echo BASE_PATH?>catroid/registration"><?php echo $this->languageHandler->getString('new_account')?></a></div>
                  </div> <!-- loginFormContainer -->
								</div> <!-- login Text -->
              </div> <!--  White Box -->            	
           </div> <!--  license Main -->  		   		
  		  </div> <!-- mainContent close //-->
  		</div> <!-- blueBoxMain close //-->
  	</div>
