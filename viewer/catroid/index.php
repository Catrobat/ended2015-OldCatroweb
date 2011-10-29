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
    <div id="catroidDescription" class="webMainMiddle">
      <div class="blueBoxMain">
        <div>
          <div class="projectListRow">
            <div class="whiteBoxMain whiteBoxGradient">
              <div class="infoboxRight">
                <div><img class="infoboxCloseButton" id="catroidDescriptionCloseButton" src="<?php echo BASE_PATH?>images/symbols/close.png" /></div>
                <div>
                  <a id="aIndexInfoboxScreenshotLink" href="http://www.youtube.com/watch?v=WTppqL6Q4Y4" target="_blank">
                    <img class="infoboxScreenshot" src="<?php echo BASE_PATH?>images/screenshots/infobox.png" />
                  </a>
                </div>
              </div>
              <div class="infoboxLeft">
                <div class="infoboxHeader"><?php echo $this->languageHandler->getString('infobox_heading')?></div>
                <div class="infoboxText"><?php echo $this->languageHandler->getString('infobox_text')?></div>
                <div class="infoboxDownloadBox">
                  <a class="noLink" id="aIndexInfoboxDownloadButton" href="http://code.google.com/p/catroid/downloads/list" target="_blank">
                    <button class="button orange infobox"><?php echo $this->languageHandler->getString('download_catroid')?></button>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- blueBoxMain close //-->
    </div> <!--  WEBMAINMIDDLE -->
    <div class="webMainMiddle">
      <div class="blueBoxMain">
        <div>
          <div id="projectListTitle" class="webMainContentTitle"></div>
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
