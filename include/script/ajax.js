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
  },

  request : function(object) {
    switch(object.siteState) {
      case 'newestProjects':
        this.actionNewestPage(object);
        break;
      case 'searchResults':
        this.actionShowSearchResults(object);
        break;
      default:
        alert('nix');
    }
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
          
          object.hideOrShowButtons(result);
          object.fillSkeletonWithContent(result.content['prev'], object.pageNr - 1);
          object.fillSkeletonWithContent(result.content['current'], object.pageNr);
          object.fillSkeletonWithContent(result.content['next'], object.pageNr + 1);

          if(object.writeHistory) {
            history.pushState({pageNr: object.pageNr, searchQuery : object.searchQuery}, "Search " + object.pageNr, 
                              object.basePath+"catroid/search/" + object.searchQuery + "/"  + object.pageNr);
          }
          object.writeHistory = true;
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
      url: object.basePath+"catroid/loadNewestProjects/"+(object.pageNr*2-1)+".json",
      cache: false,
      timeout: (5000),
    
      success: function(result){
        if(result != "") {
          $("#projectListTitle").text(result.labels['title']);
          $("#fewerProjects").children("span").html(result.labels['prevButton']);
          $("#moreProjects").children("span").html(result.labels['nextButton']);
          
          object.hideOrShowButtons(result);
          object.fillSkeletonWithContent(result.content['prev'], object.pageNr - 1);
          object.fillSkeletonWithContent(result.content['current'], object.pageNr);
          object.fillSkeletonWithContent(result.content['next'], object.pageNr + 1);

          if(object.writeHistory) {
            history.pushState({pageNr: object.pageNr}, "Page " + object.pageNr, 
                              object.basePath+"catroid/index/" + object.pageNr);
          }
          object.writeHistory = true;
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
