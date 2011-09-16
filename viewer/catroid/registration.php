<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team 
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
              <div class="registrationText">
                <div class="registrationFormContainer">
                  <div class="registrationInfoText" id="registrationInfoText">
                    <div class="registrationErrorMsg" id="registrationErrorMsg"></div>
                  </div>
                  <form method="post" name="registrationFormDialog" id="registrationFormDialog">
                    <?php echo $this->languageHandler->getString('nickname')?><br/>
                    <input type="text" id="registrationUsername" name="registrationUsername" value="<?php echo htmlspecialchars($this->postData['registrationUsername'])?>" required="required" placeholder="<?php echo $this->languageHandler->getString('enter_nickname')?>" /><br/>
                    <?php echo $this->languageHandler->getString('password')?><br/>
                    <input type="password" id="registrationPassword" name="registrationPassword" value="<?php if($this->passOk) { echo htmlspecialchars($this->postData['registrationPassword']); }?>" required="required" placeholder="<?php echo $this->languageHandler->getString('enter_password')?>" /><br/>
                    <?php echo $this->languageHandler->getString('email')?><br/>
                    <input type="email" id="registrationEmail" name="registrationEmail" value="<?php echo htmlspecialchars($this->postData['registrationEmail'])?>" required="required" placeholder="<?php echo $this->languageHandler->getString('enter_email')?>" /><br/>
                    <?php echo $this->languageHandler->getString('country')?><br/>
                    <select id="registrationCountry" name="registrationCountry" class="registration" required="required">
<?php echo $this->countryCodeListHTML; ?>
                    </select><br/>
                    <?php echo $this->languageHandler->getString('city')?><br/>
                    <input type="text" id="registrationCity" name="registrationCity" value="<?php echo htmlspecialchars($this->postData['registrationCity'])?>" placeholder="<?php echo $this->languageHandler->getString('enter_city')?>" /><br/>
                    <?php echo $this->languageHandler->getString('birth')?><br/>
                    <select id="registrationMonth" name="registrationMonth" class="registration">
<?php echo $this->monthListHTML; ?>
                    </select> 
                    <select id="registrationYear" name="registrationYear" class="registration">
<?php echo $this->yearListHTML; ?>
                    </select><br/>
                    <?php echo $this->languageHandler->getString('gender')?><br/>
                    <select id="registrationGender" name="registrationGender" class="registration" >
                      <option value="" selected="selected"><?php echo $this->languageHandler->getString('select_gender')?></option>
                      <option value="female"><?php echo $this->languageHandler->getString('female')?></option>
                      <option value="male"><?php echo $this->languageHandler->getString('male')?></option>
                    </select>
                    <br/><br/>
                    <input type="button" name="registrationSubmit" id="registrationSubmit" value="<?php echo $this->languageHandler->getString('create')?>" class="button orange compact registrationSubmitButton"/>
                    <br/><br/><br/>
                    <div class="passwordRecoveryHelper">
                      <a id="registrationLogin" href="javascript:;"><?php echo $this->languageHandler->getString('login')?></a><br/>
                      <?php echo $this->languageHandler->getString('or')?><br/>
                      <a id="forgotPassword" target="_self" href="<?php echo BASE_PATH?>catroid/passwordrecovery"><?php echo $this->languageHandler->getString('password_recover')?></a>
                    </div>
                  </form>
                </div> <!-- registrationFormContainer -->
              </div> <!-- Registration Text -->
            </div> <!--  White Box -->            	
          </div> <!--  license Main -->  		   		
        </div> <!-- mainContent close //-->
      </div> <!-- blueBoxMain close //-->
    </div>