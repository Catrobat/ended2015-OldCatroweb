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
    <div class="webMainMiddle">
      <div class="blueBoxMain">
        <div>
          <div id="projectListTitle" class="webMainContentTitle"></div>          
          <div class="whiteBoxMain whiteBoxGradient sortLinkContainer">
            <div id="filterContainer" class="whiteBoxMain whiteBoxGradient sortLinkContainer">
              <?php foreach ($this->links as $link):?>
                <a id="<?php echo 'sortby_'.$link['title']?>" href="#<?php //echo $link['url'].$searchUrl; ?>" class="sortLink <?php echo $link['style']?>"><img src="<?php echo $link['image']; ?>" ><?php echo $link['title']; ?></a>
                <span style="padding-left: 15px; padding-right: 15px"></span>
              <?php endforeach;?>
            </div>
          </div>
          <div id="projectContainer">
            <noscript>
               <div class="projectListRow">
                 <div class="whiteBoxMain">
                   <div class="webMainContentTitle"><?php echo $this->languageHandler->getString('enable_javascript')?></div>                   
                 </div>
               </div>
            </noscript>  
          </div>        
        </div> <!-- mainContent close //-->
      </div> <!-- blueBoxMain close //-->
    </div> <!--  WEBMAINMIDDLE -->
