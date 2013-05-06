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
        
        this.fillLayout = null;
        this.reset = null;
        this.error = {"type" : "", code : 0, extra : ""};

        this.initializeLayout(this.params.layout);
      },
      
      isReady: function() {
        return this.ready;
      },
      
      initializeLayout : function(layout) {
        switch(layout) {
          case this.params.config.PROJECT_LAYOUT_GRID_ROW:
            this.visibleRows = 0;
            this.rowHeight = 0;
            this.ready = true;

            this.fillLayout = this.fillGridRowAge;
            this.fill(this.params.firstPage);

            break;
          default:
            this.ready = false;
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
          
          if(this.rowHeight == 0) {
            this.rowHeight = $(container).height();
          }
        }
        
        this.visibleRows += 1;
        $(container).height(this.rowHeight * this.visibleRows);
      },
      
      fillGridRowAge : function(result) {
        if(result.CatrobatInformation != null && result.CatrobatProjects != null) {
          this.addGridRow();
          
          var info = result.CatrobatInformation;
          var projects = result.CatrobatProjects;
          
          var elements = $('> ul', this.params.container).children();
          
          var elementOffset = (this.visibleRows - 1) * this.params.page.numProjectsPerPage;
          for(var i = 0; i < this.params.page.numProjectsPerPage; i++){
            if(projects != null && projects[i]) {
              var index = elementOffset + i;
              $(elements[index]).css("visibility", "visible");
              $('a', elements[index]).attr('href', info['BaseUrl'] + projects[i]['ProjectUrl']).attr('title', projects[i]['ProjectName']);
              $('img', elements[index]).attr('src', info['BaseUrl'] + projects[i]['ScreenshotSmall']).attr('alt', projects[i]['ProjectName']);
              $('div.projectTitle', elements[index]).text(projects[i]['ProjectName']);
              $('div.projectAddition', elements[index]).text(projects[i]['UploadedString']);
            } else {
              $(elements[i]).css("visibility", "hidden");
            }
          }
        }
      },
   
      fill : function(result) {
        this.fillLayout.call(this, result);
        
        var searchWords = this.params.filter.query.split(" ");
        $.each(searchWords, $.proxy(function(index, value) { 
          $(this.params.container).highlight(value);
        }, this));
      }
    });
