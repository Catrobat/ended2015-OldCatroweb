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

var Profile = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function(languageStringsObject) {
    var self = this;
    this.languageStringsObject = languageStringsObject;
    
    $("#profileChangePassword").click(function() { $("#profilePasswordInput").toggle() });
    $("#profilePasswordSubmit").click($.proxy(this.updatePaswordRequest, this));
    $("#profileOldPassword").keypress($.proxy(this.passwordCatchKeypress, this));
    $("#profileNewPassword").keypress($.proxy(this.passwordCatchKeypress, this));
    
    this.updateEmailListRequest();
    $("#addEmailButton").click($.proxy(this.addEmailRequest, this));
    $("#addEmailInput").keypress($.proxy(this.addEmailCatchKeypress, this));
    
    $("#cityInput").change($.proxy(this.updateCityRequest, this));
    $("#countrySelect").change($.proxy(this.updateCountryRequest, this));
    $("#genderSelect").change($.proxy(this.updateGenderRequest, this));
    $("#birthdayMonthSelect").change($.proxy(this.updateBirthdayRequest, this));
    $("#birthdayYearSelect").change($.proxy(this.updateBirthdayRequest, this));
  },
  
  //-------------------------------------------------------------------------------------------------------------------
  passwordCatchKeypress : function(event) {
    if(event.which == '13') {
      this.updatePaswordRequest();
      event.preventDefault();
    }
  },
  
  updatePaswordRequest : function() {
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/profile/updatePasswordRequest.json',
      data : ({
        profileOldPassword : $("#profileOldPassword").val(),
        profileNewPassword : $("#profileNewPassword").val()
      }),
      timeout : (this.ajaxTimeout),
      success : $.proxy(this.passwordRequestSuccess, this),
      error : $.proxy(this.profilePasswordRequestSubmitError, this)
    });
  },
  
  passwordRequestSuccess : function(result) {
    if(result.statusCode == 200) {
      $("#profilePasswordInput").toggle(false);
      $("#profileOldPassword").val("");
      $("#profileNewPassword").val("");
      common.showAjaxSuccessMsg(result.answer);
    } else {
      common.showAjaxErrorMsg(result.answer);
    }
  },

  profilePasswordRequestSubmitError : function(errCode) {
    if(errCode == "timeout") {
      window.location.reload(false);   
    }
  },

  //-------------------------------------------------------------------------------------------------------------------
  updateEmailListRequest : function() {
    $.ajax({
      type: "GET",
      url: this.basePath + 'catroid/profile/getEmailListRequest.json',
      timeout : (this.ajaxTimeout),
      success : $.proxy(this.updateEmailList, this),
      error : $.proxy(this.profilePasswordRequestSubmitError, this)
    });
  },
  
  updateEmailList : function(result) {
    $('#emailDeleteButtons').empty();
    $('#emailDeleteButtons').html(result.answer);
    
    var self = this;
    $('#emailDeleteButtons > button').each(function() {
      $(this).click(function(e) {
        if(confirm(self.languageStringsObject.really_delete + " '" + e.target.name + "' ?")) {
          $.proxy(self.deleteEmailRequest(e.target.name), self);
        }
      });
    });
  },
  
  addEmailCatchKeypress : function(event) {
    if(event.which == '13') {
      this.addEmailRequest();
      event.preventDefault();
    }
  },

  addEmailRequest : function() {
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/profile/addEmailRequest.json',
      data : ({
        profileEmail : $("#addEmailInput").val()
      }),
      timeout : (this.ajaxTimeout),
      success : $.proxy(this.emailRequestSuccess, this),
      error : $.proxy(this.profileDeleteEmailRequestSubmitError, this)
    });
  },

  deleteEmailRequest : function(email) {
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/profile/deleteEmailRequest.json',
      data : ({
        profileEmail : email
      }),
      timeout : (this.ajaxTimeout),
      success : $.proxy(this.emailRequestSuccess, this),
      error : $.proxy(this.profileDeleteEmailRequestSubmitError, this)
    });
  },

  emailRequestSuccess : function(result) {
    if(result.statusCode == 200) {
      $("#addEmailInput").val("");
      this.updateEmailListRequest();
      common.showAjaxSuccessMsg(result.answer);
    } else {
      common.showAjaxErrorMsg(result.answer);
    }
  },
  
  //-------------------------------------------------------------------------------------------------------------------
  updateCityRequest : function() {
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/profile/updateCityRequest.json',
      data : ({
        city: $("#cityInput").val()
      }),
      timeout : (this.ajaxTimeout),
      success : $.proxy(this.genericRequestSuccess, this),
      error : $.proxy(this.profilePasswordRequestSubmitError, this)
    });
  },  
  
  //-------------------------------------------------------------------------------------------------------------------
  updateCountryRequest : function() {
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/profile/updateCountryRequest.json',
      data : ({
        country: $("#countrySelect").val()
      }),
      timeout : (this.ajaxTimeout),
      success : $.proxy(this.genericRequestSuccess, this),
      error : $.proxy(this.profilePasswordRequestSubmitError, this)
    });
  },  
  
  //-------------------------------------------------------------------------------------------------------------------
  updateGenderRequest : function() {
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/profile/updateGenderRequest.json',
      data : ({
        gender: $("#genderSelect").val()
      }),
      timeout : (this.ajaxTimeout),
      success : $.proxy(this.genericRequestSuccess, this),
      error : $.proxy(this.profilePasswordRequestSubmitError, this)
    });
  },  
  
  //-------------------------------------------------------------------------------------------------------------------
  updateBirthdayRequest : function() {
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/profile/updateBirthdayRequest.json',
      data : ({
        birthdayMonth: $("#birthdayMonthSelect").val(),
        birthdayYear: $("#birthdayYearSelect").val()
      }),
      timeout : (this.ajaxTimeout),
      success : $.proxy(this.genericRequestSuccess, this),
      error : $.proxy(this.profilePasswordRequestSubmitError, this)
    });
  },  
  
  genericRequestSuccess : function(result) {
    if(result.statusCode == 200) {
      common.showAjaxSuccessMsg(result.answer);
    } else {
      common.showAjaxErrorMsg(result.answer);
    }
  }
});
