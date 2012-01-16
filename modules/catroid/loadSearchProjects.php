<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2012 The Catroid Team
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

class loadSearchProjects extends CoreAuthenticationNone {
  protected $pageNr = 0;
  protected $searchQuery = "";

  public function __construct() {
    parent::__construct();

    $labels = array();
    $labels['websitetitle'] = SITE_DEFAULT_TITLE;
    $labels['title'] = $this->languageHandler->getString('title');
    $labels['prevButton'] = $this->languageHandler->getString('prev_button', '&laquo;');
    $labels['nextButton'] = $this->languageHandler->getString('next_button', '&raquo;');
    $labels['loadingButton'] = $this->languageHandler->getString('loading_button');
    $this->labels = $labels;
  }

  public function __default() {
    if(isset($_REQUEST['query'])) {
      $this->searchQuery = $_REQUEST['query'];
    }
    if(isset($_REQUEST['page'])) {
      $this->pageNr = intval($_REQUEST['page'])-1;
    }

    $this->content = $this->retrieveSearchResultsFromDatabase($this->searchQuery, $this->pageNr);
  }

  public function retrieveSearchResultsFromDatabase($keywords, $pageNr) {    
    if($pageNr < 0) {
      return "NIL";
    }

    $searchTerms = explode(" ", $keywords);
    $keywordsCount = 3;
    $searchQuery = "";
    $searchRequest = "";

    foreach($searchTerms as $term) {
      if ($term != "") {
        $searchQuery .= (($searchQuery=="")?"":" OR " )."title ILIKE \$".$keywordsCount;
        $searchQuery .= " OR description ILIKE \$".$keywordsCount;
        $searchTerm = pg_escape_string(preg_replace("/\\\/", "\\\\\\", $term));
        $searchTerm = preg_replace(array("/\%/", "/\_/"), array("\\\\\%", "\\\\\_"), $searchTerm);
        $searchRequest .= ", '%".$searchTerm."%'";
        $keywordsCount++;
      }
    }
     
    pg_prepare($this->dbConnection, "get_search_results", "SELECT projects.id, projects.title, projects.upload_time, cusers.username AS uploaded_by FROM projects, cusers WHERE ($searchQuery) AND visible = 't' AND cusers.id=projects.user_id ORDER BY upload_time DESC  LIMIT \$1 OFFSET \$2")
    or die("Couldn't prepare statement: " . pg_last_error());
    $query = 'EXECUTE get_search_results('.PROJECT_PAGE_LOAD_MAX_PROJECTS.', '.(PROJECT_PAGE_LOAD_MAX_PROJECTS * $pageNr).$searchRequest.');';
    $result = @pg_query($this->dbConnection, $query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $projects = pg_fetch_all($result);
    pg_query($this->dbConnection, 'DEALLOCATE get_search_results');
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
    } elseif($pageNr == 0) {
        $projects[0]['id'] = 0;
        $projects[0]['title'] = $this->languageHandler->getString('no_results');
        $projects[0]['title_short'] = $this->languageHandler->getString('no_results');
        $projects[0]['upload_time'] =  "";
        $projects[0]['thumbnail'] = BASE_PATH."images/symbols/thumbnail_gray.jpg";
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
