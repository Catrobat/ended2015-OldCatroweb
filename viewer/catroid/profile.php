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
          "emailCount" : "<?php echo count($this->userEmailsArray); ?>",
          "emailDeleteAlertTitle" : "<?php echo $this->languageHandler->getString('alertbox_really_want_to_delete_email'); ?>",
  	      "addNewEmailButtonLanguageString" : "<?php echo $this->languageHandler->getString('add_new_email_button'); ?>",
  	      "addNewEmailLanguageString" : "<?php echo $this->languageHandler->getString('add_new_email'); ?>",
          "addNewEmailPlaceholderLanguageString" : "<?php echo $this->languageHandler->getString('add_new_email_placeholder'); ?>",
          "changeEmailLanguageString" : "<?php echo $this->languageHandler->getString('email'); ?>",
          "changeEmailDeleteButtonLanguageString" : "<?php echo $this->languageHandler->getString('delete_email'); ?>",
          "changeEmailSaveChangesLanguageString" : "<?php echo $this->languageHandler->getString('save_button'); ?>"
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
                  	<form method="POST" name="profileFormDialog" id="profileFormDialog" action="">
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
                          $x = 0;
                          for($x; $x < count($this->userEmailsArray); $x++) {
                            if($x < count($this->userEmailsArray)-1) { ?>
            		   			      <div id="div<?php echo $x; ?>"><a href="javascript:;" class="profileText" id="<?php echo $x; ?>"><?php echo $this->userEmailsArray[$x]; ?></a></div>
                        <?php }
                            else { ?>
                              <div id="div<?php echo $x; ?>"><a href="javascript:;" class="profileText" id="<?php echo $x; ?>"><?php echo $this->userEmailsArray[$x]; ?></a></div>
                              <input type="button" name="buttonProfileOpenAddNewEmailField" id="buttonProfileOpenAddNewEmailField" value=" <?php echo $this->languageHandler->getString('add_email_field_button') ?> " class="button orange compact profileSubmitButton">
                        <?php }
                          }
                        ?>
          		   			</div>
                      <div id="profileAdditionalEmailInputFields">
                        <div id="emailTextFields">
                        </div>
                        <div id="emailAddButton">
                          <input type="button" name="buttonProfileSaveNewEmailSubmit" id="buttonProfileSaveNewEmailSubmit" value="<?php echo $this->languageHandler->getString('add_new_email_button')?>" class="button orange compact profileSubmitButton"> <input type="button" name="buttonProfileCloseAddNewEmailField" id="buttonProfileCloseAddNewEmailField" value=" <?php echo $this->languageHandler->getString('cancel') ?> " class="button orange compact profileSubmitButton">
                        </div>
                      </div>
          		   			<br>
          		   			<div id="profileCountryDiv">
          		   			  <a href="javascript:;" class="profileText" id="profileChangeCountryClose"><?php echo $this->languageHandler->getString('country')?></a><br>
          		   			  <select id="profileCountry" name="profileCountry" class="profile" required="required" >
              		   		<?php // country 
              		   			$x = 0;
              		   			$sumCount = count($this->countryCodeList);
              		   			while($x < $sumCount+1) {
              		   			  if($x == 0) {
                              echo '<option value="0" >select your country</option>\r';
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
                              echo 'from <a href="javascript:;" class="profileText" id="profileChangeCountryOpen">'.$this->countryNameList[$x].'</a>';
                              break;
                            }
                            $x++;           
              		   			}
              		   		?>
              		   	  <br>
											</div>
                      <br>
                      <div id="profileCityDiv">
                        <a href="javascript:;" class="profileText" id="profileChangeCityOpen"><?php echo $this->languageHandler->getString('city')?></a><br>
                      </div>  
                      <div id="profileCityDivOpened">
                        <a href="javascript:;" class="profileText" id="profileChangeCityClose"><?php echo $this->languageHandler->getString('city')?></a><br>
                        <input type="text" id="profileCity" name="profileCity" value="<?php echo htmlspecialchars($this->postData['profileCity']); ?>" required="required" placeholder="<?php echo $this->languageHandler->getString('new_city')?>" ><br>
                        <input type="button" name="profileCitySubmit" id="profileCitySubmit" value="<?php echo $this->languageHandler->getString('save_button')?>" class="button orange compact profileSubmitButton">
                      </div>
                      <br>
                      <div id="profileBirthDiv">
                        <a href="javascript:;" class="profileText" id="profileChangeBirthOpen"><?php echo $this->languageHandler->getString('birth')?></a><br>
                      </div>  
                      <div id="profileBirthDivOpened">
                        <a href="javascript:;" class="profileText" id="profileChangeBirthClose"><?php echo $this->languageHandler->getString('birth')?></a><br>
                        <input type="text" id="profileBirth" name="profileBirth" value="<?php echo htmlspecialchars($this->postData['profileBirth']); ?>" required="required" placeholder="<?php echo $this->languageHandler->getString('new_birth')?>" ><br>
                        <input type="button" name="profileBirthSubmit" id="profileBirthSubmit" value="<?php echo $this->languageHandler->getString('save_button')?>" class="button orange compact profileSubmitButton">
                      </div>
                      <br>
                      <div id="profileGenderDiv">
                        <a href="javascript:;" class="profileText" id="profileChangeGenderOpen"><?php echo $this->languageHandler->getString('gender')?></a><br>
                      </div>  
                      <div id="profileGenderDivOpened">
                        <a href="javascript:;" class="profileText" id="profileChangeGenderClose"><?php echo $this->languageHandler->getString('gender')?></a><br>
                        <input type="text" id="profileGender" name="profileGender" value="<?php echo htmlspecialchars($this->postData['profileGender']); ?>" required="required" placeholder="<?php echo $this->languageHandler->getString('new_gender')?>" ><br>
                        <input type="button" name="profileGenderSubmit" id="profileGenderSubmit" value="<?php echo $this->languageHandler->getString('save_button')?>" class="button orange compact profileSubmitButton">
                      </div>
                      <br>
            		   	</form>
            		   	<?php 
                      }
                      else {
                    ?>
                    <div id="profileCountryTextDiv">
          		   		<?php 
          		   			$x = 0;
          		   			$sumCount = count($this->countryCodeList);
          		   			while($x < $sumCount+1) {
          		   			  if(strcmp($this->countryCodeList[$x], $this->userCountryCode) == 0) {
                          echo 'from '.$this->countryNameList[$x];
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

