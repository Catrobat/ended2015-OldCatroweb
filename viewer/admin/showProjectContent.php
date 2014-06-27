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
    function submitApproveForm(id, title, action) {
      if(confirm(action + " project "+title+" ?"))
        document.getElementById(id).submit();
    }
  </script>
  <a id="aAdminToolsBackToCatroidweb" href="<?php echo BASE_PATH;?>admin/tools/approveProjects">&lt;- back</a><br /><br />
  <?php if($this->answer) {
    echo 'Answer:<br/>'.$this->answer.'<br /><br/>';
  }?>
  <h1><?php echo $this->projectTitle . ' - ID: <span id="projectId">' . $this->projectId . "</span>"?></h1>
  <div>
  <form id="approveform<?php echo $this->projectId?>" class="admin" action="showProjectsContent" method="POST">
    <input type="hidden" name="projectId" value="<?php echo $this->projectId?>"/>
    <input type="hidden" name="approve" value="approve"/>
    <input type="hidden" name="title" value="<?php echo $this->projectTitle?>"/>
    <input type="button" value="Approve" name="approveButton" id="approve<?php echo $this->projectId?>" onclick="javascript:submitApproveForm('approveform<?php echo $this->projectId?>', '<?php echo addslashes(htmlspecialchars($project['title']))?>','Approve');" />
  </form>
  <form id="approveform<?php echo $this->projectId?>_inv" class="admin" action="showProjectsContent" method="POST">
    <input type="hidden" name="projectId" value="<?php echo $this->projectId?>"/>
    <input type="hidden" name="approve" value="invisible"/>
    <input type="hidden" name="title" value="<?php echo $this->projectTitle?>"/>
    <input type=<?= sizeof($this->unapprovedProjects) == 1 ? "hidden" : "button"?> value="Hide" name="approveButton" id="approve<?php echo $this->projectId?>" onclick="javascript:submitApproveForm('approveform<?php echo $this->projectId?>_inv', '<?php echo addslashes(htmlspecialchars($project['title']))?>','Hide');" />
  </form>
 
  <form id="nextform" class="admin" action="showProjectsContent" method="POST">
    <input type="hidden" name="projectId" value="<?php echo ($this->projectId)?>"/>
    <input id="nextClick" type = <?= sizeof($this->unapprovedProjects) == 1 ? "hidden" : "submit"?> value="Skip" name="nextProject" id="next<?php echo $project['id']?>" >
  </form>  
  </div>  
    <article>
      <div>
        <div class="projects">
          <h2>Images</h2>
          <?php 
            $dir = new DirectoryIterator(PROJECTS_UNZIPPED_DIRECTORY . $this->projectId . '/images');
            foreach($dir as $file ){ 
              if($file != '.' && $file != '..' && $file != '.nomedia') { ?>
                <div class="projectImages"><img width="auto" height="auto" src="<?php
                if(pathinfo($file,PATHINFO_EXTENSION) == "")
                  echo "/admin/tools/getResource.png?project_id=".$this->projectId ."&file_name=images/".pathinfo($file,PATHINFO_FILENAME);
                else
                  echo "/admin/tools/getResource.".pathinfo($file,PATHINFO_EXTENSION)."?project_id=".$this->projectId ."&file_name=images/".pathinfo($file,PATHINFO_FILENAME) ?>"></div>
              <?php }} ?>
        </div>
        <div class="projects">
          <h2>Strings</h2>
          <div class="innerStringDiv">
          <?php 
            $divClosed = false;
            $i = 1;
            foreach($this->parsedStrings as $string) {
              $divClosed = false;echo $string;
              ?><br>
              <?php 
              if($i % 10 == 0) {
                echo '</div><div class="innerStringDiv">';
                $divClosed = true;
              }
              $i++;
            }
            if(!$divClosed) {
              echo "</div>";
            }
          ?>
        </div>
        <div class="projects">
          <h2>Sounds</h2>
          <?php 
            $dir = new DirectoryIterator(PROJECTS_UNZIPPED_DIRECTORY . $this->projectId . '/sounds');
            foreach($dir as $file ){ 
              if($file != '.' && $file != '..' && $file != '.nomedia') {?>
                <div class="projectImages">
                  <audio controls style="height:50px;" preload="auto">
                    <source src="<?php echo "/admin/tools/getResource.".pathinfo($file,PATHINFO_EXTENSION)."?project_id=".$this->projectId ."&file_name=sounds/".pathinfo($file,PATHINFO_FILENAME) ?>" preload="auto">
                    <embed  width="auto" height="auto" src="<?php echo "/admin/tools/getResource.".pathinfo($file,PATHINFO_EXTENSION)."?project_id=". $this->projectId ."&file_name=sounds/". pathinfo($file,PATHINFO_FILENAME) ?>">
                  </audio>
                  <br/>
                  <a href="<?php echo "/admin/tools/getResource.".pathinfo($file,PATHINFO_EXTENSION)."?project_id=".$this->projectId ."&file_name=sounds/".pathinfo($file,PATHINFO_FILENAME) ?>">Download <?=$file?></a>
                </div>
              <?php 
                }
              } ?>
        </div>
      </div>        
    </article>        
    
    
</body>

