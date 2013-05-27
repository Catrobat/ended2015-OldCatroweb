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
    this.state = this.getProfileState();
    
    this.initAvatarUploader();
   
    $(".profilePasswordItem input").keypress($.proxy(this.passwordCatchKeypress, this));
    $(".profileRepeatPassword input").keypress($.proxy(this.passwordCatchKeypress, this));
    $(".profileFirstEmailItem input").keypress($.proxy(this.firstEmailCatchKeypress, this));
    $(".profileSecondEmailItem input").keypress($.proxy(this.secondEmailCatchKeypress, this));
    $(".profileCountry").change($.proxy(this.countryChangedEvent, this));
    $("#profileSaveChanges").click($.proxy(this.updateChangesRequest, this));
    $("#profileDeleteFirstEmail").click($.proxy(this.deleteEmail,this, 0));
    $("#profileDeleteSecondEmail").click($.proxy(this.deleteEmail,this, 1));

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

  getProfileState : function() {
    state = { 'pwd' : $("#profileNewPassword").val(), 'firstEmail' : $(".profileFirstEmailItem input").val(),
        'secondEmail' : $(".profileSecondEmailItem input").val(), 'country' : $(".profileCountry select").val()  };
    return state;
  },
  
  modifiedProfileState : function(state) {
    var state = this.getProfileState();
    var changed = "";
    var changes = 0;
    
    for(var property in state) {
      if(state[property] != this.state[property]) {
        changed = property;
        changes++;
      }
    }

    if(changes > 1) {
      return "multiple";
    }
    return changed;
  },

  hideErrorMessages : function() {
    $(".profileLoader").hide();
    $("#profileChangesSuccess").css("visibility", "hidden");

    $("#profileAvatarError").hide();
    $(".profileAvatarImageError").addClass("profileAvatarImage");
    $(".profileAvatarImageError").removeClass("profileAvatarImageError");

    $("#profilePasswordError").hide();
    $(".profilePasswordItem").removeClass("profileInvalid");
    $(".profilePasswordItem").addClass("profileValid");
    $(".profilePasswordItem input").removeClass("inputInvalid");
    $(".profilePasswordItem div").removeClass("img-failedPw");
    $(".profilePasswordItem div").addClass("img-password");

    $("#profileEmailError").hide();
    $(".profileFirstEmailItem").removeClass("profileInvalid");
    $(".profileFirstEmailItem").addClass("profileValid");
    $(".profileFirstEmailItem input").removeClass("inputInvalid");
    $(".profileFirstEmailItem div").removeClass("img-failed-first-email");
    $(".profileFirstEmailItem div").addClass("img-first-email");
    
    $(".profileSecondEmailItem").removeClass("profileInvalid");
    $(".profileSecondEmailItem").addClass("profileValid");
    $(".profileSecondEmailItem input").removeClass("inputInvalid");
    $(".profileSecondEmailItem div").removeClass("img-failed-second-email");
    $(".profileSecondEmailItem div").addClass("img-second-email");
  },

  //-------------------------------------------------------------------------------------------------------------------
  initAvatarUploader : function() {
    var self = this;
    var wrapper = $('<div/>', {'id': 'profileAvatarFileWrapper'}).css({height:0, width:0, 'overflow':'hidden'});
    var fileInput = $('#profileAvatarFile').wrap(wrapper);
    fileInput.change(function() {
      if(this.files[0].size > 5 * 1024 * 1024) {
        this.avatarRequestError(self.languageStringsObject.image_too_big);
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
      self.hideErrorMessages();
      fileInput.click();
    }).show();
  },


  avatarRequestSuccess : function(result) {
    if(result.statusCode == 200) {
      $('.profileAvatarImage').attr("src", result.avatar);
      $('#largeMenuButton button.img-avatar').css("background-image", 'url(' + result.avatar + ')');
      $('#mobileMenuButton button.img-avatar').css("background-image", 'url(' + result.avatar + ')');
      $("#profileUpdateSuccess").show();
    } else {
      this.avatarRequestError(result.answer);
    }
  },
  
  avatarRequestError : function(message) {
    $("#profileAvatarError").text(message);
    $("#profileAvatarError").show();
    $(".profileAvatarImage").addClass("profileAvatarImageError");
    $(".profileAvatarImage").removeClass("profileAvatarImage");
  },

  //-------------------------------------------------------------------------------------------------------------------
  
  deleteEmail : function(additional){ 
    this.hideErrorMessages();
    
    if(additional) {
      if(confirm(this.languageStringsObject.really_delete_email + " '" + $(".profileSecondEmailItem input").val() + "' ?")) {
        $(".profileLoader").show();
        $.ajax({
          type: "POST",
          url: this.basePath + 'profile/deleteEmailRequest.json',
          data : ({
            additional  : additional,
            firstEmail  : $(".profileFirstEmailItem input").val(), 
            secondEmail : $(".profileSecondEmailItem input").val(),
          }),
          success : $.proxy(function(result) {
            if(result.statusCode == 200) {
              $(".profileLoader").hide();
              $(".profileSecondEmailItem input").val('');
              this.state = this.getProfileState();
            } else {
              this.deleteEmailRequestError(result);
            }
          }, this),
          error : $.proxy(this.deleteEmailRequestError, this)
        });
      }
    } else {
      if(confirm(this.languageStringsObject.really_delete_email + " '" + $(".profileFirstEmailItem input").val() + "' ?")) {
        $(".profileLoader").show();
        $.ajax({
          type: "POST",
          url: this.basePath + 'profile/deleteEmailRequest.json',
          data : ({
            additional  : additional,
            firstEmail  : $(".profileFirstEmailItem input").val(), 
            secondEmail : $(".profileSecondEmailItem input").val(),
          }),
          success : $.proxy(function(result) {
            if(result.statusCode == 200) {
              $(".profileLoader").hide();
              $(".profileFirstEmailItem input").val('');
              this.state = this.getProfileState();
            } else {
              this.deleteEmailRequestError(result);
            }
          }, this),
          error : $.proxy(this.deleteEmailRequestError, this)
        });
      }      
    }
  },
  
  passwordCatchKeypress : function(event) {
    if(event.which == '13') {
      this.updateChangesRequest();
      event.preventDefault();
    }
  },
  
  updateChangesRequest : function() {
    this.hideErrorMessages();

    switch(this.modifiedProfileState()) {
      case 'multiple':
        alert(this.languageStringsObject['edit_one_entry']);
        break;
      case 'pwd':
        $(".profileLoader").show();
        $.ajax({
          type: "POST",
          url: this.basePath + 'profile/updatePasswordRequest.json',
          data : ({
            profileNewPassword : $("#profileNewPassword").val(),
            profileRepeatPassword : $("#profileRepeatPassword").val()
          }),
          success : $.proxy(this.passwordRequestSuccess, this),
          error : $.proxy(this.passwordRequestError, this)
        });
        break;
      case 'firstEmail':
        $(".profileLoader").show();
        $.ajax({
          type: "POST",
          url: this.basePath + 'profile/updateEmailRequest.json',
          data : ({
            email : $(".profileFirstEmailItem input").val(),
            additional : 0
          }),
          success : $.proxy(this.firstEmailRequestSuccess, this),
          error : $.proxy(this.firstEmailRequestError, this)
        });
        break;
      case 'secondEmail':
        $(".profileLoader").show();
        $.ajax({
          type: "POST",
          url: this.basePath + 'profile/updateEmailRequest.json',
          data : ({
            email : $(".profileSecondEmailItem input").val(),
            additional : 1
          }),
          success : $.proxy(this.secondEmailRequestSuccess, this),
          error : $.proxy(this.secondEmailRequestError, this)
        });
        break;
      case 'country':
        $(".profileLoader").show();
        $.ajax({
          type: "POST",
          url: this.basePath + 'profile/updateCountryRequest.json',
          data : ({
            country : $(".profileCountry select").val()
          }),
          success : $.proxy(this.countryRequestSuccess, this), 
          error : $.proxy(this.ajaxTimedOut, this)
        });
        break;
      default:
        break;
    }
  },
  
  passwordRequestSuccess : function(result) {
    if(result.statusCode == 200) {
      $(".profileLoader").hide();
      $("#profileChangesSuccess").css("visibility", "visible");
      $("#profileNewPassword").val('');
      $("#profileRepeatPassword").val('');
    } else {
      this.passwordRequestError(result);
    }
  },
  
  passwordRequestError : function(result) {
    $(".profileLoader").hide();
    
    $("#profilePasswordError").text(result.answer);
    $("#profilePasswordError").show();
    $(".profilePasswordItem").removeClass("profileValid");
    $(".profilePasswordItem").addClass("profileInvalid");
    $(".profilePasswordItem input").addClass("inputInvalid");
    $(".profilePasswordItem div").removeClass("img-password");
    $(".profilePasswordItem div").addClass("img-failedPw");

    $("#profileNewPassword").val('');
    $("#profileRepeatPassword").val('');
  },
 
  firstEmailRequestSuccess : function(result) {
    if(result.statusCode == 200) {
      $(".profileLoader").hide();
      $("#profileChangesSuccess").css("visibility","visible");
      this.state = this.getProfileState();
      alert(this.languageStringsObject['email_verification']);
    } else {
      this.firstEmailRequestError(result);
    }
  },
  
  firstEmailRequestError : function(result) {
    $(".profileLoader").hide();

    $("#profileEmailError").text(result.answer);
    $("#profileEmailError").show();

    $(".profileFirstEmailItem").removeClass("profileValid");
    $(".profileFirstEmailItem").addClass("profileInvalid");
    $(".profileFirstEmailItem input").addClass("inputInvalid");
    $(".profileFirstEmailItem div").removeClass("img-first-email");
    $(".profileFirstEmailItem div").addClass("img-failed-first-email");
    $(".profileFirstEmailItem input").val(this.state['firstEmail']);
  },
  
  secondEmailRequestSuccess : function(result) {
    if(result.statusCode == 200) {
      $(".profileLoader").hide();
      $("#profileChangesSuccess").css("visibility","visible");
      this.state = this.getProfileState();
      alert(this.languageStringsObject['email_verification']);
    } else {
      this.secondEmailRequestError(result);
    }
  },
  
  secondEmailRequestError : function(result) {
    $(".profileLoader").hide();

    $("#profileEmailError").text(result.answer);
    $("#profileEmailError").show();

    $(".profileSecondEmailItem").removeClass("profileValid");
    $(".profileSecondEmailItem").addClass("profileInvalid");
    $(".profileSecondEmailItem input").addClass("inputInvalid");
    $(".profileSecondEmailItem div").removeClass("img-second-email");
    $(".profileSecondEmailItem div").addClass("img-failed-second-email");
    $(".profileSecondEmailItem input").val(this.state['secondEmail']);
  },

  deleteEmailRequestError : function(result) {
    $(".profileLoader").hide();
    $("#profileEmailError").text(result.answer);
    $("#profileEmailError").show();
    $(".profileFirstEmailItem input").val(this.state['firstEmail']);
    $(".profileSecondEmailItem input").val(this.state['secondEmail']);
  },
  
  countryRequestSuccess : function(result) {
    $(".profileLoader").hide();
    if(result.statusCode == 200) {
      $("#profileChangesSuccess").css("visibility", "visible");
      this.state = this.getProfileState();
    }
  },
  
  firstEmailCatchKeypress : function(event) {
    if(event.which == '13') {
      this.updateChangesRequest();
      event.preventDefault();
    }
  },
  
  secondEmailCatchKeypress : function(event) {
    if(event.which == '13') {
      this.updateChangesRequest();
      event.preventDefault();
    }
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
