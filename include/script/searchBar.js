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

var SearchBar = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function(header) {
    this.projects = null;
    this.header = header;
    
    this.searchResultBox = $('#searchResultContainer');
    this.largeSearchBox = $('div.largeSearchBarMiddle input[type="search"]');
    this.smallSearchBox = $('div#smallSearchBar input[type="search"]');
    
    this.largeSearchBox.keyup($.proxy(this.submitSearch, this));
    this.smallSearchBox.keyup($.proxy(this.submitSearch, this));
  },
  
  setProjectObject : function(object) {
    this.projects = object;
    this.updateSearchBoxQuery();
    this.header.toogleSearchBar();
  },
  
  submitSearch : function(event) {
    if(event.keyCode == 13) {
      if(this.projects != null) {
        this.ajaxSearch(event.target.value);
        this.largeSearchBox.blur();
        this.smallSearchBox.blur();
      } else {
        location.href = this.basePath + 'catroid/search/?q=' + event.target.value + '&p=1';
      }
    }
    event.preventDefault();
  },
  
  ajaxSearch : function(query) {
    this.projects.setFilter({'author' : '', 'searchQuery' : query});
    this.updateSearchBoxQuery();
  },
  
  updateSearchBoxQuery : function() {
    if(this.projects != null) {
      this.largeSearchBox.val(this.projects.params.filter.searchQuery);
      this.smallSearchBox.val(this.projects.params.filter.searchQuery);
    }
  }
});
