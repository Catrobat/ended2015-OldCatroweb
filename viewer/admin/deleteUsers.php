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
  <script type="text/javascript">
  function deleteUsers(id, name) {
	    if (confirm("Delete user '"+name+"' and his/her projects?")) {
		    //alert("ID: '"+id+"' name: '"+name+"'?");
	      document.getElementById('deletingUser'+id).submit();
	    }
	  }
  </script>
  <h2>Administration Tools - List for deleting users - ATTENTION DELETING CANT BE UNDONE</h2>
  <a id="aAdminToolsBackToCatroidweb" href="<?php echo BASE_PATH;?>admin/tools">&lt;- back</a><br /><br />
  <?php if($this->answer) {
    echo 'Answer:<br/>'.$this->answer.'<br /><br/>';
  }?>
  <div class="projectList">

		  <p>List of all users:</p>

      <table class="projectTable">
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>E-Mail</th>
          <th>Gender</th>
          <th>Country</th>
          <th>Delete user</th>
        </tr>
      <?php
        $i=0;
        if($this->allusers) {
        foreach($this->allusers as $alluser) {
          $id = $alluser["id"];
          $username = $alluser["username"];
          $email = $alluser["email"];
          $gender = $alluser["gender"];
          $country = $alluser["country"];
          $user_id = $alluser["user_id"];
          if ($id < 1) {
            $delete_user = "-";
          } else {
              $delete_user = "<form id=\"deletingUser$id\" class=\"admin\" action=\"deletingUser\" method=\"POST\">";
              $delete_user.= "<input type=\"hidden\" name=\"deleteUserValue\" value=\"".$id."\">";
              $delete_user.= "<input type=\"button\" value=\"delete user\" id=\"deleteUser".$id."\" onclick=\"javascript:deleteUsers(".$id.", '".addslashes(htmlspecialchars($username))."');\">";
              $delete_user.= "</form>";
          }
      ?>
	    <tr>
          <td><?php echo $id ?></td>
          <td><?php echo $username ?></td>
          <td><?php echo $email ?></td>
          <td><?php echo $gender ?></td>
          <td><?php echo $country ?></td>
          <td><?php echo $delete_user ?></td> 
        </tr>
      <?php           
        $i++;
        }}?>
      </table>

  </div>
</body>
