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

class search extends CoreAuthenticationNone {

  public function __construct() {
    parent::__construct();
    $this->addCss('search.css');
    $this->addJs('projectLoader.js');
    $this->addJs('projectContentFiller.js');
    $this->addJs('projectObject.js');
    
    $this->loadModule('api/projects');
  }

  public function __default() {
    $this->session->searchPageNr = max(1, intval($_REQUEST['p']));

    $requestedPage = $this->projects->get(($this->session->searchPageNr - 1) * PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE,
        PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE, PROJECT_MASK_LIST_AGE, PROJECT_SORTBY_AGE, $_REQUEST['q']);
    $this->numberOfPages = max(1, intval(ceil(max(0, intval($requestedPage['CatrobatInformation']['TotalProjects'])) /
        PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE) - 1));

    if($this->session->searchPageNr > $this->numberOfPages) {
      $this->session->searchPageNr = $this->numberOfPages;
      $requestedPage = $this->projects->get(($this->session->searchPageNr - 1) * PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE,
          PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE, PROJECT_MASK_LIST_AGE, PROJECT_SORTBY_AGE, $_REQUEST['q']);
    }

    $params = array();
    $params['layout'] = PROJECT_LAYOUT_ROW;
    $params['container'] = '#searchResultContainer';
    $params['buttons'] = array('prev' => '#fewerResults',
        'next' => '#moreResults'
    );
    $params['firstPage'] = $requestedPage;
    
    $params['page'] = array('number' => intVal($this->session->searchPageNr),
        'numProjectsPerPage' => PROJECT_LAYOUT_ROW_PROJECTS_PER_PAGE,
        'pageNrMax' => $this->numberOfPages
    );
    
    $params['mask'] = PROJECT_MASK_LIST_AGE;
    $params['sort'] = PROJECT_SORTBY_AGE;
    $params['filter'] = array('query' => strval($_REQUEST['q']),
        'author' => ''
    );
    
    $params['config'] = array('PROJECT_LAYOUT_ROW' => PROJECT_LAYOUT_ROW,
        'sortby' => array('age' => PROJECT_SORTBY_AGE,
            'downloads' => PROJECT_SORTBY_DOWNLOADS,
            'views' => PROJECT_SORTBY_VIEWS,
            'random' => PROJECT_SORTBY_RANDOM
        )
    );
    
    $this->jsParams = "'" . json_encode($params) . "'";
  }
  
  public function __destruct() {
    parent::__destruct();
  }
}
?>
