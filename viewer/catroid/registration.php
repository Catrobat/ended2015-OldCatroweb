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
        <div class="header"><?php echo $this->languageHandler->getString('title'); ?></div>
        <div class="form">
          <form>
            <div class="label"><?php echo $this->languageHandler->getString('nickname')?></div>
            <input type="text" id="registrationUsername" placeholder="<?php echo $this->languageHandler->getString('enter_nickname')?>" /><br />
            <div><?php echo $this->languageHandler->getString('nickname_info')?></div>
  
            <div class="label"><?php echo $this->languageHandler->getString('password')?></div> 
            <input type="password" id="registrationPassword" placeholder="<?php echo $this->languageHandler->getString('enter_password')?>" />

            <div class="label"><?php echo $this->languageHandler->getString('email')?></div> 
            <input type="email" id="registrationEmail" placeholder="<?php echo $this->languageHandler->getString('enter_email')?>" />
            <div><?php echo $this->languageHandler->getString('email_info', '<a href="http://developer.catrobat.org/privacy_policy" target="_blank">' . $this->languageHandler->getString('email_info_link_title') . '</a>')?></div>

            <div class="label"><?php echo $this->languageHandler->getString('country')?></div>
            <select id="registrationCountry">
<?php echo $this->module->generateCountryCodeList(); ?>
            </select>

            <div class="label"><?php echo $this->languageHandler->getString('city')?></div>
            <input type="text" id="registrationCity" placeholder="<?php echo $this->languageHandler->getString('enter_city')?>" />

            <div class="label"><?php echo $this->languageHandler->getString('birth')?></div>
            <div>
              <select id="registrationMonth" class="two">
<?php echo $this->module->generateMonthList(); ?>
              </select>
              <select id="registrationYear" class="two right">
<?php echo $this->module->generateYearList(); ?>
              </select>
            </div>
            <div style="clear: both;"></div>
            
            <div class="label"><?php echo $this->languageHandler->getString('gender')?></div>
            <select id="registrationGender">
              <option value="" selected="selected"><?php echo $this->languageHandler->getString('select_gender')?></option>
              <option value="female"><?php echo $this->languageHandler->getString('female')?></option>
              <option value="male"><?php echo $this->languageHandler->getString('male')?></option>
            </select>
            
            <div class="footer">
              <button class="blue" id="registrationSubmit"><?php echo $this->languageHandler->getString('create')?></button>
              <div><?php echo $this->languageHandler->getString('additional_options'); ?></div>
              <ul>
                <li><a id="registrationLogin" href="javascript:;"><?php echo $this->languageHandler->getString('login')?></a></li>
                <li><a id="forgotPassword" href="<?php echo BASE_PATH?>passwordrecovery"><?php echo $this->languageHandler->getString('password_recover')?></a></li>
              </ul>
            </div>
          </form>
        </div>
        <div class="projectSpacer"></div>
      </article>

      <script type="text/javascript">
        $(document).ready(function() {
          new Registration();
        });
      </script>
