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

var AdminLanguageManagement = Class.$extend({
  __include__ : [__baseClassVars],
  __init__ : function() {
    
    $("#doUpdateLink").click(jQuery.proxy(this.doUpdateRequest, this));
  },

  doUpdateRequest : function() {
    this.hideAnswerFields();
    this.toggleLoadingMessage();
    var url = this.basePath + 'admin/languageManagement/generateLanguagePack.json';
    $.ajax({
      type : "POST",
      url : url,
      data : ({
        lang : $("#supportedLanguageSelect").val()
      }),
      timeout : this.ajaxTimeout,
      success : $.proxy(this.updateSuccess, this),
      error : $.proxy(this.updateError, this)
    });
  },
 
  updateSuccess : function(result) {
    if (result.statusCode == 200) {
      this.displayAnswer(result.answer);
    } else {
      this.displayError(result.answer);
    }
    this.toggleLoadingMessage();
  },

  updateError : function(result, errCode) {
    alert("updateError");
    this.toggleLoadingMessage();
  },
  
  displayError : function(string) {
    $("#adminError").toggle(true);
    $("#adminError").html(string);
  },
  
  displayAnswer : function(string) {
    $("#adminAnswer").toggle(true);
    $("#adminAnswer").html(string);
  },
  
  toggleLoadingMessage : function() {
    $("#doUpdateLink").toggle();
    $("#doUpdateLoadingMessage").toggle();
  },
  
  hideAnswerFields : function() {
    $("#adminAnswer").toggle(false);
    $("#adminError").toggle(false);
  }
});
