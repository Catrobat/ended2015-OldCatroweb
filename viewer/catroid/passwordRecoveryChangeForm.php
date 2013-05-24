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
            <div class="label"><?php echo $this->languageHandler->getString('headline')?></div>
            <input type="hidden" id="passwordRecoveryHash" value="<?php echo htmlentities($_GET['c']); ?>" />
            <input type="text" id="passwordSavePassword" placeholder="<?php echo $this->languageHandler->getString('change_password')?>" /><br />

            <div class="footer">
              <nav>
                <span id="passwordSaveLoader"><img src="<?php echo BASE_PATH; ?>images/symbols/ajax-loader-bright.gif" /></span>
                <button id="passwordSaveSubmit"><?php echo $this->languageHandler->getString('change_password')?></button>
              </nav>
            </div>
          </form>
        </div>
        <div class="projectSpacer"></div>
      </article>

      <script type="text/javascript">
        $(document).ready(function() {
          new PasswordRecovery();
        });
      </script>
