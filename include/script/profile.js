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
    this.passwordadditionalChanged = 0;
    this.countryChanged = 0;
    this.firstEmailChanged = 0;
    this.secondEmailChanged = 0;

    $("#profileUpdateSuccess").toggle(false);
    this.initAvatarUploader();
    this.setEmailValuesRequest();
    
//    $("#profileChangePassword").click(function() { $("#profilePasswordInput").toggle() });
//    $("#profilePasswordSubmit").click($.proxy(this.updatePaswordRequest, this));
//    $("#profileOldPassword").keypress($.proxy(this.passwordCatchKeypress, this));
//    $("#profileNewPassword").keypress($.proxy(this.passwordCatchKeypress, this));
   
    $("#profileNewPassword").keypress($.proxy(this.passwordCatchKeypress, this));
    $("#profileRepeatPassword").keypress($.proxy(this.passwordCatchKeypress, this));
    $("#profileFirstEmail").keypress($.proxy(this.firstEmailCatchKeypress, this));
    $("#profileSecondEmail").keypress($.proxy(this.secondEmailCatchKeypress, this));
    $(".profileCountry").change($.proxy(this.countryChanged = 1));
    $("#profileSaveChanges").click($.proxy(this.updateChangesRequest, this));


    
    //this.updateEmailListRequest();
    //$("#addEmailButton").click($.proxy(this.addEmailRequest, this));
    //$("#addEmailInput").keypress($.proxy(this.addEmailCatchKeypress, this));
    
//    $("#cityInput").change($.proxy(this.updateCityRequest, this));
//    $("#countrySelect").change($.proxy(this.updateCountryRequest, this));
//    $("#genderSelect").change($.proxy(this.updateGenderRequest, this));
//    $("#birthdayMonthSelect").change($.proxy(this.updateBirthdayRequest, this));
//    $("#birthdayYearSelect").change($.proxy(this.updateBirthdayRequest, this));
//    $("#profileSwitchLanguage").change($.proxy(this.updateLanguageRequest, this));

    $("#searchForm").submit($.proxy(this.search, this));
  },
  
  //-------------------------------------------------------------------------------------------------------------------
  initAvatarUploader : function() {

    var self = this;
    var wrapper = $('<div/>', {'id': 'profileAvatarFileWrapper'}).css({height:0, width:0, 'overflow':'hidden'});
    var fileInput = $('#profileAvatarFile').wrap(wrapper);
    fileInput.change(function() {
      if(this.files[0].size > 5 * 1024 * 1024) {
        //common.showAjaxErrorMsg(self.languageStringsObject.image_too_big);
        $("#profilePasswordError").text(self.languageStringsObject.image_too_big);
        $("#profilePasswordError").toggle(true);
        $(".profileAvatarImage img").css({"border" : "5px solid #880000"})
        return;
      }
      
      var data = new FormData();
      data.append('file', this.files[0]);
      
      $.ajax({
        type: "POST",
        url: self.basePath + 'profile/updateAvatarRequest.json',
        data: data,
        processData: false,
        contentType: false,
        success : $.proxy(self.avatarRequestSuccess, self),
        //error : $.proxy(common.ajaxTimedOut, self)
        //error: alert("AjaxTimedOut")
      });
      
      //common.disableAutoHideAjaxLoader();
    })

    $('#profileChangeAvatarButton').click(function(){
      fileInput.click();
    }).show();
  },


  avatarRequestSuccess : function(result) {
    //common.showPreHeaderMessages(result);
    if(result.statusCode == 200) {
      $('.profileAvatarImage img').attr("src", result.avatar);
      $("#profileUpdateSuccess").toggle(true);
      $("#profilePasswordError").toggle(false);
      $(".profileAvatarImage img").css({"border" : "5px solid #FFFFFF"})
      
    } else {
//      $("#profilePasswordError").text(result.answer);
//      $("#profilePasswordError").toggle(true);
      $(".profileAvatarImage img").css({"border" : "5px solid #880000"})
    }
  },

  //-------------------------------------------------------------------------------------------------------------------
  passwordCatchKeypress : function(event) {
    this.passwordChanged = 1;
    if(event.which == '13') {
      this.updateChangesRequest();
      //this.updatePaswordRequest();
      event.preventDefault();
    }
  },

