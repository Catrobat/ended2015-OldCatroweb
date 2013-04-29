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
        <div class="left">
           <div class="profileAvatarImage"><img src="<?php echo BASE_PATH; ?>images/symbols/placeholder.png" /></div>
           <div class="profileChangeAvatar"><button id="profileChangeAvatarButton"><?php echo $this->languageHandler->getString('changeAvatar') ?></button></div>
           <input id="profileAvatarFile" type="file" style="visibility:hidden;"/>
         </div>
         
         
         <div id="profileUpdateError">
          <div id="profilePasswordError">
           </div>
         </div>
       </div>
         <div class="middle">
            
           <div class="profilePasswordItem" >
               <input type="password" id="profileNewPassword" value="<?php echo htmlspecialchars($this->postData['profileNewPassword']); ?>" placeholder="<?php echo $this->languageHandler->getString('new_password') ?>" />
               <img src="<?php echo BASE_PATH; ?>images/symbols/add.png" />
           </div>
           <div class="profilePasswordItem">
             <input type="password" id="profileRepeatPassword" value="<?php echo htmlspecialchars($this->postData['profileRepeatPassword']); ?>"placeholder="<?php echo $this->languageHandler->getString('repeat_password') ?>" />
             <img src="<?php echo BASE_PATH; ?>images/symbols/add.png" />
           </div>
           <div><?php echo $this->languageHandler->getString('country') ?>:</div>
           <div class="profileCountry">
              <select><?php echo $this->countryCodeListHTML;?></select>
           </div>      
         </div>
         <div class="right">
           <div class="profileItem" >
             <input id="profileFirstEmail" type="email" />
             <img src="<?php echo BASE_PATH; ?>images/symbols/add.png" />
           </div>
           <div class="profileItem">
             <input id="profileSecondEmail" type="email"/>
             <img src="<?php echo BASE_PATH; ?>images/symbols/add.png" />
           </div>
           <div class="saveChanges">
             <button id="profileSaveChanges"><?php echo $this->languageHandler->getString('save') ?></button>
           </div>
           <div id="profileUpdateSuccess">
             <img src="<?php echo BASE_PATH; ?>images/symbols/placeholder.png" />
             Saved!
           </div> 
         </div>

       
       <div class="myProjects"><?php echo $this->languageHandler->getString('my_projects')," ", $this->userData['username'];?></div>
        <div id="newestProjects">
          <ul>
					  <li>
					    <a href="<?php echo BASE_PATH?>details/1">
					      <img src="<?php echo BASE_PATH; ?>images/symbols/thumb1.png" width="80" height="72" />
					      <div class="projectTitle">The Happy Hippo</div>
                <div class="projectAddition">20 minutes ago</div>
                <div class="changeProject">
                  <button class="change"><img src="<?php echo BASE_PATH; ?>images/symbols/placeholder.png" /></button>
                  <button class="change"><img src="<?php echo BASE_PATH; ?>images/symbols/placeholder.png" /></button>
                </div>
              </a>           
						</li>
					</ul>
        </div>
  </article>
  
  <script type="text/javascript">
      $(document).ready(function() {
        var languageStringsObject = { 
          "really_delete" : "<?php echo $this->languageHandler->getString('really_delete'); ?>",
          "image_too_big" : "<?php echo $this->languageHandler->getString('image_too_big'); ?>"
        };
        new Profile(languageStringsObject);
      });
  </script>
      
