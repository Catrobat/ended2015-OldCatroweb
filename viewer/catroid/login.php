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
        <div class="form">
          <form>
          
          <div class="loginValid">
          <input type="text" id="loginUsername" placeholder="<?php echo $this->languageHandler->getString('enter_nick')?>" />
          <div class="img-login loginInputIcon"></div>
          </div>
            
            <div class="loginValid">
            <input type="password" id="loginPassword" placeholder="<?php echo $this->languageHandler->getString('enter_password')?>" />
            <div class="img-password loginInputIcon"></div>
            </div>
            
            <div id="loginError"></div>

            <div class="footer">
              <nav>
                <span id="loginLoader"><img src="<?php echo BASE_PATH; ?>images/symbols/ajax-loader-bright.gif" /></span>
                <button class="blue" id="loginSubmitButton"><?php echo $this->languageHandler->getString('login')?></button>
              </nav>
              <div>
                <a id="forgotPassword" href="<?php echo BASE_PATH?>passwordrecovery"><?php echo $this->languageHandler->getString('password_link')?></a>
              </div>
            </div>
          </form>
        </div>
        <div class="projectSpacer"></div>
      </article>

      <script type="text/javascript">
      	$(document).ready(function() {
          var languageStringsObject = { 
              "username_missing" : "<?php echo $this->module->errorHandler->getError('userFunctions', 'username_missing'); ?>",
              "password_missing" : "<?php echo $this->module->errorHandler->getError('userFunctions', 'password_missing'); ?>"
              };
          new Login(languageStringsObject);
      	});
      </script>
