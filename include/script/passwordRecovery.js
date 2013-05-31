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

var PasswordRecovery = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function() {
    $("#passwordRecoverySendLink").click($.proxy(this.passwordRecoverySendLink, this));
    $("#passwordRecoveryUserdata").keypress($.proxy(this.passwordRecoveryUserdataCatchKeypress, this));
    
    $("#passwordSaveSubmit").click($.proxy(this.passwordSaveSubmit, this));
    $("#passwordSavePassword").keypress($.proxy(this.passwordSavePasswordCatchKeypress, this));
  },

  passwordRecoveryUserdataCatchKeypress : function(event) {
    if(event.which == '13') {
      this.passwordRecoverySendLink(event);
      event.preventDefault();
    }
  },
  
  passwordRecoverySendLink : function(event) {
    $("#passwordRecoverySendLink").hide();
    $("#passwordRecoverySendLoader").css('display', 'block');
    $("#recoveryMessage").hide();
    
    $("#passwordRecoveryUserdata").blur();

    $.ajax({
      type: "POST",
      url: this.basePath + 'passwordrecovery/sendMailRequest.json',
      data : ({
        passwordRecoveryUserdata : $("#passwordRecoveryUserdata").val()
      }),
      success : $.proxy(this.passwordRecoverySendLinkRequestSuccess, this),
      error   : $.proxy(this.passwordRecoverySendLinkRequestError, this)
    });
    event.preventDefault();
  },

  passwordRecoverySendLinkRequestSuccess : function(result) {
    $("#passwordRecoverySendLoader").hide();
    if(result.statusCode == 200) {
      $("#recoveryMessage").show().text(result.answer).css("color", "#a8dff4");
    } else {
      $("#passwordRecoverySendLink").show();
      $("#recoveryMessage").show().text(result.answer);
    }
  },

  passwordRecoverySendLinkRequestError : function(error) {
    $("#passwordRecoverySendLink").show();
    $("#passwordRecoverySendLoader").hide();
    $("#recoveryMessage").show().text(result.answer);
  },
  
  passwordSavePasswordCatchKeypress : function(event) {
    if(event.which == '13') {
      this.passwordSaveSubmit(event);
      event.preventDefault();
    }
  },

  passwordSaveSubmit : function(event) {
    $("#passwordSaveSubmit").hide();
    $("#passwordSaveLoader").css('display', 'block');
    $("#recoveryMessage").hide();
    
    $("#passwordSavePassword").blur();

    $.ajax({
      type: "POST",
      url: this.basePath + 'passwordrecovery/changeMyPasswordRequest.json',
      data : ({
        c : $("#passwordRecoveryHash").val(),
        passwordSavePassword : $("#passwordSavePassword").val()
      }),
      success : $.proxy(this.changeMyPasswordRequestSuccess, this),
      error : $.proxy(this.changeMyPasswordRequestError, this)
    });
    event.preventDefault();
  },

  changeMyPasswordRequestSuccess : function(result) {
    $("#passwordSaveLoader").hide();
    if(result.statusCode == 200) {
      location.href = this.basePath + 'profile';
    } else {
      $("#passwordSaveSubmit").show();
      $("#recoveryMessage").show().text(result.answer);
    }
  },

  changeMyPasswordRequestError : function(error) {
    $("#passwordSaveSubmit").show();
    $("#passwordSaveLoader").hide();
    $("#recoveryMessage").show().text(result.answer);    
  }
});
