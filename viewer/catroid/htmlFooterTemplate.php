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
      <div>
        <div id="largeFooterMenu">
          <div>
            <ul style="float: left;">
              <li><a href="http://catrobat.org/" target="_blank"><?php echo $this->languageHandler->getString('template_footer_about')?></a></li>
              <li><a href="https://play.google.com/store/apps/details?id=org.catrobat.catroid" target="_blank"><?php echo $this->languageHandler->getString('template_footer_google_play')?></a></li>
              <li><a href="http://developer.catrobat.org/privacy_policy" target="_blank"><?php echo $this->languageHandler->getString('template_footer_privacy_policy_link')?></a></li>
            </ul>
           <ul style="padding-left: 150px;">
              <!-- <li><a href="<?php echo BASE_PATH;?>tutorial"><?php echo $this->languageHandler->getString('template_footer_tutorial_link')?></a></li>-->
              <li><a href="<?php echo BASE_PATH?>termsOfUse"><?php echo $this->languageHandler->getString('template_footer_terms_of_use_link')?></a></li>
              <li><a href="<?php echo BASE_PATH?>licenseToPlay"><?php echo $this->languageHandler->getString('template_footer_rights_reserved');?></a></li>
              <li><a href="http://developer.catrobat.org/imprint" target="_blank"><?php echo $this->languageHandler->getString('template_footer_imprint_link')?></a></li>
            </ul> 
          </div>
          <div>
            <span id="footerSearchButton" class="img-magnifying-glass"></span>
            <span>
               <header><?php echo $this->languageHandler->getString('template_footer_search_header'); ?></header>
               <input type="search" placeholder="<?php echo $this->languageHandler->getString('template_header_search_placeholder'); ?>" />
            </span>
          </div>
        </div>
        <div class="footerMenu">
          <div>
            <select id="switchLanguage">
<?php 
  $supportedLanguages = getSupportedLanguagesArray($this->languageHandler);
    foreach($supportedLanguages as $lang => $details) {
      if($details['supported']) {
        $selected = "";
        if(strcmp($lang, $this->languageHandler->getLanguage()) == 0) {
          $selected = "selected ";
        }
?>
              <option <?php echo $selected?>value="<?php echo $lang?>"><?php echo $details['nameNative']?></option>
<?php }
    } ?>
            </select>
          </div>
          <div id="copyright">
            <a href="http://catrobat.org/" target="_blank">&copy; Catrobat</a>
          </div>
          <div>
            <span id="footerMoreButton">
              <span class="img-more"></span> <?php echo $this->languageHandler->getString('template_footer_more')?>
            </span>
            <span id="footerLessButton">
              <span class="img-less"></span> <?php echo $this->languageHandler->getString('template_footer_less')?>
            </span>
          </div>
        </div>
      </div>

      <div id="mobileFooterMenu">
        <ul>
          <!-- <li><a href="<?php echo BASE_PATH;?>tutorial"><?php echo $this->languageHandler->getString('template_footer_tutorial_link')?></a></li>-->
          <li><a href="http://catrobat.org/" target="_blank"><?php echo $this->languageHandler->getString('template_footer_about')?></a></li>
              <li><a href="https://play.google.com/store/apps/details?id=org.catrobat.catroid" target="_blank"><?php echo $this->languageHandler->getString('template_footer_google_play')?></a></li>
          <li><a href="http://developer.catrobat.org/privacy_policy" target="_blank"><?php echo $this->languageHandler->getString('template_footer_privacy_policy_link')?></a></li>
          <li><a href="<?php echo BASE_PATH?>termsOfUse"><?php echo $this->languageHandler->getString('template_footer_terms_of_use_link')?></a></li>
          <li><a href="<?php echo BASE_PATH?>licenseToPlay"><?php echo $this->languageHandler->getString('template_footer_rights_reserved');?></a></li>
          <li><a href="http://developer.catrobat.org/imprint" target="_blank"><?php echo $this->languageHandler->getString('template_footer_imprint_link')?></a></li>
        </ul>
      </div>
        
      <script type="text/javascript">
        $(document).ready(function() {
          new Footer();
          new LanguageHandler();
        });
      </script>
      
