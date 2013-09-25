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
    <?php echo $this->languageHandler->getString('title'); ?>
  </div>
  <div class="starterProjectsMain">
    <div class="starterProjectsMainHeader">
      <?php echo $this->languageHandler->getString('head_text1'); ?>&nbsp;
    </div>
    <div class="starterProjectsMainContent">
      <div class="starterProjectsGroup">
        <div class="starterProjectsGroupHeadline"><?php echo $this->languageHandler->getString('group1'); ?></div>
        <div class="border"></div>
        <?php
        for($i=0;isset($this->projectsGrouped["group1"][$i]);$i++) { 
        ?>
        <div class="starterProjectsMainContentDetail">
          <a class="starterProjectsLinkStyle" href="/details/<?php echo $this->projectsGrouped["group1"][$i]->id; ?>">
            <img class="starterProjectsThumbs" src="<?php echo $this->projectsGrouped["group1"][$i]->thumb; ?>" title="<?php echo $this->projectsGrouped["group1"][$i]->title; ?>" />
            <div class="starterProjectsTitle"><?php echo $this->projectsGrouped["group1"][$i]->title; ?></div>
            <div class="starterProjectsAdditional">
            <div class="starterProjectsTime"></div>
              <?php echo $this->projectsGrouped["group1"][$i]->download_count; ?>
            </div>
          </a>
        </div>
        <?php } ?>
      </div>
      <div class="starterProjectsGroup">
        <div class="starterProjectsGroupHeadline"><?php echo $this->languageHandler->getString('group2'); ?></div>
        <div class="border"></div>
        <?php
        for($i=0;isset($this->projectsGrouped["group2"][$i]);$i++) { 
        ?>
        <div class="starterProjectsMainContentDetail">
          <a class="starterProjectsLinkStyle" href="/details/<?php echo $this->projectsGrouped["group2"][$i]->id; ?>">
            <img class="starterProjectsThumbs" src="<?php echo $this->projectsGrouped["group2"][$i]->thumb; ?>" title="<?php echo $this->projectsGrouped["group2"][$i]->title; ?>" />
            <div class="starterProjectsTitle"><?php echo $this->projectsGrouped["group2"][$i]->title; ?></div>
            <div class="starterProjectsAdditional">
            <div class="starterProjectsTime"></div>
              <?php echo $this->projectsGrouped["group2"][$i]->download_count; ?>
            </div>
          </a>
        </div>
        <?php } ?>
      </div>
      <div class="starterProjectsGroup">
        <div class="starterProjectsGroupHeadline"><?php echo $this->languageHandler->getString('group3'); ?></div>
        <div class="border"></div>
        <?php
        for($i=0;isset($this->projectsGrouped["group3"][$i]);$i++) { 
        ?>
        <div class="starterProjectsMainContentDetail">
          <a class="starterProjectsLinkStyle" href="/details/<?php echo $this->projectsGrouped["group3"][$i]->id; ?>">
            <img class="starterProjectsThumbs" src="<?php echo $this->projectsGrouped["group3"][$i]->thumb; ?>" title="<?php echo $this->projectsGrouped["group3"][$i]->title; ?>" />
            <div class="starterProjectsTitle"><?php echo $this->projectsGrouped["group3"][$i]->title; ?></div>
            <div class="starterProjectsAdditional">
            <div class="starterProjectsTime"></div>
              <?php echo $this->projectsGrouped["group3"][$i]->download_count; ?>
            </div>
          </a>
        </div>
        <?php } ?>
      </div>
       <div class="starterProjectsGroup">
        <div class="starterProjectsGroupHeadline"><?php echo $this->languageHandler->getString('group4'); ?></div>
        <div class="border"></div>
        <?php
        for($i=0;isset($this->projectsGrouped["group4"][$i]);$i++) { 
        ?>
        <div class="starterProjectsMainContentDetail">
          <a class="starterProjectsLinkStyle" href="/details/<?php echo $this->projectsGrouped["group4"][$i]->id; ?>">
            <img class="starterProjectsThumbs" src="<?php echo $this->projectsGrouped["group4"][$i]->thumb; ?>" title="<?php echo $this->projectsGrouped["group4"][$i]->title; ?>" />
            <div class="starterProjectsTitle"><?php echo $this->projectsGrouped["group4"][$i]->title; ?></div>
            <div class="starterProjectsAdditional">
            <div class="starterProjectsTime"></div>
              <?php echo $this->projectsGrouped["group4"][$i]->download_count; ?>
            </div>
          </a>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>  <!--  license Main -->
  <div class="projectSpacer"></div>
</article>