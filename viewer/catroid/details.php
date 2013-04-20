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
        <header><?php echo $this->project['title']?></header>
        <div>
          <div style="float:left; margin: 0 20px 20px 20px;">
            <a href="<?php echo BASE_PATH?>catroid/download/<?php echo $this->project['id']; echo PROJECTS_EXTENSION; ?>?fname=<?php echo urlencode($this->project['title'])?>">
              <img src="<?php echo $this->project['image']?>" alt="project thumbnail" style="width: 260px; height: 260px; border: 10px solid #ffffff;" />
            </a>
          </div>
          <div style="float:left;  margin: 0 20px 20px 20px;">
            <img src="<?php echo BASE_PATH; ?>images/symbols/placeholder2.png" style="width: 32px; height: 32px; margin: 10px 10px 10px 0; " /><?php echo $this->project['uploaded_by_string']; ?><br>
            <img src="<?php echo BASE_PATH; ?>images/symbols/placeholder2.png" style="width: 32px; height: 32px; margin: 10px 10px 10px 0; " /><?php echo $this->project['publish_time_in_words']; ?><br>
            <img src="<?php echo BASE_PATH; ?>images/symbols/placeholder2.png" style="width: 32px; height: 32px; margin: 10px 10px 10px 0; " /><?php echo $this->project['download_count'] . " " . $this->languageHandler->getString('downloads'); ?> <br>
            <img src="<?php echo BASE_PATH; ?>images/symbols/placeholder2.png" style="width: 32px; height: 32px; margin: 10px 10px 10px 0; " /><?php echo $this->project['view_count'] . " " . $this->languageHandler->getString('views'); ?><br>
            <img src="<?php echo BASE_PATH; ?>images/symbols/placeholder2.png" style="width: 32px; height: 32px; margin: 10px 10px 10px 0; " /><?php echo $this->project['fileSize'] . " MB " . $this->languageHandler->getString('filesize'); ?><br>
          </div>
          <div style="float:left;  margin: 0 20px 20px 20px; text-align: right;">
            <?php echo $this->languageHandler->getString('version_info_text') . " " . $this->project['version_name'];?> <br />
            <div style="text-transform: uppercase;font-size: 1.8em; text-decoration: none; text-shadow: #333333 1px 1px 1px; background-color: #7cb5ca; padding: 0.4em 2.13em 0.4em 2.13em; margin: 0.2em 0 0.2em 0; border-radius: 4px;
              background-image: linear-gradient(top, #93c4d7, #74b0c7);
  background-image: -moz-linear-gradient(top, #93c4d7, #74b0c7);
  background-image: -webkit-linear-gradient(top, #93c4d7, #74b0c7);">
              <a style=" color:#ffffff; text-decoration: none; text-shadow: #333333 1px 1px 1px;"
               href="<?php echo BASE_PATH?>catroid/download/<?php echo $this->project['id']; echo PROJECTS_EXTENSION; ?>?fname=<?php echo urlencode($this->project['title'])?>">Download</a><br />
            </div>
            <a href="<?php echo BASE_PATH?>catroid/licensetoplay"><?php echo $this->languageHandler->getString('some_rights_reserved')?></a><br />
          </div>
          <div style="clear:both;margin-left:20px;">
            <div style="text-transform: uppercase; font-size:1.4em; font-weight:bold; ">Beschreibung</div>
            <div style="margin: 10px  0 20px 0;"><?php echo $this->project['description']; ?></div>
            <div style="text-transform: uppercase; "><a href="#"><?php echo $this->languageHandler->getString('report_as_inappropriate')?></a></div>
          </div>
        </div>
      </article>
