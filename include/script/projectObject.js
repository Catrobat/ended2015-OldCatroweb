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
    this.params = params;   
    this.projectLoader = new ProjectLoader($.proxy(this.getParameters, this), $.proxy(this.loadProjectsRequestSuccess, this), $.proxy(this.loadProjectsRequestError, this));    
    this.projectContentFiller = new ProjectContentFiller($.proxy(this.getParameters, this));

    if(this.params.buttons != null) {
      if(this.params.buttons.prev != null && this.params.buttons.prev != "") {
      $(this.params.buttons.prev).click($.proxy(this.prevPage, this));  
    }
    
    if(this.params.buttons.next != null && this.params.buttons.next != "") {
      $(this.params.buttons.next).click($.proxy(this.nextPage, this)); 
    }
  }
},

getParameters : function() {
  return this.params;
},

loadProjectsRequestSuccess : function(result) {
  if(this.projectContentFiller.isReady()) {
    if(!this.projectContentFiller.fill(result)) {
      var error = this.projectContentFiller.getError();
      common.showAjaxErrorMsg(error); 
    }
  }
},

loadProjectsRequestError : function(result) {
  common.showAjaxErrorMsg(result);
},

prevPage : function() {
  this.params.page.number = (this.params.page.number > 1)? this.params.page.number - 1 : 1;
  this.projectLoader.loadPage();
},

nextPage : function() {
  this.params.page.number = (this.params.page.number >= this.params.page.pageNrMax)? this.params.page.pageNrMax : this.params.page.number + 1;
  this.projectLoader.loadPage();
}, 

setSort : function(sortby) {
  this.params.sort = sortby;
  this.params.page.number = 1;
  this.projectLoader.loadPage();
},

setFilter : function(filter) {
  this.params.filter.author = filter.author;
  this.params.filter.searchQuery = filter.searchQuery;
  this.params.page.number = 1;
  this.projectLoader.loadPage();
}

});
 