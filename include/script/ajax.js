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


var Ajax = Class.$extend( {
  __init__ : function() {
    this.state = 'newestProjects';
  },

  request : function(action, object) {
    switch(action) {
      case 'showFirstPage':
        this.state = 'newestProjects';
        this.actionShowFirstPage(object);
        break;
      case 'showSearchResults':
        this.state = 'search';
        this.actionShowSearchResults(object);
        break;
      case 'update':
        if(this.state == 'newestProjects') {
          this.actionNewestPage(object);
        }
        break;
      default:
        alert('nix');
    }
  },

  actionShowFirstPage : function(object) {
    $.ajax({
      url: object.basePath+"catroid/loadNewestProjects/1.json",
      async: false,
      success: function() {
        location.href = object.basePath+"catroid/index";
      }
    });
  },

  actionShowSearchResults : function(object) {
    $.ajax({
      url: object.basePath+"catroid/loadSearchProjects/result.json?query="+object.searchQuery+"&page="+object.pageNr,
      cache: false,
      timeout: (5000),
    
      success: function(result){
        if(result != "") {
          $("#projectListTitle").text(result.labels['title']);
          $("#fewerProjects").children("span").html(result.labels['prevButton']);
          $("#moreProjects").children("span").html(result.labels['nextButton']);
          
          object.fillSkeletonWithContent(result.content['prev'], object.pageNr - 1);
          object.fillSkeletonWithContent(result.content['current'], object.pageNr);
          object.fillSkeletonWithContent(result.content['next'], object.pageNr + 1);
        }
      },
      error: function(result, errCode) {
        if(errCode == "timeout") {
          window.location.reload(false);          
        }        
      }
    });
  },

  actionNewestPage : function(object) {
    $.ajax({
      url: object.basePath+"catroid/loadNewestProjects/"+object.pageNr+".json",
      cache: false,
      timeout: (5000),
    
      success: function(result){
        if(result != "") {
          $("#projectListTitle").text(result.labels['title']);
          $("#fewerProjects").children("span").html(result.labels['prevButton']);
          $("#moreProjects").children("span").html(result.labels['nextButton']);
          
          object.fillSkeletonWithContent(result.content['prev'], object.pageNr - 1);
          object.fillSkeletonWithContent(result.content['current'], object.pageNr);
          object.fillSkeletonWithContent(result.content['next'], object.pageNr + 1);
        }
      },
      error: function(result, errCode) {
        if(errCode == "timeout") {
          window.location.reload(false);          
        }        
      }
    });
  }

});
