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
  function submitResolveForm(id, title) {
    var doApprove = window.confirm("Resolve project '"+title+"'?");
    if(doApprove) {
      document.getElementById("resolveForm"+id).submit();
    }
  }
  
  function submitDeleteForm(id, title) {
    var doDelete = window.confirm("Delete project '"+title+"'?");
    if(doDelete) {
      document.getElementById("deleteForm"+id).submit();
    }
  }
  </script>
  <h2>Administration Tools - List of inappropriate projects</h2>
  <a id="aAdminToolsBackToCatroidweb" href="<?php echo BASE_PATH;?>admin/tools">&lt;- back</a><br /><br />
  <?php if($this->answer) {
    echo 'Answer:<br/>'.$this->answer.'<br /><br/>';
  }?>
  <div class="projectList">
      <table class="projectTable">
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Upload Time</th>
          <th>Upload IP</th>
          <th>Flagged</th>
          <th>Flagged by and reason</th>
          <th>Visible</th>
          <th>Details</th>
          <th>Resolve</th>
          <th>Delete</th>
        </tr>
      <?php
        if($this->projects) {
        foreach($this->projects as $project) {?>
        <tr>
          <td><?php echo $project['id']?></td>
          <td><?php echo $project['title']?></td>
          <td><?php echo date('Y-m-d H:i:s', strtotime($project['upload_time']))?></td>
          <td><?php echo $project['upload_ip']?></td>
          <td><?php echo $project['num_flags'].'x'?></td>
          <td><?php echo $project['flag_details']?></td>
          <td><?php echo ($project['visible']=='t' ? 'visible' : '<em>invisible</em>');?></td>
          <td><a id="detailsLink<?php echo $project['id']?>" href="<?php echo BASE_PATH.'details/'.$project['id']?>" target="_blank">link</a></td>
          <td>
            <form id="resolveForm<?php echo $project['id']?>" class="admin" action="inappropriateProjects" method="POST">
              <input type="hidden" name="projectId" value="<?php echo $project['id']?>"/>
              <input type="hidden" name="resolve" value="resolve"/>
              <input type="button" value="resolve" name="resolveButton" id="resolve<?php echo $project['id']?>" onclick="javascript:submitResolveForm('<?php echo $project['id']?>', '<?php echo addslashes(htmlspecialchars($project['title']))?>');" />
            </form>
          </td>
          <td>
            <form id="deleteForm<?php echo $project['id']?>" class="admin" action="inappropriateProjects" method="POST">
              <input type="hidden" name="projectId" value="<?php echo $project['id']?>"/>
              <input type="hidden" name="delete" value="delete"/>
              <input type="button" value="delete" name="deleteButton" id="delete<?php echo $project['id']?>" onclick="javascript:submitDeleteForm('<?php echo $project['id']?>', '<?php echo addslashes(htmlspecialchars($project['title']))?>');" /> <!-- chg -->
            </form>
          </td>
        </tr>
      <?php }}?>
      </table>
  </div>
</body>
