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
      this.passwordRecoverySendLink();
      event.preventDefault();
    }
  },
  
  passwordRecoverySendLink : function() {
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/passwordrecovery/sendMailRequest.json',
      data : ({
        passwordRecoveryUserdata : $("#passwordRecoveryUserdata").val()
      }),
      timeout : this.ajaxTimeout,
      success : $.proxy(this.genericRequestSuccess, this),
      error : $.proxy(common.ajaxTimedOut, this)
    });
  },
  
  passwordSavePasswordCatchKeypress : function(event) {
    if(event.which == '13') {
      this.passwordSaveSubmit();
      event.preventDefault();
    }
  },

  passwordSaveSubmit : function() {
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/passwordrecovery/changeMyPasswordRequest.json',
      data : ({
        c : $("#passwordRecoveryHash").val(),
        passwordSavePassword : $("#passwordSavePassword").val()
      }),
      timeout : this.ajaxTimeout,
      success : $.proxy(this.changeMyPasswordRequestSuccess, this),
      error : $.proxy(common.ajaxTimedOut, this)
    });
  },

  changeMyPasswordRequestSuccess : function(result) {
    common.showPreHeaderMessages(result);
    if(result.statusCode == 200) {
      location.href = this.basePath + 'catroid/profile';
    } else {
      common.showAjaxErrorMsg(result.answer);
    }
  },

  genericRequestSuccess : function(result) {
    common.showPreHeaderMessages(result);
    if(result.statusCode == 200) {
      common.showAjaxSuccessMsg(result.answer);
    } else {
      common.showAjaxErrorMsg(result.answer);
    }
  }
});