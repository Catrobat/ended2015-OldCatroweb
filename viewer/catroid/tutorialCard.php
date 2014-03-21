<?php
/*
 * Catroid: An on-device visual programming system for Android devices
 * Copyright (C) 2010-2014 The Catrobat Team
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
<!--  <?php echo $this->languageHandler->getString('project1_description'); ?>-->
<!--   <div id="hiddxen"> -->
    <?php 
    
//       for($i=1;$i<12;$i++) {
//         echo $this->languageHandler->getString('project'.$i);
//         echo $this->languageHandler->getString('project'.$i.'_extratip_'.$i);
//         echo $this->languageHandler->getString('project'.$i.'_description');
//       }
//     ?>
<!--   </div> -->
  <div class="header"><?php echo $this->languageHandler->getString('project'.intval($_GET['id']).''); ?></div>
  <div class="tutorialCardMain">
   <div class="tutorialCardMainContentDescription"><?php echo $this->languageHandler->getString('project'.intval($_GET['id']).'_description'); ?></div>
    <div class="tutorialCardMainContent">
      <img class="tutorialCardImage" src="<?php echo BASE_PATH;?>images/tutorial/tutorialcards/<?php echo intval($_GET['id']); ?>_1.png" />
    </div>
    <div class="tutorialCardMainContentTitle">
      <?php echo $this->languageHandler->getString('headline1')?>
    </div>
    <div class="tutorialCardMainContent">
      <img class="tutorialCardImage" src="<?php echo BASE_PATH;?>images/tutorial/tutorialcards/<?php echo intval($_GET['id']); ?>_2_1.png" />
       <?php     
      
      if(@fopen(BASE_PATH.'images/tutorial/tutorialcards/'.intval($_GET['id']).'_2_2.png','r')) 
      { ?>
        <div class="tutorialCardMainSpace"></div>
        <img class="tutorialCardImage" src="<?php echo BASE_PATH;?>images/tutorial/tutorialcards/<?php echo intval($_GET['id']); ?>_2_2.png" />
<?php }?>
    </div>
    <div class="tutorialCardMainContentTitle">
      <?php echo $this->languageHandler->getString('headline2')?>
    </div>
    <div class="tutorialCardMainContent">
      <img class="tutorialCardImage" src="<?php echo BASE_PATH;?>images/tutorial/tutorialcards/<?php echo intval($_GET['id']); ?>_3.png" />
    </div>
    <div class="tutorialCardMainContentTitle">
      <?php echo $this->languageHandler->getString('headline3')?>
    </div>
    <div class="tutorialCardMainContent">
      <img class="tutorialCardImage" src="<?php echo BASE_PATH;?>images/tutorial/tutorialcards/do_it.png" />
    </div>    
    <?php     
      
      for($i=1;@fopen(BASE_PATH.'images/tutorial/tutorialcards/'.intval($_GET['id']).'_4_'.$i.'.png','r');$i++) 
      { 
        if($i == 1) 
        { ?>
        <div class="tutorialCardMainContentTitle">
          <?php echo $this->languageHandler->getString('headline4')?>
        </div>
        <?php 
        }?>
        <div class="tutorialCardMainContent">
        <div class="tutorialCardMainContentDescription"><?php echo $this->languageHandler->getString('project'.intval($_GET['id']).'_extratip_'.$i); ?></div>
          <img class="tutorialCardImage" src="<?php echo BASE_PATH;?>images/tutorial/tutorialcards/<?php echo intval($_GET['id']); ?>_4_<?php echo $i;?>.png" />
        </div>
        <div class="tutorialCardMainSpace"></div>
<?php } ?>
  </div>  <!--  license Main -->
  <div class="projectSpacer"></div>
</article>