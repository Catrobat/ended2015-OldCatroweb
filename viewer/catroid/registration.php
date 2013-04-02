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
    <script type="text/javascript">
      $(document).ready(function() {
        new Registration();
      });
    </script>
    <div class="webMainMiddle">
      <div class="blueBoxMain">
        <div class="webMainContent">
          <div class="webMainContentTitle"><?php echo $this->languageHandler->getString('title')?></div>
          <div class="registrationMain">            	
            <div class="whiteBoxMain">
              <form class="registrationForm catroid">
                <label for="registrationUsername"><?php echo $this->languageHandler->getString('nickname')?></label> 
                <input type="text" id="registrationUsername" required="required" placeholder="<?php echo $this->languageHandler->getString('enter_nickname')?>" class="catroid" />
                <div class="catroidFormInfoBox"><?php echo $this->languageHandler->getString('nickname_info')?></div>
                <label for="registrationPassword"><?php echo $this->languageHandler->getString('password')?></label> 
                <input type="password" id="registrationPassword" required="required" placeholder="<?php echo $this->languageHandler->getString('enter_password')?>" class="catroid" />
                <label for="registrationEmail"><?php echo $this->languageHandler->getString('email')?></label> 
                <input type="email" id="registrationEmail" required="required" placeholder="<?php echo $this->languageHandler->getString('enter_email')?>" class="catroid" />
                <div class="catroidFormInfoBox"><?php echo $this->languageHandler->getString('email_info', '<a href="http://developer.catrobat.org/privacy_policy" target="_blank">' . $this->languageHandler->getString('email_info_link_title') . '</a>')?></div>
                <div><?php echo $this->languageHandler->getString('country')?></div>
                <select id="registrationCountry" required="required" class="catroid">
<?php echo $this->module->generateCountryCodeList(); ?>
                </select>
                <label for="registrationCity"><?php echo $this->languageHandler->getString('city')?></label> 
                <input type="text" id="registrationCity" placeholder="<?php echo $this->languageHandler->getString('enter_city')?>" class="catroid" />
                <div><?php echo $this->languageHandler->getString('birth')?></div>
                <div>
                  <select id="registrationMonth" class="catroid catroidTwoColumn catroidLeftColumn">
<?php echo $this->module->generateMonthList(); ?>
                  </select>
                  <select id="registrationYear" class="catroid catroidTwoColumn catroidRightColumn">
<?php echo $this->module->generateYearList(); ?>
                  </select>
                </div>
                <div><?php echo $this->languageHandler->getString('gender')?></div>
                <select id="registrationGender" class="catroid" >
                  <option value="" selected="selected"><?php echo $this->languageHandler->getString('select_gender')?></option>
                  <option value="female"><?php echo $this->languageHandler->getString('female')?></option>
                  <option value="male"><?php echo $this->languageHandler->getString('male')?></option>
                </select>
                <input type="button" id="registrationSubmit" value="<?php echo $this->languageHandler->getString('create')?>" class="catroidSubmit button orange registrationSubmitButton"/>
                <br /> <br /> <br /> <br />
                
                <div class="otherOptions"><?php echo $this->languageHandler->getString('additional_options'); ?></div>
                <ul class="loginOptions">
                  <li><a id="registrationLogin" href="javascript:;"><?php echo $this->languageHandler->getString('login')?></a></li>
                  <li><a id="forgotPassword" href="<?php echo BASE_PATH?>catroid/passwordrecovery"><?php echo $this->languageHandler->getString('password_recover')?></a></li>
                </ul>
              </form> <!-- registrationForm -->
            </div> <!-- White Box -->
          </div> <!-- registration Main -->
        </div> <!-- mainContent close //-->
      </div> <!-- blueBoxMain close //-->
    </div>