//not needed in new version  
//  updatePaswordRequest : function() {
//    $.ajax({
//      type: "POST",
//      url: this.basePath + 'profile/updatePasswordRequest.json',
//      data : ({
//        profileOldPassword : $("#profileOldPassword").val(),
//        profileNewPassword : $("#profileNewPassword").val()
//      }),
//      timeout : (this.ajaxTimeout),
//      success : $.proxy(this.passwordRequestSuccess, this),
//      error : $.proxy(common.ajaxTimedOut, this)
//    });
//  },
  
  updateChangesRequest : function() {
    if(this.passwordChanged == 1) {
      this.passwordChanged = 0;
      $.ajax({
        type: "POST",
        url: this.basePath + 'catroid/profile/updatePasswordRequest.json',
        data : ({
          profileNewPassword : $("#profileNewPassword").val(),
          profileRepeatPassword : $("#profileRepeatPassword").val()
        }),
        timeout : (this.ajaxTimeout),
        success : $.proxy(this.passwordRequestSuccess, this),
        //error : $.proxy(common.ajaxTimedOut, this)
      });
    }
    if(this.firstEmailChanged == 1) {
      $.ajax({
        type: "POST",
        url: this.basePath + 'catroid/profile/updateEmailRequest.json',
        data : ({
          email : $("#profileFirstEmail").val(),
          additional : 0
        }),
        timeout : (this.ajaxTimeout),
        success : $.proxy(this.firstEmailRequestSuccess, this),
        //error : $.proxy(common.ajaxTimedOut, this)
      });
    }
    if(this.secondEmailChanged == 1) {
      $.ajax({
        type: "POST",
        url: this.basePath + 'catroid/profile/updateEmailRequest.json',
        data : ({
          email : $("#profileSecondEmail").val(),
          additional : 1
        }),
        timeout : (this.ajaxTimeout),
        success : $.proxy(this.secondEmailRequestSuccess, this),
        //error : $.proxy(common.ajaxTimedOut, this)
      });
    }
    if(this.countryChanged == 1) {
      $.ajax({
        type: "POST",
        url: this.basePath + 'profile/updateCountryRequest.json',
        data : ({
          country : $(".profileCountry").val()
        }),
        timeout : (this.ajaxTimeout),
        success : $.proxy(this.countryRequestSuccess, this),
        //error : $.proxy(common.ajaxTimedOut, this)
        
      });
    }


  },
  
  passwordRequestSuccess : function(result) {
    //common.showPreHeaderMessages(result);
    if(result.statusCode == 200) {
      //$("#profileUpdateSuccess").text(result.answer);
      $("#profileUpdateSuccess").toggle(true);
      $("#profilePasswordError").toggle(false);
      $("#profileEmailError").toggle(false);
      $("#profileNewPassword").val("");
      $("#profileRepeatPassword").val("");
      $("#profileNewPassword").css({"background-color" : "#FFFFFF", "color" : "#000000"});
      $("#profileRepeatPassword").css({"background-color" : "#FFFFFF", "color" : "#000000"});
      $(".profilePasswordItem").css({"border" : "0.2em solid #05222a","background-color" : "#FFFFFF", 
        "-mox-box-shadow:" : "inset 2px 2px 5px #05222a;", "-webkit-box-shadow" : "inset 2px 2px 5px #05222a;", 
        "box-shadow" : "inset 2px 2px 5px #05222a;"});
    } else {
      $("#profilePasswordError").text(result.answer);
      $("#profilePasswordError").toggle(true);
      $("#profileEmailError").toggle(true);
      $(".profilePasswordItem").css({"border" : "0.2em solid #880000","background-color" : "#F78181", "-mox-box-shadow:" : "none",
        "-webkit-box-shadow" : "none", "box-shadow" : "none"});
      $("#profileNewPassword").css({"background-color" : "#F78181", "color" : "#880000"});
      $("#profileRepeatPassword").css({"background-color" : "#F78181", "color" : "#880000"});
    }
  },
 
  firstEmailRequestSuccess : function(result) {
    //common.showPreHeaderMessages(result);
    if(result.statusCode == 200) {
      $("#profileUpdateSuccess").text(result.answer);
      $("#profileUpdateSuccess").toggle(true);
      $("#profileEmailError").toggle(false);
      $("#profilePasswordError").toggle(false);
      $(".profileFirstEmailItem").css({"-mox-box-shadow" : "inset 2px 2px 5px #05222a","-webkit-box-shadow" : "inset 2px 2px 5px #05222a",
        "box-shadow" : "inset 2px 2px 5px #05222a","background-color" : "#FFFFFF","border" : "2px solid #05222a"});
      $("#profileFirstEmail").css({"background-color" : "#FFFFFF","color" : "#000000"});
    } else {
      $("#profileEmailError").text(result.answer);
      $("#profileEmailError").toggle(true);
      $("#profilePasswordError").toggle(true);
      $(".profileFirstEmailItem").css({"border" : "0.2em solid #880000 ","background-color" : "#F78181", "-mox-box-shadow:" : "none",
        "-webkit-box-shadow" : "none", "box-shadow" : "none"});
      $("#profileFirstEmail").css({"background-color" : "#F78181", "color" : "#880000"});
    }
  },
  
  secondEmailRequestSuccess : function(result) {
    //common.showPreHeaderMessages(result);
    if(result.statusCode == 200) {
      $("#profileUpdateSuccess").text(result.answer);
      $("#profileUpdateSuccess").toggle(true);
      $("#profileEmailError").toggle(false);
      $("#profilePasswordError").toggle(false);
      $(".profileSecondEmailItem").css({"-mox-box-shadow" : "inset 2px 2px 5px #05222a","-webkit-box-shadow" : "inset 2px 2px 5px #05222a",
        "box-shadow" : "inset 2px 2px 5px #05222a","background-color" : "#FFFFFF","border" : "2px solid #05222a"});
      $("#profileFirstEmail").css({"background-color" : "#FFFFFF","color" : "#000000"});
    } else {
      $("#profileEmailError").text(result.answer);
      $("#profileEmailError").toggle(true);
      $("#profilePasswordError").toggle(true);
      $(".profileSecondEmailItem").css({"border" : "0.2em solid #880000 ","background-color" : "#F78181", "-mox-box-shadow:" : "none",
        "-webkit-box-shadow" : "none", "box-shadow" : "none"});
      $("#profileSecondEmail").css({"background-color" : "#F78181", "color" : "#880000"});
    }
  },
  
  countryRequestSuccess : function(result) {
    if(result.statusCode == 200) {
      $("#profileUpdateSuccess").toggle(true);
    }
  },

  //needed??-------------------------------------------------------------------------------------------------------------------
