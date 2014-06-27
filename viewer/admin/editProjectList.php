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
  function submitDeleteForm(id, name) {
    if (confirm("Delete project '"+name+"'?"))
      document.getElementById(id).submit();
  }
  function submitToggleForm(id, name, newstate) {
	  if (confirm("Change project '"+name+"' to "+newstate+"?"))
	    document.getElementById(id).submit();
	}
	function submitToggleApprovedForm(id, name, newstate) {
		if (confirm("Change project '"+name+"' to "+newstate+"?"))
			document.getElementById(id).submit();
	}

	function updateTagRef(check,p_id,t_id)
	{
	  $.post('/admin/tools/updateTagRef',{project_id:p_id,tag_id:t_id,checked:check});
	}
  </script>
  <h2>Administration Tools - List of available projects</h2>
  <a id="aAdminToolsBackToCatroidweb" href="<?php echo BASE_PATH;?>admin/tools">&lt;- back</a><br /><br />
  <?php if($this->answer) {
    echo 'Answer:<br/>'.$this->answer.'<br /><br/>';
  }?>
  <div class="projectList">
      <table class="projectTable" id="projectTableId">
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Upload Time</th>
          <th>Upload IP</th>
          <th>Downloads</th>
          <th>Flagged</th>
          <th>Visible</th>
          <th>Delete</th>
          <th>Approved</th>
          <?php 
            for($i=0;$i<count($this->tags);$i++) {
              echo "<th>".$this->tags[$i]["name"]."</th>";
            }
          ?>
        </tr>
      <?php
        if($this->projects) {
        foreach($this->projects as $project) {?>
        <tr>
          <td><?php echo $project['id']?></td>
          <td><?php echo $project['title']?></td>
          <td><?php echo date('Y-m-d H:i:s', strtotime($project['upload_time']))?></td>
          <td><?php echo $project['upload_ip']?></td>
          <td><?php echo $project['download_count']?></td>
          <td><?php echo $project['num_flags'].'x'?></td>
          <td>
            <form id="toggleform<?php echo $project['id']?>" class="admin" action="toggleProjects" method="POST">
            <?php echo ($project['visible']=='t' ? 'visible' : '<em>invisible</em>');?> 
              <input type="hidden" name="projectId" value="<?php echo $project['id']?>"/>
              <input type="hidden" name="toggle" value="<?php echo ($project['visible']=='t' ? 'invisible' : 'visible'); ?>">
              <input type="button" value="change" name="toggleProject" id="toggle<?php echo $project['id']?>" onclick="javascript:submitToggleForm('toggleform<?php echo $project['id']?>', '<?php echo addslashes(htmlspecialchars($project['title']))?>', '<?php echo ($project['visible']=='t' ? 'invisible' : 'visible');?>');" /> <!-- chg -->
            </form>
          </td>
                  
          <td>
            <form id="deleteform<?php echo $project['id']?>" class="admin" action="editProjects" method="POST">
              <input type="hidden" name="projectId" value="<?php echo $project['id']?>"/>
              <input type="hidden" name="delete" value="delete"/>
              <input type="button" value="delete" name="deleteButton" id="delete<?php echo $project['id']?>" onclick="javascript:submitDeleteForm('deleteform<?php echo $project['id']?>', '<?php echo addslashes(htmlspecialchars($project['title']))?>');" /> <!-- chg -->
            </form>
          </td>
          <td>
            <form id="toggleApprovedForm<?php echo $project['id']?>" class="admin" action="toggleApprovedProjects" method="POST">
            <?php echo ($project['approved']=='t' ? 'approved' : '<em>unapproved</em>');?> 
              <input type="hidden" name="projectId" value="<?php echo $project['id']?>"/>
              <input type="hidden" name="toggle" value="<?php echo ($project['approved']=='t' ? 'unapprove' : 'approve'); ?>">
              <input type="button" value="change" name="toggleApprovedProject" id="toggle<?php echo $project['id']?>" onclick="javascript:submitToggleApprovedForm('toggleApprovedForm<?php echo $project['id']?>', '<?php echo addslashes(htmlspecialchars($project['title']))?>', '<?php echo ($project['approved']=='t' ? 'unapprove' : 'approve');?>');" /> <!-- chg -->
            </form>
          </td>
          <?php 
            foreach($this->tags as $tag) {
              echo "<td><input type=\"checkbox\" ".(in_array($tag["id"], $project['tags'],true)?"checked":"")." name=\"haaallo\" onchange=\"updateTagRef(this.checked,".$project['id'].",".$tag["id"].");\" /></td>";
            }
          ?>
        </tr>
      <?php }}?>
      </table>
  </div>
</body>
