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
    function submitToggleForm(id, title, newstate) {
      if (confirm("Change project '"+title+"' to "+newstate+"?"))
	      document.getElementById(id).submit();
    }
    
    function submitDeleteForm(id, title) {
      if(confirm("Delete project '"+title+"'?"))
        document.getElementById(id).submit();
    }

    function submitApproveForm(id, title) {
      if(confirm("Approve project "+title+"?"))
        document.getElementById(id).submit();
    }
  </script>
  <h2>Administration Tools - List of inappropriate projects</h2>
  <a id="aAdminToolsBackToCatroidweb" href="<?php echo BASE_PATH;?>admin/tools">&lt;- back</a><br /><br />
  <?php if($this->answer) {
    echo 'Answer:<br/>'.$this->answer.'<br /><br/>';
  }?>
  <div class="projectList">
    <table class="projectTable" id="projectTableId">
      <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Description</th>
        <th>Visible</th>
        <th>Delete</th>
        <th>Approve</th>
      </tr>
      
      <?php if($this->unapprovedProjects) : ?>
      <?php foreach ($this->unapprovedProjects as $project) : ?>
      <tr>
        <td><?php echo $project['id']; ?></td>
        <td><?php echo $project['title']; ?></td>
        <td><?php echo $project['description']; ?></td>
        <td>
          <form id="toggleform<?php echo $project['id']?>" class="admin" action="approveProjects" method="POST">
            <?php echo ($project['visible']=='t' ? 'visible' : '<em>invisible</em>');?> 
            <input type="hidden" name="projectId" value="<?php echo $project['id']?>"/>
            <input type="hidden" name="toggle" value="<?php echo ($project['visible']=='t' ? 'invisible' : 'visible'); ?>">
            <input type="button" value="change" name="toggleProject" id="toggle<?php echo $project['id']?>" onclick="javascript:submitToggleForm('toggleform<?php echo $project['id']?>', '<?php echo addslashes(htmlspecialchars($project['title']))?>', '<?php echo ($project['visible']=='t' ? 'invisible' : 'visible');?>');" /> <!-- chg -->
          </form>
        </td>
        <td>
          <form id="deleteform<?php echo $project['id']?>" class="admin" action="approveProjects" method="POST">
            <input type="hidden" name="projectId" value="<?php echo $project['id']?>"/>
            <input type="hidden" name="delete" value="delete"/>
            <input type="button" value="delete" name="deleteButton" id="delete<?php echo $project['id']?>" onclick="javascript:submitDeleteForm('deleteform<?php echo $project['id']?>', '<?php echo addslashes(htmlspecialchars($project['title']))?>');" />
          </form>
        </td>
        <td>
          <form id="approveform<?php echo $project['id']?>" class="admin" action="approveProjects" method="POST">
            <input type="hidden" name="projectId" value="<?php echo $project['id']?>"/>
            <input type="hidden" name="approve" value="approve"/>
            <input type="button" value="approve" name="approveButton" id="approve<?php echo $project['id']?>" onclick="javascript:submitApproveForm('approveform<?php echo $project['id']?>', '<?php echo addslashes(htmlspecialchars($project['title']))?>');" />
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
      
    </table>
  </div>
</body>
