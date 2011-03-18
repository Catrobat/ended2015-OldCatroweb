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
              <div class="webMainContentTitle">Registration</div>
                <div class="registrationMain">            	
            	  <div class ="whiteBoxMain">
            	    <div class="registrationText">
            	    <div class="registrationFormContainer">
                  	<form method="post" action="./registration" name="registrationForm">
          		   			Nickname*<br>
          		   			<input type="text" name="registrationUsername" value="<?php echo htmlspecialchars($this->postData['registrationUsername'])?>" ><br>
          		   			<div class="registrationInfoText">Your nick name may only contain letters A-Z (a-z), numbers from 0-9, underscores or spaces and must be between 4 to 32 characters.</div>
          		   			Password*<br>
          		   			<input type="password" name="registrationPassword" ><br>
          		   			Repeat password*<br>
          		   			<input type="password" name="registrationPasswordRepeat" ><br>
          		   			Email*<br>
          		   			<input type="email" name="registrationEmail" value="<?php echo htmlspecialchars($this->postData['registrationEmail'])?>" ><br>
          		   			Birthday*<br>
          		   			<select name="registrationMonth" id="registrationMonth" ><?php print_r ($this->month) ?></select> <select name="registrationYear" id="registrationYear" ><?php print_r ($this->year) ?></select><br>
          		   			Gender*<br>
          		   			<select name="registrationGender" ><?php print_r ($this->gender) ?></select><br>
          		   			Country*<br>
          		   			<select name="registrationCountry" id="registrationCountry" ><?php print_r ($this->countrylist) ?></select><br>
          		   			Province<br>
          		   			<input type="text" name="registrationProvince" value="<?php echo htmlspecialchars($this->postData['registrationProvince'])?>"><br>
          		   			City<br>
          		   			<input type="text" name="registrationCity" value="<?php echo htmlspecialchars($this->postData['registrationCity'])?>"><br>
          		   			<input class="registrationSubmitButton" type="submit" name="registrationSubmit" value="Register">
            		   	</form>
            		   	<div class="registrationErrorMsg">
            		   	  <?php if($this->answer) {
            		   	    echo $this->answer;
            		   	  }?>
            		   	</div>
                  </div> <!-- registrationFormContainer -->
				</div> <!-- Registration Text -->
              </div> <!--  White Box -->            	
           </div> <!--  license Main -->  		   		
  		  </div> <!-- mainContent close //-->
  		</div> <!-- blueBoxMain close //-->
  	</div>

