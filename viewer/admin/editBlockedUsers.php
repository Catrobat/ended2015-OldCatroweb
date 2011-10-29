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
 *    An additional term exception under section 7 of the GNU Affero
 *    General Public License, version 3, is available at
 *    http://www.catroid.org/catroid/licenseadditionalterm
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
  <script type="text/javascript">
  function blockUser(id, name) {
	    if (confirm("Block user '"+name+"'?"))
	      document.getElementById('addBlockedUser'+id).submit();
	  }
  function unblockUser(id, name) {
	    if (confirm("Unblock user '"+name+"'?"))
	      document.getElementById('removeBlockedUser'+id).submit();
	  }
  </script>
  <h2>Administration Tools - List of blocked users</h2>
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
          <th>Block user</th>
          <th>Unblock user</th>
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
            $block_user = "-";
            $unblock_user = "-";
          } else {
            if (!$user_id) {
              $block_user = "<form id=\"addBlockedUser$id\" class=\"admin\" action=\"addBlockedUser\" method=\"POST\">";
              $block_user.= "<input type=\"hidden\" name=\"blockUserValue\" value=\"".$id."\"><input type=\"button\" value=\"block user\" id=\"blockUser".$id."\" onclick=\"javascript:blockUser(".$id.", '".addslashes(htmlspecialchars($username))."');\">";
              $block_user.= "</form>";
              $unblock_user = "-";
            } else {
              $block_user = "-";
              $unblock_user = "<form id=\"removeBlockedUser$id\" class=\"admin\" action=\"removeBlockedUser\" method=\"POST\">";
              $unblock_user.= "<input type=\"hidden\" name=\"unblockUserValue\" value=\"".$id."\"><input type=\"button\" value=\"unblock user\" id=\"unblockUser".$id."\" onclick=\"javascript:unblockUser(".$id.", '".addslashes(htmlspecialchars($username))."');\">";
              $unblock_user.= "</form>";
            }
          }
      ?>
	    <tr>
          <td><?php echo $id ?></td>
          <td><?php echo $username ?></td>
          <td><?php echo $email ?></td>
          <td><?php echo $gender ?></td>
          <td><?php echo $country ?></td>
          <td><?php echo $block_user ?></td> 
          <td><?php echo $unblock_user ?></td> 
        </tr>
      <?php           
        $i++;
        }}?>
      </table>

  </div>
</body>