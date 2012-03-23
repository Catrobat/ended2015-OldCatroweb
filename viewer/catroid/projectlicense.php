<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2012 The Catroid Team 
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
              <div class="webMainContentTitle"><?php echo $this->languageHandler->getString('project_licence_title')?></div>
                <div class="licenseMain">            	
            	  <div class ="whiteBoxMain">
            	    <div class="licenseText">
            	      <p class="licenseText">
						<?php echo $this->languageHandler->getString('project_licence_part1')?>
						<br /><br />	
                        <?php echo $this->languageHandler->getString('project_licence_part2')?>
                        <br /><br />
                        <?php echo $this->languageHandler->getString('project_licence_part3')?>
                      </p>
                      <ul>
                        <li><?php echo $this->languageHandler->getString('project_licence_part3_list_element1')?></li>
                        <li><?php echo $this->languageHandler->getString('project_licence_part3_list_element2')?></li>
                      </ul>
                      <p class="licenseText">
                      	<?php echo $this->languageHandler->getString('project_licence_part4')?>
                        <br /><br />
                        <?php echo $this->languageHandler->getString('project_licence_part5')?>
                        <br /><br />
                        <?php echo $this->languageHandler->getString('project_licence_learn_more_new','<a class="license" href="'.BASE_PATH.'catroid/termsofuse">'.BASE_PATH.'catroid/termsofuse</a>')?>
                        <br /><br />
                        <?php echo $this->languageHandler->getString('project_licence_team')?>
                        <br /><br />
                       </p>
                       <p class="licenseText">
                        <?php echo $this->languageHandler->getString('project_licence_dated')?><br />
                        <?php echo $this->languageHandler->getString('project_licence_copy', '<a href="'.BASE_PATH.'">&lt;'.BASE_PATH.'&gt;</a>')?>
                      </p>
                   </div> <!-- License Text -->
                 </div> <!--  White Box -->            	
              </div> <!--  license Main -->
  		  </div> <!-- mainContent close //-->
  		</div> <!-- blueBoxMain close //-->
  	</div>