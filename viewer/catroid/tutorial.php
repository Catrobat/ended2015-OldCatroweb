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
   
    <div class="tutorialTopContainer" onclick="self.location.href='/stepByStep'">
      <div class="tutorialHeader1">
        Step By Step
        <img src="images/symbols/step_by_step.png" />
      </div>
      <div style="clear: both;"></div>
      <div class="tutorialText">
      Do you want to program in Pocket Code?<br />
      Do this in 10 steps. <img src="images/symbols/arrow_right.png" />
      </div>
    </div> 
    <div class="clear"></div>
    <div class="tutorialCardsContainer" onclick="self.location.href='/tutorialCards'">
      <div class="tutorialHeader2">
        Tutorial Cards
        <img src="images/symbols/cards.png" />
      </div>
      <div class="tutorialText">
        This Cards show you effective Tricks
        in Pocket Code. 
        <img src="images/symbols/arrow_right.png" />
      </div>
    </div>
    <div class="break"></div>
    <div class="tutorialStarterProgamsContainer" onclick="self.location.href='/starterPrograms'">
      <div class="tutorialHeader3">
        Starter Programs
        <img src="images/symbols/starter.png" />
      </div>
      <div class="tutorialText1">
      Try out these programs and
      remix them. <img src="images/symbols/arrow_right.png" />
      </div>
    </div> 
    <div class="clear"></div>
    </div> 
  </div> <!--  license Main -->
  <div class="projectSpacer"></div>
</article>