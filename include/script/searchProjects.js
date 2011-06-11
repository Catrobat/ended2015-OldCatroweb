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


var SearchProjects = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function(maxLoadProjects, maxLoadPages, pageNr, searchQuery, strings) {
    this.strings = strings;
    this.maxLoadProjects = parseInt(maxLoadProjects);
    this.maxVisibleProjects = parseInt(maxLoadPages) * maxLoadProjects;
    this.pageNr = { prev : parseInt(pageNr)-1, current : parseInt(pageNr), next : parseInt(pageNr)+1 };
    this.searchQuery = searchQuery;
   
    this.initialized = false;
    this.ajaxRequestMutex = false;
    this.pageLabels = new Array();
    this.pageContent = { prev : null, current : null, next : null };
  },

  initialize : function(object) {
    if(!object.initialized) { 
      object.createSkeleton();
      $("#fewerProjects").click($.proxy(object.prevPage, object));
      $("#moreProjects").click($.proxy(object.nextPage, object));
      
      $("#searchQuery").val(this.searchQuery);
      $("#normalHeaderButtons").toggle(false);
      $("#cancelHeaderButton").toggle(true);
      $("#headerSearchBox").toggle(true);
      $("#searchQuery").focus();

      object.loadAndCachePage();
      object.initialized = true;
    }
  },
  
  setDocumentTitle : function() {
    document.title = this.pageLabels['websitetitle'] + " - " + this.pageLabels['title'] + " - " + this.searchQuery  + " - " + this.pageNr.current;
  },
  
  setActive : function() {
    if(!this.initialized) {      
      this.initialize(this);
      this.fillSkeletonWithContent();
      if(this.pageContent.current == null) {
        this.setLoadingPage();
      }
    }
  },
  
  setInactive : function() {
    if(this.initialized) {
      $("#projectContainer").children().remove();
      this.initialized = false;
    }
  },
  
  saveHistoryState : function() {    
    if(history.pushState) {
      var stateObject = { pageNr: {}, pageContent: {}, pageLabels: new Array()};
      stateObject.pageNr = this.pageNr;
      stateObject.searchQuery = this.searchQuery;
      stateObject.pageLabels = this.pageLabels;
      stateObject.pageContent = this.pageContent;
      stateObject.searchProjects = true;
	      
      history.pushState(stateObject, "Page " + this.pageNr.current, this.basePath+"catroid/search/?q=" + escape(this.searchQuery) + "&p=" + this.pageNr.current);      
    }
    this.saveStateToSession(this.pageNr.current);
  },

  restoreHistoryState : function(state) {
    if(state != null) {      
      this.createSkeleton();
      $("#fewerProjects").click($.proxy(this.prevPage, this));
      $("#moreProjects").click($.proxy(this.nextPage, this));
      if(state.searchProjects) {        
        this.pageNr = state.pageNr;
        this.searchQuery = state.searchQuery;
        this.pageLabels = state.pageLabels;
        this.pageContent = state.pageContent;         
      } 
      $("#searchQuery").val(this.searchQuery);
      $("#normalHeaderButtons").toggle(false);
      $("#cancelHeaderButton").toggle(true);
      $("#headerSearchBox").toggle(true);
      $("#searchQuery").focus();
      
      this.setDocumentTitle();
      this.fillSkeletonWithContent();
      this.initialized = true;
    }
  },
  
  saveStateToSession : function(pageNumber) {
    var self = this;
    $.ajax({
      type: "POST",
      url: self.basePath+"catroid/saveDataToSession/save.json",
      cache: false,      
      data: {
          content: {
            pageNr: pageNumber,
            searchQuery: self.searchQuery,
            task : "searchProjects"
          }
      }
    });
  },
  
  blockAjaxRequest : function() {
    if(!this.ajaxRequestMutex) {
      this.ajaxRequestMutex = true;
      $("#projectContainer").fadeTo(100, 0.60);
      $("#ajax-loader").val("on");
      return true;
    }
    return false;
  },
	  
  unblockAjaxRequest : function() {
    $("#projectContainer").fadeTo(10, 1.0);
    $("#ajax-loader").val("off");
    this.ajaxRequestMutex = false;
  },

  nextPage : function() {
    if(this.blockAjaxRequest()) {     
      $("#moreProjects").children("span").html("<img src='" + this.basePath + "images/symbols/ajax-loader.gif' /> "
          + this.pageLabels['loadingButton']);
      
      this.pageContent.current = this.pageContent.current.concat(this.pageContent.next);
      this.pageNr.current++;
      
      this.pageContent.next = null;
      this.pageNr.next++;
      
      if(this.pageContent.current.length > this.maxVisibleProjects) {
        this.pageContent.current = this.pageContent.current.slice(this.maxLoadProjects);
        this.pageContent.prev = null; 
        this.pageNr.prev++;        
      }
     
      this.loadAndCachePage();
    }
  },

  prevPage : function() {
    if(this.blockAjaxRequest()) {
      $("#fewerProjects").children("span").html("<img src='" + this.basePath + "images/symbols/ajax-loader.gif' /> "
          + this.pageLabels['loadingButton']);
      
      this.pageContent.current = this.pageContent.prev.concat(this.pageContent.current);
      this.pageNr.current--;

      this.pageContent.prev = null;
      this.pageNr.prev--;

      if(this.pageContent.current.length > this.maxVisibleProjects) {
        this.pageContent.current = this.pageContent.current.slice(0, this.maxVisibleProjects);
        this.pageContent.next = null;
        this.pageNr.next--;
      }

      this.loadAndCachePage();
	  }
  },

  triggerSearch : function(loadAndCache) {
    var search = $.trim($("#searchQuery").val());    
    if(search != "" && this.searchQuery != search) {
      if(this.blockAjaxRequest()) {
        this.searchQuery = search;
      
        this.pageContent.prev = "NIL";
        this.pageContent.current = null;
        this.pageContent.next = null;

        this.pageNr.prev = 0;
        this.pageNr.current = 1;
        this.pageNr.next = 2;
        
        this.setLoadingPage();
      
        if(loadAndCache) {
          this.loadAndCachePage();
        }
        else { 
          this.unblockAjaxRequest();
        }
      }
    }
  },

  loadAndCachePage : function() {
    if(this.pageContent.next == null) {
      this.requestPage(this.pageNr.next);
    }
    if(this.pageContent.current == null) {
      this.requestPage(this.pageNr.current);
    }
    if(this.pageContent.prev == null) {
      this.requestPage(this.pageNr.prev);
    }    
  },

  requestPage : function(pageNr) {
    var self = this;
    $.ajax({
      url: self.basePath+"catroid/loadSearchProjects/result.json",
      cache: false,
      type : "POST",
      data : {
        query : self.searchQuery,
        page : pageNr
      },
      timeout: (this.ajaxTimeout),
    
      success: function(result) {
        if(result != "") {
          if(result.error) {
            self.showErrorPage(result.error['type'], result.error['code'], result.error['extra']);
          } else {
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
              self.saveHistoryState();
              self.saveStateToSession(self.pageNr.current);
              self.setDocumentTitle();
              self.unblockAjaxRequest();            
            }
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

  showErrorPage: function(type, code, extra) {
    var self = this;
    $.ajax({
      type: 'POST',
      url: self.basePath+"catroid/saveDataToSession/save.json",
      data : {
        content: {
          errorType : type,
          errorCode : code,
          errorExtraInfo : extra
        }
      },
      success: function(result) {
          location.href = self.basePath + "catroid/errorPage"; 
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

      var navigationButtonPrev = $("<button />").addClass("navigationButtons").addClass("button").addClass("white").addClass("medium").attr("type", "button");
      var navigationButtonNext = $("<button />").addClass("navigationButtons").addClass("button").addClass("white").addClass("medium").attr("type", "button");

      $("#projectContainer").append($("<div />").addClass("webMainNavigationButtons").append(navigationButtonPrev.attr("id", "fewerProjects").append($("<span />").addClass("navigationButtons"))));
      $("#projectContainer").append($("<div />").addClass("projectListSpacer"));
      $("#projectContainer").append(containerContent);
      $("#projectContainer").append($("<div />").addClass("projectListSpacer"));
      $("#projectContainer").append($("<div />").addClass("webMainNavigationButtons").append(navigationButtonNext.attr("id", "moreProjects").append($("<span />").addClass("navigationButtons"))));
      
      $("#projectContainer").append($("<div />").append($("<input />").attr("type", "hidden").attr("id", "ajax-loader")));
    }
  },
  
  setLoadingPage : function() {
    $("#whiteBox0").css("display", "block");
    $("#projectListSpacer0").css("display", "block");
    
    $("#fewerProjects").toggle(false);
    $("#moreProjects").toggle(false);
    for(var i=1; i<this.maxVisibleProjects; i++) {
      $("#whiteBox"+i).css("display", "none");
      $("#projectListSpacer"+i).css("display", "none");
    }
    
    $("#projectListPreview0").attr("src", this.basePath + "images/symbols/thumbnail_gray.png");
    $("#projectListTitle0").html("<div class='projectDetailLineMaxWidth'><img src='" + this.basePath + "images/symbols/ajax-loader.gif' /> " + this.strings['loading'] + "</div>");
    $("#projectListDescription0").html("");    
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
          $("#projectListDetailsLinkThumb"+i).unbind('click');
          if (content[i]['id'] != 0) {
            $("#projectListDetailsLinkThumb"+i).attr("href", this.basePath+"catroid/details/"+content[i]['id']);  
            $("#projectListDetailsLinkThumb"+i).bind("click", { pageNr: content[i]['pageNr'] }, function(event) { self.saveStateToSession(event.data.pageNr); });
          }
          else {
            $("#projectListDetailsLinkThumb"+i).attr("href", "#");
          }
          $("#projectListPreview"+i).attr("src", content[i]['thumbnail']).attr("alt", content[i]['title']);
          
          $("#projectListTitle"+i).unbind('click');          
          if (content[i]['id'] != 0) {
            $("#projectListTitle"+i).html("<div class='projectDetailLineMaxWidth'><a class='projectListDetailsLinkBold' href='"+this.basePath+"catroid/details/"+content[i]['id']+"'>"+content[i]['title']+"</a></div>");  
            $("#projectListTitle"+i).bind("click", { pageNr: content[i]['pageNr'] }, function(event) { self.saveStateToSession(event.data.pageNr); });
          }
          else {
            $("#projectListTitle"+i).html("<div class='projectDetailLineMaxWidth'><a class='projectListDetailsLinkBold' href='#'>"+content[i]['title']+"</a></div>");            
          }
          if (content[i]['upload_time'] != "") {
            $("#projectListDescription"+i).html(content[i]['upload_time']+" "+content[i]['uploaded_by_string']);          
          } 
          else {
            $("#projectListDescription"+i).html("");            
          }
        }
      }
      else {
        $("#whiteBox"+i).css("display", "none");
        $("#projectListSpacer"+i).css("display", "none");
      }
    }    
    if(this.pageContent.prev == "NIL") {
      $("#fewerProjects").toggle(false);
    } else if(this.pageContent.prev != null) {
      $("#fewerProjects").toggle(true);
    }

    if(this.pageContent.next == "NIL") {
      $("#moreProjects").toggle(false);
    } else if(this.pageContent.next != null) {
      $("#moreProjects").toggle(true);
    }
  }
});
