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
    <div class="profileAvatar">
      <img class="profileAvatarImage" src="<?php echo $this->userData['avatar']; ?>" />
    </div>

    <div class="profileInputs">
      <div class="profileInformations">
      <p>
        <strong><?php echo $this->languageHandler->getString('country'); ?>: </strong>
        <?php 
        if($this->userData['country'] != "") {
          $countries = getCountryArray($this->languageHandler);
          echo $countries[$this->userData['country']];
        }
        ?>
      </p>
      <p>
        <strong><?php echo $this->languageHandler->getString('projects'); ?>: </strong>
        <?php echo $this->userData['project_count']; ?>
      </p>
      </div>
    </div>
    <div style="clear: both;"></div>

    <h3><?php echo $this->languageHandler->getString('user_projects')," ", $this->userData['username']; ?></h3>
    <div id="userProjectContainer" class="projectContainer">
      <span id="profileNoResults"><?php echo $this->languageHandler->getString('no_projects_available'); ?></span>
    </div>
    <div id="userProjectLoader" class="projectFooter">
      <img src="<?php echo BASE_PATH; ?>images/symbols/ajax-loader-dark.gif" />
      <p>&nbsp;</p>
    </div>
    <div id="moreResults" class="projectFooter">
      <div class="img-load-more"></div>
      <p><?php echo $this->languageHandler->getString('showMore'); ?></p>
    </div>
    <div class="projectSpacer"></div>
  </article>

  <script type="text/javascript">
    $(document).ready(function() {
      var profile = Profile({'websiteTitle' : '<?php echo SITE_DEFAULT_TITLE; ?>', 'title' : '<?php echo $this->languageHandler->getString('userTitle'); ?>'});
      var projects = ProjectObject(<?php echo $this->jsParams; ?>, {'history' : $.proxy(profile.saveHistoryState, profile) });

      profile.setProjectObject(projects);
      projects.init();
    });
  </script>
