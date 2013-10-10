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

    <p>Known Checksums:</p>
    <pre>
      95c94843a95062bb8f83336df21cd2db  /home/catroweb/Workspace/catroweb/tests/phpunit/api/testdata/test.catrobat
      d20c3ca0d3cd601582510fe6aca3ad0e  /home/catroweb/Workspace/catroweb/tests/phpunit/api/testdata/test.zip
      fe312b4d2f8fe0a04fa1b9ca0a9bbdbe  /home/catroweb/Workspace/catroweb/tests/phpunit/api/testdata/test2.zip
      a810e424a5b4044c9b27e683feac080c  /home/catroweb/Workspace/catroweb/tests/phpunit/api/testdata/test_invalid_projectfile.zip
      f1a71e31eb7cfff5cc2b5272b5e08845  /home/catroweb/Workspace/catroweb/tests/phpunit/api/testdata/test_thumbnail.zip
      a6c78067ea4f5340c631b9529c2f7b99  /home/catroweb/Workspace/catroweb/tests/phpunit/api/testdata/test-0.7.0beta.catrobat
    </pre>
    <input type="submit" name="uploadButton" value="upload" />
  </form>

  <br />
  <a id="aAdminToolsBackToCatroidweb" href="<?php echo BASE_PATH;?>admin/index">&lt;- back</a>
</body>
