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
    $this->addJs('search.js');
    
    $this->loadModule('api/projects');
  }

  public function __default() {
    $projectsPerRow = 9;
    $this->session->searchPageNr = max(1, intval($_REQUEST['p']));

    $requestedPage = $this->projects->get(($this->session->searchPageNr - 1) * $projectsPerRow,
        $projectsPerRow, PROJECT_MASK_GRID_ROW_AGE, PROJECT_SORTBY_AGE, $_REQUEST['q']);
    $this->numberOfPages = max(1, intval(ceil(max(0, intval($requestedPage['CatrobatInformation']['TotalProjects'])) /
        $projectsPerRow)));

    if($this->session->searchPageNr > $this->numberOfPages) {
      $this->session->searchPageNr = $this->numberOfPages;
      $requestedPage = $this->projects->get(($this->session->searchPageNr - 1) * $projectsPerRow,
          $projectsPerRow, PROJECT_MASK_GRID_ROW_AGE, PROJECT_SORTBY_AGE, $_REQUEST['q']);
    }

    $params = array();
    $params['layout'] = PROJECT_LAYOUT_GRID_ROW;
    $params['container'] = '#searchResultContainer';
    $params['loader'] = '#searchResultLoader';
    $params['buttons'] = array('prev' => null,
        'next' => '#moreResults'
    );
    $params['content'][0] = $requestedPage;
    $params['numProjects'] = intval($requestedPage['CatrobatInformation']['TotalProjects']);
    
    $params['page'] = array('number' => intVal($this->session->searchPageNr),
        'numProjectsPerPage' => $projectsPerRow,
        'pageNrMax' => $this->numberOfPages
    );
    
    $params['mask'] = PROJECT_MASK_GRID_ROW_AGE;
    $params['sort'] = PROJECT_SORTBY_AGE;
    $params['filter'] = array('query' => strval($_REQUEST['q']),
        'author' => ''
    );
    
    $params['config'] = array('LAYOUT_GRID_ROW' => PROJECT_LAYOUT_GRID_ROW,
        'sortby' => array('age' => PROJECT_SORTBY_AGE,
            'downloads' => PROJECT_SORTBY_DOWNLOADS,
            'views' => PROJECT_SORTBY_VIEWS,
            'random' => PROJECT_SORTBY_RANDOM
        )
    );
    
    $this->jsParams = "'" . addslashes(json_encode($params)) . "'";
    $this->setWebsiteTitle($this->languageHandler->getString('header') . " - " . 
        strval($_REQUEST['q']) . " - " . $this->session->searchPageNr);
  }
  
  public function __destruct() {
    parent::__destruct();
  }
}
?>
