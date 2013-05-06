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
  <header>
    <div>
      <span id="projectDetailsProjectTitle"><?php echo $this->project['title']?></span>
      <div class="projectDetailsAuthorTop">
        <div>
          <img src="<?php echo BASE_PATH; ?>images/symbols/placeholder2.png" />
          <span>
            <?php echo $this->project['uploaded_by_string']; ?>
          </span>
        </div>
      </div>
    </div>
  </header>
  <div id="projectDetailsContainer">
    <div class="projectDetailsThumbnail">
      <a href="<?php echo BASE_PATH?>download/<?php echo $this->project['id']; echo PROJECTS_EXTENSION; ?>?fname=<?php echo urlencode($this->project['title'])?>">
        <img src="<?php echo $this->project['image']?>" alt="<?php $this->project['title']?>" />
      </a>
    </div>
  
    <div class="projectDetailsDescription">
      <div>
        <span class="projectDetailsDescriptionHeading">
          <?php echo $this->languageHandler->getString('description'); ?>
        </span>
      </div>
      <div class="projectDetailsDescriptionText">
        <span>
          <?php echo ($this->project['description'])? $this->project['description'] : $this->languageHandler->getString('no_description_available');?>
        </span>
      </div>
    </div>
  
    <div class="projectDetailsDownload">
      <div class="projectDetailsDownloadButton">
        <span id="projectDetailsDownloadVersion"><?php echo $this->languageHandler->getString('version_info_text') . " " . $this->project['version_name'];?></span>
        <a style="text-decoration: none;" href="<?php echo BASE_PATH?>download/<?php echo $this->project['id']; echo PROJECTS_EXTENSION; ?>?fname=<?php echo urlencode($this->project['title'])?>">
          <div class="green">
            <?php echo $this->languageHandler->getString('download_button');?>
          </div>
        </a>
        <a id="projectDetailsDownloadLicense" href="<?php echo BASE_PATH?>licensetoplay"><?php echo $this->languageHandler->getString('some_rights_reserved');?></a>
      </div>
    </div>
  
    <div class="projectDetailsInformationSeperator">
      <hr/>
    </div>
    <div class="projectDetailsInformation">
      <ul>
        <li>
          <img src="<?php echo BASE_PATH; ?>images/symbols/placeholder2.png" />
          <span><?php echo $this->project['uploaded_by']; ?></span>
        </li>
        <li>
          <img src="<?php echo BASE_PATH; ?>images/symbols/placeholder2.png" />
          <span><?php echo $this->project['publish_time_in_words']; ?></span>
        </li>
        <li>
          <img src="<?php echo BASE_PATH; ?>images/symbols/placeholder2.png" />
          <span><?php echo $this->project['fileSize'] . " MB " . $this->languageHandler->getString('filesize'); ?></span>
        </li>
        <li>
          <img src="<?php echo BASE_PATH; ?>images/symbols/placeholder2.png" />
          <span><?php echo $this->project['download_count'] . " " . $this->languageHandler->getString('downloads'); ?></span>
        </li>
        <li>
          <img src="<?php echo BASE_PATH; ?>images/symbols/placeholder2.png" />
          <span><?php echo $this->project['view_count'] . " " . $this->languageHandler->getString('views'); ?></span>
        </li>
      </ul>
    </div>
  </div>
</article>
