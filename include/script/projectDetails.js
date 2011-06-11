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

var ProjectDetails = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function() {
    $("#qrcodeInfo").toggle(false);
    $("#hideQrCodeInfoButton").toggle(false);
    $("#showQrCodeInfoButton").click($.proxy(this.showQrCodeInfo, this));
    $("#hideQrCodeInfoButton").click($.proxy(this.hideQrCodeInfo, this));

    $("#reportAsInappropriateDialog").toggle(false);
    $("#reportAsInappropriateAnswer").toggle(false);
    $("#showFullDescriptionButton").click(
        $.proxy(this.showFullDescription, this));
    $("#showShortDescriptionButton").click(
        $.proxy(this.showShortDescription, this));
    $("#reportAsInappropriateButton").click(function() {
      $("#reportAsInappropriateAnswer").toggle(false);
      $("#reportAsInappropriateDialog").toggle();
    });
    $("#reportInappropriateCancelButton").click(function() {
      $("#reportAsInappropriateDialog").toggle(false);
    });
    $("#reportInappropriateReportButton").click(
        $.proxy(this.reportInappropriateSubmit, this));
    $("#reportInappropriateReason").keypress(
        $.proxy(this.reportInappropriateCatchKeypress, this));
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
    
    var url = this.basePath + 'catroid/flagInappropriate/flag.json';
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
  
  showFullDescription : function() {
    $("#detailsDescription").html($("#fullDescriptionText").attr("value"));
    $("#showFullDescriptionButton").toggle(false);
    $("#showShortDescriptionButton").toggle(true);
  },
  
  showShortDescription : function() {
    $("#detailsDescription").html($("#shortDescriptionText").attr("value"));
    $("#showFullDescriptionButton").toggle(true);
    $("#showShortDescriptionButton").toggle(false);
  },

  showQrCodeInfo : function() {
	  $("#qrcodeInfo").toggle(true);
	  $("#showQrCodeInfoButton").toggle(false);
	  $("#hideQrCodeInfoButton").toggle(true);
  },

  hideQrCodeInfo : function() {
	  $("#qrcodeInfo").toggle(false);
	  $("#showQrCodeInfoButton").toggle(true);
	  $("#hideQrCodeInfoButton").toggle(false);
  }

});
