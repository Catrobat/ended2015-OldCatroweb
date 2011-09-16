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
        var languageStringsObject = { 
          "emailArrayDiv" : "<?php echo $this->emailArrayDiv; ?>",
          "emailCount" : "<?php echo count($this->emailArray); ?>",
          "emailDeleteAlertTitle" : "<?php echo $this->languageHandler->getString('alertbox_really_want_to_delete_email'); ?>",
          "addNewEmailButtonLanguageString" : "<?php echo $this->languageHandler->getString('add_new_email_button'); ?>",
          "addNewEmailLanguageString" : "<?php echo $this->languageHandler->getString('add_new_email'); ?>",
          "addNewEmailPlaceholderLanguageString" : "<?php echo $this->languageHandler->getString('add_new_email_placeholder'); ?>",
          "changeEmailLanguageString" : "<?php echo $this->languageHandler->getString('email'); ?>",
          "changeEmailDeleteButtonLanguageString" : "<?php echo $this->languageHandler->getString('delete_email'); ?>",
          "changeEmailSaveChangesLanguageString" : "<?php echo $this->languageHandler->getString('save_button'); ?>",
          "emailAddressStringChangedLanguageString" : "<?php echo $this->languageHandler->getString('email_address_string_changed'); ?>",
          "birthdayChangeLanguageString" : "<?php echo $this->languageHandler->getString('birthday_is_empty'); ?>",
          "birthdayBornInLanguageString" : "<?php echo $this->languageHandler->getString('born_in'); ?>",
          "birthdayAddDateLanguageString" : "<?php echo $this->languageHandler->getString('add_your_birth_date'); ?>"
          };
          new Profile(languageStringsObject);
      });
  </script>
      <div class="webMainMiddle">
          <div class="blueBoxMain">
              <div class="webMainContent">
                <div class="webMainContentTitle">
                  <?php echo $this->languageHandler->getString('title', $this->requestedUser)."\n"; ?>
                </div> <!-- webMainContentTitle --> 
                <div class="profileMain">                
                  <div class ="whiteBoxMain">
                    <div class="profileText">
                    <div class="profileFormContainer">
                      <div class="profileFormAnswer" id="profileFormAnswer">
                      <div class="errorMsg" id="errorMsg">
                      <!-- error messages here -->
                      </div>
                      <div class="okMsg" id="okMsg">
                      <!-- ok messages here -->
                      </div>
                      </div>
                      <br>
