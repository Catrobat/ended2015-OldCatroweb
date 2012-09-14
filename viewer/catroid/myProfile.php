<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2012 The Catroid Team 
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>
      <div class="webMainMiddle">
        <div class="blueBoxMain">
          <div class="webMainContent">
            <div class="webMainContentTitle"><?php echo $this->languageHandler->getString('myTitle'); ?></div> 
            <div class="profileMain">                
              <div class ="whiteBoxMain">
                <div class="profileText">
                  <div class="profileFormContainer">
                      
                      <img src="<?php echo BASE_PATH; ?>images/symbols/avatar_boys.png" class="avatar" />

                      <div class="profileItem">
                        <div class="label"><?php echo $this->languageHandler->getString('name'); ?></div>
                        <div>
                          <?php echo $this->userData['username']; ?><br />
                          <a href="<?php echo BASE_PATH . 'catroid/myprojects' ?>"><?php echo $this->languageHandler->getString('my_projects'); ?></a>
                        </div>
                      </div>

                      <div class="profileItem">
                        <div class="label"><?php echo $this->languageHandler->getString('password'); ?></div>
                        <div>
                          <a href="javascript:;" id="profileChangePassword"><?php echo $this->languageHandler->getString('change_my_password')?></a><br>
                          <div id="profilePasswordInput" style="padding-bottom: 20px; display:none;">
                            <input class="catroid" style="display: inline-block; margin:20px 0 0 0; width: 300px;" type="text" id="profileOldPassword" value="<?php echo htmlspecialchars($this->postData['profileOldPassword']); ?>" required="required" placeholder="<?php echo $this->languageHandler->getString('old_password')?>" /><br />
                            <input class="catroid" style="display: inline-block; margin:20px 0 0 0; width: 300px;" type="text" id="profileNewPassword" value="<?php echo htmlspecialchars($this->postData['profileNewPassword']); ?>" required="required" placeholder="<?php echo $this->languageHandler->getString('new_password')?>" /><br />
                            <input type="button" id="profilePasswordSubmit" value="<?php echo $this->languageHandler->getString('save_button')?>" class="button orange compact profileSubmitButton" style="margin:20px 0 0 0; width: 300px;" />
                          </div>
                        </div>
                        <div style="clear:both"></div>  
                      </div>

                      <div class="profileItem">
                        <div class="label" style="padding: 10px 0 10px 0;"><?php echo $this->languageHandler->getString('email'); ?></div>
                        <div>
                          <span id="emailDeleteButtons"></span><br />
                          <div><strong>Add another email address:</strong></div> <br/>
                          <div>
                            <input id="addEmailInput" tpye="text" class="catroid" style="display:inline-block; width:400px;" />
                            <button id="addEmailButton" style="margin: 5px;" class="button orange compact"><img width="24px" src="http://catroid.local/images/symbols/add.png"></button>
                          </div>                          
                        </div>
                        <div style="clear:both"></div>  
                      </div>
                      
                      <div class="profileItem">
                        <div class="label" style="padding: 14px 0 14px 0;"><?php echo $this->languageHandler->getString('location'); ?></div>
                        <div>
                          <input id="cityInput" class="catroid" style="display:inline-block; width:400px;" type="text" value="<?php echo $this->userData['city']; ?>" placeholder="<?php echo $this->languageHandler->getString('enter_city'); ?>" />
                          <select id="countrySelect" class="catroid" style="width: 400px;">
                            <?php echo $this->countryCodeListHTML; ?>
                          </select>
                        </div>
                      </div>

                      <div class="profileItem">
                        <div class="label" style="padding: 14px 0 14px 0;"><?php echo $this->languageHandler->getString('gender'); ?></div>
                        <div>
                          <select id="genderSelect" class="catroid" style="width: 400px;" >
                            <?php echo $this->genderListHTML; ?>
                          </select>
                        </div>
                      </div>

                      <div class="profileItem">
                        <div class="label" style="padding: 14px 0 14px 0;"><?php echo $this->languageHandler->getString('birthday'); ?></div>
                        <div>
                          <select id="birthdayMonthSelect" class="catroid" style="width: 198px; display:inline-block;" >
                            <?php echo $this->monthListHTML; ?>
                          </select> 
                          <select id="birthdayYearSelect" class="catroid" style="width: 198px; display:inline-block;" >
                            <?php echo $this->yearListHTML; ?>
                          </select>
                        </div>
                      </div>

                      <div class="profileItem">
                        <div class="label" style="padding: 14px 0 14px 0;"><?php echo $this->languageHandler->getString('language'); ?></div>
                        <div>
                          <select id="profileSwitchLanguage"  class="catroid" style="width: 400px; display:inline-block;" >
                            <?php echo $this->laguageListHTML; ?>
                          </select>
                        
                        </div>
                      </div>

                      <div style="clear:both;"></div>
                  </div> <!-- profileFormContainer -->
                </div> <!-- profile Text -->
              </div> <!--  White Box -->                
            </div> <!--  license Main -->                     
          </div> <!-- mainContent close //-->
        </div> <!-- blueBoxMain close //-->
      </div>

      <script type="text/javascript">
          $(document).ready(function() {
            var languageStringsObject = { 
              "really_delete" : "<?php echo $this->languageHandler->getString('really_delete'); ?>"
              };
            new Profile(languageStringsObject);
          });
      </script>