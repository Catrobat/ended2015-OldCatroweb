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
        new LanguageHandler();
      });
    </script>
    <div class="webMainBottom">
      <div class="blueBoxMain">
        <div class="webMainLicense"> 
          <a class="license" id="_privacy" href="<?php echo BASE_PATH?>catroid/privacypolicy"><?php echo $this->languageHandler->getString('template_footer_privacy_policy_link')?></a>
          <span class="webMainBottomSpacer">|</span>
          <a class="license" id="_termsofuse" href="<?php echo BASE_PATH?>catroid/termsofuse"><?php echo $this->languageHandler->getString('template_footer_terms_of_use_link')?></a>  	      
          <span class="webMainBottomSpacer">|</span>
          <a class="license" id="_copyright" href="<?php echo BASE_PATH?>catroid/copyrightpolicy"><?php echo $this->languageHandler->getString('template_footer_copyright_policy_link')?></a>
          <span class="webMainBottomSpacer">|</span>
          <a class="license" id="_imprint" href="<?php echo BASE_PATH?>catroid/imprint"><?php echo $this->languageHandler->getString('template_footer_imprint_link')?></a>
          <span class="webMainBottomSpacer">|</span>
          <select id="switchLanguage" class="languageSwitchSelect">
<?php 
  $supportedLanguages = getSupportedLanguagesArray($this->languageHandler);
    foreach($supportedLanguages as $lang => $details) {
      if($details['supported']) {
        $selected = "";
        if(strcmp($lang, $this->languageHandler->getLanguage()) == 0) {
          $selected = "selected ";
        }
?>
            <option <?php echo $selected?>value="<?php echo $lang?>"><?php echo $details['name'].' - '.$details['nameNative']?></option>
<?php }
    } ?>
          </select>
        </div>
      </div>
    </div> <!--  WEBMAINBOTTOM -->
