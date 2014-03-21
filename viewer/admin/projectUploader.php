<?php
/*
 * Catroid: An on-device visual programming system for Android devices
 * Copyright (C) 2010-2014 The Catrobat Team
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

<body>
  <h2>Project Uploader</h2>
  
  <form action="<?php echo BASE_PATH;?>api/upload/upload.json" method="post" enctype="multipart/form-data">
    <p>Project Title:<br />
      <input name=projectTitle type="text" />
    </p>
    <p>Project Description:<br />
      <textarea name="projectDescription"></textarea>
    </p>
    <p>User eMail:<br />
      <input name="userEmail" type="text" value="webmaster@catroid.org" />
    </p>
    <p>Username:<br />
      <input name="username" type="text" value="catroweb" />
    </p>
    <p>Token:<br />
      <input name="token" type="text" value="31df676f845b4ce9908f7a716a7bfa50" />
    </p>
    <p>Please select a project for upload:<br />
      <input name="upload" type="file" />
    </p>
    <p>Checksum:<br />
      <input name="fileChecksum" type="text" value="95c94843a95062bb8f83336df21cd2db" />
    </p>

    <p>Found Checksums (realtime, on Host):</p>
    <pre>
<?php foreach($this->md5FileList as $sum=>$filePath)
    {
      echo $sum."  ".$filePath."\n";
    }
?>
    </pre>
    <input type="submit" name="uploadButton" value="upload" />
  </form>

  <br />
  <a id="aAdminToolsBackToCatroidweb" href="<?php echo BASE_PATH;?>admin/index">&lt;- back</a>
</body>
