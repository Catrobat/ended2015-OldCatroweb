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

$.fn.highlight = function(word) {
  function innerHighlight(node, word) {
    var result = 0;
   
    if(node.nodeType === 3) {
      var position = node.data.toUpperCase().indexOf(word);
      if(position >= 0) {
        var spannode = document.createElement('span');
        spannode.className = 'highlight';
        var selection = node.splitText(position);
        selection.splitText(word.length);
        var selectionclone = selection.cloneNode(true);
        spannode.appendChild(selectionclone);

        selection.parentNode.replaceChild(spannode, selection);
        result = 1;
      }
    } else if(node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
      for(var i = 0; i < node.childNodes.length; ++i) {
        i += innerHighlight(node.childNodes[i], word);
      }
    }
    return result;
  }
  return this.length && word && word.length ? this.each(function() {
    innerHighlight(this, word.toUpperCase());
  }) : this;
};


var ProjectContentFiller = Class
    .$extend({
      __include__ : [__baseClassVars],
      __init__ : function(cbParams) {
        this.cbParams = cbParams;
        this.ready = false;
        this.params = this.cbParams.call(this);
        
        this.fillSkeleton = null;
        this.error = {"type" : "", code : 0, extra : ""};

        this.createSkeletonHandler(this.params.layout);
      },
      
      isReady: function() {
        return this.ready;
      },
      
      createSkeletonHandler : function(layout) {
        switch(layout) {
          case this.params.config.PROJECT_LAYOUT_ROW:
            this.createSkeletonRowNew();
            this.fillSkeleton = this.fillSkeletonRowListAge;
            this.ready = true;
            break;
          default:
            this.ready = false;
        }
        if(typeof this.params.firstPage !== 'undefined' && this.params.firstPage != null) {
          this.fill(this.params.firstPage);
        }
      },
      
      createSkeletonRowNew : function() {
        if(!this.initialized) {
          var contentContainer = $("<ul />");
          for(var i = 0; i < this.params.page.numProjectsPerPage; i++) {
            var project = $("<a />");
            
            var projectThumbnail = $("<img />");
            var projectTitle = $("<div />").addClass("projectTitle");
            var projectAddition = $("<div />").addClass("projectAddition");
            
            project.append(projectThumbnail);
            project.append(projectTitle);
            project.append(projectAddition);
            
            contentContainer.append($("<li />").append(project));
          }
          $(this.params.container).append(contentContainer);
        }
      },

      fillSkeletonRowListAge : function(results) {
        if(results.CatrobatInformation != null && results.CatrobatProjects != null) {
          var info = results.CatrobatInformation;
          var projects = results.CatrobatProjects;
          
          var elements = $('> ul', this.params.container).children();
          for(var i = 0; i < this.params.page.numProjectsPerPage; i++){
            if(projects != null && projects[i]) {
              $(elements[i]).css("visibility", "visible");
              $('a', elements[i]).attr('href', info['BaseUrl'] + projects[i]['ProjectUrl']).attr('title', projects[i]['ProjectName']);
              $('img', elements[i]).attr('src', info['BaseUrl'] + projects[i]['ScreenshotSmall']).attr('alt', projects[i]['ProjectName']);
              $('div.projectTitle', elements[i]).text(projects[i]['ProjectName']);
              $('div.projectAddition', elements[i]).text(projects[i]['UploadedString']);
            } else {
              $(elements[i]).css("visibility", "hidden");
            }
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
                  .attr("href", this.basePath + "details/" + projects[i]['id']);
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
                      + "details/" + projects[i]['id'] + "'>" + projects[i]['title'] + "</a></div>");
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
        if(result.error) {
          alert(result.error);
          return false;
        } else {        
          this.fillSkeleton.call(this, result);
          
          var searchWords = this.params.filter.query.split(" ");
          $.each(searchWords, $.proxy(function(index, value) { 
            $(this.params.container).highlight(value);
          }, this));

          return true;
        }
      }
    });
