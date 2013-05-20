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

var Index = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function(pageLabels) {
    this.pageLabels = pageLabels;
    this.newestProjects = null;
    this.downloadProjects = null;
    this.viewProjects = null;
    this.history = window.History;
    this.history.Adapter.bind(window, 'statechange', $.proxy(this.restoreHistoryState, this));
  },
  
  setProjectObjects : function(newest, downloads, views) {
    this.newestProjects = newest;
    this.downloadProjects = downloads;
    this.viewProjects = views;
  },
  
  saveHistoryState : function(action) {
    var context = [];
    context['newest'] = this.newestProjects.getHistoryState();
    context['downloads'] = this.downloadProjects.getHistoryState();
    context['views'] = this.viewProjects.getHistoryState();

    if($.isEmptyObject(context['newest']) == false && $.isEmptyObject(context['downloads']) == false && $.isEmptyObject(context['views']) == false) {
      if(action == 'init') {
        var state = this.history.getState();
        if(typeof state.data.newest === 'undefined' && typeof state.data.downloads === 'undefined' && typeof state.data.views === 'undefined') {
          this.history.replaceState({newest: context['newest'], downloads: context['downloads'], views: context['views']}, 
                this.pageLabels['websiteTitle'], '');
        } else {
          this.restoreHistoryState();
        }
      }
      
      if(action == 'push') {
        this.history.pushState({newest: context['newest'], downloads: context['downloads'], views: context['views']},
              this.pageLabels['websiteTitle'], '');
      }
    }
  },

  restoreHistoryState : function() {
    var state = this.history.getState();
    if(this.newestProjects != null) {
      var current = this.newestProjects.getHistoryState();
      if(typeof state.data.newest === 'object') {
        if(state.data.newest.visibleRows != current.visibleRows) {
          this.newestProjects.restoreHistoryState(state.data.newest);
        }
      }
    }
    if(this.downloadProjects != null) {
      var current = this.downloadProjects.getHistoryState();
      if(typeof state.data.downloads === 'object') {
        if(state.data.downloads.visibleRows != current.visibleRows) {
          this.downloadProjects.restoreHistoryState(state.data.downloads);
        }
      }
    }
    if(this.viewProjects != null) {
      var current = this.viewProjects.getHistoryState();
      if(typeof state.data.views === 'object') {
        if(state.data.views.visibleRows != current.visibleRows) {
          this.viewProjects.restoreHistoryState(state.data.views);
        }
      }
    }
  }
});
