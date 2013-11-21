<?php
/*
 * Catroid: An on-device visual programming system for Android devices
 * Copyright (C) 2010-2013 The Catrobat Team
 * (<http://developer.catrobat.org/credits>)
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * An additional term exception under section 7 of the GNU Affero
 * General Public License, version 3, is available at
 * http://developer.catrobat.org/license_additional_term
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
?>

<article>
  <div class="header">
    <div class="headerLarge">
      <?php echo $this->languageHandler->getString('title'); ?>
    </div>  
     <div class="headerSmall">
    &nbsp;<?php echo $this->languageHandler->getString('head_text'); ?>
    </div>  
  </div> 
  <div class="clear"></div>
  <div class="tutorialMain">
    <div class="tutorialStepByStep1" onclick="self.location.href='/stepByStep'"><img src="images/tutorial/1_stepbystep.png" /></div>
    <div class="tutorialStepByStep2"  onclick="self.location.href='/stepByStep'"><img src="images/tutorial/2_stepbystep.png" /></div>
    <div class="tutorialBottomContainer">
      <div class="tutorialTutorials"  onclick="self.location.href='/tutorials'"><img src="images/tutorial/tutorials.png" /></div>
      <div class="break"></div>
      <div class="spacer"></div>
      <div class="tutorialStarters"  onclick="self.location.href='/starterPrograms'"><img src="images/tutorial/1_starters.png" /></div>
      <div class="tutorialStartersThumb"  onclick="self.location.href='/starterPrograms'"><img src="images/tutorial/1_starters.png" /></div>
    </div>
    <div class="clear"></div>
     <div class="tutorialDiscuss1" onclick="window.open('https://groups.google.com/forum/m/?fromgroups#!forum/pocketcode', '_blank')"><img src="images/tutorial/1_discuss.png" /></div>
     <div class="tutorialDiscuss2" onclick="window.open('https://groups.google.com/forum/m/?fromgroups#!forum/pocketcode', '_blank')"><img src="images/tutorial/2_discuss.png" /></div>    
  </div> <!--  license Main -->
  <div class="projectSpacer"></div>
</article>