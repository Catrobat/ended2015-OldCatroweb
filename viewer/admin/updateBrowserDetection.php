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
<body>
  <h2>Administration Tools - Update browser-detection RegEx-Pattern</h2>
  <a id="aAdminToolsBackToCatroidweb" href="<?php echo BASE_PATH;?>admin/tools">&lt;- back</a><br /><br />
  <?php if($this->answer) {
    echo 'Answer:<br/>'.$this->answer.'<br /><br/>';
  }?>
  <div class="overview">

			  <form id="browserDetection" class="admin" action="updateBrowserDetection" method="POST">
          Current RegEx for Mobile-Browser-Detection:<br />
          <textarea readonly style="height: 220px; width: 98%;"><?php echo $this->currentRegEx; ?></textarea>
          <br /><br />
          Will be updated to the following new RegEx-Pattern:<br />  
          <textarea readonly style="height: 220px; width: 98%;"><?php echo $this->newRegEx; ?></textarea>  
			  
<?php 

$currentPart = strlen($this->currentRegEx);
$newPart = strlen($this->newRegEx);

if ($currentPart != $newPart && $newPart >= 100) {
  print "<div style=\"color: red\">A regex-pattern update is available.</div>";
?>
			    <input type="hidden" name="action" value="update">
			    <br />
          <input type="submit" value="Update regex-pattern now!" name="submitButton" id="submit" />

<?php 
} else {
  print "<div style=\"color: green\">There current code is up-to-date.</div>";
}

?>
  </form>
        
  </div>
</body>
