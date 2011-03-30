<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team 
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or License, or (at your option) any later version.
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
  		   		<div class="webMainContentTitle">
  		   			Login
  		   		</div>
  		   		<form method="post" action="./login">
  		   			<?php if($this->module->session->userLogin_userId <= 0) {?>
  		   				Nickname: <input type="text" name="loginUsername"><br>
  		   				Password: <input type="password" name="loginPassword"><br>
  		   				<!-- <input type="hidden" name="loginUserId" value="2"> -->
  		   				<input type="submit" name="loginSubmit" value="Login"><br>
  		   			<?php } else {?>
  		   				Hello <?php echo $this->module->session->userLogin_userNickname?>!<br>
  		   				You are logged in with ID <?php echo $this->module->session->userLogin_userId?><br>
  		   				<input type="submit" name="logoutSubmit" value="Logout">
  		   			<?php }?>
  		   		</form>
  		   		<br>
  		   		<?php if($this->answer) {
  		   		  echo $this->answer;  		   		  
  		   		}?>
  		   		<br><br>
  		   		<a id="aBoardLink" target="_blank" href="<?php echo BASE_PATH?>addons/board/">Board</a>
  		   		<br>
  		   		<?php if($this->module->session->userLogin_userId > 0) {?>
  		   			<a id="aWikiLink" target="_blank" href="<?php echo BASE_PATH?>wiki/?action=purge">Wiki</a>
  		   		<?php } else {?>
  		   			<a id="aWikiLink" target="_blank" href="<?php echo BASE_PATH?>wiki/">Wiki</a>
  		   		<?php }?>
  		  	</div>
  		</div>
  	</div>