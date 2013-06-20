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
    <div class="header"><?php echo $this->languageHandler->getString('title')?></div>
    <div class="licenseMain">            	
      <div class="licenseText">
        <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_use_welcome')?></p>
        <ul>
          <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_1')?></li>
          <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_2')?></li>
          <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_3')?></li>
        </ul>
        <br />
        <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_use_your_support')?></p>
        <ul>
          <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_4')?></li>
          <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_5')?></li>
          <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_6')?></li>
          <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_7')?></li>
          <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_8')?></li>
        </ul>
        <br />
        <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_use_become_a_member')?></p>
        <ul>
          <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_9')?></li>
          <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_10')?></li>
          <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_11')?></li>
          <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_12', '<a href="http://developer.catrobat.org/licenses_of_uploaded_catrobat_programs" target="_blank">developer.catrobat.org/licenses_of_uploaded_catrobat_programs</a>')?></li>
        </ul>
        <br />
        <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_use_our_gift')?></p>
        <ul>
          <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_13')?></li>
          <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_14')?></li>
          <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_15', '<a href="http://developer.catrobat.org/licenses" target="_blank">developer.catrobat.org/licenses</a>', '<a href="http://developer.catrobat.org" target="_blank">developer.catrobat.org</a>')?></li>
        </ul>
        <br />
        <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_use_our_terms')?></p>
        <ul>
          <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_16')?></li>
          <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_17')?></li>
          <li><?php echo $this->languageHandler->getString('terms_of_use_enumeration_18', '<a href="http://developer.catrobat.org/terms_of_service" target="_blank">developer.catrobat.org/terms_of_service</a>')?></li>
        </ul>
        <br />
        <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_use_check_back')?></p>
        <br />
        <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_use_mail_us', '<a href="'.impedeCrawling("mailto:".CONTACT_EMAIL).'?subject='.rawurlencode($this->languageHandler->getString('title')).'">'.impedeCrawling(CONTACT_EMAIL).'</a>')?></p>
        <br />
        <p class="licenseText"><?php echo $this->languageHandler->getString('terms_of_use_dated')?></p>
      </div> <!-- License Text -->    	
    </div> <!--  license Main -->
    <div class="projectSpacer"></div>
  </article>
