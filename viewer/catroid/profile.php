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
  	      "emailsArrayDiv" : "<?php echo $this->emailsArrayDiv; ?>",
          "emailCount" : "<?php echo count($this->emailsArray); ?>",
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
                <?php echo $this->languageHandler->getString('title', $this->requestedUser)?>
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
            		   	<?php 
                      if($this->ownProfile) {           		   	
            		   	?>
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
                        <?php 

                          //echo $this->userEmailsArrayDiv;
                        
                        ?>
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
                      <?php 
                        echo $this->languageHandler->getString('from');
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
                      <?php 
                        }
                        else {
                      ?>
                      <div id="profileEmptyCityDiv">
                        <a href="javascript:;" class="profileText" id="profileChangeCityOpen"><?php echo $this->languageHandler->getString('add_city'); ?></a><br>
                      </div> 
                      <div id="profileEmptyCityDivOpened">
                        <a href="javascript:;" class="profileText" id="profileChangeCityClose"><?php echo $this->languageHandler->getString('enter_city')?></a><br>
                        <input type="text" id="profileCity" name="profileCity" value="" required="required" placeholder="<?php echo $this->languageHandler->getString('city')?>" ><br>
                        <input type="button" name="profileCitySubmit" id="profileCitySubmit" value="<?php echo $this->languageHandler->getString('save_button')?>" class="button orange compact profileSubmitButton">
                      </div>
                      <?php
                        }  // end city if
                      ?>
          		   			<div id="profileCountryDiv">
          		   			  <a href="javascript:;" class="profileText" id="profileChangeCountryClose"><?php echo $this->languageHandler->getString('change_country')?></a><br>
          		   			  <select id="profileCountry" name="profileCountry" class="profile" required="required" >
              		   		<?php // country 
              		   			$x = 0;
              		   			$sumCount = count($this->countryCodeList);
              		   			while($x < $sumCount+1) {
              		   			  if($x == 0) {
                              echo '<option value="0" >'.$this->languageHandler->getString('country').'</option>\r';
                            }
              		   			  else if(strcmp($this->countryCodeList[$x], $this->userCountryCode) == 0) {
                              echo "<option value=\"" . $this->countryCodeList[$x] . "\" selected >" . $this->countryNameList[$x] . "</option>\r";
                            }
                            else {
                              echo "<option value=\"" . $this->countryCodeList[$x] . "\" >" . $this->countryNameList[$x] . "</option>\r";
                            }
                            $x++;           
              		   			}
              		   		?>
          		   			</select><br>
          		   			<input type="button" name="profileCountrySubmit" id="profileCountrySubmit" value="<?php echo $this->languageHandler->getString('save_button')?>" class="button orange compact profileSubmitButton">
          		   			</div>
											<div id="profileCountryTextDiv">
              		   		<?php 
              		   			$x = 0;
              		   			$sumCount = count($this->countryCodeList);
              		   			while($x < $sumCount+1) {
              		   			  if(strcmp($this->countryCodeList[$x], $this->userCountryCode) == 0) {
                              echo '<a href="javascript:;" class="profileText" id="profileChangeCountryOpen">'.$this->countryNameList[$x].'</a>';
                              break;
                            }
                            $x++;           
              		   			}
              		   		?>
              		   	  <br>
											</div>
                      <br>
                      <?php 
                        if(intval($this->userBirthArray['year']) > 1900 && intval($this->userBirthArray['year']) != 0) {
                      ?>
                      <div id="profileBirthDiv">
                        <a href="javascript:;" class="profileText" id="profileChangeBirthOpen"><?php echo $this->languageHandler->getString('born_in').' '.$this->userBirthArray["month"].' '.$this->userBirthArray["year"]; ?></a><br>
                      </div>  
                      <div id="profileBirthDivOpened">
                      <a href="javascript:;" class="profileText" id="profileChangeBirthClose"><?php echo $this->languageHandler->getString('change_birth')?></a><br>
                      <?php 
                        $this->months = getMonthsArray($this->languageHandler);
                      ?>
                        <select id="profileMonth" name="profileMonth" class="profile" >
                      <?php // month 
                        $x = 0;
                        while($x < 13) {
                          $selected = "";
                          $monthValue = "value";
                          $monthString = "";
                          if($x == 0 && !$this->userBirthArray["month_id"]) {
                            $monthString = $this->languageHandler->getString('month');
                            $selected = "selected";
                          }
                          else if($x == 0 && $this->userBirthArray["month_id"]) {
                            $monthString = $this->languageHandler->getString('month');
                          }
                          else if($x == $this->userBirthArray["month_id"]) {
                            $selected = "selected";
                            $monthString = $this->months[$x];
                            $monthValue = "value=\"" . $x ."\"";
                          }                            
                          else {
                            $monthString = $this->months[$x];
                            $monthValue = "value=\"" . $x ."\"";
                          }
                          echo "<option " . $monthValue . " ". $selected .">" . $monthString . "</option>\r";
                          $x++; 
                        }
                        
                      ?>
                        </select> 
                        <select id="profileYear" name="profileYear" class="profile" >
                      <?php // year
                        $year_up = 0;
                        $year_down = date('Y') + 1;
                        while($year_up < 101) {
                          $year_down--;
                          $selected = "";
                          $yearValue = "";
                          $yearString = "";
                          if($year_up == 0 && !$this->userBirthArray["year"]) {
                            $yearValue = "value";
                            $selected = "selected";
                          }
                          else if($year_up == 0 && $this->userBirthArray["year"]) {
                            $yearValue = "value";
                            $yearString = $this->languageHandler->getString('year');     
                          }
                          else if($year_down == $this->userBirthArray["year"]) {
                            $yearValue = "value=\"" . $year_down ."\"";
                            $yearString = $year_down;
                            $selected = "selected";
                          }
                          else {
                            $yearValue = "value=\"" . $year_down ."\"";
                            $yearString = $year_down;
                          }
                          echo "<option " . $yearValue . " ". $selected .">" . $yearString . "</option>\r";
                          $year_up++;
                        }                     
                      ?>
                        </select><br>
                        <input type="button" name="profileBirthSubmit" id="profileBirthSubmit" value="<?php echo $this->languageHandler->getString('save_button')?>" class="button orange compact profileSubmitButton">
                      </div>
                      <br>
                      <?php 
                        } // end birthday if
                        else {
                      ?>
                      <div id="profileBirthDiv">
                        <a href="javascript:;" class="profileText" id="profileChangeBirthOpen"><?php echo $this->languageHandler->getString('add_your_birth_date'); ?></a><br>
                      </div>  
                      <div id="profileBirthDivOpened">
                      <a href="javascript:;" class="profileText" id="profileChangeBirthClose"><?php echo $this->languageHandler->getString('select_birth_year_month')?></a><br>
                      <?php 
                        $this->months = getMonthsArray($this->languageHandler);
                      ?>
                        <select id="profileMonth" name="profileMonth" class="profile" >
                      <?php // month 
                        $x = 0;
                        while($x < 13) {
                          if($x == 0) {
                            echo '<option value="" selected>'.$this->languageHandler->getString('month').'</option>';
                          }
                          else {
                            echo "<option value=\"" . $x . "\">" . $this->months[$x] . "</option>\r";
                          }
                          $x++;
                        }
                      ?>
                        </select> 
                        <select id="profileYear" name="profileYear" class="profile" >
                      <?php // year
                        $year_up = 0;
                        $year_down = date('Y') + 1;
                        while($year_up < 101) {
                          $year_down--;
                          if($year_up == 0) {
                            echo '<option value="" selected >'.$this->languageHandler->getString('year').'</option>';
                          }
                          else {
                            echo "<option value=\"" . $year_down . "\" >" . $year_down . "</option>\r";
                          }
                          $year_up++;
                        }                     
                      ?>
                        </select><br>
                        <input type="button" name="profileBirthSubmit" id="profileBirthSubmit" value="<?php echo $this->languageHandler->getString('save_button')?>" class="button orange compact profileSubmitButton">
                      </div>
                      <br>
                      <?php 
                        } // end birthday else
                        if($this->userGender) {
                      ?>
                        <div id="profileGenderDiv">
                          <a href="javascript:;" class="profileText" id="profileChangeGenderOpen"><?php echo $this->languageHandler->getString($this->userGender); ?></a><br>
                        </div>  
                        <div id="profileGenderDivOpened">
                          <a href="javascript:;" class="profileText" id="profileChangeGenderClose"><?php echo $this->languageHandler->getString('change_gender')?></a><br>
    											<select id="profileGender" name="profileGender" class="profile" >
                            <?php 
                              if(strcmp($this->userGender, "female") == 0)
                                $femaleSelected = "selected";
                              else if (strcmp($this->userGender, "male") == 0)
                                $maleSelected = "selected";
                              else 
                                $selected = "selected";
                            ?>
                            <option value="" <?php echo $selected ?> ><?php echo $this->languageHandler->getString('gender')?></option>
    												<option value="female" <?php echo $femaleSelected ?> ><?php echo $this->languageHandler->getString('female')?></option>
    												<option value="male" <?php echo $maleSelected ?> ><?php echo $this->languageHandler->getString('male')?></option>
                          </select>
                          <br>
                          <input type="button" name="profileGenderSubmit" id="profileGenderSubmit" value="<?php echo $this->languageHandler->getString('save_button')?>" class="button orange compact profileSubmitButton">
                        </div>
                        <br>
            		   	<?php 

                        } // end gender if
                        else {
                      ?>
                      <div id="profileGenderDiv">
                        <a href="javascript:;" class="profileText" id="profileChangeGenderOpen"><?php echo $this->languageHandler->getString('add_gender'); ?></a><br>
                      </div>  
                      <div id="profileGenderDivOpened">
                        <a href="javascript:;" class="profileText" id="profileChangeGenderClose"><?php echo $this->languageHandler->getString('gender')?></a><br>
  											<select id="profileGender" name="profileGender" class="profile" >
  												<option value="" selected><?php echo $this->languageHandler->getString('gender')?></option>
  												<option value="female"><?php echo $this->languageHandler->getString('female')?></option>
  												<option value="male"><?php echo $this->languageHandler->getString('male')?></option>
                        </select>
                        <br>
                        <input type="button" name="profileGenderSubmit" id="profileGenderSubmit" value="<?php echo $this->languageHandler->getString('save_button')?>" class="button orange compact profileSubmitButton">
                      </div>
                      <br>
            		   	<?php
                        } // end gender else  
                    ?><!--
                    
                      <div id="profileLanguageDiv">
                        <a href="javascript:;" class="profileText" id="profileChangeLanguageOpen"><?php echo $this->languageHandler->getString('change_language'); ?></a><br>
                      </div>  
                      --><div id="profileLanguageDivOpened">
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
                              }
                    ?>
                          <option <?php echo $selected?>value="<?php echo $lang?>"><?php echo $details['name'].' - '.$details['nameNative']?></option>
                    <?php 
                          }
                        } 
                    ?>
                        </select>

                        <br>
                      </div>
                      <div id="profileLanguageTextDiv">
                        <?php 
                          echo '<a href="javascript:;" class="profileText" id="profileChangeLanguageOpen">'.$this->languageString.'</a>';
                        ?>
                        <br>
                      </div>

                    <?php  
                      } // end own profile if
                      else { // start public profile
                    ?>
        		   			  <br>
                    <?php 
                        echo $this->languageHandler->getString('from');
                        if($this->userCity) {
                    ?>
                        <div id="profileCityDiv">
                    <?php echo $this->userCity; ?><br>
                        </div> 
                    <?php
                        } 
                    ?>
                      <div id="profileCountryTextDiv">
          		   		<?php 
          		   			  $x = 0;
          		   			  $sumCount = count($this->countryCodeList);
            		   			while($x < $sumCount+1) {
            		   			  if(strcmp($this->countryCodeList[$x], $this->userCountryCode) == 0) {
                            echo $this->countryNameList[$x];
                            break;
                          }
                          $x++;           
            		   			}
          		   	  ?>
          		   			</div>
                    <?php
                      }
                    ?> 
  

          		   			<br>
          		   			<br>
										
                  </div> <!-- profileFormContainer -->
								</div> <!-- profile Text -->
              </div> <!--  White Box -->            	
           </div> <!--  license Main -->  		   		
  		  </div> <!-- mainContent close //-->
  		</div> <!-- blueBoxMain close //-->
  	</div>

