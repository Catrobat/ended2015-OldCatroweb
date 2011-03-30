<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team 
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or License, or (at your option) any later version.
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
Upload File:<br>
<?php 
  echo 'Answer: '.$this->answer.'<br/>';
?>
<form method="post" action="./upload" enctype="multipart/form-data">
  <input type="file" name="upload"/>
  <br />
  <input type="text" name="projectTitle"/>
  <br />
  <textarea rows="5" cols="40" name="projectDescription"></textarea> 
  <br />
  <input type="submit" name="submit_upload" value="upload" />  
</form>
</body>
