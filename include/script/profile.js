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

var Profile = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function(languageStringsObject) {
    var self = this;
    this.languageStringsObject = languageStringsObject;
    

    this.initAvatarUploader();
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
    $("#profileSwitchLanguage").change($.proxy(this.updateLanguageRequest, this));

    $("#searchForm").submit($.proxy(this.search, this));
  },
  
  //-------------------------------------------------------------------------------------------------------------------
  initAvatarUploader : function() {
    var self = this;
    var wrapper = $('<div/>', {'id': 'profileAvatarFileWrapper'}).css({height:0, width:0, 'overflow':'hidden'});
    var fileInput = $('#profileAvatarFile').wrap(wrapper);

    fileInput.change(function() {
      if(this.files[0].size > 5 * 1024 * 1024) {
        common.showAjaxErrorMsg(self.languageStringsObject.image_too_big);
        return;
      }
      
      var data = new FormData();
      data.append('file', this.files[0]);
      
      $.ajax({
        type: "POST",
        url: self.basePath + 'catroid/profile/updateAvatarRequest.json',
        data: data,
        processData: false,
        contentType: false,
        success : $.proxy(self.avatarRequestSuccess, self),
        error : $.proxy(common.ajaxTimedOut, self)
      });
      
      common.disableAutoHideAjaxLoader();
    })

    $('#profileAvatarImage').click(function(){
      fileInput.click();
    }).show();

    $('#profileChangeAvatar').click(function(){
      fileInput.click();
    }).show();
  },

  avatarRequestSuccess : function(result) {
    common.showPreHeaderMessages(result);
    
    if(result.statusCode == 200) {
      $('#profileAvatarImage').attr("src", result.avatar);
      common.showAjaxSuccessMsg(result.answer);
    } else {
      common.showAjaxErrorMsg(result.answer);
    }
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
      error : $.proxy(common.ajaxTimedOut, this)
    });
  },
  
  passwordRequestSuccess : function(result) {
    common.showPreHeaderMessages(result);
    if(result.statusCode == 200) {
      $("#profilePasswordInput").toggle(false);
      $("#profileOldPassword").val("");
      $("#profileNewPassword").val("");
      common.showAjaxSuccessMsg(result.answer);
    } else {
      common.showAjaxErrorMsg(result.answer);
    }
  },

  //-------------------------------------------------------------------------------------------------------------------
  updateEmailListRequest : function() {
    $.ajax({
      type: "GET",
      url: this.basePath + 'catroid/profile/getEmailListRequest.json',
      timeout : (this.ajaxTimeout),
      success : $.proxy(this.updateEmailList, this),
      error : $.proxy(common.ajaxTimedOut, this)
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
      error : $.proxy(common.ajaxTimedOut, this)
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
      error : $.proxy(common.ajaxTimedOut, this)
    });
  },

  emailRequestSuccess : function(result) {
    common.showPreHeaderMessages(result);
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
      error : $.proxy(common.ajaxTimedOut, this)
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
      error : $.proxy(common.ajaxTimedOut, this)
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
      error : $.proxy(common.ajaxTimedOut, this)
    });
  },  
  
  //-------------------------------------------------------------------------------------------------------------------
  updateBirthdayRequest : function() {
    var month = $("#birthdayMonthSelect").val();
    var year = $("#birthdayYearSelect").val();
    
    if(!((month == 0 && year == 0) || (month > 0 && year > 0))) {
      return;
    }
    
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/profile/updateBirthdayRequest.json',
      data : ({
        birthdayMonth: month,
        birthdayYear: year
      }),
      timeout : (this.ajaxTimeout),
      success : $.proxy(this.genericRequestSuccess, this),
      error : $.proxy(common.ajaxTimedOut, this)
    });
  },  
  
  //-------------------------------------------------------------------------------------------------------------------
  updateLanguageRequest : function() {
    $.ajax({
      type : "POST",
      url : this.basePath + 'catroid/switchLanguage/switchIt.json',
      data : ({
        language : $("#profileSwitchLanguage").val()
      }),
      timeout : this.ajaxTimeout,
      success : $.proxy(this.genericRequestSuccess, this),
      error : $.proxy(common.ajaxTimedOut, this)
    });
  },

  genericRequestSuccess : function(result) {
    common.showPreHeaderMessages(result);
    if(result.statusCode == 200) {
      common.showAjaxSuccessMsg(result.answer);
    } else {
      common.showAjaxErrorMsg(result.answer);
    }
  },

  search : function() {
    location.href = "/catroid/search/?q=" + $.trim($("#searchQuery").val()) + "&p=1";
    return false;
  }
});
