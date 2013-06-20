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
  function submitDeleteForm(id, name) {
    if (confirm("Delete featured project '"+name+"'?"))
      document.getElementById(id).submit();
  }
  function submitToggleForm(id, name, newstate) {
	    if (confirm("Change featured project '"+name+"' to "+newstate+"?"))
	      document.getElementById(id).submit();
	  }
  </script>
  <h2>Administration Tools - Edit Featured Projects</h2>
  <a id="aAdminToolsBackToCatroidweb" href="<?php echo BASE_PATH;?>admin/tools">&lt;- back</a><br /><br />
  <?php if($this->answer) {
    echo 'Answer:<br/>'.$this->answer.'<br /><br/>';
  }?>
  <div class="projectList">
      <table class="projectTable">
        <tr>
          <th>Featured ID</th>
          <th>Project ID</th>
          <th>Title</th>
          <th>Description</th>
          <th>User</th>
          <th>Image</th>
          <th>Time</th>
          <th>Downloads</th>
          <th>Views</th>
          <th>Visible</th>
          <th>Delete</th>
        </tr>
      <?php
        if($this->projects) {
        foreach($this->projects as $project) {?>
        <tr>
          <td><?php echo $project['id']?></td>
          <td><?php echo $project['project_id']?></td>
          <td><?php echo $project['title']?></td>
          <td><?php echo $project['description']?></td>
          <td><?php echo $project['uploaded_by']?></td>
          <td width="20%" style="text-align:right;">
            <img src="<?php echo $project['image']?>" alt="<?php echo $project['image']?>" class="projectTableFeaturedImage"/>
            <form style="" id="imageform<?php echo $project['id']?>" class="admin" action="updateFeaturedProjectsImage" method="POST" enctype="multipart/form-data">
              <input type="file" name="file"/>
              <input type="hidden" name="featuredId" value="<?php echo $project['id']?>"/>
              <input type="hidden" name="projectId" value="<?php echo $project['project_id']?>"/>
              <input type="submit" name="submit_upload" id="imagesubmit<?php echo $project['id']?>" value="upload" />
            </form>
          </td>
          <td><?php echo date('Y-m-d H:i:s', strtotime($project['update_time']))?></td>
          <td><?php echo $project['download_count']?></td>
          <td><?php echo $project['view_count']?></td>
          <td>
            <form id="toggleform<?php echo $project['id']?>" class="admin" action="toggleFeaturedProjectsVisiblity" method="POST">
            <?php echo ($project['visible']=='t' ? 'visible' : '<em>invisible</em>');?> 
              <input type="hidden" name="featuredId" value="<?php echo $project['id']?>"/>
              <input type="hidden" name="projectId" value="<?php echo $project['project_id']?>"/>
              <input type="hidden" name="toggle" value="<?php echo ($project['visible']=='t' ? 'invisible' : 'visible'); ?>">
              <input type="button" value="change" name="toggleProject" id="toggle<?php echo $project['id']?>" onclick="javascript:submitToggleForm('toggleform<?php echo $project['id']?>', '<?php echo addslashes(htmlspecialchars($project['title']))?>', '<?php echo ($project['visible']=='t' ? 'invisible' : 'visible');?>');" /> <!-- chg -->
            </form>
          </td>
                  
          <td>
            <form id="deleteform<?php echo $project['id']?>" class="admin" action="editFeaturedProjects" method="POST">
              <input type="hidden" name="featuredId" value="<?php echo $project['id']?>"/>
              <input type="hidden" name="delete" value="delete"/>
              <input type="button" value="delete" name="deleteButton" id="delete<?php echo $project['id']?>" onclick="javascript:submitDeleteForm('deleteform<?php echo $project['id']?>', '<?php echo addslashes(htmlspecialchars($project['title']))?>');" /> <!-- chg -->
            </form>
          </td>
        </tr>
      <?php }}?>
      </table>
      <p>
        <a id="aAdminToolsAddFeaturedProject" href="<?php echo BASE_PATH;?>admin/tools/addFeaturedProject">add featured projects</a><br />
      </p>
  </div>
</body>
