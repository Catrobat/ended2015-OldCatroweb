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

var ProjectDetails = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function(remixedProjects, projectId) {
    this.downloadState = { 'selected': 0, 'catroid': 0, 'app': 1 };
    this.downloadInfoVisible = false;
    this.projectId = projectId;
    this.remixedProjects = remixedProjects;

//    $("#downloadCatroidSwitch").bind('click', { type: this.downloadState.catroid }, $.proxy(this.changeDownloadType, this));
//    $("#downloadAppSwitch").bind('click', { type: this.downloadState.app }, $.proxy(this.changeDownloadType, this));
//    $("#downloadInfoButton").click($.proxy(this.toggleDownloadInfo, this));

   $("#reportAsInappropriateDialog").toggle(false);
   $("#reportAsInappropriateAnswer").toggle(false);
   $("#reportAsInappropriateButton").click(function() {
     $("#reportAsInappropriateAnswer").toggle(false);
     $("#reportAsInappropriateDialog").toggle();
   });
   $("#reportInappropriateCancelButton").click(function() {
     $("#reportAsInappropriateDialog").toggle(false);
   });
   $("#reportInappropriateReportButton").click($.proxy(this.reportInappropriateSubmit, this));
   $("#reportInappropriateReason").keypress($.proxy(this.reportInappropriateCatchKeypress, this));

    window.onresize = $.proxy(function(){
      $.proxy(this.resizeColumnsAndText(), this);
    }, this);

    window.onload = $.proxy(function(){
      $.proxy(this.resizeColumnsAndText(), this);
    }, this);
    
    this.setUpRemixContainer();
  },

  reportInappropriateCatchKeypress : function(event) {
    if(event.which == '13') {
      event.preventDefault();
      this.reportInappropriateSubmit();
    }
  },

  reportInappropriateSubmit : function() {
    $("#reportInappropriateReportButton").attr('disabled', true);
    $("#reportInappropriateReason").attr('disabled', true);

    var url = this.basePath + 'flagInappropriate/flag.json';
    $.post(url, {
      projectId : $("#reportInappropriateProjectId").val(),
      flagReason : $("#reportInappropriateReason").val()
    }, $.proxy(this.reportInappropriateSuccess, this), "json");
  },

  reportInappropriateSuccess : function(response) {
    $("#reportAsInappropriateAnswer").toggle(true);
    $("#reportAsInappropriateAnswer").html(response.answer);
    if(response.statusCode == 200) {
      $("#reportAsInappropriateDialog").toggle(false);
      $("#detailsFlagButton").toggle(false);
    }  else {
      $("#reportAsInappropriateDialog").toggle(false);
      $("#reportInappropriateReportButton").attr('disabled', false);
      $("#reportInappropriateReason").attr('disabled', false);
    }
  },

  changeDownloadType : function(event) {
    if(event.data.type != this.downloadState.selected) {
      this.downloadState.selected = event.data.type;

      if(this.downloadState.selected == this.downloadState.catroid) {
        $("#downloadProjectThumb").attr("href", $("#downloadCatroidProjectLink").attr("href"));

        $("#downloadAppSection").toggle(false);
        $("#downloadCatroidSection").toggle(true);

        $("#downloadCatroidSwitch").removeClass('blue');
        $("#downloadCatroidSwitch").addClass('blueSelected');

        $("#downloadAppSwitch").removeClass('blueSelected');
        $("#downloadAppSwitch").addClass('blue');

        if(this.downloadInfoVisible) {
          $("#downloadCatroidInfo").toggle(true);
          $("#downloadAppInfo").toggle(false);
        }
      }

      if(this.downloadState.selected == this.downloadState.app) {
        $("#downloadProjectThumb").attr("href", $("#downloadAppProjectLink").attr("href"));

        $("#downloadAppSection").toggle(true);
        $("#downloadCatroidSection").toggle(false);

        $("#downloadCatroidSwitch").removeClass('blueSelected');
        $("#downloadCatroidSwitch").addClass('blue');

        $("#downloadAppSwitch").removeClass('blue');
        $("#downloadAppSwitch").addClass('blueSelected');

        if(this.downloadInfoVisible) {
          $("#downloadCatroidInfo").toggle(false);
          $("#downloadAppInfo").toggle(true);
        }
      }
    }
  },

  toggleDownloadInfo : function() {
    this.downloadInfoVisible = !this.downloadInfoVisible;

    if(this.downloadState.selected == this.downloadState.catroid) {
      $("#downloadCatroidInfo").toggle(this.downloadInfoVisible);
    } else if(this.downloadState.selected == this.downloadState.app){
      $("#downloadAppInfo").toggle(this.downloadInfoVisible);
    }
  },

  resizeColumnsAndText : function() {
    this.resizeText($(".shrinkTextToFit"));
    this.resizeColumns();
  },

  resizeColumns : function() {
    var colThumbnail       = $(".projectDetailsThumbnail");
    var colDescription     = $(".projectDetailsDescription");
    var colDownload        = $(".projectDetailsDownload");
    var colInformation     = $(".projectDetailsInformation");
    var colThumbnailImg    = $("#projectDetailsThumbnailImage");
    var colHeightMax       = Math.max(colThumbnail.height(), colThumbnailImg.height(), colDescription.height());
    var colHeightMin       = Math.min(colDescription.height(), colDownload.height());
    var cssMediaQueryWidth = 535; /* defined in details.css (media query for single column layout) */

    if($('body').width() > cssMediaQueryWidth) {
      colDownload.height(colHeightMax);
    }else {
      colThumbnail.css("height", "auto");
      colDescription.css("height", "auto");
      colDownload.css("height", "auto");
    }
  },

  resizeText : function(elem) {

    while(elem.width() > elem.parent().width()-20) {
      var fontsize = parseFloat(elem.css('font-size')) - 2.0;
      elem.css('font-size', parseFloat(fontsize) + "px" );
    }

    while(elem.width() < elem.parent().width() - 60) {
      var fontsize = parseFloat(elem.css('font-size')) + 2.0;
      elem.css('font-size', parseFloat(fontsize) + "px" );
    }
  },
  
  setUpRemixContainer : function() {
    $('#remixesProjectLoader').css({'display':'none'});
    $('#moreResultsRemixes').css({'display':'none'});
    
    $('#moreResultsRemixes').click(function(){
      $(this).css({'display':'none'});
      $('#remixesProjectLoader').css({'display':'block'});
    });
    
    var ul = document.createElement('ul');
    ul.style.height = 'auto';
    $('#remixesProjectContainer').append(ul);
    
    var numRemixes = this.remixedProjects.length;
    
    for(var i = 0; i < numRemixes; i++) {
      var li = document.createElement('li');
      var a = document.createElement('a');
      var img = document.createElement('img');
      var div_title = document.createElement('div');
      var div_addition = document.createElement('div');
      div_title.className = 'projectTitle';
      div_addition.className = 'projectAddition';
      
      a.href = this.remixedProjects[i]['id'];
      img.src = this.remixedProjects[i]['thumbnail'];
      div_title.innerText = this.remixedProjects[i]['title'];
      div_addition.innerText = this.remixedProjects[i]['remix_count'] + ' remixes';
      
      ul.appendChild(li);
      li.appendChild(a);
      a.appendChild(img);
      a.appendChild(div_title);
      a.appendChild(div_addition);
    }
  },

});
