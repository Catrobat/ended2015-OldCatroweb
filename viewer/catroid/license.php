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
              <div class="webMainContentTitle"><?php echo $this->languageHandler->getString('license')?></div>
                <div class="licenseMain">            	
            	  <div class ="whiteBoxMain">
            	    <div class="licenseText"><p class="licenseText">
                  <?php echo $this->languageHandler->getString('p1')?>
                  <br><br>
                  <?php echo $this->languageHandler->getString('p2', 
                  																						 '<a href="http://www.gnu.org/licenses/gpl.html" target="_blank" id="gnugpl">http://www.gnu.org/licenses/gpl.html</a>', 
                  																						 '<a href="http://www.gnu.org/licenses/agpl.html" target="_blank" id="gnuagpl">http://www.gnu.org/licenses/agpl.html</a>' 
                                                               )?>
                  <br><br>
                  <?php echo $this->languageHandler->getString('p3', '<a href="http://code.google.com/p/catroid" target="_blank" id="googlecode">Google Code</a>')?>
            	    </p>
                  </div> <!-- License Text -->
                  </div> <!--  White Box -->            	
            	</div> <!--  license Main -->
  		   		
  		  </div> <!-- mainContent close //-->
  		</div> <!-- blueBoxMain close //-->
  	</div>  
