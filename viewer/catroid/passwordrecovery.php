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
  	<div class="webMainMiddle">
  		<div class="blueBoxMain">
  		   	<div class="webMainContent">
              <div class="webMainContentTitle">Recover your password</div>
                <div class="passwordRecoveryMain">            	
            	  <div class ="whiteBoxMain">
            	    <div class="passwordRecoveryText">
            	    <div class="passwordRecoveryFormContainer">
      	  		   		
      	  		   			<?php 
      	  		   			  if(isset($_GET['c'])) {
      	  		   			    $this->getData = $_GET['c']; 
      	  		   			    echo ( $this->passwordRecoveryForm ); ?>
    	  		   				<br>
   	  		   				  <?php 
  	  		   				        if($this->answer) {
                              echo "<div class='passwordRecoveryErrorMsg'>";
  	  		   				          echo $this->answer;
  	  		   				          echo "</div>";
  	  		   				        }
        		   	            if($this->answer_ok) {
        		   	              echo "<div class='passwordRecoveryOkMsg'>";
        		   	              echo $this->answer_ok;
        		   	              echo "</div>";
        		   	            }
        		   	      ?>
	      	  		   		<br>
											<div class="passwordRecoveryHelper"><a id="forgotPassword" target="_self" href="<?php echo BASE_PATH?>catroid/login">Login</a></div>
      	  		   			<?php }
      	  		   				else if(isset($_POST['passwordSaveSubmit'])) { 
      	  		   			    //$this->getData = $_POST['passwordSaveSubmit']; 
      	  		   			    echo ( $this->passwordRecoveryForm ); ?>
    	  		   				<br>
   	  		   				  <?php 
  	  		   				        if($this->answer) {
                              echo "<div class='passwordRecoveryErrorMsg'>";
  	  		   				          echo $this->answer;
  	  		   				          echo "</div>";
  	  		   				        }
        		   	            if($this->answer_ok) {
        		   	              echo "<div class='passwordRecoveryOkMsg'>";
        		   	              echo $this->answer_ok;
        		   	              echo "</div>";
        		   	            }
        		   	      ?>
    	  		   				</div>
	      	  		   		<br>
											<div class="passwordRecoveryHelper"><a id="forgotPassword" target="_self" href="<?php echo BASE_PATH?>catroid/login">Login</a></div>
      	  		   			<?php }
    	  		   				else {?>
    	  		   				<form method="post" action="./passwordrecovery">
    	  		   					<div class="passwordRecoveryHeadline">Enter your nickname or email address:</div>
    	  		   					<input type="text" name="passwordRecoveryUserdata"><br>
    	  		   					<input type="submit" name="passwordRecoverySubmit" value="Recover password"><br>
    	  		   				</form>
    	  		   				<br>
   	  		   				  <?php 
  	  		   				        if($this->answer) {
                              echo "<div class='passwordRecoveryErrorMsg'>";
  	  		   				          echo $this->answer;
  	  		   				          echo "</div>";
  	  		   				        }
        		   	            if($this->answer_ok) {
        		   	              echo "<div class='passwordRecoveryOkMsg'>";
        		   	              echo $this->answer_ok;
        		   	              echo "</div>";
        		   	            }
        		   	      ?>
	      	  		   		<br>
											<div class="passwordRecoveryHelper"><a id="forgotPassword" target="_self" href="<?php echo BASE_PATH?>catroid/login">Login</a> or <a id="signUp" target="_self" href="<?php echo BASE_PATH?>catroid/registration">sign up now!</a></div>
      	  		   			<?php } ?>
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
