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
    this.footerSearchBox = $('div#largeFooterMenu input[type="search"]');
    
    this.largeSearchBox.keyup($.proxy(this.submitSearchKey, this));
    this.smallSearchBox.keyup($.proxy(this.submitSearchKey, this));
    this.footerSearchBox.keyup($.proxy(this.submitSearchKey, this));
    
    $("#largeSearchButton").click({input: this.largeSearchBox}, $.proxy(this.submitSearchClick, this));
    $("#footerSearchButton").click({input: this.footerSearchBox}, $.proxy(this.submitSearchClick, this));
  },
  
  setProjectObject : function(object) {
    this.projects = object;
    this.updateSearchBoxQuery();
    this.header.toggleSearchBar();
  },
  
  submitSearchKey: function(event) {
    if(event.keyCode == 13) {
      if(this.projects != null) {
        this.ajaxSearch(event.target.value); 
        this.largeSearchBox.blur();
        this.smallSearchBox.blur();
        this.footerSearchBox.blur();
      } else {
        location.href = this.basePath + 'search/?q=' + encodeURIComponent(event.target.value) + '&p=1';
      }
      event.preventDefault();
    }
  },
  
  submitSearchClick : function(event) {
    if(this.projects != null) {
      this.ajaxSearch(event.data.input.val());
    } else {
      location.href = this.basePath + 'search/?q=' + encodeURIComponent(event.data.input.val()) + '&p=1';
    }
  },
  
  ajaxSearch : function(query) {
    this.projects.setFilter({'author' : '', 'query' : query});
    this.updateSearchBoxQuery();
  },
  
  updateSearchBoxQuery : function() {
    if(this.projects != null) {
      this.largeSearchBox.val(this.projects.params.filter.query);
      this.smallSearchBox.val(this.projects.params.filter.query);
      this.footerSearchBox.val(this.projects.params.filter.query);
    }
  }
});
