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
  </div> 
  <div class="clear"></div>
  <div class="stepByStepMain">
    <div class="stepByStepMainContent1">
      1
    </div>
    <div class="stepByStepMainContent2">
      2
    </div>
    <div class="stepByStepMainContent3">
      3
    </div>
    <div class="stepByStepMainContent4">
      4
    </div>
    <div class="stepByStepMainContent5">
      5
    </div>
    <div class="stepByStepMainContent6">
      6
    </div>
    <div class="stepByStepMainContent7">
      7
    </div>
    <div class="stepByStepMainContent8">
      8
    </div>
    <div class="stepByStepMainContent9">
      9
    </div>
    <div class="stepByStepMainContent10">
      10
    </div>
    <div class="stepByStepNavigation">
      <a href="#" onclick="decrementContainer()"> < </a>
      <?php for($i=1;$i<=10;$i++) {?>
        <a class="navigation<?php echo $i; ?>" href="#" onclick="changeContainer(<?php echo $i; ?>);"><?php echo $i; ?></a>
      <?php }?>
      <a href="#" onclick="incrementContainer()"> > </a>
    </div>
  </div> 
      <!--  license Main -->
  <div class="projectSpacer"></div>
</article>