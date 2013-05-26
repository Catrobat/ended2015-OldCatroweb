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
   
    $(".profilePasswordItem input").keypress($.proxy(this.passwordCatchKeypress, this));
    $(".profileRepeatPassword input").keypress($.proxy(this.passwordCatchKeypress, this));
    $(".profileFirstEmailItem input").keypress($.proxy(this.firstEmailCatchKeypress, this));
    $(".profileSecondEmailItem input").keypress($.proxy(this.secondEmailCatchKeypress, this));
    $(".profileCountry").change($.proxy(this.countryChangedEvent, this));
    $("#profileSaveChanges").click($.proxy(this.updateChangesRequest, this));
    $(".img-delete").mouseover($(".img-delete").css('cursor', 'pointer'));
    $(".profileDeleteFirstEmail").click($.proxy(this.deleteEmail,this, 0));
    $(".profileDeleteSecondEmail").click($.proxy(this.deleteEmail,this, 1));

    $("#searchForm").submit($.proxy(this.search, this));

    this.projectObject = null;
    this.history = window.History;
    this.history.Adapter.bind(window, 'statechange', $.proxy(this.restoreHistoryState, this));
    
    $(window).keydown($.proxy(function(event) {
      if(event.keyCode == 116 || (event.ctrlKey == true && event.keyCode == 82)) {
        this.clearHistory(event);
      }
    }, this));
  },
  

  clearHistory : function(event) {
    this.history.replaceState({}, this.languageStringsObject['websiteTitle'] + " - " + this.languageStringsObject['title'], '');
    location.reload();
    event.preventDefault();
  },
  
  //-------------------------------------------------------------------------------------------------------------------
  enableSaveButton : function() {
    //$("#profileSaveChanges").removeAttr("disabled");
  },
  initAvatarUploader : function() {

    var self = this;
    var wrapper = $('<div/>', {'id': 'profileAvatarFileWrapper'}).css({height:0, width:0, 'overflow':'hidden'});
    var fileInput = $('#profileAvatarFile').wrap(wrapper);
    fileInput.change(function() {
      if(this.files[0].size > 5 * 1024 * 1024) {
        $("#profilePasswordError").text(self.languageStringsObject.image_too_big);
        $("#profilePasswordError").toggle(true);
        $("#profileEmailError").toggle(true);
        $(".profileAvatarImage img").css({"outline" : "0.7em solid #880000"})
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
      });
    })

    $('#profileChangeAvatarButton').click(function(){
      fileInput.click();
    }).show();
  },


  avatarRequestSuccess : function(result) {
    if(result.statusCode == 200) {
      $('.profileAvatarImage img').attr("src", result.avatar);
      $("#profileUpdateSuccess").toggle(true);
      $("#profileAvatarError").toggle(false);
      $("#profileEmailError").toggle(false);
      $("#profilePasswordError").toggle(false);
      $(".profileAvatarImage img").css({"outline" : "0.7em solid #FFFFFF"})
      
    } else {
      $("#profileAvatarError").text(this.languageStringsObject.image_too_big);
      $("#profileAvatarError").toggle(true);
      $("#profileEmailError").toggle(true);
      $("#profilePasswordError").toggle(true);
      $(".profileAvatarImage img").css({"outline" : "0.7em solid #880000"})
    }
  },

  //-------------------------------------------------------------------------------------------------------------------
  
  deleteEmail : function(id){
    if(id == 1)
      emailId = "first";
    else
      emailId = "second";
    
      $.ajax({
        type: "POST",
        url: this.basePath + 'catroid/profile/deleteEmailRequest.json',
        data : ({
          emailToDelete  : emailId,
          firstEmail     : $(".profileFirstEmailItem input").val(), 
          secondEmail    : $(".profileSecondEmailItem input").val(),
        }),
        timeout : (this.ajaxTimeout),
        success : $.proxy(this.deleteEmailRequestSuccess, this),
        error : $.proxy(this.ajaxTimedOut, this)
      });
    
  },
  
  passwordCatchKeypress : function(event) {
    this.passwordChanged = 1;
    $(".profileChangesSuccess").css("visibility","hidden");
    if(event.which == '13') {
      this.updateChangesRequest();
      event.preventDefault();
    }
  },
  
  countryChangedEvent : function(event) {
    this.countryChanged = 1;
    $(".profileChangesSuccess").css("visibility","hidden");
  },
  
  updateChangesRequest : function() {
    $("#profileEmailError").toggle(false);
    $("#profilePasswordError").toggle(false);
    if($("#profileNewPassword").val() == ""){
      this.passwordChanged = 0;
    }
    if($(".profileFirstEmailItem input").val() == ""){
      this.firstEmailChanged = 0;
    }
    if($(".profileSecondEmailItem input").val() == ""){
      this.secondEmailChanged = 0;
    }
    
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
        error : $.proxy(this.ajaxTimedOut, this)
      });
    }
    if(this.firstEmailChanged == 1) {
      this.firstEmailChanged = 0;
      $.ajax({
        type: "POST",
        url: this.basePath + 'catroid/profile/updateEmailRequest.json',
        data : ({
          email : $(".profileFirstEmailItem input").val(),
          additional : 0
        }),
        timeout : (this.ajaxTimeout),
        success : $.proxy(this.firstEmailRequestSuccess, this),
        error : $.proxy(this.ajaxTimedOut, this)
      });
    }
    if(this.secondEmailChanged == 1) {
      this.secondEmailChanged = 0;
      $.ajax({
        type: "POST",
        url: this.basePath + 'catroid/profile/updateEmailRequest.json',
        data : ({
          email : $(".profileSecondEmailItem input").val(),
          additional : 1
        }),
        timeout : (this.ajaxTimeout),
        success : $.proxy(this.secondEmailRequestSuccess, this),
        error : $.proxy(this.ajaxTimedOut, this)
      });
    }
    if(this.countryChanged == 1) {
      this.countryChanged = 0;
      $.ajax({
        type: "POST",
        url: this.basePath + 'profile/updateCountryRequest.json',
        data : ({
          country : $(".profileCountry select").val()
        }),
        timeout : (this.ajaxTimeout),
        success : $.proxy(this.countryRequestSuccess, this), 
        error : $.proxy(this.ajaxTimedOut, this)
      });
    }


  },
  
  passwordRequestSuccess : function(result) {
    this.passwordChanged = 0;
    if(result.statusCode == 200) {
      $("#profileUpdateSuccess").text("success");
      $("#profileUpdateSuccess").toggle(true);
      $("#profileNewPassword").val("");
      $("#profileRepeatPassword").val("");
      $(".profileErrorMessage").val("");
      $(".profilePasswordItem").removeClass("profileInvalid");
      $(".profilePasswordItem").addClass("profileValid");
      $(".profilePasswordItem input").removeClass("inputInvalid");
      $(".profilePasswordItem input").addClass("inputValid");
      $(".profilePasswordItem img").removeClass("img-failedPw");
      $(".profilePasswordItem img").addClass("img-password");
      $(".profileChangesSuccess").css("visibility","visible");
    } else {
      $("#profilePasswordError").text(result.answer);
      $("#profilePasswordError").toggle(true);
      $("#profileEmailError").toggle(true);
      $(".profilePasswordItem").removeClass("profileValid");
      $(".profilePasswordItem").addClass("profileInvalid");
      $(".profilePasswordItem input").removeClass("inputValid");
      $(".profilePasswordItem input").addClass("inputInvalid");
      $(".profilePasswordItem img").removeClass("img-password");
      $(".profilePasswordItem img").addClass("img-failedPw");
    }
  },
 
  firstEmailRequestSuccess : function(result) {
    this.firstEmailChanged = 0;
    $("#profileEmailError").toggle(false);
    $("#profilePasswordError").toggle(false);
    if(result.statusCode == 200) {
      $("#profileUpdateSuccess").text("success");
      $("#profileUpdateSuccess").toggle(true);
      $(".profileFirstEmailItem").removeClass("profileInvalid");
      $(".profileFirstEmailItem").addClass("profileValid");
      $(".profileFirstEmailItem input").removeClass("inputInvalid");
      $(".profileFirstEmailItem input").addClass("inputValid");
      $(".profileFirstEmailItem img").removeClass("img-failed-first-email");
      $(".profileFirstEmailItem img").addClass("img-first-email");
      $(".profileChangesSuccess").css("visibility","visible");
    } else {
      $("#profileEmailError").text(result.answer);
      $("#profileEmailError").toggle(true);
      $("#profilePasswordError").toggle(true);
      $(".profileFirstEmailItem").removeClass("profileValid");
      $(".profileFirstEmailItem").addClass("profileInvalid");
      $(".profileFirstEmailItem input").removeClass("inputValid");
      $(".profileFirstEmailItem input").addClass("inputInvalid");
      $(".profileFirstEmailItem img").removeClass("img-first-email");
      $(".profileFirstEmailItem img").addClass("img-failed-first-email");
    }
  },
  
  secondEmailRequestSuccess : function(result) {
    this.secondEmailChanged = 0;
    $("#profileEmailError").toggle(false);
    $("#profilePasswordError").toggle(false);
    if(result.statusCode == 200) {
      $("#profileUpdateSuccess").text("success");
      $("#profileUpdateSuccess").toggle(true);
      $(".profileSecondEmailItem").removeClass("profileInvalid");
      $(".profileSecondEmailItem").addClass("profileValid");
      $(".profileSecondEmailItem").removeClass("profileInvalid");
      $(".profileSecondEmailItem").addClass("profileValid");
      $(".profileSecondEmailItem img").removeClass("img-failed-second-email");
      $(".profileSecondEmailItem img").addClass("img-second-email");
      $(".profileChangesSuccess").css("visibility","visible");
    } else {
      $("#profileEmailError").text(result.answer);
      $("#profileEmailError").toggle(true);
      $("#profilePasswordError").toggle(true);
      $(".profileSecondEmailItem").removeClass("profileValid");
      $(".profileSecondEmailItem").addClass("profileInvalid");
      $(".profileSecondEmailItem input").removeClass("inputValid");
      $(".profileSecondEmailItem input").addClass("inputInvalid");
      $(".profileSecondEmailItem img").removeClass("img-second-email");
      $(".profileSecondEmailItem img").addClass("img-failed-second-email");
    }
  },
  
  deleteEmailRequestSuccess : function(result) {
    if(result.statusCode == 200)
      this.setEmailValuesRequest();
    else
      {
        $("#profileEmailError").text(result.answer);
        $("#profileEmailError").toggle(true);
        $("#profilePasswordError").toggle(true);
      }  
  },
  
  ajaxTimedOut : function() {
    alert("ajaxTimedOut");
  },
  
  countryRequestSuccess : function(result) {
    if(result.statusCode == 200) {
      $(".profileChangesSuccess").css("visibility","visible");
    }
  },

  
  setEmailValuesRequest : function() {
    $.ajax({
      type: "GET",
      url: this.basePath + 'catroid/profile/getEmailListRequest.json',
      timeout : (this.ajaxTimeout),
      success : $.proxy(this.setEmailValues, this),
    });
  },
  
  setEmailValues : function(result) {
    console.log(result.answer);
    $(".profileFirstEmailItem input").val(result.answer[0].address);
    if(result.answer[1] != null)
      $(".profileSecondEmailItem input").val(result.answer[1].address);
    else
      $(".profileSecondEmailItem input").attr('placeholder', this.languageStringsObject.second_email);
  },
  
  
  firstEmailCatchKeypress : function(event) {
    this.firstEmailChanged = 1;
    $(".profileChangesSuccess").css("visibility","hidden");
    if(event.which == '13') {
      this.updateChangesRequest();
      event.preventDefault();
    }
  },
  
  secondEmailCatchKeypress : function(event) {
    this.secondEmailChanged = 1;
    $(".profileChangesSuccess").css("visibility","hidden");
    if(event.which == '13') {
      this.updateChangesRequest();
      event.preventDefault();
    }
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
      error : $.proxy(this.ajaxTimedOut, this)
    });
  },


  search : function() {
    location.href = "/search/?q=" + $.trim($("#searchQuery").val()) + "&p=1";
    return false;
  },
  
  deleteProject : function(event) {
    if(confirm(this.languageStringsObject.really_delete_project + " '" + event.data.name + "' ?")) {
      document.location = this.basePath + "profile/?delete=" + event.data.id;
    }
  },

  setProjectObject : function(projectObject) {
    this.projectObject = projectObject;
  },
  
  saveHistoryState : function(action) {
    var context  = this.projectObject.getHistoryState();
    var title = this.languageStringsObject['websiteTitle'] + " - " + this.languageStringsObject['title'];

    if(action == 'init') {
      var state = this.history.getState();
      if(typeof state.data.content === 'undefined') {
        this.history.replaceState({content: context}, title, "");
      } else {
        this.restoreHistoryState();
      }
    }

    if(action == 'push') {
      this.history.pushState({content: context}, title, "");
    }
  },

  restoreHistoryState : function() {
    if(this.projectObject != null) {
      var current = this.projectObject.getHistoryState();
      var state = this.history.getState();
      if(typeof state.data.content === 'object') {
        if(state.data.content.visibleRows != current.visibleRows) {
          this.projectObject.restoreHistoryState(state.data.content);
        }
      }
    }
  }
});