//  updateEmailListRequest : function() {
//    $.ajax({
//      type: "GET",
//      url: this.basePath + 'profile/getEmailListRequest.json',
//      timeout : (this.ajaxTimeout),
//      success : $.proxy(this.updateEmailList, this),
//      error: alert("Error update Email List")
//      //error : $.proxy(common.ajaxTimedOut, this)
//    });
//  },
//  
//  updateEmailList : function(result) {
//    $('#emailDeleteButtons').empty();
//    $('#emailDeleteButtons').html(result.answer);
//    
//    var self = this;
//    $('#emailDeleteButtons > button').each(function() {
//      $(this).click(function(e) {
//        if(confirm(self.languageStringsObject.really_delete + " '" + e.target.name + "' ?")) {
//          $.proxy(self.deleteEmailRequest(e.target.name), self);
//        }
//      });
//    });
//  },
  
  setEmailValuesRequest : function() {
    $.ajax({
      type: "GET",
      url: this.basePath + 'catroid/profile/getEmailListRequest.json',
      timeout : (this.ajaxTimeout),
      success : $.proxy(this.setEmailValues, this),
      //error : $.proxy(common.ajaxTimedOut, this)
    });
  },
  
  setEmailValues : function(result) {
    console.log(result.answer);
    $("#profileFirstEmail").attr('placeholder',result.answer[0].address);
    if(result.answer[1] != null)
      $("#profileSecondEmail").attr('placeholder',result.answer[1].address);
    else
      $("#profileSecondEmail").attr('placeholder', this.languageStringsObject.second_email);
  },
  
  
  firstEmailCatchKeypress : function(event) {
    this.firstEmailChanged = 1;
    if(event.which == '13') {
      this.updateChangesRequest();
      event.preventDefault();
    }
  },
  
  secondEmailCatchKeypress : function(event) {
    this.secondEmailChanged = 1;
    if(event.which == '13') {
      this.updateChangesRequest();
      event.preventDefault();
    }
  },

