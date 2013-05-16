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
    <header><?php echo $this->userData['username'];?></header> 
    <div style="margin:2em;" class="profileAvatarImage"><img src="<?php echo $this->userData['avatar']; ?>" /></div>
    <div style="margin:2em;">
      <?php echo $this->languageHandler->getString('location');
      echo ": ";
      $countries = getCountryArray($this->languageHandler);
      if($this->userData['country'] != "") {
        echo $countries[$this->userData['country']];
      }
      ?>
    </div>
    <div style="margin:2em; text-align: left;">
      <?php echo $this->languageHandler->getString('projects');
      echo ": ";
      echo $this->userData['project_count'];
      ?>
    </div>

    <div style="clear: both;"></div>
    <h3><?php echo $this->languageHandler->getString('my_projects')," ", $this->userData['username']; ?></h3>
    <div id="userProjectContainer" class="projectContainer"></div>
    <div id="userProjectLoader" class="projectLoader"><img src="<?php echo BASE_PATH; ?>images/symbols/ajax-loader-dark.gif" /></div>
    <div id="moreResults" class="moreButton">
      <div class="img-load-more"></div>
      <p><?php echo $this->languageHandler->getString('showMore'); ?></p>
    </div>
  </article>

  <script type="text/javascript">
    $(document).ready(function() {
      ProjectObject(<?php echo $this->jsParams; ?>).init();
    });
  </script>
