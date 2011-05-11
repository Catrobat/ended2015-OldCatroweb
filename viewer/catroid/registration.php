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
  		new Registration($("#basePath").attr("value"));
  	});
  </script>
  
  	<div class="webMainMiddle">
  		<div class="blueBoxMain">
  		   	<div class="webMainContent">
              <div class="webMainContentTitle">Create a new account</div>
                <div class="registrationMain">            	
            	  <div class ="whiteBoxMain">
            	    <div class="registrationText">
            	    <div class="registrationFormContainer">
            	    	<div class="registrationFormAnswer" id="registrationFormAnswer">
            	    		<div class="errorMsg" id="errorMsg">
            	    		<!-- error messages here -->
              		   	</div>
            		   	</div>
                  	<form method="post" name="registrationFormDialog" id="registrationFormDialog">
      	  		   			<div class="registrationH2">Please choose your nickname. </div>
          		   			Nickname<br>
          		   			<input type="text" id="registrationUsername" name="registrationUsername" value="<?php echo htmlspecialchars($this->postData['registrationUsername'])?>" required="required" placeholder="enter a nickname" ><br>
          		   			Password<br>
          		   			<input type="text" id="registrationPassword" name="registrationPassword" value="<?php if($this->passOk) { echo htmlspecialchars($this->postData['registrationPassword']); }?>" required="required" placeholder="enter a password" ><br>
          		   			Email<br>
          		   			<input type="email" id="registrationEmail" name="registrationEmail" value="<?php echo htmlspecialchars($this->postData['registrationEmail'])?>" required="required" placeholder="enter your email address" ><br>
          		   			Country<br>
          		   			<select id="registrationCountry" name="registrationCountry" class="registration" id="registrationCountry" required="required" >
          		   			<?php // country 
          		   			$x = 0;
          		   			$sumCount = count($this->countryCodeList);
          		   			while($x < $sumCount+1) {
                        if($x == 0) {
                          echo '<option value="0" selected>select your country</option>';
                        }
                        else {
                          echo "<option value=\"" . $this->countryCodeList[$x] . "\">" . $this->countryNameList[$x] . "</option>\r";
                        }
                        $x++;           
          		   			}
          		   			?>
          		   			</select><br>
          		   			City<br>
          		   			<input type="text" id="registrationCity" name="registrationCity" value="<?php echo htmlspecialchars($this->postData['registrationCity'])?>" placeholder="enter your city"><br>
          		   			Birthday<br>
          		   			
          		   			<select id="registrationMonth" name="registrationMonth" class="registration" >
          		   			<?php // month 
        		   			    $x = 0;
                        while($x < 13) {
                          if($x == 0) {
                            echo '<option value="0" selected>select your month</option>';
                          }
                          else {
                            echo "<option value=\"" . $x . "\">" . $this->months[$x] . "</option>\r";
                          }
                          $x++;
                        }
          		   			?>
          		   			</select> 
          		   			<select id="registrationYear" name="registrationYear" class="registration" >
          		   			<?php // year
                        $x = 0;
                        $year = date('Y') + 1;
                        while($x < 101) {
                          $year--;
                          if($x == 0) {
                            echo '<option value="0" selected>select <our year</option>';
                          }
                          else {
                            echo "<option value=\"" . $year . "\" >" . $year . "</option>\r";
                          }
                          $x++;
                        }          		   			
          		   			?>
          		   			</select><br>
          		   			Gender<br>
											<select id="registrationGender" name="registrationGender" class="registration" >
												<option value="0" selected>select your gender</option>
												<option value="female">female</option>
												<option value="male">male</option>
                      </select>
          		   			<br>
          		   			<br>
                      <input type="button" name="registrationSubmit" id="registrationSubmit" value="Create my account now" class="button orange compact registrationSubmitButton">
                      <br>
                      <br>
                      <br> 
											<div class="passwordRecoveryHelper"><a id="forgotPassword" target="_self" href="<?php echo BASE_PATH?>catroid/login">Login</a> <br>or<br><a id="forgotPassword" target="_self" href="<?php echo BASE_PATH?>catroid/passwordrecovery">click here if you forgot your password?</a></div>
            		   	</form>
                  </div> <!-- registrationFormContainer -->
								</div> <!-- Registration Text -->
              </div> <!--  White Box -->            	
           </div> <!--  license Main -->  		   		
  		  </div> <!-- mainContent close //-->
  		</div> <!-- blueBoxMain close //-->
  	</div>

