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
  function addEmailForm() {
    document.getElementById('enterEmailForm').submit();
  }

  function removeEmailForm(id) {
    if(confirm('Do you really want to delete this email adress?')) {
      document.getElementById('removeEmailForm_' + id).submit();
    }
  }
  </script>


  <h2>Administration Tools - Upload Notifications List</h2>
  <br />
  <a id="aAdminToolsBackToCatroidweb" href="<?php echo BASE_PATH;?>admin/tools">&lt;- back</a><br /><br />
  
  <?php if($this->answer) {
    echo 'Answer:<br/>'.$this->answer.'<br /><br/>';
  }?>
  
  <form id="enterEmailForm" class="admin" action="uploadNotificationsList" method="POST">
    <input type="hidden" name="uploadNotificationsAdd" />
    <input type="text" name="email" placeholder="email..." id="emailInput"/>
    <input type="button" value="add email" name="addButton" id="addEmail" onclick="javascript:addEmailForm();" />
  </form>
  
  <br />
  
  <div class="projectList">
    <table class="projectTable" id="projectTableId">
      <tr>
        <th>E-Mail</th>
        <th>delete</th>
      </tr>
      
      <?php if(!$this->emailList) : ?>
      <tr>
        <td colspan=2>No entry</td>
      </tr>
      <?php else: ?>
        <?php foreach($this->emailList as $email) : ?>
        <tr>
          <td><?php echo $email['email']; ?></td>
          <td>
            <form id="removeEmailForm_<?php echo $email['id']; ?>" class="admin" action="uploadNotificationsList" method="POST">
              <input type="hidden" name="uploadNotificationsRemove" />
              <input type="hidden" name="id" value="<?php echo $email['id']; ?>" />
              <input type="button" name="deleteButton" value="delete" id="deleteEmail" onclick="javascript:removeEmailForm(<?php echo $email['id']; ?>);" />
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      
    </table>
  </div>
  
</body>
