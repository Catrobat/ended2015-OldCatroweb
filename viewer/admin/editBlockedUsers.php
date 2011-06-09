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
  function submitRemoveUserForm(id, name) {
    if (confirm("Remove blocking of User '"+name+"'?"))
      document.getElementById(id).submit();
  }
  function submitBlockUserForm(id) {
	    document.getElementById(id).submit();
	}
  </script>
  <h2>Administration Tools - List of blocked users</h2>
  <a id="aAdminToolsBackToCatroidweb" href="<?php echo BASE_PATH;?>admin/tools">&lt;- back</a><br /><br />
  <?php if($this->answer) {
    echo 'Answer:<br/>'.$this->answer.'<br /><br/>';
  }?>
  <div class="projectList">

			  Add new username to block: 
			  <form id="newblockuserform" class="admin" action="addBlockedUser" method="POST">
          <input type="text" name="blockuser" value=""/>
          <input type="button" value="add username" name="addButton" id="adduser" onclick="submitBlockUserForm('newblockuserform');" />
        </form>

			<br/>
			<br/>

      <table class="projectTable">
        <tr>
          <th>Username</th>
          <th>Remove blocking</th>
        </tr>
      <?php
        $i=0;
        if($this->blockedusers) {
        foreach($this->blockedusers as $blockeduser) {
          $user = $blockeduser["user_name"];
          ?>
        <tr>
          <td><?php echo $user ?></td>
          <td>
            <form id="removeuserform<?php echo $i ?>" class="admin" action="removeBlockedUser" method="POST">
              <input type="hidden" name="blockeduser" value="<?php echo $user ?>"/>
              <input type="hidden" name="remove" value="remove"/>
              <input type="button" value="remove" name="removeButton" id="<?php echo "removeuser$i"; ?>" onclick="javascript:submitRemoveUserForm('removeuserform<?php echo $i ?>', '<?php echo $user ?>');" />
            </form>
          </td>
        </tr>
      <?php           
        $i++;
        }}?>
      </table>
  </div>
</body>