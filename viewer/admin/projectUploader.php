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
      <input name="fileChecksum" type="text" value="5a7e4a5a4da9a5f2e8ba91eb5424ad8d" />
    </p>

    <p>Known Checksums:</p>
    <pre>
      5a7e4a5a4da9a5f2e8ba91eb5424ad8d  /home/catroweb/Workspace/catroweb/tests/phpunit/api/testdata/test.catrobat
      cb5e8fdfba7db96bf2764191bad2befc  /home/catroweb/Workspace/catroweb/tests/phpunit/api/testdata/test.zip
      7f5fec6c47975cbcb11b8848ba6fcda7  /home/catroweb/Workspace/catroweb/tests/phpunit/api/testdata/test2.zip
      a810e424a5b4044c9b27e683feac080c  /home/catroweb/Workspace/catroweb/tests/phpunit/api/testdata/test_invalid_projectfile.zip
      7f2fe1c18bb49f0514168c4b3a2ef652  /home/catroweb/Workspace/catroweb/tests/phpunit/api/testdata/test_thumbnail.zip
      a70a14eaa842464bdae13d0aef55fd10  /home/catroweb/Workspace/catroweb/tests/phpunit/api/testdata/test-0.7.0beta.catrobat
    </pre>
    <input type="submit" name="uploadButton" value="upload" />
  </form>

  <br />
  <a id="aAdminToolsBackToCatroidweb" href="<?php echo BASE_PATH;?>admin/index">&lt;- back</a>
</body>
