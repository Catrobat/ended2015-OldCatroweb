/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    An additional term exception under section 7 of the GNU Affero
 *    General Public License, version 3, is available at
 *    http://www.catroid.org/catroid/licenseadditionalterm
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

var Index = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function(maxLoadProjects, maxLoadPages, pageNr, searchQuery, task, strings) {
    var self = this;
    this.newestProjects = new NewestProjects(maxLoadProjects, maxLoadPages, pageNr, strings);
    this.searchProjects = new SearchProjects(maxLoadProjects, maxLoadPages, pageNr, searchQuery, strings);

    this.task = task;
    this.state = "newestProjects";
    window.onpopstate = function(event) {      
      if(event.state && event.state.newestProjects) {        
        self.newestProjects.restoreHistoryState(event.state);
        self.state = "newestProjects";
      }
      if(event.state && event.state.searchProjects) {
        self.searchProjects.restoreHistoryState(event.state);
        self.state = "searchProjects";
      }        
    }
    setTimeout(function() { self.initialize(self); }, 50);
    
    $("#aIndexWebLogoLeft").click($.proxy(this.startPage, this));
    $("#headerMenuButton").click(function() { self.newestProjects.saveStateToSession(self.newestProjects.pageNr.current); });
    $("#searchForm").submit($.proxy(this.search, this));
    $("#headerCancelSearchButton").click($.proxy(this.cancelSearch, this));
  },
  
  initialize : function(object) {    
    if(window.history.state != null && window.history.state.pageContent.current != null) {      
      // FF 4.0 does not fire onPopState.event, webkit does
      if(window.history.state.newestProjects) {
        object.newestProjects.restoreHistoryState(window.history.state);
        object.state = "newestProjects";
      }
      if(window.history.state.searchProjects) {        
        object.searchProjects.restoreHistoryState(window.history.state);
        object.state = "searchProjects";        
      }      
      return;
    }
    if(object.task == "newestProjects") {
      object.newestProjects.initialize(object.newestProjects);
    }
    if(object.task == "searchProjects") {
      object.searchProjects.initialize(object.searchProjects);
    }            
  },
  
  switchState : function(state) {
    if(state == "newestProjects") {
      this.searchProjects.setInactive();
      this.newestProjects.setActive();
      this.state = "newestProjects";
    }
    if(state == "searchProjects") {
      this.newestProjects.setInactive();
      this.searchProjects.setActive();      
      this.state = "searchProjects";
    }
  },
  
  search : function() {     
    if (this.state == "searchProjects")
      this.searchProjects.triggerSearch(true);
    else if($.trim($("#searchQuery").val()) != "") {
      this.searchProjects.triggerSearch(true);
      this.switchState("searchProjects");      
    }
    return false;
  },

  cancelSearch : function() {
    this.switchState("newestProjects");
  },
  
  startPage : function() {
    this.switchState("newestProjects");
    this.newestProjects.showStartPage();
  }
});
