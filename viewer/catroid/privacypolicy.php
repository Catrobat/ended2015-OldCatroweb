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
              <div class="webMainContentTitle"><?php echo $this->languageHandler->getString('privacy_policy_title')?></div>
                <div class="licenseMain">            	
            	  <div class ="whiteBoxMain">
            	    <div class="licenseText">
            	      <p class="licenseText">            	          
						<?php echo $this->languageHandler->getString('privacy_policy_intro')?>
						<br><br>
						<span class="licenseHeader"><?php echo $this->languageHandler->getString('privacy_policy_using_software_head')?></span>
						<br><br>
						<?php echo $this->languageHandler->getString('privacy_policy_using_software')?>
						<br><br>
						<span class="licenseHeader"><?php echo $this->languageHandler->getString('privacy_policy_using_website_head')?></span>
						<br><br>
						<?php echo $this->languageHandler->getString('privacy_policy_using_website')?>
						<br><br>
						
						<!-- CURRENTLY NOT AVAILABLE 
						But if you want to upload your own projects to the website, or add comments or tags to projects, you need to register for an account on the website.<br>
						
						<br>
						When you register for an account on the website, we ask for some information. The only required information is your username, password, gender, country and your month and year of birth.<br>
						<br>
						We also ask for your city and state or province, but this information is optional. We do <b>not</b> ask for your name, phone number, or home address.<br>
						<br>
						If you are 13 or over, we ask for your email address so that you can change your password (see below). If you are under 13, we ask for your parent's email address.<br>
						<br>
						 -->
						<!-- CURRENTLY NOT AVAILABLE We do not make any of your profile information public on the website.<br><!-- , except your username and country.<br> -->
						<!-- CURRENTLY NOT AVAILABLE <br>
						We put "cookies" on your computer to keep track of when you are logged onto the Catroid website. (A cookie is a small data file that indicates that you have been to a particular website.)<br>
						<br> -->

						<!-- CURRENTLY NOT AVAILABLE
						<font class ="licenseHeader">Changing Your Password or Deleting Your Account</font><br><br>
						You can change your password or delete your account at any time. To change your password, enter your account name or email address on the password recovery page. 
						<br><br>If you want to delete your (or your child's) account, please email <a href="mailto:webmaster@catroid.org?subject=Account Deletion">webmaster@catroid.org</a> and let us know 1) the username, 2) the email address used on the account, and 3) the date of birth used on the account.<br> 
						<br>
						-->

						<span class="licenseHeader"><?php echo $this->languageHandler->getString('privacy_policy_contact_head')?></span>
						<br><br>
						<?php echo $this->languageHandler->getString('privacy_policy_contact', 
						  '<a class="license" href="'.BASE_PATH.'catroid/contactus">'.$this->languageHandler->getString('privacy_policy_contact_link').'</a>')?>
					  </p>
                   </div> <!-- License Text -->
                   </div> <!--  White Box -->            	
            	</div> <!--  license Main -->
  		   		
  		  </div> <!-- mainContent close //-->
  		</div> <!-- blueBoxMain close //-->
  	</div>