//not needed in new version
//  addEmailCatchKeypress : function(event) {
//    if(event.which == '13') {
//      this.addEmailRequest();
//      event.preventDefault();
//    }
//  },
//new version
//  addEmailRequest : function(value) {
//    $.ajax({
//      type: "POST",
//      url: this.basePath + 'profile/addEmailRequest.json',
//      data : ({
        //if(value == 1)
//          profileEmail : $("#profileFirstEmail").val()
//        else
//          profileEmail : $("#profileSecondEmail").val()
//      }),
//      timeout : (this.ajaxTimeout),
//      success : $.proxy(this.emailRequestSuccess, this),
//      error : $.proxy(common.ajaxTimedOut, this)
//    });
//  },

//  addEmailRequest : function() {
//    $.ajax({
//      type: "POST",
//      url: this.basePath + 'profile/addEmailRequest.json',
//      data : ({
//        profileEmail : $("#addEmailInput").val()
//      }),
//      timeout : (this.ajaxTimeout),
//      success : $.proxy(this.emailRequestSuccess, this),
//      //error : $.proxy(common.ajaxTimedOut, this)
//      error : alert("Error add Email Request")
//    });
//  },
//
//  deleteEmailRequest : function(email) {
//    $.ajax({
//      type: "POST",
//      url: this.basePath + 'profile/deleteEmailRequest.json',
//      data : ({
//        profileEmail : email
//      }),
//      timeout : (this.ajaxTimeout),
//      success : $.proxy(this.emailRequestSuccess, this),
//      //error : $.proxy(common.ajaxTimedOut, this)
//      error : alert("Error delete Email")
//    });
//  },

//  emailRequestSuccess : function(result) {
//    common.showPreHeaderMessages(result);
//    if(result.statusCode == 200) {
//      $("#addEmailInput").val("");
//      this.updateEmailListRequest();
//      common.showAjaxSuccessMsg(result.answer);
//    } else {
//      common.showAjaxErrorMsg(result.answer);
//    }
//  },
  
  //-------------------------------------------------------------------------------------------------------------------
  updateCityRequest : function() {
    $.ajax({
      type: "POST",
      url: this.basePath + 'profile/updateCityRequest.json',
      data : ({
        city: $("#cityInput").val()
      }),
      timeout : (this.ajaxTimeout),
      success : $.proxy(this.genericRequestSuccess, this),
     //error : $.proxy(common.ajaxTimedOut, this)
      error : alert("update City error")
    });
  },  
  
  //-------------------------------------------------------------------------------------------------------------------
//  updateCountryRequest : function() {
//    this.countryChanged = 1
//  },  
  
  //-------------------------------------------------------------------------------------------------------------------
  updateGenderRequest : function() {
    $.ajax({
      type: "POST",
      url: this.basePath + 'profile/updateGenderRequest.json',
      data : ({
        gender: $("#genderSelect").val()
      }),
      timeout : (this.ajaxTimeout),
      success : $.proxy(this.genericRequestSuccess, this),
      //error : $.proxy(common.ajaxTimedOut, this)
      error : alert("update gender")
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
      url: this.basePath + 'profile/updateBirthdayRequest.json',
      data : ({
        birthdayMonth: month,
        birthdayYear: year
      }),
      timeout : (this.ajaxTimeout),
      success : $.proxy(this.genericRequestSuccess, this),
      //error : $.proxy(common.ajaxTimedOut, this)
      error : alert("update birthday")
    });
  },  
  
  //-------------------------------------------------------------------------------------------------------------------
  updateLanguageRequest : function() {
    $.ajax({
      type : "POST",
      url : this.basePath + 'switchLanguage/switchIt.json',
      data : ({
        language : $("#profileSwitchLanguage").val()
      }),
      timeout : this.ajaxTimeout,
      success : $.proxy(this.genericRequestSuccess, this),
      //error : $.proxy(common.ajaxTimedOut, this)
      error : alert("update language")
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
    location.href = "/search/?q=" + $.trim($("#searchQuery").val()) + "&p=1";
    return false;
  }
});
