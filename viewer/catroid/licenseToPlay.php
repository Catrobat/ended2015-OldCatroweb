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
      <p class="licenseText">
        <?php echo $this->languageHandler->getString('project_licence_part1')?>
        <br /> <br />
        <?php echo $this->languageHandler->getString('project_licence_part2')?>
        <br /> <br />
        <?php echo $this->languageHandler->getString('project_licence_part3')?>
      </p>
      <ul>
        <li><?php echo $this->languageHandler->getString('project_licence_part3_list_element1')?>
        </li>
        <li><?php echo $this->languageHandler->getString('project_licence_part3_list_element2')?>
        </li>
      </ul>
      <p class="licenseText">
        <?php echo $this->languageHandler->getString('project_licence_part4')?>
        <br /> <br />
        <?php echo $this->languageHandler->getString('project_licence_part5')?>
        <br /> <br />
        <?php echo $this->languageHandler->getString('project_licence_learn_more_new','<a class="license" href="'.BASE_PATH.'termsOfUse">'.BASE_PATH.'termsOfUse</a>')?>
        <br /> <br />
      </p>
      <p class="licenseText">
        <?php echo $this->languageHandler->getString('project_licence_dated')?>
        <br />
      </p>
    </div>  <!-- License Text -->
  </div>  <!--  license Main -->
  <div class="projectSpacer"></div>
</article>
