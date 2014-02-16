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
  <table>
    <tr>
      <td style="text-align: left">
        <b><?php echo $this->count;?> Words in Database</b> 
      </td>
      <td style="text-align: left">
        <?php echo " --> Choose words per Site: ";
          if ($this->per_page != 10) {?> 
            <a id="Projects10" href="approveWords.php?per_page=10&page_number=<?php echo $this->start;?>">10</a>
        <?php }else {
            echo "10";
          }
          if ($this->per_page != 20){ ?> 
            <a id="Projects20" href="approveWords.php?per_page=20&page_number=<?php echo $this->start;?>">20</a>
        <?php }else {
            echo "20";
          }
          if ($this->per_page != 50){ ?>
            <a id="Projects50" href="approveWords.php?per_page=50&page_number=<?php echo $this->start;?>">50</a>
        <?php }else {
            echo "50";
          }
          if ($this->per_page != $this->count){ ?> 
            <a id="allProjects" href="approveWords.php?per_page=<?php echo $this->count;?>&page_number=<?php echo $this->start;?>">ALL</a>
        <?php }else {
            echo "ALL";
          } ?>
      </td>
    </tr>
  </table>
  
  <table>
    <tr>
      <td style="width:50px">
        <?php echo "Seite: \n"; ?>
      </td>
      <td>
        <?php if ($this->start != 1) { ?>
            <a id="lessThen" href="approveWords.php?per_page=<?php echo $this->per_page; ?>&page_number=<?php echo ($this->start-1);?>">&lt;</a>
        <?php }for($i=1; $i<=$this->num_pages; $i++) {
          if ($i==$this->start){
            echo $i."\n";
          }else { 
            if ((($i >= ($this->start - 5)) && (($i <= ($this->start + 5)))) || ($i == 1) || ($i == $this->num_pages)) { ?> 
            <a id="site<?php echo $i;?>" href="approveWords.php?per_page=<?php echo $this->per_page;?>&page_number=<?php echo $i;?>"><?php echo $i;?></a>
        <?php  } else {
          if (($i < $this->start) && $check != 1) {
            $check = 1;
            echo "...";
        } if (($i > $this->start) && $check1 != 1) {
            $check1 = 1;
            echo "..."; 
          }}}}
          $check1 = 0;
          $check = 0;
          if ($this->start != $this->num_pages) {?>
            <a id="greaterThen" href="approveWords.php?per_page=<?php echo $this->per_page;?>&page_number=<?php echo ($this->start+1);?>">&gt;</a>
        <?php }?>
      </td>
    </tr>
  </table>  
</body>