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

class index extends CoreAuthenticationNone {
  public function __construct() {
    parent::__construct();
    if($this->clientDetection->isBrowser(CoreClientDetection::BROWSER_FIREFOX) ||
      $this->clientDetection->isBrowser(CoreClientDetection::BROWSER_FIREFOX_MOBILE) ||
      $this->clientDetection->isBrowser(CoreClientDetection::BROWSER_SAFARI) ||
      $this->clientDetection->isBrowser(CoreClientDetection::BROWSER_CHROME) ||
      $this->clientDetection->isBrowser(CoreClientDetection::BROWSER_ANDROID)) {
        $this->addCss('projectList.css');
    } else {
      $this->addCss('projectList_nohtml5.css');
    }
    $this->addCss('buttons.css');
    $this->addJs('newestProjects.js');
    $this->addJs('searchProjects.js');
    $this->addJs('index.js');
    $this->htmlHeaderFile = 'htmlIndexHeaderTemplate.php';
    
    $this->numberOfPages = ceil($this->getNumberOfVisibleProjects() / PROJECT_PAGE_LOAD_MAX_PROJECTS);    //TODO deprecated???
    
    if(!$this->session->pageNr) {      
      $this->session->pageNr = 1;
      $this->session->task = "newestProjects";
    }
    if(isset($_REQUEST['method']) || isset($_REQUEST['p'])) {
    	if(isset($_REQUEST['method'])) {
        $this->session->pageNr = intval($_REQUEST['method']);
    	}
      if(isset($_REQUEST['p'])) {
        $this->session->pageNr = intval($_REQUEST['p']);
      }
      if($this->session->pageNr < 1) {
        $this->session->pageNr = 1;
      }
      if($this->session->pageNr > $this->numberOfPages) {
        $this->session->pageNr = $this->numberOfPages - 1; 
      }
    }
    if(!$this->session->referer) {
    	$this->session->referer = $_SERVER['HTTP_REFERER'];
    }
    if($this->session->referer != $_SERVER['HTTP_REFERER']) {
      $this->session->referer = $_SERVER['HTTP_REFERER'];
      $this->session->task = "newestProjects";
    }
    
    if(isset($_REQUEST['q'])) {
    	$this->session->searchQuery = $_REQUEST['q'];
    }
      
    $this->task = $this->session->task;
    $this->pageNr = $this->session->pageNr;
    $this->searchQuery = "";
    if($this->session->searchQuery != "") {
      $this->searchQuery = $this->session->searchQuery;
    }
  }

  public function __default() {
    
  }

  public function getNumberOfVisibleProjects() {
    $query = 'EXECUTE get_number_of_visible_projects;';
    $result = @pg_query($query) or $this->errorHandler->showErrorPage('db', 'query_failed', pg_last_error());
    $number = pg_fetch_all($result);
    pg_free_result($result);
    
    if($number[0]['count']) {
      return $number[0]['count'];
    }
    return 0;
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
