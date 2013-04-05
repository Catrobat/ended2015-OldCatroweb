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
     <header><?php echo $this->languageHandler->getString('myTitle'); ?></header> 
        <div class="profileItem">
        <input type="search" placeholder="Passwort ändern" />
        <img src="<?php echo BASE_PATH; ?>images/symbols/6-social-person.png" />
         
        </div>

                  <!-- <div class="avatarContainer">
                    <img id="profileAvatarImage" src="<?php echo $this->userData['avatar']; ?>" class="avatar" /><br />
                    <div id="profileChangeAvatar"><a href="javascript:;"><?php echo $this->languageHandler->getString('changePicture'); ?></a></div>
                    <input id="profileAvatarFile" type="file" />
                  </div>

                  
                  <div class="profileItem">
                    <div class="label"><?php echo $this->languageHandler->getString('name'); ?></div>
                    <div>
                      <?php echo $this->userData['username']; ?>
                    </div>
                  </div>

                  <div class="profileItem">
                    <div class="label">&nbsp;</div>
                    <div>
                      <a id="profileMyProfileLink" href="<?php echo BASE_PATH . 'catroid/myprojects' ?>"><?php echo $this->languageHandler->getString('my_projects'); ?></a>
                    </div>
                  </div>

                  <div class="profileItem">
                    <div class="label"><?php echo $this->languageHandler->getString('password'); ?></div>
                    <div>
                      <a href="javascript:;" id="profileChangePassword"><?php echo $this->languageHandler->getString('change_my_password')?></a><br>
                      <div id="profilePasswordInput">
                        <input class="catroid profileChangePassword" type="text" id="profileOldPassword" value="<?php echo htmlspecialchars($this->postData['profileOldPassword']); ?>" required="required" placeholder="<?php echo $this->languageHandler->getString('old_password')?>" /><br />
                        <input class="catroid profileChangePassword" type="text" id="profileNewPassword" value="<?php echo htmlspecialchars($this->postData['profileNewPassword']); ?>" required="required" placeholder="<?php echo $this->languageHandler->getString('new_password')?>" /><br />
                        <input type="button" id="profilePasswordSubmit" value="<?php echo $this->languageHandler->getString('save_button')?>" class="button orange compact profileSubmitButton" />
                      </div>
                    </div>
                    <div style="clear:both;"></div>  
                  </div>

                  <div class="profileItem">
                    <div class="label labelEmail"><?php echo $this->languageHandler->getString('email'); ?></div>
                    <div>
                      <span id="emailDeleteButtons"></span><br />
                      <div><strong>Add another email address:</strong></div> <br/>
                      <div>
                        <input id="addEmailInput" type="text" class="catroid profileInputSmall" />
                        <button id="addEmailButton" class="button orange compact"><img width="24px" src="<?php echo BASE_PATH; ?>images/symbols/add.png"></button>
                      </div>                          
                    </div>
                    <div style="clear:both"></div>  
                  </div>
                  
                  <div class="profileItem">
                    <div class="label labelInput"><?php echo $this->languageHandler->getString('country'); ?></div>
                    <div>
                      <select id="countrySelect" class="catroid profileInput">
                        <?php echo $this->countryCodeListHTML; ?>
                      </select>
                    </div>
                    <div style="clear:both"></div>  
                  </div>
                  
                  <div class="profileItem">
                    <div class="label labelInput"><?php echo $this->languageHandler->getString('city'); ?></div>
                    <div>
                      <input id="cityInput" class="catroid profileInput" type="text" value="<?php echo $this->userData['city']; ?>" /><br />
                    </div>
                    <div style="clear:both"></div>  
                  </div>

                  <div class="profileItem">
                    <div class="label labelInput"><?php echo $this->languageHandler->getString('gender'); ?></div>
                    <div>
                      <select id="genderSelect" class="catroid profileInput">
                        <?php echo $this->genderListHTML; ?>
                      </select>
                    </div>
                    <div style="clear:both"></div>  
                  </div>

                  <div class="profileItem">
                    <div class="label labelInput"><?php echo $this->languageHandler->getString('birthday'); ?></div>
                    <div>
                      <select id="birthdayMonthSelect" class="catroid profileInputTwo">
                        <?php echo $this->monthListHTML; ?>
                      </select> 
                      <select id="birthdayYearSelect" class="catroid profileInputTwo">
                        <?php echo $this->yearListHTML; ?>
                      </select>
                    </div>
                    <div style="clear:both"></div>  
                  </div>

                  <div class="profileItem">
                    <div class="label labelInput"><?php echo $this->languageHandler->getString('language'); ?></div>
                    <div>
                      <select id="profileSwitchLanguage" class="catroid profileInput">
                        <?php echo $this->laguageListHTML; ?>
                      </select>
                    </div>
                    <div style="clear:both"></div>  
                  </div>-->

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
      
