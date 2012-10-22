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
  <h2>Administration Tools</h2>
  <a id="aAdminToolsRemoveInconsitantProjectFiles" href="<?php echo BASE_PATH;?>admin/tools/removeInconsistantProjectFiles">remove inconsistant project files</a><br />
  <a id="aAdminToolsEditProjects" href="<?php echo BASE_PATH;?>admin/tools/editProjects">edit projects</a><br />
  <a id="aAdminToolsThumbnailUploader" href="<?php echo BASE_PATH;?>admin/tools/thumbnailUploader">thumbnail uploader</a><br />
  <a id="aAdminToolsInappropriateProjects" href="<?php echo BASE_PATH;?>admin/tools/inappropriateProjects">inappropriate projects</a><br />
  <a id="aAdminToolsApproveWords" href="<?php echo BASE_PATH;?>admin/tools/approveWords">approve unapproved words</a><br />
  <br />
  <a id="aAdminToolsLanguageManagement" href="<?php echo BASE_PATH;?>admin/languageManagement">manage Languages</a><br />
  <br />
  <a id="aAdminToolsBlockIp" href="<?php echo BASE_PATH;?>admin/tools/editBlockedIps">block IPs</a><br />
  <a id="aAdminToolsBlockUser" href="<?php echo BASE_PATH;?>admin/tools/editBlockedUsers">block Users</a><br />
  <br />
  <a id="aAdminToolsUpdateBrowserDetection" href="<?php echo BASE_PATH;?>admin/tools/updateBrowserDetection">update browser-detection RegEx-pattern</a><br />
  <br />
  <a id="aAdminToolsBackToCatroidweb" href="<?php echo BASE_PATH;?>admin/index">&lt;- back</a>
  <br /><br />
  <?php if($this->answer) {
    echo 'Answer:<br/>'.$this->answer.'<br />';
  }?>
</body>
