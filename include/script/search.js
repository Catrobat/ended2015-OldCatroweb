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

var Search = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function(pageLabels, searchBar) {
    this.pageLabels = pageLabels;
    this.searchBar = searchBar;
    this.projectObject = null;
    this.params = null;
    this.history = window.History;
    this.history.Adapter.bind(window, 'statechange', $.proxy(this.restoreHistoryState, this));

    $(window).keydown($.proxy(function(event) {
      if(event.keyCode == 116 || (event.ctrlKey == true && event.keyCode == 82)) {
        this.clearHistory(event);
      }
    }, this));
  },

  clearHistory : function(event) {
    var context  = this.projectObject.getHistoryState();
    this.history.replaceState({}, this.pageLabels['websiteTitle'] + " - " + this.pageLabels['title'] + " - " + 
        context.query + " - 1", "?q=" + escape(context.query) + "&p=1");
    location.reload();
    event.preventDefault();
  },
  
  setProjectObject : function(projectObject) {
    this.projectObject = projectObject;
    this.params = projectObject.getParameters();
    this.searchBar.setProjectObject(projectObject);
  },
  
  updateSearchResults : function() {
    $('#numberOfSearchResults').text(this.params.numProjects);
  },

  saveHistoryState : function(action) {
    var context  = this.projectObject.getHistoryState();
    var title = this.pageLabels['websiteTitle'] + " - " + this.pageLabels['title'] + " - " + 
          context.query + " - " + context.visibleRows;

    if(action == 'init') {
      var state = this.history.getState();
      if(typeof state.data.content === 'undefined') {
        this.history.replaceState({content: context}, title, "?q=" + escape(context.query) + "&p=" + context.visibleRows);
      } else {
        this.restoreHistoryState();
      }
    }

    if(action == 'push') {
      this.history.pushState({content: context}, title, "?q=" + escape(context.query) + "&p=" + context.visibleRows);
    }
  },

  restoreHistoryState : function() {
    if(this.projectObject != null) {
      var current = this.projectObject.getHistoryState();
      var state = this.history.getState();
      if(typeof state.data.content === 'object') {
        if(state.data.content.visibleRows != current.visibleRows || state.data.content.query != current.query) {
          this.projectObject.restoreHistoryState(state.data.content);
          this.searchBar.updateSearchBoxQuery();
        }
      }
    }
  }
});
