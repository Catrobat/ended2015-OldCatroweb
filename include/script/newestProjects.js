/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010  Catroid development team
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


var NewestProjects = Class.$extend( {
  __init__ : function(basePath, projectPageMaxProjects, pageNr, numberOfPages) {
    this.basePath = basePath;
    this.pageNr = parseInt(pageNr);
    this.numberOfPages = parseInt(numberOfPages);
    this.projectPageMaxProjects = parseInt(projectPageMaxProjects);
    
    this.testAndSetBounderies();
    this.loadAndCachePage();
    
    $("#fewerProjects").click($.proxy(this.prevPage, this));
    $("#moreProjects").click($.proxy(this.nextPage, this));
    $("#aIndexWebLogoLeft").click($.proxy(this.showFirstPage, this));
  },

  nextPage : function() {
    this.pageNr = parseInt(this.pageNr) + 1;
    this.testAndSetBounderies();
    this.loadAndCachePage();
    $(window).scrollTop($("#page"+(this.pageNr+1)).offset().top);
  },

  prevPage : function() {
    this.pageNr = parseInt(this.pageNr) - 1;
    this.testAndSetBounderies();
    this.loadAndCachePage();
    $(window).scrollTop($("#page"+this.pageNr).offset().top - $(window).height());
  },
  
  showFirstPage : function() {
    this.pageNr = 1;
    this.loadAndCachePage();
  },

  testAndSetBounderies : function() {
    if(this.pageNr >= this.numberOfPages) {
	  this.pageNr = this.numberOfPages - 1;
    }
    if(this.pageNr >= this.numberOfPages - 2) {
      $("#moreProjects").toggle(false);
    } else {
      $("#moreProjects").toggle(true);
    }
	    
    if(this.pageNr < 0) {
      this.pageNr = 0;
    }
    if(this.pageNr < 2) {
        $("#fewerProjects").toggle(false);
      } else {
        $("#fewerProjects").toggle(true);
      }
  },

  loadAndCachePage : function() {
    var self = this;
    
    this.createSkeleton(this.pageNr);
    if(this.pageNr < this.numberOfPages - 1) {
      this.createSkeleton(this.pageNr + 1);
    }
    if(this.pageNr > 0) {
      this.createSkeleton(this.pageNr - 1);
    }

    if($("#page"+(this.pageNr-2)).length > 0) {
      $("#page"+(this.pageNr-2)).remove();
    }
    
    if($("#page"+(this.pageNr+2)).length > 0) {
      $("#page"+(this.pageNr+2)).remove();
    }

    $.ajax({
      url: this.basePath+"catroid/loadNewestProjects/"+self.pageNr,
      cache: false,
      success: function(result){
        if(result != "") {
          var content = eval('(' + result + ')');
          if(content['prev']) {
            self.fillSkeletonWithContent(content['prev'], self.pageNr - 1);
          }
          if(content['current']) {
            self.fillSkeletonWithContent(content['current'], self.pageNr);
          }
          if(content['next']) {
            self.fillSkeletonWithContent(content['next'], self.pageNr + 1);
          }
        }
      }
    });
  },


  
  createSkeleton : function(pageNr) {
    if($("#page"+pageNr).length == 0) {
      var containerContent = $("<div />").addClass("projectListRow").attr("id", "page"+pageNr);
	     
	  var whiteBox = null;
	  var projectListElementRow = null;
	  for(var i = 0; i < this.projectPageMaxProjects; i++) {
        var projectListRowItemId = this.projectPageMaxProjects * pageNr + i;

          if(whiteBox != null) {
            whiteBox.append(projectListElementRow);
            whiteBox.append("<div />").css("clear", "both");
            containerContent.append(whiteBox);
            var projectListSpacer = $("<div />").addClass("projectListSpacer").attr("id", "projectListSpacer"+projectListRowItemId);
            containerContent.append(projectListSpacer);
	      }

	      whiteBox = $("<div />").addClass("whiteBoxMain").attr("id", "whiteBox"+projectListRowItemId);
          projectListElementRow = $("<div />").addClass("projectListElementRow");


	    var projectListElement = $("<div />").addClass("projectListElement").attr("id", "projectListElement"+projectListRowItemId);
	     
        var projectListThumbnail = $("<div />").addClass("projectListThumbnail").attr("id", "projectListThumbnail"+projectListRowItemId);
        var projectListDetailsLinkThumb = $("<a />").addClass("projectListDetailsLink").attr("id", "projectListDetailsLinkThumb"+projectListRowItemId);
	    var projectListPreview = $("<img />").addClass("projectListPreview").attr("id", "projectListPreview"+projectListRowItemId).attr("src", this.basePath+"images/symbols/ajax-loader.gif").attr("alt", "loading...");
	     
	    var projectListTitle = $("<div />").addClass("projectDetailLine").attr("id", "projectListTitle"+projectListRowItemId).html("loading...");
	    var projectListDescription = $("<div />").addClass("projectDetailLine").attr("id", "projectListDescription"+projectListRowItemId);
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
      containerContent.append($("<div />").addClass("projectListSpacer").attr("id", "projectListSpacer"+projectListRowItemId));

      if($("#page"+(pageNr+1)).length > 0) {
        $("#projectContainer").prepend(containerContent);
      }
      else {
        $("#projectContainer").append(containerContent);
      }
    }
  },
 
  fillSkeletonWithContent : function(content, pageNr) {
	if(content != null) {
      for(var i=0; i<this.projectPageMaxProjects; i++) {
        var projectListRowItemId = this.projectPageMaxProjects * pageNr + i;
        if(content[i]) {
          if($("#projectListElement"+projectListRowItemId).length > 0) {
            $("#whiteBox"+projectListRowItemId).css("display", "block");
            $("#projectListSpacer"+projectListRowItemId).css("display", "block");
            $("#projectListThumbnail"+projectListRowItemId).attr("title", content[i]['title']);
            $("#projectListDetailsLinkThumb"+projectListRowItemId).attr("href", this.basePath+"catroid/details/"+content[i]['id']);
            $("#projectListPreview"+projectListRowItemId).attr("src", content[i]['thumbnail']).attr("alt", content[i]['title']);

            $("#projectListTitle"+projectListRowItemId).html("<a class='projectListDetailsLinkBold' href='"+this.basePath+"catroid/details/"+content[i]['id']+"'>"+content[i]['title']+"</a>");
            $("#projectListDescription"+projectListRowItemId).html("by <a class='projectListDetailsLink' href='#'>unknown</a><br />uploaded "+content[i]['upload_time']+" ago");
          }
        }
        else {
          $("#projectListElement"+projectListRowItemId).css("display", "none");
          $("#whiteBox"+projectListRowItemId).css("display", "none");
          $("#projectListSpacer"+projectListRowItemId).css("display", "none");
        }
      } 
    }
  }

});
