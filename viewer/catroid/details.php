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
          <div style="float:left;">
            <a id="downloadProjectThumb" href="<?php echo BASE_PATH?>catroid/download/<?php echo $this->project['id']; echo PROJECTS_EXTENSION; ?>?fname=<?php echo urlencode($this->project['title'])?>">
              <img src="<?php echo $this->project['image']?>" alt="project thumbnail" style="width: 312px; heigh: 520px; border: 10px solid #ffffff; margin: 20px;" />
            </a>
          </div>
          <header><?php echo $this->project['title']?></header>
          <div>
            <img src="<?php echo BASE_PATH; ?>images/symbols/details-user.png" /><?php echo $this->project['uploaded_by_string']; ?><br>
            <img src="<?php echo BASE_PATH; ?>images/symbols/details-time.png" /><?php echo $this->project['publish_time_in_words']; ?><br>
            <img src="<?php echo BASE_PATH; ?>images/symbols/details-downloads.png" /><?php echo $this->project['download_count'] . " " . $this->languageHandler->getString('downloads'); ?> <br>
            <img src="<?php echo BASE_PATH; ?>images/symbols/details-views.png" /><?php echo $this->project['view_count'] . " " . $this->languageHandler->getString('views'); ?><br>
            <button>Download</button><br>
            <?php echo $this->languageHandler->getString('version_info_text') . " " . $this->project['version_name'];?>
          </div>
        </div>
      </article>
