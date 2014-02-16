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
    <?php echo $this->languageHandler->getString('title'); ?>
  </div>
  <div class="tutorialCardsMain">
    <div class="tutorialCardsMainHeader">
      <div class="tutorialCardsHeadText1"><?php echo $this->languageHandler->getString('head_text1'); ?>&nbsp;</div>
      <div class="tutorialCardsHeadText2"><?php echo $this->languageHandler->getString('head_text2'); ?></div>
    </div>
    <div id="clear"></div>
    <div class="tutorialCardsMainContent">
      <div class="tutorialLeftContainer">
        <div class="tutorialCardsMainContentDetail">
          <a class="tutorialCardsLinkStyle" href="/tutorialCard?id=1">
            <img class="tutorialCardsThumbs" src="<?php echo BASE_PATH; ?>images/tutorial/1.png" title="<?php echo $this->languageHandler->getString('project1'); ?>" />
            <div class="tutorialCardsTitle"><?php echo $this->languageHandler->getString('project1'); ?></div>
          </a>
        </div>
        <div id="clear"></div>
        <div class="tutorialCardsMainContentDetail">
          <a href="/tutorialCard?id=2">
            <img class="tutorialCardsThumbs" src="<?php echo BASE_PATH; ?>images/tutorial/2.png" title="<?php echo $this->languageHandler->getString('project2'); ?>" />
            <div class="tutorialCardsTitle"><?php echo $this->languageHandler->getString('project2'); ?></div>
          </a>
        </div>
        <div class="tutorialCardsMainContentDetail">
          <a href="/tutorialCard?id=3">
            <img class="tutorialCardsThumbs" src="<?php echo BASE_PATH; ?>images/tutorial/3.png" title="<?php echo $this->languageHandler->getString('project3'); ?>" />
            <div class="tutorialCardsTitle"><?php echo $this->languageHandler->getString('project3'); ?></div>
          </a>
        </div>
        <div class="tutorialCardsMainContentDetail">
          <a href="/tutorialCard?id=4">
            <img class="tutorialCardsThumbs" src="<?php echo BASE_PATH; ?>images/tutorial/4.png" title="<?php echo $this->languageHandler->getString('project4'); ?>"/>
            <div class="tutorialCardsTitle"><?php echo $this->languageHandler->getString('project4'); ?></div>
          </a>
        </div>
        <div class="tutorialCardsMainContentDetail">
          <a href="/tutorialCard?id=5">
            <img class="tutorialCardsThumbs" src="<?php echo BASE_PATH; ?>images/tutorial/5.png" title="<?php echo $this->languageHandler->getString('project5'); ?>" />
            <div class="tutorialCardsTitle"><?php echo $this->languageHandler->getString('project5'); ?></div>
          </a>
        </div>
      </div>
      <div class="tutorialRightContainer">
        <div class="tutorialCardsMainContentDetail">
          <a href="/tutorialCard?id=6">
            <img class="tutorialCardsThumbs" src="<?php echo BASE_PATH; ?>images/tutorial/6.png" title="<?php echo $this->languageHandler->getString('project6'); ?>" />
            <div class="tutorialCardsTitle"><?php echo $this->languageHandler->getString('project6'); ?></div>
          </a>
        </div>
        <div class="tutorialCardsMainContentDetail">
          <a href="/tutorialCard?id=7">
            <img class="tutorialCardsThumbs" src="<?php echo BASE_PATH; ?>images/tutorial/7.png" title="<?php echo $this->languageHandler->getString('project7'); ?>" />
            <div class="tutorialCardsTitle"><?php echo $this->languageHandler->getString('project7'); ?></div>
          </a>
        </div>
        <div class="tutorialCardsMainContentDetail">
          <a href="/tutorialCard?id=8">
            <img class="tutorialCardsThumbs" src="<?php echo BASE_PATH; ?>images/tutorial/8.png" title="<?php echo $this->languageHandler->getString('project8'); ?>" />
            <div class="tutorialCardsTitle"><?php echo $this->languageHandler->getString('project8'); ?></div>
          </a>
        </div>
        <div class="tutorialCardsMainContentDetail">
          <a href="/tutorialCard?id=9">
            <img class="tutorialCardsThumbs" src="<?php echo BASE_PATH; ?>images/tutorial/9.png" title="<?php echo $this->languageHandler->getString('project9'); ?>" />
            <div class="tutorialCardsTitle"><?php echo $this->languageHandler->getString('project9'); ?></div>
          </a>
        </div>
      </div>
    </div>
  </div>  <!--  license Main -->
  <div class="projectSpacer"></div>
</article>