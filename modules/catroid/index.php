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

class index extends CoreAuthenticationNone {
  
  public function __construct() {
    parent::__construct();
    $this->addCss('index.css');
    $this->addJs('projectLoader.js');
    $this->addJs('projectContentFiller.js');
    $this->addJs('projectObject.js');
    $this->addJs('index.js');
    
    $this->loadModule('api/projects');
  }

  public function __default() {
    $pageNr = 2;
    $projectsPerRow = 9;

    $pageOne = $this->projects->get(($pageNr - 2) * $projectsPerRow,
        $projectsPerRow, PROJECT_MASK_GRID_ROW_AGE, PROJECT_SORTBY_AGE);
    $pageTwo = $this->projects->get(($pageNr - 1) * $projectsPerRow,
        $projectsPerRow, PROJECT_MASK_GRID_ROW_AGE, PROJECT_SORTBY_AGE);
    $this->numberOfPages = max(1, intval(ceil(max(0, intval($pageOne['CatrobatInformation']['TotalProjects'])) /
        $projectsPerRow)));

    $params = array();
    $params['layout'] = PROJECT_LAYOUT_GRID_ROW;
    $params['container'] = '#newestProjects';
    $params['loader'] = '#newestProjectsLoader';
    $params['buttons'] = array('prev' => null,
        'next' => '#newestShowMore'
    );
    $featured = $this->projects->featured(1);
    $this->featuredProject = "'" . addslashes(json_encode($featured)) . "'";
    
    $params['content'][0] = $pageOne;
    $params['content'][1] = $pageTwo;
    $params['numProjects'] = intval($pageOne['CatrobatInformation']['TotalProjects']);
    
    $params['page'] = array('number' => $pageNr,
        'numProjectsPerPage' => $projectsPerRow,
        'pageNrMax' => $this->numberOfPages
    );
    
    $params['mask'] = PROJECT_MASK_GRID_ROW_AGE;
    $params['sort'] = PROJECT_SORTBY_AGE;
    $params['filter'] = array('query' => '',
        'author' => ''
    );
    
    $params['config'] = array('LAYOUT_GRID_ROW' => PROJECT_LAYOUT_GRID_ROW,
        'sortby' => array('age' => PROJECT_SORTBY_AGE,
            'downloads' => PROJECT_SORTBY_DOWNLOADS,
            'views' => PROJECT_SORTBY_VIEWS,
            'random' => PROJECT_SORTBY_RANDOM
        )
    );
    
    $this->newestProjectsParams = "'" . addslashes(json_encode($params)) . "'";

    $params['content'][0] = $this->projects->get(($pageNr - 2) * $projectsPerRow,
        $projectsPerRow, PROJECT_MASK_GRID_ROW_DOWNLOADS, PROJECT_SORTBY_DOWNLOADS);
    $params['content'][1] = $this->projects->get(($pageNr - 1) * $projectsPerRow,
        $projectsPerRow, PROJECT_MASK_GRID_ROW_DOWNLOADS, PROJECT_SORTBY_DOWNLOADS);
    $params['container'] = '#mostDownloadedProjects';
    $params['loader'] = '#mostDownloadedProjectsLoader';
    $params['buttons'] = array('prev' => null,
        'next' => '#mostDownloadedShowMore'
    );
    $params['mask'] = PROJECT_MASK_GRID_ROW_DOWNLOADS;
    $params['sort'] = PROJECT_SORTBY_DOWNLOADS;
    $params['additionalTextLabel'] = $this->languageHandler->getString('downloaded');
    $this->mostDownloadedProjectsParams = "'" . addslashes(json_encode($params)) . "'";

    $params['content'][0] = $this->projects->get(($pageNr - 2) * $projectsPerRow,
        $projectsPerRow, PROJECT_MASK_GRID_ROW_VIEWS, PROJECT_SORTBY_VIEWS);
    $params['content'][1] = $this->projects->get(($pageNr - 1) * $projectsPerRow,
        $projectsPerRow, PROJECT_MASK_GRID_ROW_VIEWS, PROJECT_SORTBY_VIEWS);
    $params['container'] = '#mostViewedProjects';
    $params['loader'] = '#mostViewedProjectsLoader';
    $params['buttons'] = array('prev' => null,
        'next' => '#mostViewedShowMore'
    );
    $params['mask'] = PROJECT_MASK_GRID_ROW_VIEWS;
    $params['sort'] = PROJECT_SORTBY_VIEWS;
    $params['additionalTextLabel'] = $this->languageHandler->getString('viewed');
    $this->mostViewedProjectsParams = "'" . addslashes(json_encode($params)) . "'";
  }

  public function __destruct() {
    parent::__destruct();
  }
}
?>
