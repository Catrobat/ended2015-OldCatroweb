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


var Index = Class.$extend( {
  __init__ : function(basePath, maxLoadProjects, maxVisibleProjects, pageNr, searchQuery) {
    var self = this;
    this.newestProjects = new NewestProjects(this, basePath, maxLoadProjects, maxVisibleProjects, pageNr);
    this.searchProjects = new SearchProjects(this, basePath, maxLoadProjects, maxVisibleProjects, pageNr, searchQuery);

    this.state = "newestProjects";
    window.onpopstate = function(event) {
      self.newestProjects.restoreHistoryState(event.state);  
    }
    setTimeout(this.newestProjects.initialize, 50, this.newestProjects);
    
    $("#aIndexWebLogoLeft").click($.proxy(this.startPage, this));
    $("#headerMenuButton").click(function() { self.newestProjects.saveStateToSession(self.newestProjects.pageNr.current); });
    $("#searchForm").submit($.proxy(this.search, this));
    $("#headerCancelSearchButton").click($.proxy(this.cancelSearch, this));
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
    this.searchProjects.triggerSearch();
    this.switchState("searchProjects");
    return false;
  },

  cancelSearch : function() {
    this.switchState("newestProjects");
  },
  
  startPage : function() {
    this.newestProjects.showStartPage();
    this.switchState("newestProjects");
  }

});
