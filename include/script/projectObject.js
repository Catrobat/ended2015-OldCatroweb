/*
 *Catroid: An on-device visual programming system for Android devices
 *Copyright (C) 2010-2013 The Catrobat Team
 *(<http://developer.catrobat.org/credits>)
 *
 *This program is free software: you can redistribute it and/or modify
 *it under the terms of the GNU Affero General Public License as
 *published by the Free Software Foundation, either version 3 of the
 *License, or (at your option) any later version.
 *
 *An additional term exception under section 7 of the GNU Affero
 *General Public License, version 3, is available at
 *http://developer.catrobat.org/license_additional_term
 *
 *This program is distributed in the hope that it will be useful,
 *but WITHOUT ANY WARRANTY; without even the implied warranty of
 *MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *GNU Affero General Public License for more details.
 *
 *You should have received a copy of the GNU Affero General Public License
 *along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
 
 var ProjectObject = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function(params, callbacks) {
    this.params = $.parseJSON(params);
    
    if(typeof callbacks === 'undefined') {
      this.params.callbacks = {};
    } else {
      this.params.callbacks = callbacks;
    }
    
    if(typeof this.params.layout === 'undefined') {
      return this.missing('layout');
    }
    if(typeof this.params.container === 'undefined') {
      return this.missing('container');
    }
    if(typeof this.params.loader === 'undefined') {
      return this.missing('loader');
    }
    if(typeof this.params.buttons === 'undefined') {
      return this.missing('buttons');
    }
    if(typeof this.params.buttons.prev === 'undefined') {
      return this.missing('buttons.prev');
    }
    if(typeof this.params.buttons.next === 'undefined') {
      return this.missing('buttons.next');
    }
    if(typeof this.params.content === 'undefined') {
      this.params.content = array();
    }
    if(typeof this.params.numProjects === 'undefined') {
      return this.missing('numProjects');
    }
    if(typeof this.params.page === 'undefined') {
      return this.missing('page');
    }
    if(typeof this.params.page.number === 'undefined') {
      return this.missing('page.number');
    }
    if(typeof this.params.page.numProjectsPerPage === 'undefined') {
      return this.missing('page.numProjectsPerPage');
    }
    if(typeof this.params.page.pageNrMax === 'undefined') {
      return this.missing('page.pageNrMax');
    }
    if(typeof this.params.mask === 'undefined') {
      return this.missing('mask');
    }
    if(typeof this.params.sort === 'undefined') {
      return this.missing('sort');
    }
    if(typeof this.params.filter === 'undefined') {
      return this.missing('filter');
    }
    if(typeof this.params.filter.query === 'undefined') {
      return this.missing('filter.query');
    }
    if(typeof this.params.filter.author === 'undefined') {
      return this.missing('filter.author');
    }
    if(typeof this.params.config === 'undefined') {
      return this.missing('config');
    }
    
    if($(this.params.container).length == 0) {
      return this.missing('container');
    }
    
    this.params.reachedLastPage = false;
    this.hasPrevButton = false;
    this.hasNextButton = false;
    
    if($(this.params.buttons.prev).length != 0) {
      this.hasPrevButton = true;
      $(this.params.buttons.prev).click($.proxy(this.prevPage, this));  
    }
    if($(this.params.buttons.next).length != 0) {
      this.hasNextButton = true;
      $(this.params.buttons.next).click($.proxy(this.nextPage, this)); 
    }

    this.projectLoader = null;
    this.projectContentFiller = null;
  },

  missing : function(object) {
    alert(object + ': obligatory paramater missing or does not exist!');
    console.log(object + ': obligatory paramater missing or does not exist!');
    return false;
  },

  init : function() {
    this.projectLoader = new ProjectLoader($.proxy(this.getParameters, this), $.proxy(this.loadProjectsRequestSuccess, this), $.proxy(this.loadProjectsRequestError, this));    
    this.projectContentFiller = new ProjectContentFiller($.proxy(this.getParameters, this));

    if(typeof this.params.callbacks['history'] === 'function') {
      this.params.callbacks['history'].call(this, 'init');
    }
  },

  getParameters : function() {
    return this.params;
  },

  loadProjectsRequestSuccess : function(result) {
    if(result == null) {
      alert('loadProjectsRequestSuccess: no result!');
      return;
    }
    if(result.error) {
      alert('loadProjectsRequestSuccess: ' + result.error);
      return;
    }
    if(result.CatrobatInformation == null || result.CatrobatProjects == null) {
      alert('loadProjectsRequestSuccess: wrong result!');
      return;
    }
    if(!this.projectContentFiller.isReady()) {
      alert('loadProjectsRequestSuccess: no layout selected!');
      return;
    }

    var info = result.CatrobatInformation;
    this.params.numProjects = info['TotalProjects'];
    this.params.page.pageNrMax = Math.max(1, Math.ceil(Math.max(0, this.params.numProjects) / this.params.page.numProjectsPerPage));
    this.params.content.push(result);

    this.projectContentFiller.fill(result);

    if(typeof this.params.callbacks['history'] === 'function') {
      this.params.callbacks['history'].call(this, 'push');
    }
  },

  loadProjectsRequestError : function(error, errCode) {
    alert(error + ' ' + errCode);
    console.log(error + ' ' + errCode);
    
    if(typeof this.params.callbacks['error'] === 'function') {
      this.params.callbacks['error'].call();
    }
  },

  prevPage : function() {
    this.params.page.number = Math.max(1, this.params.page.number - 1);
    this.projectLoader.loadPage();
  },

  nextPage : function() {
    this.params.page.number = Math.min(this.params.page.pageNrMax, this.params.page.number + 1);
    if(!this.params.reachedLastPage) {
      this.projectLoader.loadPage();
    } else {
      this.projectContentFiller.extend();
      
      if(typeof this.params.callbacks['history'] === 'function') {
        this.params.callbacks['history'].call(this, 'push');
      }
    }
  },
  
  getHistoryState : function() {
    var state = {};
    if(this.projectContentFiller != null) {
      state = {content: this.params.content, 
          page: this.params.page.number,
          pageNrMax: this.params.page.pageNrMax,
          sort: this.params.sort,
          author: this.params.filter.author,
          query: this.params.filter.query,
          amount: this.params.numProjects,
          last: this.params.reachedLastPage}
      
      if(this.projectContentFiller.getHistoryState != null) {
        state = this.projectContentFiller.getHistoryState(state);
      }
    }
    return state;
  },
  
  restoreHistoryState : function(state) {
    if(this.projectContentFiller != null) {
      this.projectContentFiller.clear();
      this.params.content = state.content;
      this.params.page.number = state.page;
      this.params.page.pageNrMax = state.pageNrMax;
      this.params.sort = state.sort;
      this.params.filter.author = state.author;
      this.params.filter.query = state.query;
      this.params.numProjects = state.amount;
      this.params.reachedLastPage = state.last;
      this.projectContentFiller.display();
      
      if(this.projectContentFiller.restoreHistoryState != null) {
        this.projectContentFiller.restoreHistoryState(state);
      }
    }
  },

  setSort : function(sortby) {
    this.params.sort = sortby;
    this.params.page.number = 1;
    this.params.content = [];
    this.params.reachedLastPage = false;

    this.projectContentFiller.clear();
    this.projectLoader.loadPage();
  },
  
  setFilter : function(filter) {
    this.params.filter.author = filter.author;
    this.params.filter.query = filter.query;
    this.params.page.number = 1;
    this.params.content = [];
    this.params.reachedLastPage = false;
    
    this.projectContentFiller.clear();
    this.projectLoader.loadPage();
  }

});
 