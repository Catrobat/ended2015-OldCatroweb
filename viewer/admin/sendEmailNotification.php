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
  <script type="text/javascript">
  function toggleSelectAll() {
    var val = (chkboxSelectAll.checked);
    $('.chkBoxEmail').each(function(i, obj) {
      $(obj).attr('checked', val);
    });
    if(!val) {
      $("#labelSelectAll").text("Select all");
    }
    else {
      $("#labelSelectAll").text("Deselect all");
    }
    updateSelectCount();
  }

  function updateSelectCount() {
    var selected = 0;
    $('.chkBoxEmail').each(function(i, obj) {
      if($(obj).attr('checked')) {
        selected += 1;
      }
    });
    $("#selectCount").text(selected + " user(s) selected.");
  }

  function sendEmail() {
    var count = 0;
    console.log("sendEmail");
    $('.chkBoxEmail').each(function(i, obj) {
      if ($(obj).attr('checked')) {
        count += 1;
      }
    });
    if(count == 99) {
     // alert("No e-mail adresses selected!");
    }
    else {
      if (confirm("Really send '"+count +"' e-mails?")) {
        document.getElementById('sendEmailNotificationSubmit').submit();
      }
    }
  }
  
  </script>
  <h2>Administration Tools - Send e-mail Notification</h2>
  <a id="aAdminToolsBackToCatroidweb" href="<?php echo BASE_PATH;?>admin/tools">&lt;- back</a><br /><br />
  <?php if($this->answer) {
    echo 'Answer:<br/>'.$this->answer.'<br /><br/>';
  }?>
  <div class="projectList">
		  <p>List of all users:</p>
      <label id="labelSelectAll">Select all</label>
      <input type="checkbox" id="chkboxSelectAll" value="0" onclick="javascript:toggleSelectAll();"/>
      <p id="selectCount">0 user(s) selected</p>
      <br/><br/>
      
      <form id="sendEmailNotificationSubmit" class="admin" action="sendEmailNotification" method="POST">
        <input type="hidden" name="sendEmailNotification">
        <table class="projectTable">
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>E-Mail</th>
            <th>Gender</th>
            <th>Country</th>
            <th>Blocked user</th>
            <th>Send E-Mail?</th>
          </tr>
        <?php
          $i=0;
          if($this->allusers) {
          foreach($this->allusers as $alluser) {
            $chkbox_send = "";
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
                $block_user = "-";              
              } else {
                //TODO: send blocked users email?
              }
              $checkbox_send= "<input type = \"checkbox\" ";
              $checkbox_send.= " name=\"username[]\" ";
              $checkbox_send.= "id = \"sendEmail".$id."\" ";
              $checkbox_send.= "class = \"chkBoxEmail\" ";
              $checkbox_send.= "onclick=\"javascript:updateSelectCount();\"";
              $checkbox_send.= "value = \"".$username."\" />";
            }
        ?>
  	    <tr>
            <td><?php echo $id ?></td>
            <td><?php echo $username ?></td>
            <td><?php echo $email ?></td>
            <td><?php echo $gender ?></td>
            <td><?php echo $country ?></td>
            <td><?php echo $block_user ?></td> 
            <td><?php echo $checkbox_send ?></td> 
          </tr>
        <?php           
          $i++;
          }}?>
        </table>
        <input type="submit" value="Send" onclick="javascript:sendEmail()" />
      </form>
  </div>
</body>
