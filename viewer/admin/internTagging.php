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
  function addTagForm() {
    document.getElementById('enterTagForm').submit();
  }

  function removeTagForm(id) {
    if(confirm('Do you really want to delete this tag?')) {
      document.getElementById('removeTagForm_' + id).submit();
    }
  }
  </script>


  <h2>Administration Tools - Intern Tagging</h2>
  <br />
  <a id="aAdminToolsBackToCatroidweb" href="<?php echo BASE_PATH;?>admin/tools">&lt;- back</a><br /><br />
  
  <?php if($this->answer) {
    echo 'Answer:<br/>'.$this->answer.'<br /><br/>';
  }?>
  
  <form id="enterTagForm" class="admin" action="internTagging" method="POST">
    <input type="hidden" name="internTaggingAdd" />
    <input type="text" name="tag" placeholder="tag..." id="tagInput"/>
    <input type="button" value="add tag" name="addButton" id="addTag" onclick="javascript:addTagForm();" />
  </form>
  
  <br />
  
  <div class="projectList">
    <table class="projectTable" id="projectTableId">
      <tr>
        <th>Tag</th>
        <th>delete</th>
      </tr>
      
      <?php if(!$this->emailList) : ?>
      <tr>
        <td colspan=2>No entry</td>
      </tr>
      <?php else: ?>
        <?php foreach($this->tagList as $tag) : ?>
        <tr>
          <td><?php echo $tag['name']; ?></td>
          <td>
            <form id="removeTagForm_<?php echo $tag['id']; ?>" class="admin" action="internTagging" method="POST">
              <input type="hidden" name="internTaggingRemove" />
              <input type="hidden" name="id" value="<?php echo $tag['id']; ?>" />
              <input type="button" name="deleteButton" value="delete" id="deleteTag" onclick="javascript:removeTagForm(<?php echo $tag['id']; ?>);" />
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      
    </table>
  </div>
  
</body>
