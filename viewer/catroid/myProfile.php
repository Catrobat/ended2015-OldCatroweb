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
     <div id="profileContainer">
       <div>
          <div class="profileAvatar">
          <div class="profileErrorMessage" id="profileAvatarError"></div>
             <div class="profileAvatarImage"><img src="<?php echo $this->userData['avatar']; ?>" />
               <div class="profileChangeAvatar"><button id="profileChangeAvatarButton"><?php echo $this->languageHandler->getString('changeAvatar') ?></button></div>
             </div>
             <input id="profileAvatarFile" type="file" style="visibility:hidden;"/>
           </div>
     
         </div>
       
         <div class="profileInputs">
           <div class="profileInputsLeft">
              <div class="profileErrorMessage" id="profilePasswordError"></div>
             <div class="profilePasswordItem profileValid" >
                 <input type="password" class="inputValid" id="profileNewPassword" value="<?php echo htmlspecialchars($this->postData['profileNewPassword']); ?>" placeholder="<?php echo $this->languageHandler->getString('new_password') ?>" />
                 <img class="img-password" />
                 
             </div>
             <div class="profilePasswordItem profileValid">
               <input type="password" class="inputValid" id="profileRepeatPassword" value="<?php echo htmlspecialchars($this->postData['profileRepeatPassword']); ?>"placeholder="<?php echo $this->languageHandler->getString('repeat_password') ?>" />
               <img class="img-password" />
             </div>
             <?php echo $this->languageHandler->getString('country') ?>:
             <div class="profileCountry">
                <select><?php echo $this->countryCodeListHTML;?></select>
                <img class="img-select profileSelectImage"/>
             </div>      
           </div>
           <div class="profileInputsRight">
           <div class="profileErrorMessage" id="profileEmailError"></div>
             <div class="profileFirstEmailItem profileValid" >
               <input class="inputValid" type="email" />
               <img class="img-first-email" />
               
             </div>
             <a></a><img class="img-delete profileDeleteFirstEmail profileDeleteEMail"/></a>
             <div class="profileSecondEmailItem profileValid">
               <input class="inputValid" type="email"/>
               <img class="img-second-email" />
             </div>
             <img class="img-delete profileDeleteSecondEmail profileDeleteEMail"/>
             
             
             <div class="saveChanges">
             <div class="profileChangesSuccess">
                <img class="img-saved"   />
                <p><?php echo $this->languageHandler->getString('saved') ?></p>
             </div>
               <button id="profileSaveChanges"><?php echo $this->languageHandler->getString('save') ?></button>
             </div>
             
           </div>
         </div>
       </div>

    <div style="clear: both;"></div>
    <h3><?php echo $this->languageHandler->getString('my_projects')," ", $this->userData['username']; ?></h3>
    <div id="userProjectContainer" class="projectContainer"></div>
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
          "title" : "<?php echo $this->languageHandler->getString('userTitle'); ?>"
        };
        var profile = Profile(languageStringsObject);
        var projects = ProjectObject(<?php echo $this->jsParams; ?>, {'delete' : $.proxy(profile.deleteProject, profile), 
          'history' : $.proxy(profile.saveHistoryState, profile)});

        profile.setProjectObject(projects);
        projects.init();
      });
  </script>
      
