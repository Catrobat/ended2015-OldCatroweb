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
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class loadNewestProjects extends CoreAuthenticationNone {
  protected $pageNr = 0;

  public function __construct() {
    parent::__construct();
    
    $labels = array();
    $labels['websitetitle'] = "Catroid Website";
    $labels['title'] = "Newest Projects";
    $labels['prevButton'] = "&laquo; Newer";
    $labels['nextButton'] = "Older &raquo;";
    $labels['loadingButton'] = "loading...";
    $this->labels = $labels;
  }

  public function __default() {
    if(isset($_REQUEST['method'])) {
      $this->pageNr = intval($_REQUEST['method'])-1;
    }    
    
    $this->content = $this->retrievePageNrFromDatabase($this->pageNr);
  }

  public function retrievePageNrFromDatabase($pageNr) {
  	if($pageNr < 0) {
      return "NIL";
  	}
  	 
    $query = 'EXECUTE get_visible_projects_ordered_by_uploadtime_limited_and_offset('.PROJECT_PAGE_LOAD_MAX_PROJECTS.', '.(PROJECT_PAGE_LOAD_MAX_PROJECTS * $pageNr).');';
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $projects = pg_fetch_all($result);
    pg_free_result($result);
    if($projects[0]['id']) {
      $i=0;
      foreach($projects as $project) {
        $projects[$i]['title'] = $projects[$i]['title'];
        $projects[$i]['title_short'] = makeShortString($project['title'], PROJECT_TITLE_MAX_DISPLAY_LENGTH);
        $projects[$i]['upload_time'] =  $this->languageHandler->getString('uploaded', getTimeInWords(strtotime($project['upload_time']), $this->languageHandler, time()));
        $projects[$i]['thumbnail'] = getProjectThumbnailUrl($project['id']);
        $projects[$i]['uploaded_by_string'] = $this->languageHandler->getString('uploaded_by', $projects[$i]['uploaded_by']);
        $i++;
      }
      return($projects);
    } else {
      return "NIL";
    }
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
