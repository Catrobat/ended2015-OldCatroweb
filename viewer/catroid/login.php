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
  		new Login($("#basePath").attr("value"));
  	});
  </script>

  	<div class="webMainMiddle">
  		<div class="blueBoxMain">
  		   	<div class="webMainContent">
              <div class="webMainContentTitle">Login</div>
                <div class="loginMain">            	
            	  <div class ="whiteBoxMain">
            	    <div class="loginText">
            	    <div class="loginFormContainer">
            	    	<div class="loginFormAnswer" id="loginFormAnswer">
            	    		<div class="errorMsg" id="errorMsg">
            	    		<!-- error messages here -->
              		   	</div>
            		   	</div>
            		   	<div class="loginFormDialog" id="loginFormDialog">
        	  		   		<form method="post" class="loginForm" id="loginForm" >
                          <?php if($this->module->session->userLogin_userId <= 0) { ?>
        	  		   				<div class="loginH2">Please enter your nickname and your password:</div>
        	  		   				Nickname: <br>
        	  		   				<input type="text" name="loginUsername" id="loginUsername" ><br>
        	  		   				Password:<br> 
        	  		   				<input type="password" name="loginPassword" id="loginPassword"><br>
        	  		   				<?php //var_dump($this->requesturi); 
        	  		   				if(($this->requesturi) || (isset($this->requesturi)) || $this->requesturi != '') { ?>
        	  		   				  <input type="hidden" name="requesturi" id="requesturi" value="<?php echo htmlspecialchars($this->requesturi); ?>"> 
        	  		   				<?php } else { ?>
        	  		   				  <input type="hidden" name="requesturi" id="requesturi" value="<?php echo htmlspecialchars($_GET['requesturi']); ?>">
        	  		   				<?php } ?>
                        	<input type="button" name="loginSubmit" id="loginSubmit" value="Login" class="button orange compact loginSubmitButton">
        	  		   				<br>
        	  		   			<?php } else {?>
        	  		   				Hello <?php echo $this->module->session->userLogin_userNickname?>!<br>
        	  		   				You are logged in with ID <?php echo $this->module->session->userLogin_userId?><br>
                        	<input type="button" name="logoutSubmit" id="logoutSubmit" value="Logout" class="button orange compact loginSubmitButton">
        	  		   			<?php }?>
        	  		   		</form>
      	  		   		</div>
      	  		   		<br>
      	  		   		<?php if($this->module->session->userLogin_userId <= 0) {?>
										<div class="loginHelper"><a id="signUp" target="_self" href="<?php echo BASE_PATH?>catroid/registration">Create a new account now</a> <br>or<br> <a id="forgotPassword" target="_self" href="<?php echo BASE_PATH?>catroid/passwordrecovery">click here if you forgot your password?</a></div>
										<?php }?>
            		   	<br>
      		   		   		<br><br>
      		  		   		<a id="aBoardLink" target="_blank" href="<?php echo BASE_PATH?>addons/board/">Board</a>
      		  		   		<br>
      		  		   		<?php if($this->module->session->userLogin_userId > 0) {?>
      		  		   			<a id="aWikiLink" target="_blank" href="<?php echo BASE_PATH?>wiki/?action=purge">Wiki</a>
      		  		   		<?php } else {?>
      		  		   			<a id="aWikiLink" target="_blank" href="<?php echo BASE_PATH?>wiki/">Wiki</a>
      		  		   		<?php }?>
                  </div> <!-- loginFormContainer -->
								</div> <!-- login Text -->
              </div> <!--  White Box -->            	
           </div> <!--  license Main -->  		   		
  		  </div> <!-- mainContent close //-->
  		</div> <!-- blueBoxMain close //-->
  	</div>
