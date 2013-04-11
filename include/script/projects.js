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

var Projects = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function(params) {
    this.params = jQuery.parseJSON(params);;
    
    this.projects = ProjectObject({"layout"    : this.params.layout,
                               "container" : this.params.container,
                               "buttons"   : {"prev" : "#fewerProjects", "next" : "#moreProjects"}, 
                               "filter"    : {"searchQuery" : this.params.filter.searchQuery, "author" : this.params.filter.author},
                               "sort"      : this.params.sort,
                               "page"      : this.params.page,
                               "config"    : this.params.config
                             });
    
    $("#sortby_newest").click($.proxy(this.changeSort, this, "#sortby_newest", this.params.config.sortby.age));
    $("#sortby_downloads").click($.proxy(this.changeSort, this, "#sortby_downloads", this.params.config.sortby.downloads));
    $("#sortby_random").click($.proxy(this.changeSort, this, "#sortby_random", this.params.config.sortby.random));
    $("#sortby_views").click($.proxy(this.changeSort, this, "#sortby_views", this.params.config.sortby.views));
  },
  
  changeSort : function(sender, sortby) {
    $('a.sortLink[class*="sortLinkActive"]').toggleClass('sortLinkActive');
    $(sender).toggleClass('sortLinkActive');
    this.projects.setSort(sortby);
  },
  
  changeFilter : function(sender, filter) {
    this.projects.setFilter({'author' : filter.author, 'searchQuery' : filter.searchQuery});
  }
});
