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
<body onload="javascript:updateSelectCount()">
  <script type="text/javascript">
    function toggleSelectAll() {
      var val = $("#chkboxSelectAll").prop('checked');
      $('.chkBoxEmail').each(function(i, obj) {
        $(obj).prop('checked', val);
      });
      updateSelectCount();
    }
  
    function updateSelectCount() {
      var selected = 0;
      var total = 0;
      var text = "";

      $('.chkBoxEmail').each(function(i, obj) {
        total += 1;
        
        if($(obj).prop('checked')) {
          selected += 1;
        }
      });
  
      if((selected == total)) {
        $("#chkboxSelectAll").prop('checked', true);
        $("#labelSelectAll").text("unselect all");
      }
      else if (selected < total){
        $("#chkboxSelectAll").prop('checked', false);
        $("#labelSelectAll").text("select all");
      }
  
      $("#selectCount").text("(" + selected + " user(s) selected)");
      $("#selectCount").css("font-weight", "bold");
    }
  
    function sendEmail() {
      var count = 0;
      $('.chkBoxEmail').each(function(i, obj) {
        if ($(obj).prop('checked')) {
          count += 1;
        }
      });
      
      if(count == 0) {
        alert("No e-mail adresses selected!");
        return false;
      }
      else if (confirm("Really send '"+count +"' e-mails?")) {
        document.getElementById('sendEmailNotificationSubmit').submit();
        return true;
      }
      return false;
    }
  </script>
  <h2>Administration Tools - Send e-mail notification</h2>
  <a id="aAdminToolsBackToCatroidweb" href="<?php echo BASE_PATH;?>admin/tools">&lt;- back</a><br /><br />
  <?php if($this->answer) {
    echo 'Answer:<br/>'.$this->answer.'<br /><br/>';
  }?>
  <div class="projectList">    
    <p>List of all users:
      <span id="selectCount"></span>
      <input type="checkbox" id="chkboxSelectAll" value="0" onclick="javascript:toggleSelectAll();"/>
      <label id="labelSelectAll">select all</label>
    </p>
    <form id="sendEmailNotificationSubmit" class="admin" action="sendEmailNotification" method="POST" onsubmit="return sendEmail();">
      <input type="hidden" name="sendEmailNotification">
      <table class="projectTable">
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>E-Mail</th>
          <th>Gender</th>
          <th>Country</th>
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
          if($id) {
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
          <td><?php echo $checkbox_send ?></td> 
        </tr>
      <?php           
        $i++;
        }}?>          
      </table>
      <br/>
      <input id="sendEmailSubmit" type="submit" value="Send"/> <!-- onclick="javascript:sendEmail()" -->
    </form>
  </div>
</body>
