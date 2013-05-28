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
        
        this.extend = null;
        this.fillLayout = null;
        this.clear = null;
        this.getHistoryState = null;
        this.restoreHistoryState = null;
        this.error = {"type" : "", code : 0, extra : ""};

        this.initializeLayout(this.params.layout, this.params.sort);
      },
      
      isReady: function() {
        return this.ready;
      },
      
      initializeLayout : function(layout, sort) {
        this.ready = false;
        
        switch(layout) {
          case this.params.config.LAYOUT_GRID_ROW:
            this.visibleRows = 0;
            this.gridRowHeight = 0;

            switch(sort) {
              case this.params.config.sortby.downloads:
                this.fillLayout = this.fillGridRowDownloads;
                break;
              case this.params.config.sortby.views:
                this.fillLayout = this.fillGridRowViews;
                break;
              default:
                this.fillLayout = this.fillGridRowAge;
            }
            
            if(typeof this.params.callbacks['delete'] === 'function') {
              this.fillLayout = this.fillGridRowEdit;
            }
            
            this.extend = this.asyncGrowGridRowContainer;
            this.clear = this.clearGridRowContainer;
            this.getHistoryState = this.getGridRowHistory;
            this.restoreHistoryState = this.restoreGridRowHistory;
            
            this.clear();
            this.ready = true;
            break;
          default:
            this.ready = false;
        }
        this.display();
      },

      display : function() {
        if(this.ready && this.params.content != null) {
          for(var index = 0, amount = this.params.content.length; index < amount; index++) {
            if(this.params.content[index].CatrobatProjects.length > 0) {
              this.fill(this.params.content[index]);
            }
          }
          
          if(this.params.numProjects == 0) {
            this.params.reachedLastPage = true;
            $(this.params.buttons.next).hide();
            
            if(typeof this.params.noResults !== 'undefined') {
              $(this.params.noResults).show();
            }
          } else if(typeof this.params.noResults !== 'undefined') {
            $(this.params.noResults).hide();
          }
        }
      },

      addGridRow : function() {
        if($("ul", this.params.container).length == 0) {
          $(this.params.container).append($("<ul />"));
        }

        var container = $("ul", this.params.container);
        for(var i = 0; i < this.params.page.numProjectsPerPage; i++) {
          var project = $("<a />");

          var projectThumbnail = $("<img />");
          var projectTitle = $("<div />").addClass("projectTitle").text("a");
          var projectAddition = $("<div />").addClass("projectAddition").text("z");

          project.append(projectThumbnail);
          project.append(projectTitle);
          project.append(projectAddition);

          $(container).append($("<li />").append(project));

          if(this.gridRowHeight == 0) {
            this.gridRowHeight = $(container).height();
            $(container).height(this.gridRowHeight);
          }
        }
      },

      addGridRowEdit : function() {
        if($("ul", this.params.container).length == 0) {
          $(this.params.container).append($("<ul />"));
        }

        var container = $("ul", this.params.container);
        for(var i = 0; i < this.params.page.numProjectsPerPage; i++) {
          var project = $("<a />");

          var projectThumbnail = $("<img />");
          var projectTitle = $("<div />").addClass("projectTitle").text("a");
          var projectAddition = $("<div />").addClass("projectAddition").text("z");
          var projectActions = $("<div />").addClass("projectDeleteButton img-delete");
          
          project.append(projectThumbnail);
          project.append(projectTitle);
          project.append(projectAddition);
          
          $(container).append($("<li />").append(project).append(projectActions));
          
          if(this.gridRowHeight == 0) {
            this.gridRowHeight = $(container).height();
            $(container).height(this.gridRowHeight);
          }
        }
      },
      
      asyncGrowGridRowContainer : function() {
        this.visibleRows += 1;
        setTimeout($.proxy(this.extendGridRowContainer, this), 20);
      },
      
      extendGridRowContainer : function() {
        var container = $("ul", this.params.container);
        
        var heights = [];
        var hidden = [];
        var elements = container.children();
        for(var index = 0, amount = elements.length; index < amount; index++) {
          if($(elements[index]).css('visibility') == 'visible') {
            var position = Math.round($(elements[index]).position().top + this.gridRowHeight);
            if($.inArray(position, heights) === -1) {
              heights.push(position);
            }
          } else {
            hidden.push(index);
          }
        }
        
        var currentPage = Math.max(0, this.visibleRows - 1);
        if(currentPage < heights.length) {
          $(container).height(heights[currentPage]);
        }
        if(this.params.reachedLastPage && heights.length == this.visibleRows) {
          for(var index = 0, amount = hidden.length; index < amount; index++) {
            $(elements[hidden[index]]).hide();
          }
          $(this.params.buttons.next).hide();
          $(container).height('auto');
        }
      },
      
      clearGridRowContainer : function() {
        $(this.params.buttons.next).show();
        this.visibleRows = 0;
        this.params.reachedLastPage = false;
        $("ul", this.params.container).empty();
        $("ul", this.params.container).height('auto');
      },
      
      getGridRowHistory : function(state) {
        state.visibleRows = this.visibleRows;
        return state;
      },
      
      restoreGridRowHistory : function(state) {
        this.visibleRows = state.visibleRows;
        this.extendGridRowContainer();
      },
      
      fillGridRowAge : function(result) {
        if(result.CatrobatInformation != null && result.CatrobatProjects != null) {
          this.addGridRow();
          
          var info = result.CatrobatInformation;
          var projects = result.CatrobatProjects;
          
          var elements = $('> ul', this.params.container).children();
          
          var elementOffset = this.visibleRows * this.params.page.numProjectsPerPage;
          for(var i = 0; i < this.params.page.numProjectsPerPage; i++) {
            var index = elementOffset + i;
            if(projects != null && projects[i]) {
              $(elements[index]).css("visibility", "visible");
              $('a', elements[index]).attr('href', info['BaseUrl'] + 'details/' + projects[i]['ProjectId']).attr('title', projects[i]['ProjectName']);
              $('img', elements[index]).attr('src', info['BaseUrl'] + projects[i]['ScreenshotSmall']).attr('alt', projects[i]['ProjectName']);
              $('div.projectTitle', elements[index]).html(projects[i]['ProjectNameShort']);
              $('div.projectAddition', elements[index]).html(projects[i]['UploadedString']);
            } else {
              $(elements[index]).css("visibility", "hidden");
            }
          }
          this.extend();
        }
      },
      
      fillGridRowDownloads : function(result) {
        if(result.CatrobatInformation != null && result.CatrobatProjects != null) {
          this.addGridRow();
          
          var info = result.CatrobatInformation;
          var projects = result.CatrobatProjects;
          
          var elements = $('> ul', this.params.container).children();
          
          var elementOffset = this.visibleRows * this.params.page.numProjectsPerPage;
          for(var i = 0; i < this.params.page.numProjectsPerPage; i++) {
            var index = elementOffset + i;
            if(projects != null && projects[i]) {
              $(elements[index]).css("visibility", "visible");
              $('a', elements[index]).attr('href', info['BaseUrl'] + 'details/' + projects[i]['ProjectId']).attr('title', projects[i]['ProjectName']);
              $('img', elements[index]).attr('src', info['BaseUrl'] + projects[i]['ScreenshotSmall']).attr('alt', projects[i]['ProjectName']);
              $('div.projectTitle', elements[index]).html(projects[i]['ProjectNameShort']);
              $('div.projectAddition', elements[index]).html(projects[i]['Downloads'] + ' ' + this.params.additionalTextLabel);
            } else {
              $(elements[index]).css("visibility", "hidden");
            }
          }
          this.extend();
        }
      },
      
      fillGridRowViews : function(result) {
        if(result.CatrobatInformation != null && result.CatrobatProjects != null) {
          this.addGridRow();
          
          var info = result.CatrobatInformation;
          var projects = result.CatrobatProjects;
          
          var elements = $('> ul', this.params.container).children();
          
          var elementOffset = this.visibleRows * this.params.page.numProjectsPerPage;
          for(var i = 0; i < this.params.page.numProjectsPerPage; i++) {
            var index = elementOffset + i;
            if(projects != null && projects[i]) {
              $(elements[index]).css("visibility", "visible");
              $('a', elements[index]).attr('href', info['BaseUrl'] + 'details/' + projects[i]['ProjectId']).attr('title', projects[i]['ProjectName']);
              $('img', elements[index]).attr('src', info['BaseUrl'] + projects[i]['ScreenshotSmall']).attr('alt', projects[i]['ProjectName']);
              $('div.projectTitle', elements[index]).html(projects[i]['ProjectNameShort']);
              $('div.projectAddition', elements[index]).html(projects[i]['Views'] + ' ' + this.params.additionalTextLabel);
            } else {
              $(elements[index]).css("visibility", "hidden");
            }
          }
          this.extend();
        }
      },
      
      fillGridRowEdit : function(result) {
        if(result.CatrobatInformation != null && result.CatrobatProjects != null) {
          this.addGridRowEdit();
          
          var info = result.CatrobatInformation;
          var projects = result.CatrobatProjects;
          
          var elements = $('> ul', this.params.container).children();
          
          var elementOffset = this.visibleRows * this.params.page.numProjectsPerPage;
          for(var i = 0; i < this.params.page.numProjectsPerPage; i++) {
            var index = elementOffset + i;
            if(projects != null && projects[i]) {
              $(elements[index]).css("visibility", "visible");
              $('a', elements[index]).attr('href', info['BaseUrl'] + 'details/' + projects[i]['ProjectId']).attr('title', projects[i]['ProjectName']);
              $('img', elements[index]).attr('src', info['BaseUrl'] + projects[i]['ScreenshotSmall']).attr('alt', projects[i]['ProjectName']);
              $('div.projectTitle', elements[index]).html(projects[i]['ProjectNameShort']);
              $('div.projectAddition', elements[index]).html(projects[i]['UploadedString']);
              $('div.projectDeleteButton', elements[index]).click({id: projects[i]['ProjectId'], name: projects[i]['ProjectName']}, this.params.callbacks['delete']);
            } else {
              $(elements[index]).css("visibility", "hidden");
            }
          }
          this.extend();
        }
      },
   
      fill : function(result) {
        if(this.params.page.number >= this.params.page.pageNrMax) {
          this.params.reachedLastPage = true;
        }
        
        if(this.params.numProjects == 0) {
          this.params.reachedLastPage = true;
          $(this.params.buttons.next).hide();
        }
        
        this.fillLayout.call(this, result);
        
        var searchWords = this.params.filter.query.split(" ");
        $.each(searchWords, $.proxy(function(index, value) { 
          $(this.params.container).highlight(value);
        }, this));
        
        if(typeof this.params.callbacks['success'] === 'function') {
          this.params.callbacks['success'].call();
        }
      }
    });
