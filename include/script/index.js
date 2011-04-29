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
  __init__ : function(basePath, maxLoadProjects, maxVisibleProjects, pageNr) {
    var self = this;
    this.newestProjects = new NewestProjects(this, basePath, maxLoadProjects, maxVisibleProjects, pageNr);

    window.onpopstate = function(event) {
      self.newestProjects.restoreHistoryState(event.state);  
    }
    setTimeout(this.newestProjects.initialize, 50, this.newestProjects);
    
    $("#aIndexWebLogoLeft").click($.proxy(this.newestProjects.showStartPage, this.newestProjects));
    $("#headerMenuButton").click(function() { self.newestProjects.saveStateToSession(self.newestProjects.pageNr.current); });
    $("#searchForm").submit($.proxy(this.startSearch, this));
  },
  
  startSearch : function() {
    this.newestProjects.setInactive();
    this.newestProjects.setActive();

    return false;
  }

});
