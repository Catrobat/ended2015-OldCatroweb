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
  		new PasswordRecovery($("#basePath").attr("value"));
  	});
  </script>
  
  	<div class="webMainMiddle">
  		<div class="blueBoxMain">
  		   	<div class="webMainContent">
              <div class="webMainContentTitle">Change your password</div>
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
  										<div class="okMsg">Your new password is set. Please log in now.</div>
    		   						<br>
    		   						<input type="submit" id="passwordLoginSubmit" name="passwordLoginSubmit" value="Login now" class="button orange compact passwordRecoverySubmitButton">
  									</div>
      	  		   			<?php 
      	  		   			  if(isset($_GET['c'])) {
 
      	  		   			    if($this->showForm == 1) { ?>
      	  		   			      <form method="post" name="passwordRecoveryFormDialog" id="passwordRecoveryFormDialog" action="">
          										<div class="passwordRecoveryHeadline">Please enter your new password:</div>
          										<input type="hidden" id="c" name="c" value="<?php echo $_GET['c']; ?>">
															<input type="text" id="passwordSavePassword" name="passwordSavePassword" ><br>
          										<input type="button" id="passwordSaveSubmit" name="passwordSaveSubmit" value="Change my password now" class="button orange compact passwordRecoverySubmitButton">
          									</form>

	      	  		   					<br> 
														<div class="passwordRecoveryHelper"><a id="forgotPassword" target="_self" href="<?php echo BASE_PATH?>catroid/login">Login</a></div>
      	  		   			<?php } // showForm == 1
      	  		   			    else if($this->showForm == 2) {
      	  		   			?>
														<form method="post" action="./passwordrecovery">
															<div class="passwordRecoveryHeadline">Sorry! Your recovery url has expired. Please try again.</div>
															<input type="submit" id="passwordNextSubmit" name="passwordNextSubmit" value="Next" class="button orange compact passwordRecoverySubmitButton">
														</form>
	      	  		   					<br>
														<div class="passwordRecoveryHelper"><a id="forgotPassword" target="_self" href="<?php echo BASE_PATH?>catroid/login">Login</a></div>
      	  		   			<?php } // showForm == 2
      	  		   			  } // get['c']
      	  		   			  else if(isset($_POST['passwordSaveSubmit'])) {
 
      	  		   			    //echo ( $this->passwordRecoveryForm );
      	  		   			    if($this->showForm == 1) { ?>
      	  		   			      <form method="post" name="passwordRecoveryFormDialog" id="passwordRecoveryFormDialog" action="">
          										<div class="passwordRecoveryHeadline">Please enter your new password:</div>
          										<input type="hidden" id="c" name="c" value="<?php echo $_POST['c']; ?>">
															<input type="text" id="passwordSavePassword" name="passwordSavePassword" ><br>
          										<input type="button" id="passwordSaveSubmit" name="passwordSaveSubmit" value="Change my password now" class="button orange compact passwordRecoverySubmitButton">
          									</form>

	      	  		   					<br>
														<div class="passwordRecoveryHelper"><a id="forgotPassword" target="_self" href="<?php echo BASE_PATH?>catroid/login">Login</a></div>
      	  		   			<?php } // showForm == 1

      	  		   			    else if($this->showForm == 2) {
      	  		   			?>
														<form method="post" action="./passwordrecovery">
															<div class="passwordRecoveryHeadline">Sorry! Your recovery url has expired. Please try again.</div>
															<input type="submit" id="passwordNextSubmit" name="passwordNextSubmit" value="Next" class="button orange compact passwordRecoverySubmitButton">
														</form>
	      	  		   					<br>
														<div class="passwordRecoveryHelper"><a id="forgotPassword" target="_self" href="<?php echo BASE_PATH?>catroid/login">Login</a></div>
      	  		   			<?php } // showForm == 2
      	  		   			    else if($this->showForm == 3) {
      	  		   			?>		
  												<div class="okMsg">Your new password is set. Please log in now.</div>
    		   								<br>
  												<div class="passwordRecoveryHelper"><a id="forgotPassword" target="_self" href="<?php echo BASE_PATH?>catroid/login">Login</a></div>
      	  		   			<?php } // showForm == 3
           		   			  } // $_POST['passwordSaveSubmit'])
      	  		   				else {
        		   	      ?>
    	  		   				<form method="post" name="passwordRecoveryFormDialog" id="passwordRecoveryFormDialog" action=""> <!-- action="./passwordrecovery"> -->
												<div class="passwordRecoveryHeadline">Enter your nickname or email address:</div>
    	  		   					<input type="text" id="passwordRecoveryUserdata" name="passwordRecoveryUserdata" required="required" placeholder="nickname or email" ><br>
                      	<input type="button" id="passwordRecoverySubmit" name="passwordRecoverySubmit" value="Send me my password recovery link" class="button orange compact passwordRecoverySubmitButton">
    	  		   					<br>
    	  		   				</form>

    	  		   				<br>
	      	  		   		<br>
											<div class="passwordRecoveryHelper"><a id="forgotPassword" target="_self" href="<?php echo BASE_PATH?>catroid/login">Login</a> <br>or<br><a id="signUp" target="_self" href="<?php echo BASE_PATH?>catroid/registration">create a new account now!</a></div>
      	  		   			<?php } // else

                      ?>
            		   	<br>
            		   	<div class="addons_links">
      		   		   		<br><br>
      		  		   		<a id="aBoardLink" target="_blank" href="<?php echo BASE_PATH?>addons/board/">Board</a>
      		  		   		<br>
      		  		   		<?php if($this->module->session->userLogin_userId > 0) {?>
      		  		   			<a id="aWikiLink" target="_blank" href="<?php echo BASE_PATH?>wiki/?action=purge">Wiki</a>
      		  		   		<?php } else {?>
      		  		   			<a id="aWikiLink" target="_blank" href="<?php echo BASE_PATH?>wiki/">Wiki</a>
      		  		   		<?php }?>
            		   	</div>
                  </div> <!-- loginFormContainer -->
								</div> <!-- login Text -->
              </div> <!--  White Box -->            	
           </div> <!--  license Main -->  		   		
  		  </div> <!-- mainContent close //-->
  		</div> <!-- blueBoxMain close //-->
  	</div>
