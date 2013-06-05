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

var Statistics = Class.$extend({
  __include__ : [__baseClassVars],
  __init__ : function(languageObject) {
    this.languageObject = languageObject;
    this.offset = 0;
    this.limit = 5;
    
    $("#loadProjects").click($.proxy(this.showMore, this));
  },
  
  showMore : function() {
    $("#loadProjects").prop('disabled', true);
    $.ajax({
      url : this.basePath + "api/projects/list.json",
      type : "GET",
      context: this,
      data : {
        offset : this.offset,
        limit : this.limit,
        mask : 'listDownloads',
        order: 'downloads'
      },
      success : function(result) {
        if(typeof result === "object") {
          if(this.offset < result.CatrobatInformation.TotalProjects) {
            for(index in result.CatrobatProjects) {
              var element = result.CatrobatProjects[index];
              
              var row = $("<tr />");
              row.append($("<td />").text(element.ProjectName));
              row.append($("<td />").attr("align", "right").text(element.Downloads + " " + this.languageObject.downloads));
              $("#results").append(row);
            }
            
            $("#loadProjects").prop('disabled', false);
          } else {
            var row = $("<tfoot />").append($("<tr />"));
            row.append($("<td />").attr("colspan", "2").text("No more projects!"));
            $("#results").append(row);
          }
          this.offset += this.limit;
        } else {
          console.log("we have a problem");
        }
      }
    });
  }
});
