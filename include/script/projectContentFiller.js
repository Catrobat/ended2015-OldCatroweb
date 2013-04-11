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

var ProjectContentFiller = Class
    .$extend({
      __include__ : [__baseClassVars],
      __init__ : function(cbParams) {
        this.cbParams = cbParams;
        this.ready = false;
        this.params = this.cbParams.call(this);
        this.layout = this.params.layout;
        
        this.hasPrevButton = false;
        this.hasNextButton = false;
        
        if(this.params.buttons != null) {
          if((this.params.buttons.prev != null) && (this.params.buttons.prev != "")) {
            this.hasPrevButton = true;
          }
          
          if((this.params.buttons.next != null) && (this.params.buttons.next != "")) {
            this.hasNextButton = true;
          }
        }
        
        this.fillSkeleton = null;
        this.error = {"type" : "", code : 0, extra : ""};

        this.createSkeletonHandler(this.params.layout);
        if(this.params.firstPage != null) {
          console.log(this.params.firstPage);
          this.fillSkeleton(this.params.firstPage.content, this.params.firstPage.buttons, this.params.firstPage.pageLabels);
        }
      },
      
      isReady: function() {
        return this.ready;
      },
      
      createSkeletonHandler : function(layout) {
        switch(layout) {
        case this.params.config.PROJECT_LAYOUT_ROW:
          this.createSkeletonRowNew();
          this.fillSkeleton = this.fillSkeletonRowNew;
          this.ready = true;
          break;
        default:
          this.ready = false;
        }
      },
      
      createSkeletonRowNew : function() {
        if(!this.initialized) {
          var contentContainer = $("<ul />");
          for(var i = 0; i < this.params.page.numProjectsPerPage; i++) {
            var project = $("<li />");
            var projectLink = $("<a />");
            
            var projectThumbnail = $("<img />");
            var projectTitle = $("<div />").addClass("projectTitle");
            var projectAddition = $("<div />").addClass("projectAddition");
            
            projectLink.append(projectThumbnail);
            projectLink.append(projectTitle);
            projectLink.append(projectAddition);
            
            contentContainer.append(project.append(projectLink));
          }
          $(this.params.container).append(contentContainer);
        }
      },

      fillSkeletonRowNew : function(projects, buttons, pageLabels) {

        var elements = $('> ul', this.params.container).children();
        for(var i = 0; i < this.params.page.numProjectsPerPage; i++){
          if(projects != null && projects[i]) {
            //alert(projects[i]['title']);
            
            $(elements[i]).css("visibility", "visible");
            $('a', elements[i]).attr('href', this.basePath + "catroid/details/" + projects[i]['id']);
            $('img', elements[i]).attr('src', projects[i]['thumbnail']).attr('alt', projects[i]['title']);
            $('div.projectTitle', elements[i]).text(projects[i]['title']);
            $('div.projectAddition', elements[i]).text(projects[i]['last_activity']);
            
            
            
            
            /*
            if($("#projectListElement" + i).length > 0){
              $("#whiteBox" + i).css("display", "block");
              $("#projectListSpacer" + i).css("display", "block");
              $("#projectListThumbnail" + i).attr("title", projects[i]['title']);
              $("#projectListDetailsLinkThumb" + i)
                  .attr("href", this.basePath + "catroid/details/" + projects[i]['id']);
              $("#projectListDetailsLinkThumb" + i).unbind('click');
              $("#projectListDetailsLinkThumb" + i).bind("click", {
                pageNr : projects[i]['pageNr']
              }, function(event) {
                // TODO: session handling
                // self.saveStateToSession(event.data.pageNr);
              });
              $("#projectListPreview" + i).attr("src", projects[i]['thumbnail']).attr("alt", projects[i]['title']);

              $("#projectListTitle" + i).html(
                  "<div class='projectDetailLineMaxWidth'><a class='projectListDetailsLinkBold' href='" + this.basePath
                      + "catroid/details/" + projects[i]['id'] + "'>" + projects[i]['title'] + "</a></div>");
              $("#projectListTitle" + i).unbind('click');
              $("#projectListTitle" + i).bind("click", {
                pageNr : projects[i]['pageNr']
              }, function(event) {
                // session handling
              });
              $("#projectListDescription" + i).html(
                  projects[i]['last_activity'] + " " + projects[i]['uploaded_by_string'] + "<br/>" +
                  "d: " + projects[i]['download_count'] + ", v: " + projects[i]['view_count'] + "<br/>"
                  );
            }
          } else{
            $("#whiteBox" + i).css("display", "none");
            $("#projectListSpacer" + i).css("display", "none");
          }*/
          }
        }
      },

      createSkeletonRow : function() {
        if(!this.initialized){
          var containerContent = $("<div />").addClass("projectListRow");

          var whiteBox = null;
          var projectListElementRow = null;
          for( var i = 0; i < this.params.page.numProjectsPerPage; i++){
            if(whiteBox != null){
              whiteBox.append(projectListElementRow);
              whiteBox.append("<div />").css("clear", "both");
              containerContent.append(whiteBox);
              var projectListSpacer = $("<div />").addClass("projectListSpacer").attr("id", "projectListSpacer" + i)
                  .css("display", "none");
              containerContent.append(projectListSpacer);
            }

            whiteBox = $("<div />").addClass("whiteBoxMain").attr("id", "whiteBox" + i).css("display", "none");
            projectListElementRow = $("<div />").addClass("projectListElementRow");

            var projectListElement = $("<div />").addClass("projectListElement").attr("id", "projectListElement" + i);

            var projectListThumbnail = $("<div />").addClass("projectListThumbnail").attr("id",
                "projectListThumbnail" + i);
            var projectListDetailsLinkThumb = $("<a />").addClass("projectListDetailsLink").attr("id",
                "projectListDetailsLinkThumb" + i);
            var projectListPreview = $("<img />").addClass("projectListPreview").attr("id", "projectListPreview" + i);

            var projectListTitle = $("<div />").addClass("projectDetailLine").attr("id", "projectListTitle" + i);
            var projectListDescription = $("<div />").addClass("projectDetailLine").attr("id",
                "projectListDescription" + i);
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
          containerContent.append($("<div />").addClass("projectListSpacer").attr("id", "projectListSpacer" + i).css(
              "display", "none"));

          var navigationButtonPrev = $("<button />").addClass("navigationButtons").addClass("button").addClass("white")
              .addClass("medium").attr("type", "button");
          var navigationButtonNext = $("<button />").addClass("navigationButtons").addClass("button").addClass("white")
              .addClass("medium").attr("type", "button");

          $(this.params.container)
              .append(
                  $("<div />").addClass("webMainNavigationButtons").append(
                      navigationButtonPrev.attr("id", "fewerProjects").append(
                          $("<span />").addClass("navigationButtons"))));
          $(this.params.container).append($("<div />").addClass("projectListSpacer"));
          $(this.params.container).append(containerContent);
          $(this.params.container).append($("<div />").addClass("projectListSpacer"));
          $(this.params.container).append(
              $("<div />").addClass("webMainNavigationButtons").append(
                  navigationButtonNext.attr("id", "moreProjects").append($("<span />").addClass("navigationButtons"))));
        }
      },

      fillSkeletonRow : function(projects, buttons, pageLabels) {
        var self = this;
        this.params.pageLabels = pageLabels;
        $("#projectListTitle").text(this.params.pageLabels['title'] + " (" + this.params.pageLabels['pageNr'] + ")");
        if(this.hasPrevButton) {
          $(this.params.buttons.prev).children("span").html(this.params.pageLabels['prevButton']);
        }
        if(this.hasNextButton) {
          $(this.params.buttons.next).children("span").html(this.params.pageLabels['nextButton']);
        }
        
        if(projects === "NIL"){
          var msg = ("ERROR: fillSkeletonRow: no projects! ");
          console.log(msg);
          $(this.params.container).html("<b>" + msg + "</b>");
          return;
        }

        for( var i = 0; i < this.params.page.numProjectsPerPage; i++){
          if(projects != null && projects[i]){
            if($("#projectListElement" + i).length > 0){
              $("#whiteBox" + i).css("display", "block");
              $("#projectListSpacer" + i).css("display", "block");
              $("#projectListThumbnail" + i).attr("title", projects[i]['title']);
              $("#projectListDetailsLinkThumb" + i)
                  .attr("href", this.basePath + "catroid/details/" + projects[i]['id']);
              $("#projectListDetailsLinkThumb" + i).unbind('click');
              $("#projectListDetailsLinkThumb" + i).bind("click", {
                pageNr : projects[i]['pageNr']
              }, function(event) {
                // TODO: session handling
                // self.saveStateToSession(event.data.pageNr);
              });
              $("#projectListPreview" + i).attr("src", projects[i]['thumbnail']).attr("alt", projects[i]['title']);

              $("#projectListTitle" + i).html(
                  "<div class='projectDetailLineMaxWidth'><a class='projectListDetailsLinkBold' href='" + this.basePath
                      + "catroid/details/" + projects[i]['id'] + "'>" + projects[i]['title'] + "</a></div>");
              $("#projectListTitle" + i).unbind('click');
              $("#projectListTitle" + i).bind("click", {
                pageNr : projects[i]['pageNr']
              }, function(event) {
                // session handling
              });
              $("#projectListDescription" + i).html(
                  projects[i]['last_activity'] + " " + projects[i]['uploaded_by_string'] + "<br/>" +
                  "d: " + projects[i]['download_count'] + ", v: " + projects[i]['view_count'] + "<br/>"
                  );
            }
          } else{
            $("#whiteBox" + i).css("display", "none");
            $("#projectListSpacer" + i).css("display", "none");
          }
        }
        if(this.hasPrevButton) {
          $(this.params.buttons.prev).toggle(buttons.prevButton);
        }
        
        if(this.hasNextButton) { 
          $(this.params.buttons.next).toggle(buttons.nextButton);
        }
      },
      
      setError : function(error) {
        this.error = error;
      },
      
      setErrorByString : function(type, code, extra) {
        this.error.type = type;
        this.error.code = code;
        this.error.extra = extra;
      },
      
      getError : function() {       
        return this.error;
      },
      
      fill : function(result) {
        if(!this.params.container || this.params.container === 'undefined'){          
          this.setErrorByString("ERROR: no container", "501", "extra");
          return false;          
        }        
        else if(result.error){
          this.setError(result.error);
          return false;
        } else{        
          this.params.pageLabels = result.pageLabels;
          this.fillSkeleton.call(this, result.content, result.buttons, result.pageLabels);
          return true;
        }
      }
    });
