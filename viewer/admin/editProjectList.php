<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team 
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or License, or License, or (at your option) any later version.
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
  function submitForm(id, name) {
    var doDelete = window.confirm("Delete project '"+name+"'?");
    if(doDelete) {
      document.getElementById("form"+id).submit();
    }
  }
  </script>
  <h2>Administration Tools - List of available projects</h2>
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
          <td><?php echo $project['download_count']?></td>
          <td><?php echo $project['num_flags'].'x'?></td>
          <td><?php echo ($project['visible']=='t' ? 'visible' : '<em>invisible</em>');?></td>
          <td>
            <form id="form<?php echo $project['id']?>" class="admin" action="editProjects" method="POST">
              <input type="hidden" name="projectId" value="<?php echo $project['id']?>"/>
              <input type="hidden" name="delete" value="delete"/>
              <input type="button" value="delete" name="deleteButton" id="delete<?php echo $project['id']?>" onclick="javascript:submitForm('<?php echo $project['id']?>', '<?php echo $project['title']?>');" /> <!-- chg -->
            </form>
          </td>
        </tr>
      <?php }}?>
      </table>
  </div>
</body>