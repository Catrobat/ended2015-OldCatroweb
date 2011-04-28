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
    this.siteState = "newestProjects";
    this.initialized = false;
    this.basePath = basePath;    
    this.pageNr = {prev : parseInt(pageNr)-1, current : parseInt(pageNr), next : parseInt(pageNr)+1 };    
    this.maxLoadProjects = parseInt(maxLoadProjects);
    this.maxVisibleProjects = parseInt(maxVisibleProjects);
    this.pageContent = { prev : null, current : null, next : null};
    this.pageLabels = new Array();    
    this.ajaxRequestMutex = false;
    
    $("#fewerProjects").click($.proxy(this.prevPage, this));
    $("#moreProjects").click($.proxy(this.nextPage, this));
    $("#aIndexWebLogoLeft").click($.proxy(this.showFirstPage, this));
    $("#searchForm").submit($.proxy(this.showSearchResults, this));
    
    window.onpopstate = function(event) {
      self.restoreHistoryState(event.state);
    }
    
    setTimeout(this.initialize, 50, this); 
  },
  
  initialize : function(object){
    if(!object.initialized) {
      if (window.history.state!=null && window.history.state.pageContent.current != null) {
        // FF 4.0 does not fire onPopState.event, webkit does
        object.restoreHistoryState(window.history.state);
        return;
      }
      object.createSkeleton();
      object.requestPage(object.pageNr.current);
      object.requestPage(object.pageNr.next);
      object.requestPage(object.pageNr.prev);      
      object.initialized = true;
    }
  },
  
  saveHistoryState : function ()
  {
    if (history.pushState ) {
      var stateObject = { pageNr: {}, pageContent: {}, pageLabels: new Array()};
      stateObject.pageNr = this.pageNr;
      stateObject.pageContent = this.pageContent;
      stateObject.pageLabels = this.pageLabels;
    
      history.pushState(stateObject, "Page " + this.pageNr.current, this.basePath+"catroid/index/" + this.pageNr.current);
    }
  },
  
  restoreHistoryState : function(state) {    
    this.pageNr = state.pageNr;    
    this.pageContent = state.pageContent;
    this.pageLabels = state.pageLabels;
    this.createSkeleton();
    this.loadAndCachePage();
    this.fillSkeletonWithContent();
    this.hideOrShowButtons();
    this.initialized = true;
  },
  
  blockAjaxRequest: function() {
    if(!this.ajaxRequestMutex) {
      this.ajaxRequestMutex=true;
      $("#projectContainer").fadeTo(10, 0.20);
      return true;
    }
    return false;
  },
  
  unblockAjaxRequest : function() {
    $("#projectContainer").fadeTo(100, 1.0);
    this.ajaxRequestMutex=false;
  },

  nextPage : function() {
    if(this.blockAjaxRequest()) {            
      this.pageContent.current = this.pageContent.current.concat(this.pageContent.next);
      if(this.pageContent.current.length > this.maxVisibleProjects) {
        this.pageContent.current = this.pageContent.current.slice(this.maxLoadProjects);
        this.pageContent.prev = null; 
        this.pageNr.prev++;        
      }      
      this.pageNr.next++;
      this.pageNr.current++;
      
      this.pageContent.next = null;
      this.saveHistoryState();
      this.loadAndCachePage();
    }
  },

  prevPage : function() {
    if(this.blockAjaxRequest()) {
      this.pageContent.current = this.pageContent.prev.concat(this.pageContent.current);
      if(this.pageContent.current.length > this.maxVisibleProjects) {
        this.pageContent.current = this.pageContent.current.slice(0, this.maxVisibleProjects);
        this.pageContent.next = null;
        this.pageNr.next--;
      }
      this.pageNr.prev--;
      this.pageNr.current--;
      
      this.pageContent.prev = null;
      this.saveHistoryState();
      this.loadAndCachePage();
    }
  },
  
  showFirstPage : function() {
    if(this.blockAjaxRequest()) {
      this.siteState = "newestProjects";
      this.pageContent.prev = "NIL";
      this.pageContent.current = null;
      this.pageContent.next = null;
      
      this.pageNr.prev = 0;
      this.pageNr.current = 1;
      this.pageNr.next = 2;
      
      this.requestPage(this.pageNr.current);
      this.requestPage(this.pageNr.next);

      this.saveHistoryState();
      
      $("#normalHeaderButtons").toggle(true);
      $("#cancelHeaderButton").toggle(false);
      $("#headerSearchBox").toggle(false);
      $("#searchQuery").val("");     
    }
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
    /* deprecated */
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
      this.requestPage(this.pageNr.next);
    }
    if(this.pageContent.prev == null) {
      this.requestPage(this.pageNr.prev);
    }   
  },

  requestPage : function(pageNr) {
    var self = this;
    $.ajax({
      url: self.basePath+"catroid/loadNewestProjects/"+pageNr+".json",
      cache: false,
      timeout: (5000),
    
      success: function(result){
        if(result != "") {
          for(var i = 0; i < result.content.length; i++) {
            result.content[i].pageNr = pageNr;
          }         
          
          self.pageLabels = result.labels;
          
          if(self.pageNr.current == pageNr) {
            if(self.pageContent.current == null) {
              self.pageContent.current = result.content;
            }
          }
          else {
            if(self.pageNr.prev == pageNr) {
              self.pageContent.prev = result.content;
            }
            if(self.pageNr.next == pageNr) {
              self.pageContent.next = result.content;
            }
          }
          
          if(self.pageContent.prev != null && self.pageContent.current != null && self.pageContent.next != null) {
            self.fillSkeletonWithContent();
            self.hideOrShowButtons();
            self.unblockAjaxRequest();            
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

  savePageNr : function(pageNumber) {
    var self = this; 
    
    $.ajax({
      type: "POST",
      url: self.basePath+"catroid/saveDataToSession/save.json",
      cache: false,      
      data: {
          content: {
            pageNr: pageNumber            
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
          var projectListSpacer = $("<div />").addClass("projectListSpacer").attr("id", "projectListSpacer"+i).css("display","none");
          containerContent.append(projectListSpacer);
        }

        whiteBox = $("<div />").addClass("whiteBoxMain").attr("id", "whiteBox"+i).css("display","none");
        projectListElementRow = $("<div />").addClass("projectListElementRow");

	      var projectListElement = $("<div />").addClass("projectListElement").attr("id", "projectListElement"+i);
	     
        var projectListThumbnail = $("<div />").addClass("projectListThumbnail").attr("id", "projectListThumbnail"+i);
        var projectListDetailsLinkThumb = $("<a />").addClass("projectListDetailsLink").attr("id", "projectListDetailsLinkThumb"+i);
	      var projectListPreview = $("<img />").addClass("projectListPreview").attr("id", "projectListPreview"+i);
	     
	      var projectListTitle = $("<div />").addClass("projectDetailLine").attr("id", "projectListTitle"+i);
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
      containerContent.append($("<div />").addClass("projectListSpacer").attr("id", "projectListSpacer"+i).css("display","none"));

      $("#projectContainer").append(containerContent);
    }
  },
 
  fillSkeletonWithContent : function() {
    var self = this;
    
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
          $("#projectListDetailsLinkThumb"+i).unbind('click');
          $("#projectListDetailsLinkThumb"+i).bind("click", { pageNr: content[i]['pageNr']}, function(event){
            self.savePageNr(event.data.pageNr);
          });
          $("#projectListPreview"+i).attr("src", content[i]['thumbnail']).attr("alt", content[i]['title']);
          
          $("#projectListTitle"+i).html("<div class='projectDetailLineMaxWidth'><a class='projectListDetailsLinkBold' href='"+this.basePath+"catroid/details/"+content[i]['id']+"'>"+content[i]['title']+"</a></div>");
          $("#projectListTitle"+i).unbind('click');
          $("#projectListTitle"+i).bind("click", { pageNr: content[i]['pageNr']}, function(event){
            self.savePageNr(event.data.pageNr);
          });
          // + author $("#projectListDescription"+i).html("by <a class='projectListDetailsLink' href='#'>unknown</a><br />uploaded "+content[i]['upload_time']+" ago");
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
