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
  		new Profile($("#basePath").attr("value"));
  	});
  </script>
  
  	<div class="webMainMiddle">
  		<div class="blueBoxMain">
  		   	<div class="webMainContent">
              <div class="webMainContentTitle">
              <?php 
                  echo $this->requestedUser; 
              ?>'s Profile
              </div> <!-- webMainContentTitle --> 
						  <div class="profileMain">            	
            	  <div class ="whiteBoxMain">
            	    <div class="profileText">
            	    <div class="profileFormContainer">
            	    	<div class="profileFormAnswer" id="profileFormAnswer">
            	    		<div class="errorMsg" id="errorMsg">
            	    		<!-- error messages here -->
              		   	</div>
            	    		<div class="okMsg" id="okMsg">
            	    		<!-- ok messages here -->
              		   	</div>
            		   	</div>
            		   	<br>
            		   	<?php 
                      if($this->ownProfile) {            		   	
            		   	?>
                  	<form method="POST" name="profileFormDialog" id="profileFormDialog" action="">
                  	  <input type="hidden" id="profileUser" name="profileUser" value="<?php echo $this->requestedUser; ?>">
          		   			<a href="#" class="profileText" id="profileChangePassword">change my password</a><br>
          		   			<div id="profilePasswordDiv">
          		   			<input type="text" id="profileOldPassword" name="profileOldPassword" value="<?php echo htmlspecialchars($this->postData['profileOldPassword']); ?>" required="required" placeholder="enter your old password" ><br>
          		   			<input type="text" id="profileNewPassword" name="profileNewPassword" value="<?php echo htmlspecialchars($this->postData['profileNewPassword']); ?>" required="required" placeholder="enter your new password" ><br>
          		   			<input type="button" name="profilePasswordSubmit" id="profilePasswordSubmit" value="Send my changes" class="button orange compact profileSubmitButton">
          		   			</div>
          		   			<br>
          		   			<div id="profileEmailTextDiv">
          		   			<a href="#" class="profileText" id="profileChangeEmailText"><?php echo $this->userEmail; ?></a><br>
          		   			</div>
											<div id="profileEmailChangeDiv">
          		   			<a href="#" class="profileText" id="profileChangeEmail">change my e-mail address</a><br>
          		   			<input type="email" id="profileEmail" name="profileEmail" value="<?php echo htmlspecialchars($this->postData['profileEmail'])?>" required="required" placeholder="enter new email address" ><br>
          		   			<input type="button" name="profileEmailSubmit" id="profileEmailSubmit" value="Send my changes" class="button orange compact profileSubmitButton">
          		   			</div>
          		   			<br>
          		   			<div id="profileCountryDiv">
          		   			<a href="#" class="profileText" id="profileChangeCountry">change my country</a><br>
          		   			<select id="profileCountry" name="profileCountry" class="profile" id="profileCountry" required="required" >
          		   		<?php // country 
          		   			$x = 0;
          		   			$sumCount = count($this->countryCodeList);
          		   			while($x < $sumCount+1) {
          		   			  if($x == 0) {
                          echo '<option value="0" >select your country</option>\r';
                        }
          		   			  else if(strcmp($this->countryCodeList[$x], $this->userCountryCode) == 0) {
                          echo "<option value=\"" . $this->countryCodeList[$x] . "\" selected >" . $this->countryNameList[$x] . "</option>\r";
                        }
                        else {
                          echo "<option value=\"" . $this->countryCodeList[$x] . "\" >" . $this->countryNameList[$x] . "</option>\r";
                        }
                        $x++;           
          		   			}
          		   		?>
          		   			</select><br>
          		   			<input type="button" name="profileCountrySubmit" id="profileCountrySubmit" value="Send my changes" class="button orange compact profileSubmitButton">
          		   			<br>
          		   			</div>
											<div id="profileCountryTextDiv">
          		   		<?php 
          		   			$x = 0;
          		   			$sumCount = count($this->countryCodeList);
          		   			while($x < $sumCount+1) {
          		   			  if(strcmp($this->countryCodeList[$x], $this->userCountryCode) == 0) {
                          echo 'from <a href="#" class="profileText" id="profileChangeCountryText">'.$this->countryNameList[$x].'</a>';
                          break;
                        }
                        $x++;           
          		   			}
          		   		?>
          		   			<br>
											</div>
          		   			<br>
          		   			<div id="profileCancelDiv">
                      <input type="button" name="profileCancel" id="profileCancel" value="Cancel" class="button orange compact profileSubmitButton">
                      </div>
                      <br>
                      <br>
                      <br> 
											<div class="passwordRecoveryHelper"><a id="forgotPassword" target="_self" href="<?php echo BASE_PATH?>catroid/login">Login</a> <br>or<br><a id="forgotPassword" target="_self" href="<?php echo BASE_PATH?>catroid/passwordrecovery">click here if you forgot your password?</a></div>
            		   	</form>
            		   	<?php 
                      }
                      else {
                    ?>
                    <div id="profileCountryTextDiv">
          		   		<?php 
          		   			$x = 0;
          		   			$sumCount = count($this->countryCodeList);
          		   			while($x < $sumCount+1) {
          		   			  if(strcmp($this->countryCodeList[$x], $this->userCountryCode) == 0) {
                          echo 'from '.$this->countryNameList[$x];
                          break;
                        }
                        $x++;           
          		   			}
          		   	  ?>
          		   		</div>
                    <?php
                      }
          		   		?>
	          		   		<br>
          		   			<br>
          		   			<br>
          		   			<br>
										
                  </div> <!-- profileFormContainer -->
								</div> <!-- profile Text -->
              </div> <!--  White Box -->            	
           </div> <!--  license Main -->  		   		
  		  </div> <!-- mainContent close //-->
  		</div> <!-- blueBoxMain close //-->
  	</div>

