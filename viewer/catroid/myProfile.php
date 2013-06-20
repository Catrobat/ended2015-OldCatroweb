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
    <div>

      <div class="profileAvatar">
        <div id="profileAvatarError"></div>
        <img class="profileAvatarImage" src="<?php echo $this->userData['avatar']; ?>" />
        <div>
          <button id="profileChangeAvatarButton"><?php echo $this->languageHandler->getString('changeAvatar'); ?></button>
        </div>
        <input id="profileAvatarFile" type="file" />
      </div>
       
       <div class="profileInputs">
        <div class="profileInputsLeft">
          <div id="profilePasswordError"></div>
           <div id="profileNewPassword" class="profileValid">
            <input type="password" value="<?php echo htmlspecialchars($this->postData['profileNewPassword']); ?>" placeholder="<?php echo $this->languageHandler->getString('new_password'); ?>" />
            <div class="img-password profileInputIcon"></div>
          </div>

          <div id="profileRepeatPassword" class="profileValid">
            <input type="password" value="<?php echo htmlspecialchars($this->postData['profileRepeatPassword']); ?>"placeholder="<?php echo $this->languageHandler->getString('repeat_password'); ?>" />
            <div class="img-password profileInputIcon"></div>
          </div>

          <b><?php echo $this->languageHandler->getString('country') ?></b>
          <div class="profileCountry">
            <select><?php echo $this->countryCodeListHTML;?></select>
            <div class="img-select profileSelectImage"></div>
          </div>      
        </div>
        
       <div class="profileInputsRight">
          <div id="profileEmailError"></div>
          <!-- <div id="profileEmailDelete" class="img-delete profileDeleteEMaill"></div>-->
          <div id="profileEmail" class="profileValid">
            <input type="email" placeholder="<?php echo $this->languageHandler->getString('email'); ?>" value="<?php echo $this->userData['email']; ?>" />
            <div class="img-first-email profileInputIcon"></div>
          </div>

          <!-- <div id="profileSecondEmailDelete" class="img-delete profileDeleteEMaill"></div>-->
          <div id="profileSecondEmail" class="profileValid">
          <div class="img-second-email profileInputIcon"></div>
            <input type="email" placeholder="<?php echo $this->languageHandler->getString('second_email'); ?>" value="<?php echo $this->userData['additional_email']; ?>" />
          </div>

          <div>
          <button class= "blue" id="profileSaveChanges"><?php echo $this->languageHandler->getString('save'); ?></button>
            <img class="profileLoader" src="<?php echo BASE_PATH; ?>images/symbols/ajax-loader-dark.gif" />
            <div id="profileChangesSuccess">
              <div class="img-saved"></div>
              <span><?php echo $this->languageHandler->getString('saved') ?></span>
            </div>
            
          </div>
        </div>
      </div>
    </div>

    <div style="clear: both;"></div>
    <h3><?php echo $this->languageHandler->getString('my_projects'); ?></h3>
    <div id="userProjectContainer" class="projectContainer">
      <span id="profileNoResults">
        <img src="<?php echo BASE_PATH; ?>images/symbols/error_small.png" />
        <p><?php echo $this->languageHandler->getString('no_projects_available'); ?></p>
      </span>
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
        var languageStringsObject = { 
          "really_delete_email" : "<?php echo $this->languageHandler->getString('really_delete_email'); ?>",
          "really_delete_project" : "<?php echo $this->languageHandler->getString('really_delete_project'); ?>",
          "image_too_big" : "<?php echo $this->languageHandler->getString('image_too_big'); ?>",
          "second_email" : "<?php echo $this->languageHandler->getString('second_email'); ?>",
          "websiteTitle" : "<?php echo SITE_DEFAULT_TITLE; ?>",
          "title" : "<?php echo $this->languageHandler->getString('userTitle'); ?>",
          "email_verification" : "<?php echo $this->languageHandler->getString('email_add_success'); ?>",
          "edit_one_entry" : "<?php echo $this->languageHandler->getString('edit_one_entry'); ?>"
        };
        var profile = Profile(languageStringsObject);
        var projects = ProjectObject(<?php echo $this->jsParams; ?>, {'delete' : $.proxy(profile.deleteProject, profile), 
          'history' : $.proxy(profile.saveHistoryState, profile)});

        profile.setProjectObject(projects);
        projects.init();
      });
  </script>
      
