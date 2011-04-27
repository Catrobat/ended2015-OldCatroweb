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
    this.siteState = "newestProjects";  /*deprecated*/
    this.writeHistory = true;
    this.initialized = false;
    this.basePath = basePath;
    this.pageNr = parseInt(pageNr);
    this.maxLoadProjects = parseInt(maxLoadProjects);
    this.maxVisibleProjects = parseInt(maxVisibleProjects);
    this.pageContent = { prev : null, current : null, next : null};
    this.pageLabels = new Array();
    this.ajax = new Ajax(); /*deprecated*/
    this.blockAjaxRequest = false;
    
    $("#fewerProjects").click($.proxy(this.prevPage, this));
    $("#moreProjects").click($.proxy(this.nextPage, this));
    $("#aIndexWebLogoLeft").click($.proxy(this.showFirstPage, this));
    $("#searchForm").submit($.proxy(this.showSearchResults, this));
    
    window.onpopstate = function(event) {
      if(event.state != null || self.pageNr == event.state.pageNr) {
        self.writeHistory = false;
      }
      self.pageNr = event.state.pageNr;
      self.pageContent = event.state.pageContent;
      self.pageLabels = event.state.pageLabels;
      self.createSkeleton();
      self.fillSkeletonWithContent();
      self.hideOrShowButtons();
      self.initialized = true;
    }
    
    setTimeout(this.initialize, 50, this); 
  },
  
  initialize : function(object){
    if(!object.initialized) {
      object.createSkeleton();
      object.requestPage(object.pageNr);
      object.requestPage(object.pageNr-1);
      object.requestPage(object.pageNr+1);
      object.initialized = true;
    }
  },

  nextPage : function() {
    if(!this.blockAjaxRequest) {
      this.blockAjaxRequest = true;
      this.pageNr = parseInt(this.pageNr) + 1;
    
      this.pageContent.current = this.pageContent.current.concat(this.pageContent.next);
      if(this.pageContent.current.length > this.maxVisibleProjects) {
        this.pageContent.current = this.pageContent.current.slice(this.maxLoadProjects);
        this.pageContent.prev = null; 
      }
      this.pageContent.next = null;
      this.loadAndCachePage();
    }
  },

  prevPage : function() {
    if(!this.blockAjaxRequest) {
      this.blockAjaxRequest = true;
      this.pageNr = parseInt(this.pageNr) - 1;
      
      this.pageContent.current = this.pageContent.prev.concat(this.pageContent.current);
      if(this.pageContent.current.length > this.maxVisibleProjects) {
        this.pageContent.current = this.pageContent.current.slice(0, this.maxVisibleProjects);
        this.pageContent.next = null;
      }
      this.pageContent.prev = null;
      this.loadAndCachePage();
    }
  },
  
  showFirstPage : function() {
    this.pageNr = 1;
    this.siteState = "newestProjects";
    this.pageContent.prev = "NIL";
    this.pageContent.current = null;
    this.pageContent.next = null;
    this.requestPage(this.pageNr);
    this.requestPage(this.pageNr+1);

    $("#normalHeaderButtons").toggle(true);
    $("#cancelHeaderButton").toggle(false);
    $("#headerSearchBox").toggle(false);
    $("#searchQuery").val("");
  },
  
  showSearchResults : function() {
    this.searchQuery = $("#searchQuery").val();
    this.pageNr = 1;
    this.siteState = "searchResults";
    this.testAndSetBounderies();
    this.loadAndCachePage();
    return false;
  },

  testAndSetBounderies : function() {
    if((this.pageNr*2-1) >= this.numberOfPages) {
	    this.pageNr = ((this.numberOfPages+1) /2);
    }
	    
    if(this.pageNr < 0) {
      this.pageNr = 0;
    }
  },
  
  hideOrShowButtons : function() {
    if(this.pageContent.prev == "NIL") {
      $("#fewerProjects").toggle(false);
    } else {
      $("#fewerProjects").toggle(true);
    }
    if(this.pageContent.next == "NIL") {
      $("#moreProjects").toggle(false);
    } else {
      $("#moreProjects").toggle(true);
    }
  },

  loadAndCachePage : function() {
    if(this.pageContent.next == null) {
      this.requestPage(this.pageNr + 1);
    }
    if(this.pageContent.prev == null) {
      this.requestPage(this.pageNr - 1);
    }
    
    if(this.writeHistory) {
      var stateObject = { pageNr: 0, pageContent: {}, pageLabels: new Array()};
      
      stateObject.pageNr = this.pageNr;
      stateObject.pageContent = this.pageContent;
      stateObject.pageLabels = this.pageLabels;
      
      history.pushState(stateObject, "Page " + this.pageNr, this.basePath+"catroid/index/" + this.pageNr);
    }
    this.writeHistory = true;
  },

  requestPage : function(pageNr) {
    var self = this;
    $.ajax({
      url: self.basePath+"catroid/loadNewestProjects/"+pageNr+".json?cp="+self.pageNr,
      cache: false,
      timeout: (5000),
    
      success: function(result){
        if(result != "") {
          self.pageLabels = result.labels;

          if(self.pageNr == pageNr) {
            if(self.pageContent.current == null) {
              self.pageContent.current = result.content;
            }
          }
          else {
            if(self.pageNr - 1 == pageNr) {
              self.pageContent.prev = result.content;
            }
            if(self.pageNr + 1 == pageNr) {
              self.pageContent.next = result.content;
            }
          }
          
          if(self.pageContent.prev != null && self.pageContent.current != null && self.pageContent.next != null) {
            self.fillSkeletonWithContent();
            self.hideOrShowButtons();
            self.blockAjaxRequest = false;
          }
        }
      },
      error: function(result, errCode) {
        if(errCode == "timeout") {
          window.location.reload(false);          
        }        
      }
    });
  }, 

  createSkeleton : function() {
    if(!this.initialized) {
      var containerContent = $("<div />").addClass("projectListRow");

      var whiteBox = null;
      var projectListElementRow = null;
      for(var i = 0; i < this.maxVisibleProjects; i++) {

        if(whiteBox != null) {
          whiteBox.append(projectListElementRow);
          whiteBox.append("<div />").css("clear", "both");
          containerContent.append(whiteBox);
          var projectListSpacer = $("<div />").addClass("projectListSpacer").attr("id", "projectListSpacer"+i);
          containerContent.append(projectListSpacer);
        }

        whiteBox = $("<div />").addClass("whiteBoxMain").attr("id", "whiteBox"+i);
        projectListElementRow = $("<div />").addClass("projectListElementRow");

	      var projectListElement = $("<div />").addClass("projectListElement").attr("id", "projectListElement"+i);
	     
        var projectListThumbnail = $("<div />").addClass("projectListThumbnail").attr("id", "projectListThumbnail"+i);
        var projectListDetailsLinkThumb = $("<a />").addClass("projectListDetailsLink").attr("id", "projectListDetailsLinkThumb"+i);
	      var projectListPreview = $("<img />").addClass("projectListPreview").attr("id", "projectListPreview"+i).attr("src", this.basePath+"images/symbols/ajax-loader.gif").attr("alt", "loading...");
	     
	      var projectListTitle = $("<div />").addClass("projectDetailLine").attr("id", "projectListTitle"+i).html("loading...");
	      var projectListDescription = $("<div />").addClass("projectDetailLine").attr("id", "projectListDescription"+i);
	      var projectListDetails = $("<div />").addClass("projectListDetails");

	      projectListThumbnail.append(projectListDetailsLinkThumb.append(projectListPreview).wrap("<div />"));
        projectListDetails.append(projectListTitle);
        projectListDetails.append(projectListDescription);
	    	 
	      projectListElement.append(projectListThumbnail);
        projectListElement.append(projectListDetails);
	  
        projectListElementRow.append(projectListElement);
      }

      whiteBox.append(projectListElementRow);
      whiteBox.append("<div />").css("clear", "both");
      containerContent.append(whiteBox);
      containerContent.append($("<div />").addClass("projectListSpacer").attr("id", "projectListSpacer"+i));

      $("#projectContainer").append(containerContent);
    }
  },
 
  fillSkeletonWithContent : function() {
    $("#projectListTitle").text(this.pageLabels['title']);
    $("#fewerProjects").children("span").html(this.pageLabels['prevButton']);
    $("#moreProjects").children("span").html(this.pageLabels['nextButton']);

    var content = this.pageContent.current;
    for(var i=0; i<this.maxVisibleProjects; i++) {
      if(content != null && content[i]) {
        if($("#projectListElement"+i).length > 0) {
          $("#whiteBox"+i).css("display", "block");
          $("#projectListSpacer"+i).css("display", "block");
          $("#projectListThumbnail"+i).attr("title", content[i]['title']);
          $("#projectListDetailsLinkThumb"+i).attr("href", this.basePath+"catroid/details/"+content[i]['id']);
          $("#projectListPreview"+i).attr("src", content[i]['thumbnail']).attr("alt", content[i]['title']);

          $("#projectListTitle"+i).html("<div class='projectDetailLineMaxWidth'><a class='projectListDetailsLinkBold' href='"+this.basePath+"catroid/details/"+content[i]['id']+"'>"+content[i]['title']+"</a></div>");
          $("#projectListDescription"+i).html("by <a class='projectListDetailsLink' href='#'>unknown</a><br />uploaded "+content[i]['upload_time']+" ago");
          $("#projectListDescription"+i).html("uploaded "+content[i]['upload_time']+" ago");
        }
      }
      else {
        $("#whiteBox"+i).css("display", "none");
        $("#projectListSpacer"+i).css("display", "none");
      }
    }
  }

});
