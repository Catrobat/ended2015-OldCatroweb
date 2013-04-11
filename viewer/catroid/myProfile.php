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
  <!-- <header><?php echo $this->languageHandler->getString('myTitle');  $this->userData['username'];?></header> -->
     <header><?php echo $this->userData['username'];?></header> 
        <div class="left">
           <img src="<?php echo BASE_PATH; ?>images/symbols/placeholder.png" />
         </div>
         <div class="middle">
           <div class="profileItem" >
             <input type="search" placeholder="<?php echo $this->languageHandler->getString('old_password') ?>" />
             <img src="<?php echo BASE_PATH; ?>images/symbols/add.png" />
           </div>
           <div class="profileItem">
             <input type="search" placeholder="<?php echo $this->languageHandler->getString('new_password') ?>" />
             <img src="<?php echo BASE_PATH; ?>images/symbols/add.png" />
           </div>
           <div><?php echo $this->languageHandler->getString('country') ?>:</div>
           <div class="profileSelect">
              <select><?php echo $this->countryCodeListHTML;   ?></select>
           </div>
         </div>
         <div class="right">
           <div class="profileItem" >
             <input type="search" placeholder=<?php echo $this->languageHandler->getString('change_my_password') ?> />
             <img src="<?php echo BASE_PATH; ?>images/symbols/add.png" />
           </div>
           <div class="profileItem">
             <input type="search" placeholder=<?php echo $this->userData['username']; ?> />
             <img src="<?php echo BASE_PATH; ?>images/symbols/add.png" />
           </div>
         </div>
          
  </article>
  
  <script type="text/javascript">
      $(document).ready(function() {
        var languageStringsObject = { 
          "really_delete" : "<?php echo $this->languageHandler->getString('really_delete'); ?>",
          "image_too_big" : "<?php echo $this->languageHandler->getString('image_too_big'); ?>"
        };
        new Profile(languageStringsObject);
      });
  </script>
      
