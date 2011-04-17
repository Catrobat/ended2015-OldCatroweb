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
  	<div class="webMainMiddle">
  		<div class="blueBoxMain">
  		   	<div class="webMainContent">
              <div class="webMainContentTitle">Create a new account</div>
                <div class="registrationMain">            	
            	  <div class ="whiteBoxMain">
            	    <div class="registrationText">
            	    <div class="registrationFormContainer">
            		   	<?php if($this->answer) {
          		   	    echo '<div class="errorMsg">';
           		   	    echo $this->answer;
              		   	echo '</div>';
            		   	}?>
                  	<form method="post" action="./registration" name="registrationForm">
      	  		   			<div class="registrationH2">Please choose your nickname. </div>
          		   			Nickname*<br>
          		   			<input type="text" name="registrationUsername" value="<?php echo htmlspecialchars($this->postData['registrationUsername'])?>" required="required" ><br>
          		   			Password*<br>
          		   			<input type="password" name="registrationPassword" value="<?php if($this->passOk) { echo htmlspecialchars($this->postData['registrationPassword']); }?>" required="required" ><br>
          		   			Email*<br>
          		   			<input type="email" name="registrationEmail" value="<?php echo htmlspecialchars($this->postData['registrationEmail'])?>" required="required" ><br>
          		   			Country*<br>
          		   			<select name="registrationCountry" class="registration" id="registrationCountry" required="required" ><?php print_r ($this->countrylist) ?></select><br>
          		   			Birthday<br>
          		   			<select name="registrationMonth" id="registrationMonth" class="registration" required="required" ><?php print_r ($this->month) ?></select> <select name="registrationYear" class="registration" id="registrationYear" required="required"><?php print_r ($this->year) ?></select><br>
          		   			Gender<br>
          		   			<select name="registrationGender" class="registration" required="required" ><?php print_r ($this->gender) ?></select><br>
          		   			City<br>
          		   			<input type="text" name="registrationCity" value="<?php echo htmlspecialchars($this->postData['registrationCity'])?>"><br>          		   			
                      <input type="submit" name="registrationSubmit" value="Create my account now" class="button orange compact registrationSubmitButton">
            		   	</form>
                  </div> <!-- registrationFormContainer -->
								</div> <!-- Registration Text -->
              </div> <!--  White Box -->            	
           </div> <!--  license Main -->  		   		
  		  </div> <!-- mainContent close //-->
  		</div> <!-- blueBoxMain close //-->
  	</div>

