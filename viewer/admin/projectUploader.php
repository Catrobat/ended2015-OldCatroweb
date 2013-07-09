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
      <input name="fileChecksum" type="text" value="60c06bf702f4bc6ae1c5196be9b972ec" />
    </p>

    <p>Known Checksums:</p>
    <pre>
      60c06bf702f4bc6ae1c5196be9b972ec  /home/catroweb/Workspace/catroweb/tests/phpunit/api/testdata/test.catrobat
      14a5b75f6092726dbd5df8d12dc5aaf7  /home/catroweb/Workspace/catroweb/tests/phpunit/api/testdata/test.zip
      6e12cd534a77ad3a254527edacc05b74  /home/catroweb/Workspace/catroweb/tests/phpunit/api/testdata/test2.zip
      a810e424a5b4044c9b27e683feac080c  /home/catroweb/Workspace/catroweb/tests/phpunit/api/testdata/test_invalid_projectfile.zip
      3196f1c81917733747b52bea96cdd4e4  /home/catroweb/Workspace/catroweb/tests/phpunit/api/testdata/test_thumbnail.zip
      267b40d354a2b8aa344cbf8a8d7dcffb  /home/catroweb/Workspace/catroweb/tests/phpunit/api/testdata/test-0.7.0beta.catrobat
    </pre>
    <input type="submit" name="uploadButton" value="upload" />
  </form>

  <br />
  <a id="aAdminToolsBackToCatroidweb" href="<?php echo BASE_PATH;?>admin/index">&lt;- back</a>
</body>