<?php if($this->ownProfile) { ?>
                      <input type="hidden" id="profileUser" name="profileUser" value="<?php echo $this->requestedUser; ?>">
                      <div id="profilePasswordDiv">
                        <a href="javascript:;" class="profileText" id="profileChangePasswordOpen"><?php echo $this->languageHandler->getString('password')?></a><br>
                      </div>  
                      <div id="profilePasswordDivOpened">
                        <a href="javascript:;" class="profileText" id="profileChangePasswordClose"><?php echo $this->languageHandler->getString('password')?></a><br>
                        <input type="text" id="profileOldPassword" name="profileOldPassword" value="<?php echo htmlspecialchars($this->postData['profileOldPassword']); ?>" required="required" placeholder="<?php echo $this->languageHandler->getString('old_password')?>" ><br>
                        <input type="text" id="profileNewPassword" name="profileNewPassword" value="<?php echo htmlspecialchars($this->postData['profileNewPassword']); ?>" required="required" placeholder="<?php echo $this->languageHandler->getString('new_password')?>" ><br>
                        <input type="button" name="profilePasswordSubmit" id="profilePasswordSubmit" value="<?php echo $this->languageHandler->getString('save_button')?>" class="button orange compact profileSubmitButton">
                      </div>
                      <br>
                      <div id="profileEmailTextDiv">
<?php //$this->userEmailsArrayDiv; ?>
                      </div>
                      <div id="buttonProfileOpenAddNewEmailFieldDiv">
                        <input type="button" name="buttonProfileOpenAddNewEmailField" id="buttonProfileOpenAddNewEmailField" value=" <?php echo $this->languageHandler->getString('add_email_field_button') ?> " class="button orange compact profileSubmitButton">
                      </div>
                      <div id="profileAdditionalEmailInputFields">
                      <div id="emailTextFields">
                      </div>
                      <div id="emailAddButton">
                        <input type="button" name="buttonProfileSaveNewEmailSubmit" id="buttonProfileSaveNewEmailSubmit" value="<?php echo $this->languageHandler->getString('add_new_email_button')?>" class="button orange compact profileSubmitButton"> <input type="button" name="buttonProfileCloseAddNewEmailField" id="buttonProfileCloseAddNewEmailField" value=" <?php echo $this->languageHandler->getString('cancel') ?> " class="button orange compact profileSubmitButton">
                      </div>
                      </div>
                      <br>
<?php echo $this->languageHandler->getString('from');
                        if($this->userCity) {
                      ?>
                      <div id="profileCityDiv">
                        <a href="javascript:;" class="profileText" id="profileChangeCityOpen"><?php echo $this->userCity; ?></a><br>
                      </div> 
                      <div id="profileCityDivOpened">
                        <a href="javascript:;" class="profileText" id="profileChangeCityClose"><?php echo $this->languageHandler->getString('change_city')?></a><br>
                        <input type="text" id="profileCity" name="profileCity" value="" required="required" placeholder="<?php echo $this->languageHandler->getString('city')?>" ><br>
                        <input type="button" name="profileCitySubmit" id="profileCitySubmit" value="<?php echo $this->languageHandler->getString('save_button')?>" class="button orange compact profileSubmitButton">
                      </div>
<?php } else { ?>
                      <div id="profileEmptyCityDiv">
                        <a href="javascript:;" class="profileText" id="profileChangeCityOpen"><?php echo $this->languageHandler->getString('add_city'); ?></a><br>
                      </div> 
                      <div id="profileEmptyCityDivOpened">
                        <a href="javascript:;" class="profileText" id="profileChangeCityClose"><?php echo $this->languageHandler->getString('enter_city')?></a><br>
                        <input type="text" id="profileCity" name="profileCity" value="" required="required" placeholder="<?php echo $this->languageHandler->getString('city')?>" ><br>
                        <input type="button" name="profileCitySubmit" id="profileCitySubmit" value="<?php echo $this->languageHandler->getString('save_button')?>" class="button orange compact profileSubmitButton">
                      </div>
<?php } ?>
                      <div id="profileCountryDiv">
                        <a href="javascript:;" class="profileText" id="profileChangeCountryClose"><?php echo $this->languageHandler->getString('change_country')?></a><br>
                        <select id="profileCountry" name="profileCountry" class="profile" required="required" >
<?php echo $this->countryCodeListHTML; ?>
                        </select>
                        <br>
                        <input type="button" name="profileCountrySubmit" id="profileCountrySubmit" value="<?php echo $this->languageHandler->getString('save_button')?>" class="button orange compact profileSubmitButton">
                      </div>
                      <div id="profileCountryTextDiv">
<?php echo $this->countryTextHTML; ?>
                        <br>
                      </div>
                      <br>
                      <div id="profileBirthDiv">
                        <a href="javascript:;" class="profileText" id="profileChangeBirthOpen"><?php echo $this->birthdayOpenText; ?></a><br>
                      </div>  
                      <div id="profileBirthDivOpened">
                        <a href="javascript:;" class="profileText" id="profileChangeBirthClose"><?php echo $this->birthdayCloseText; ?></a><br>
                        <select id="profileMonth" name="profileMonth" class="profile" >
<?php echo $this->monthListHTML; ?>
                        </select> 
                        <select id="profileYear" name="profileYear" class="profile" >
<?php echo $this->yearListHTML; ?>
                        </select><br>
                        <input type="button" name="profileBirthSubmit" id="profileBirthSubmit" value="<?php echo $this->languageHandler->getString('save_button')?>" class="button orange compact profileSubmitButton">
                      </div>
                      <br>
                      <div id="profileGenderDiv">
                        <a href="javascript:;" class="profileText" id="profileChangeGenderOpen"><?php echo $this->genderOpenText; ?></a><br>
                      </div>  
                      <div id="profileGenderDivOpened">
                        <a href="javascript:;" class="profileText" id="profileChangeGenderClose"><?php echo $this->genderCloseText; ?></a><br>
                        <select id="profileGender" name="profileGender" class="profile" >
<?php echo $this->genderListHTML; ?>
                        </select>
                        <br>
                        <input type="button" name="profileGenderSubmit" id="profileGenderSubmit" value="<?php echo $this->languageHandler->getString('save_button')?>" class="button orange compact profileSubmitButton">
                      </div>
                      <br>
                      <div id="profileLanguageDivOpened">
                        <a href="javascript:;" class="profileText" id="profileChangeLanguageClose"><?php echo $this->languageHandler->getString('select_language')?></a><br>
                        <select id="profileSwitchLanguage" class="profile">
<?php                     
$supportedLanguages = getSupportedLanguagesArray($this->languageHandler);
foreach($supportedLanguages as $lang => $details) {
  if($details['supported']) {
    $selected = "";
      if(strcmp($lang, $this->languageHandler->getLanguage()) == 0) {
        $selected = "selected ";
        $this->languageString = $details['name'];
} ?>
                          <option <?php echo $selected?>value="<?php echo $lang?>"><?php echo $details['name'].' - '.$details['nameNative']?></option>
<?php } } ?>
                        </select>
                        <br>
                      </div>
                      <div id="profileLanguageTextDiv">
                        <a href="javascript:;" class="profileText" id="profileChangeLanguageOpen"><?php echo $this->languageString ?></a>
                      <br>
                      </div>
<?php } else { // start public profile ?>
                      <br>
<?php echo $this->languageHandler->getString('from'); ?>
                      <div id="profileCityDiv">
<?php echo $this->userCity; ?>
                      </div> 
                      <div id="profileCountryTextDiv">
<?php echo $this->countryTextHTML; ?>
                    </div>
<?php } ?>
                    <br>
                    <br>
                  </div> <!-- profileFormContainer -->
                </div> <!-- profile Text -->
              </div> <!--  White Box -->                
            </div> <!--  license Main -->                     
          </div> <!-- mainContent close //-->
        </div> <!-- blueBoxMain close //-->
      </div>

