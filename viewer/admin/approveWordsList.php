<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    An additional term exception under section 7 of the GNU Affero
 *    General Public License, version 3, is available at
 *    http://www.catroid.org/catroid/licenseadditionalterm
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
  function submitApproveForm(id, word) {
    var doApprove = window.confirm("Approve word '"+word+"'?");
    if(doApprove) {
      document.getElementById("approveForm"+id).submit();
    }
  }
  
  function submitDeleteForm(id, word) {
    var doDelete = window.confirm("Delete word '"+word+"'?");
    if(doDelete) {
      document.getElementById("deleteForm"+id).submit();
    }
  }
  </script>
  <h2>Administration Tools - List of unapproved Words</h2>
    <a id="aAdminToolsBackToCatroidweb" href="<?php echo BASE_PATH;?>admin/tools">&lt;- back</a><br /><br />
    <?php if($this->answer) {
    	echo 'Answer:<br/>'.$this->answer.'<br /><br/>';
    }?>
  <div class="projectList">
  <table class="projectTable">
	  <tr>
		  <th>ID</th>
		  <th>Word</th>
		  <th>Meaning</th>
		  <th>Approve</th>
		  <th>Delete</th>
	  </tr>
	  <?php
	  if($this->words) {
      foreach($this->words as $word) {?>
	  <tr>
		  <td><?php echo $word['id']?></td>
		  <td><?php echo $word['word']?></td>
		  <td>
		    <form id="approveForm<?php echo $word['id']?>" class="admin" action="approveWords" method="POST">
		      <select name="meaning" id="meaning<?php echo $word['id']?>">
            <option value="-1">-</option>
            <option value="1">good</option>
            <option value="0">bad</option>
          </select>
		  </td>
		  <td>
          <input type="hidden" name="wordId" value="<?php echo $word['id']?>" />
          <input type="hidden" name="approve" value="approve"/>
          <input type="button" value="approve" name="approveButton" id="approve<?php echo $word['id']?>" onclick="javascript:submitApproveForm('<?php echo $word['id']?>', '<?php echo addslashes(htmlspecialchars($word['word']))?>');" /> <!-- chg -->
		    </form>
		  </td>
		  <td>
		    <form id="deleteForm<?php echo $word['id']?>" class="admin" action="approveWords" method="POST">
          <input type="hidden" name="wordId" value="<?php echo $word['id']?>"/>
          <input type="hidden" name="delete" value="delete"/>
          <input type="button" value="delete" name="deleteButton" id="delete<?php echo $word['id']?>" onclick="javascript:submitDeleteForm('<?php echo $word['id']?>', '<?php echo addslashes(htmlspecialchars($word['word']))?>');" /> <!-- chg -->
        </form>
      </td>
     </tr>
	<?php }}?>
  </table>
  </div>
</body>
