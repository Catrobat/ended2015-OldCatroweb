<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2012 The Catroid Team 
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
     <div class="webMainMiddle">
        <div class="blueBoxMain">
          <div class="webMainContentTitle">
            <?php echo $this->languageHandler->getString('title'); ?>
          </div> <!-- webMainContentTitle -->
          <div class="whiteBoxMain menuListRow">
            <div class="table">
              <div class="cell" style="width: 54%">
                <div class="header"><?php echo $this->languageHandler->getString('table_header_title'); ?></div>
              
                <?php
                  if($this->projects != NULL) {
                    foreach($this->projects as $row) {
                      echo "<div><a class='projectListDetailsLinkBold' href='" . BASE_PATH . "catroid/details/" . $row["id"] . "'>" . $row["title"] . "</a></div>";
                    }
                  } else {
                    echo "<div>" . $this->languageHandler->getString('no_projects') . "</div>";
                  }
                ?>
              </div>

              <div class="cell align-right " style="width: 33%">
                <div class="header"><?php echo $this->languageHandler->getString('table_header_last_activity'); ?></div>
              
                <?php
                  if($this->projects != NULL) {
                    foreach($this->projects as $row) {
                      $date = $row["last_activity"];
                      echo "<div>"  . getTimeInWords($date, $this->languageHandler, time()) . "</div>";
                    }
                  }
                ?>
              </div>

              <div id="deleteButtons" class="cell align-right" style="width: 7%">
                <div class="delete-header">&nbsp;</div>
                <?php
                  if($this->projects != NULL) {
                    foreach($this->projects as $row) {
                      echo "<div><button class='button orange compact' id='" . $row['id'] . "' name='" . $row['title'] . "'><img src='" . BASE_PATH . "images/symbols/trash_recyclebin.png' width='24px' /></button></div>";
                    }
                  }
                ?>
              </div>
            </div>
            <div style="clear:both"></div>
          </div>            
        </div> <!-- blueBoxMain close //-->
      </div>
      <script type="text/javascript">
        new MyProjects({ really_delete: <?php echo "'".$this->languageHandler->getString('really_delete')."'"; ?> });
      </script>

