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
  function submitAddForm(id, name) {
    if (confirm("Add featured project '"+name+"'?"))
      document.getElementById(id).submit();
  }
  </script>
  <h2>Administration Tools - Add Featured Projects</h2>
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
          <th>Downloads</th>
          <th>Flagged</th>
          <th>Visible</th>
          <th>Add</th>
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
            <?php echo ($project['visible']=='t' ? 'visible' : '<em>invisible</em>');?> 
          </td>
                  
          <td>
          <?php 
            if($this->featuredProjectIds && in_array($project['id'], $this->featuredProjectIds)) {
              ?>
              already featured 
           <?php 
            }
          else{ ?>
          <form id="addform<?php echo $project['id']?>" class="admin" action="addFeaturedProject" method="POST">
              <input type="hidden" name="projectId" value="<?php echo $project['id']?>"/>
              <input type="hidden" name="add" value="add"/>
              <input type="button" value="feature" name="addButton" id="add<?php echo $project['id']?>" onclick="javascript:submitAddForm('addform<?php echo $project['id']?>', '<?php echo addslashes(htmlspecialchars($project['title']))?>');" /> <!-- chg -->
            </form>
            <?php 
            }
          ?>
            
          </td>
        </tr>
      <?php }}?>
      </table>
      <p>
        <a id="aAdminToolsEditFeaturedProjects" href="<?php echo BASE_PATH;?>admin/tools/editFeaturedProjects">edit / view featured projects</a><br />
      </p>
  </div>
</body>
