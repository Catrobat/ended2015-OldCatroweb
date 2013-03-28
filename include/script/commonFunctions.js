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
  
  disableAutoHideAjaxLoader : function() {
    clearTimeout(this.timeoutAutoHideAjaxLoader);
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
  
  isElementInView: function(element) {
    var docViewTop = $(window).scrollTop(),
    docViewBottom = docViewTop + $(window).height(),
    elemTop = $(element).offset().top,
    elemBottom = elemTop + $(element).height();
    return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
  },

  displayAjaxAnswerBox : function() {
    if(!this.isElementInView($("#ajaxAnswerBoxContainer"))) {
      $('html, body').animate({ scrollTop: $(".webMainTop").height() });
    }
    
    $("#ajaxAnswerBox").delay(400).slideDown(500);
    clearTimeout(this.timeoutAjaxAnswerBoxVisible);
    this.timeoutAjaxAnswerBoxVisible = setTimeout(function(){ $("#ajaxAnswerBox").slideUp(1000); }, 10000);
  },
  
  displayMessage : function(message, type) {
    if(message == null) {
      message = '';
    }

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
    
    setTimeout(function() { ajaxMessage.slideUp(1000, function() { $(this).remove(); }); }, 11000);
    ajaxMessage.toggle(false).delay(400).slideDown(500);
    $("#ajaxAnswerBox").children(":first").append(ajaxMessage);
  },

  showPreHeaderMessages : function(result) {
    if(result.preHeaderMessages) {
      common.showAjaxMsg(result.preHeaderMessages);
    }
  },

  ajaxTimedOut : function(error) {
    common.showAjaxErrorMsg(common.languageStrings.ajax_timed_out);
  }
});
