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
  __init__ : function(params) {
    this.params = $.parseJSON(params);
    
    if(typeof this.params.layout === 'undefined') {
      alert('layout: obligatory paramater missing!');
      return;
    }
    if(typeof this.params.container === 'undefined') {
      alert('container: obligatory paramater missing!');
      return;
    }
    if(typeof this.params.loader === 'undefined') {
      alert('loader: obligatory paramater missing!');
      return;
    }
    if(typeof this.params.buttons === 'undefined') {
      alert('buttons: obligatory paramater missing!');
      return;
    }
    if(typeof this.params.buttons.prev === 'undefined') {
      alert('buttons.prev: obligatory paramater missing!');
      return;
    }
    if(typeof this.params.buttons.next === 'undefined') {
      alert('buttons.next: obligatory paramater missing!');
      return;
    }
    if(typeof this.params.page === 'undefined') {
      alert('page: obligatory paramater missing!');
      return;
    }
    if(typeof this.params.firstPage === 'undefined') {
      this.params.firstPage = null;
    }
    if(typeof this.params.page.number === 'undefined') {
      alert('page.number: obligatory paramater missing!');
      return;
    }
    if(typeof this.params.page.numProjectsPerPage === 'undefined') {
      alert('page.numProjectsPerPage: obligatory paramater missing!');
      return;
    }
    if(typeof this.params.page.pageNrMax === 'undefined') {
      alert('page.pageNrMax: obligatory paramater missing!');
      return;
    }
    if(typeof this.params.mask === 'undefined') {
      alert('mask: obligatory paramater missing!');
      return;
    }
    if(typeof this.params.sort === 'undefined') {
      alert('sort: obligatory paramater missing!');
      return;
    }
    if(typeof this.params.filter === 'undefined') {
      alert('filter: obligatory paramater missing!');
      return;
    }
    if(typeof this.params.filter.query === 'undefined') {
      alert('filter.query: obligatory paramater missing!');
      return;
    }
    if(typeof this.params.filter.author === 'undefined') {
      alert('filter.author: obligatory paramater missing!');
      return;
    }
    if(typeof this.params.config === 'undefined') {
      alert('config: obligatory paramater missing!');
      return;
    }
    
    if($(this.params.container).length == 0) {
      alert('container: element does not exist!');
      return;
    }
    
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

    this.projectLoader = new ProjectLoader($.proxy(this.getParameters, this), $.proxy(this.loadProjectsRequestSuccess, this), $.proxy(this.loadProjectsRequestError, this));    
    this.projectContentFiller = new ProjectContentFiller($.proxy(this.getParameters, this));
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
    this.params.page.pageNrMax = Math.max(1, Math.ceil(Math.max(0, info['TotalProjects']) / this.params.page.numProjectsPerPage) - 1);
    this.projectContentFiller.fill(result);
  },
  
  loadProjectsRequestError : function(error, errCode) {
    alert(error + ' ' + errCode);
  },
  
  prevPage : function() {
    this.params.page.number = Math.max(1, this.params.page.number - 1);
    this.projectLoader.loadPage();
  },
  
  nextPage : function() {
    this.params.page.number = Math.min(this.params.page.pageNrMax, this.params.page.number + 1);
    this.projectLoader.loadPage();
  }, 
  
  setSort : function(sortby) {
    this.params.sort = sortby;
    this.params.page.number = 1;
    this.projectLoader.loadPage();
  },
  
  setFilter : function(filter) {
    this.params.filter.author = filter.author;
    this.params.filter.query = filter.query;
    this.params.page.number = 1;
    
    $(this.params.container).each(
        function(element){
          alert(element);
        }
     );
    
    this.projectLoader.loadPage();
  }

});
 