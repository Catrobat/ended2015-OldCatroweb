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
  <div class="header">
    <div class="headerLarge">
      <?php echo $this->languageHandler->getString('title'); ?>
    </div>     
  </div> 
  <div class="clear"></div>
  <div class="hourOfCodeMain">
    <?php for($i=1;$i<21;$i++) {?>
    <div id="content<?php echo $i;?>" class="hourOfCodeMainContent<?php echo  $i!=1?" hide":" "?>">
        <div class="detailHeaderSide" onclick="prev(<?php echo $i; ?>);"><?php echo $this->languageHandler->getString('prev'); ?></div>
        <div class="detailHeaderCenter"><?php echo $this->languageHandler->getString('title'.$i); ?></div>
        <div class="detailHeaderSide" onclick="next(<?php echo $i; ?>);"><?php echo $this->languageHandler->getString('next'); ?></div>
        <div class="clear"></div>
        <div class="detailDescription"><?php echo $this->languageHandler->getString('description'.$i); ?></div>
        <div class="detailContainer">
          <div class="detailImage">Image1<br /><?php echo $this->languageHandler->getString('imageText'.$i."_1"); ?></div>
          <div class="detailSpacer"> --> </div>
          <div class="detailImage">Image2<br /><?php echo $this->languageHandler->getString('imageText'.$i."_2"); ?></div>
          <div class="detailSpacer"> --> </div>
          <div class="detailImage">Image3<br /><?php echo $this->languageHandler->getString('imageText'.$i."_3"); ?></div>
        </div>
    </div>
    <?php }?>
  </div> 
</article>