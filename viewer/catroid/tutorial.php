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
  <div class="header"><?php echo $this->languageHandler->getString('title')?></div>
  <div class="tutorialMain">
    <div class="tutorialMainHeader">
      <?php echo $this->languageHandler->getString('head_text')?>
    </div>
     <div class="tutorialMainContent">
      <div class="tutorialMainContentDetail">
        <a class="tutorialLinkStyle" href="/tutorialCard?id=1">
          <img class="thumbs" src="<?php echo BASE_PATH; ?>images/tutorial/1.png" title="<?php echo $this->languageHandler->getString('project1')?>" />
          <div class="tutorialTitle"><?php echo $this->languageHandler->getString('project1_short')?></div>
        </a>
      </div>
      <div class="tutorialMainContentDetail">
        <a href="/tutorialCard?id=2">
          <img class="thumbs" src="<?php echo BASE_PATH; ?>images/tutorial/2.png" title="<?php echo $this->languageHandler->getString('project2')?>" />
          <div class="tutorialTitle"><?php echo $this->languageHandler->getString('project2_short')?></div>
        </a>
      </div>
      <div class="tutorialMainContentDetail">
        <a href="/tutorialCard?id=3">
          <img class="thumbs" src="<?php echo BASE_PATH; ?>images/tutorial/3.png" title="<?php echo $this->languageHandler->getString('project3')?>" />
          <div class="tutorialTitle"><?php echo $this->languageHandler->getString('project3_short')?></div>
        </a>
      </div>
      <div class="tutorialMainContentDetail">
        <a href="/tutorialCard?id=4">
          <img class="thumbs" src="<?php echo BASE_PATH; ?>images/tutorial/4.png" title="<?php echo $this->languageHandler->getString('project4')?>"/>
          <div class="tutorialTitle"><?php echo $this->languageHandler->getString('project4_short')?></div>
        </a>
      </div>
      <div class="tutorialMainContentDetail">
        <a href="/tutorialCard?id=5">
          <img class="thumbs" src="<?php echo BASE_PATH; ?>images/tutorial/5.png" title="<?php echo $this->languageHandler->getString('project5')?>" /><br />
          <div class="tutorialTitle"><?php echo $this->languageHandler->getString('project5_short')?></div>
        </a>
      </div>
      <div class="tutorialMainContentDetail">
        <a href="/tutorialCard?id=6">
          <img class="thumbs" src="<?php echo BASE_PATH; ?>images/tutorial/6.png" title="<?php echo $this->languageHandler->getString('project6')?>" />
          <div class="tutorialTitle"><?php echo $this->languageHandler->getString('project6_short')?></div>
        </a>
      </div>
      <div class="tutorialMainContentDetail">
        <a href="/tutorialCard?id=7">
          <img class="thumbs" src="<?php echo BASE_PATH; ?>images/tutorial/7.png" title="<?php echo $this->languageHandler->getString('project7')?>" />
          <div class="tutorialTitle"><?php echo $this->languageHandler->getString('project7_short')?></div>
        </a>
      </div>
      <div class="tutorialMainContentDetail">
        <a href="/tutorialCard?id=8">
          <img class="thumbs" src="<?php echo BASE_PATH; ?>images/tutorial/8.png" title="<?php echo $this->languageHandler->getString('project8')?>" />
          <div class="tutorialTitle"><?php echo $this->languageHandler->getString('project8_short')?></div>
        </a>
      </div>
      <div class="tutorialMainContentDetail">
        <a href="/tutorialCard?id=9">
          <img class="thumbs" src="<?php echo BASE_PATH; ?>images/tutorial/9.png" title="<?php echo $this->languageHandler->getString('project9')?>" />
          <div class="tutorialTitle"><?php echo $this->languageHandler->getString('project9_short')?></div>
        </a>
      </div>
      <div class="tutorialMainContentDetail">
        <a href="/tutorialCard?id=10">
          <img class="thumbs" src="<?php echo BASE_PATH; ?>images/tutorial/10.png" title="<?php echo $this->languageHandler->getString('project10')?>" />
          <div class="tutorialTitle"><?php echo $this->languageHandler->getString('project10_short')?>
        </a>
      </div>
    </div> 
    <!-- <div class="tutorialMainContent1">
      <ul style="height: 643px;">
        <li style="visibility: visible;">
          <img class="thumbs" src="<?php echo BASE_PATH; ?>images/tutorial/1.png" title="<?php echo $this->languageHandler->getString('project1')?>"/>
        </li>
        <li style="visibility: visible;">
          <img class="thumbs" src="<?php echo BASE_PATH; ?>images/tutorial/2.png" title="<?php echo $this->languageHandler->getString('project2')?>"/>
        </li>
        <li style="visibility: visible;">
          <img class="thumbs" src="<?php echo BASE_PATH; ?>images/tutorial/2.png" title="<?php echo $this->languageHandler->getString('project2')?>"/>
        </li>
        <li style="visibility: visible;">
          <img class="thumbs" src="<?php echo BASE_PATH; ?>images/tutorial/2.png" title="<?php echo $this->languageHandler->getString('project2')?>"/>
        </li>
        <li style="visibility: visible;">
          <img class="thumbs" src="<?php echo BASE_PATH; ?>images/tutorial/2.png" title="<?php echo $this->languageHandler->getString('project2')?>"/>
        </li>
        <li style="visibility: visible;">
          <img class="thumbs" src="<?php echo BASE_PATH; ?>images/tutorial/2.png" title="<?php echo $this->languageHandler->getString('project2')?>"/>
        </li>
        <li style="visibility: visible;">
          <img class="thumbs" src="<?php echo BASE_PATH; ?>images/tutorial/2.png" title="<?php echo $this->languageHandler->getString('project2')?>"/>
        </li>
        <li style="visibility: visible;">
          <img class="thumbs" src="<?php echo BASE_PATH; ?>images/tutorial/2.png" title="<?php echo $this->languageHandler->getString('project2')?>"/>
        </li>
        <li style="visibility: visible;">
          <img class="thumbs" src="<?php echo BASE_PATH; ?>images/tutorial/2.png" title="<?php echo $this->languageHandler->getString('project2')?>"/>
        </li>
        <li style="visibility: visible;">
          <img class="thumbs" src="<?php echo BASE_PATH; ?>images/tutorial/2.png" title="<?php echo $this->languageHandler->getString('project2')?>"/>
        </li>
      </ul>
    
    </div>-->
  </div>  <!--  license Main -->
  <div class="projectSpacer"></div>
</article>