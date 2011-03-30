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
  		   		<div class="webMainContentTitle">
  		   			Registration
  		   		</div>
  		   		<form method="post" action="./registration">
  		   			Nickname:<br>
  		   			<input type="text" name="registrationUsername" value="<?php echo htmlspecialchars($this->postData['registrationUsername'])?>"><br>
  		   			Password:<br>
  		   			<input type="password" name="registrationPassword"><br>
  		   			Repeat password:<br>
  		   			<input type="password" name="registrationPasswordRepeat"><br>
  		   			Email:<br>
  		   			<input type="text" name="registrationEmail" value="<?php echo htmlspecialchars($this->postData['registrationEmail'])?>"><br>
  		   			<input type="submit" name="registrationSubmit" value="Register">
  		   		</form>
  		   		<br>
  		   		<?php if($this->answer) {
  		   		  echo $this->answer;  		   		  
  		   		}?>
  		  	</div>
  		</div>
  	</div>