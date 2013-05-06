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

var LanguageHandler = Class.$extend({
  __include__ : [__baseClassVars],
  __init__ : function() {
    $("#switchLanguage").change($.proxy(this.switchLanguage, this));
  },

  switchLanguage : function(eventObject) {
    this.disableForm();
    var selectedLanguage = $(eventObject.target).val();
    var url = this.basePath + 'switchLanguage/switchIt.json';
    $.ajax({
      type : "POST",
      url : url,
      data : ({
        language : selectedLanguage
      }),
      timeout : (this.ajaxTimeout),
      success : $.proxy(this.switchLanguageSuccess, this),
      error : $.proxy(this.switchLanguageError, this)
    });
  },

  switchLanguageSuccess : function(result) {
    if(result.statusCode == 200) {
      location.reload();
    }
    this.enableForm();
  },

  switchLanguageError : function(result, errCode) {
    this.enableForm();
  },
  
  disableForm : function() {
    $("#switchLanguage").attr("disabled", "disabled");
  },
  
  enableForm : function() {
    $("#switchLanguage").removeAttr("disabled");
  }
  
});
