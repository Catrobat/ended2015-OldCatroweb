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
  __init__ : function() {
    this.projectObject = null;
    this.params = null;
    this.history = window.History;
    this.history.Adapter.bind(window, 'statechange', $.proxy(this.restoreHistoryState, this));
  },
  
  setProjectObject : function(projectObject) {
    this.projectObject = projectObject;
    this.params = projectObject.getParameters();
  },
  
  updateSearchResults : function() {
    $('#numberOfSearchResults').text(this.projectObject.getNumProjects());
  },

  saveHistoryState : function(action) {
    if(action == 'init') {
      var state = this.history.getState();
      if(typeof state.data.content === 'undefined') {
        var context  = this.projectObject.getHistoryState();
        this.history.replaceState({content: context}, "State " + context.page, "?state=" + context.page);
      } else {
        this.restoreHistoryState();
      }
    }

    if(action == 'push') {
      var context  = this.projectObject.getHistoryState();
      this.history.pushState({content: context}, "State " + context.page, "?state=" + context.page);
    }
  },

  restoreHistoryState : function() {
    if(this.projectObject != null) {
      var state = this.history.getState();
      if(state.data.content.page != this.params.page.number) {
        this.projectObject.restoreHistoryState(state.data.content);
      }
    }
  }
});
