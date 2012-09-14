/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2012 The Catroid Team
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

var Common = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function(languageStrings) {
    this.languageStrings = languageStrings;

    this.timeoutShowAjaxLoader;
    this.timeoutAutoHideAjaxLoader;
    this.timeoutAjaxAnswerBoxVisible;
    
    $(document).ajaxStart($.proxy(this.enableAjaxLoader, this));
    $(document).ajaxStop($.proxy(this.disableAjaxLoader, this));
  },
  
  enableAjaxLoader : function() {
    clearTimeout(this.timeoutShowAjaxLoader);
    clearTimeout(this.timeoutAutoHideAjaxLoader);
    this.timeoutShowAjaxLoader = setTimeout($.proxy(function(){ this.showAjaxLoader(); }, this), 200);
    this.timeoutAutoHideAjaxLoader = setTimeout($.proxy(function(){ this.showAjaxErrorMsg(this.languageStrings.ajax_took_too_long); this.hideAjaxLoader(); }, this), 10000);
  },
  
  disableAjaxLoader : function() {
    this.hideAjaxLoader();
    clearTimeout(this.timeoutShowAjaxLoader);
    clearTimeout(this.timeoutAutoHideAjaxLoader);
  },

  showAjaxLoader : function() {
    var height = Math.max($(document).height(), $(window).height(), document.documentElement.clientHeight);
    $('body').append($('<div>').attr('id', 'webAjaxLoadingContainer').height(height));
  },

  hideAjaxLoader : function() {
    $("#webAjaxLoadingContainer").remove();
  },

  showAjaxMsg : function(message) {
    this.displayMessage(message, 'default');
  },

  showAjaxSuccessMsg : function(message) {
    this.displayMessage(message, 'success');
  },
  
  showAjaxErrorMsg : function(message) {
    this.displayMessage(message, 'error');
  },
  
  displayAjaxAnswerBox : function() {
    $(document).scrollTop($(".webMainTop").height());
    
    $("#ajaxAnswerBox").delay(400).slideDown(500);
    clearTimeout(this.timeoutAjaxAnswerBoxVisible);
    this.timeoutAjaxAnswerBoxVisible = setTimeout(function(){ $("#ajaxAnswerBox").slideUp(1000); }, 10000);
  },
  
  displayMessage : function(message, type) {
    this.displayAjaxAnswerBox();
    var ajaxMessage = $('<div>').text(message);
    
    switch(type) {
      case "success":
        ajaxMessage.addClass('success');
        break;
      case "error":
        ajaxMessage.addClass('error');
        break;
    }
    
    setTimeout(function(){ ajaxMessage.remove(); }, 11000);
    $("#ajaxAnswerBox").children(":first").append(ajaxMessage);
  }
});
