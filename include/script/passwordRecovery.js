/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team
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

    this.answer = ''; 
    this.saving_password = '';

    $("#passwordRecoveryFormDialog").toggle(true);
    $("#passwordRecoverySaveFormDialog").toggle(true);
    $("#loginOk").toggle(false);
    
    $("#errorMsg").toggle(false);
    $("#okMsg").toggle(false);
    $("#passwordRecoveryFormAnswer").toggle(false);

    $("#passwordRecoverySendLink").click(
      $.proxy(this.passwordRecoverySendLink, this));
    $("#passwordRecoveryUserdata").keypress(
      $.proxy(this.passwordRecoveryUserdataCatchKeypress, this));
 
    $("#passwordSaveSubmit").click(
      $.proxy(this.passwordSaveSubmit, this));
    $("#passwordSavePassword").keypress(
      $.proxy(this.passwordSavePasswordCatchKeypress, this));

    $("#loginOkForwardSubmit").click(
        $.proxy(this.loginOkSubmit, this));  
  },
  
  passwordRecoverySendLink : function() {
    // disable form fields
    $("#passwordRecoverySendLink").attr("disabled", "disabled");
    $("#passwordRecoveryUserdata").attr("disabled", "disabled");

    var url = this.basePath + 'catroid/passwordrecovery/passwordRecoverySendMailRequest.json';
    $.post(url, {
      passwordRecoveryUserdata : $("#passwordRecoveryUserdata").val()
    }, $.proxy(this.passwordRecoverySuccess, this), "json");
  },
  
  passwordRecoverySuccess : function(response) {
    $("#passwordRecoveryFormAnswer").toggle(true);
    if(response.answer) {
      $("#okMsg").toggle(false);
      $("#errorMsg").toggle(true);
      $("#errorMsg").html(response.answer);
    }
    if(response.answer_ok) {
      $("#errorMsg").toggle(false);
      $("#okMsg").toggle(true);
      $("#okMsg").html(response.answer_ok);
      $("#passwordRecoveryFormDialog").toggle(false);
    }
    $("#passwordRecoverySendLink").removeAttr("disabled");
    $("#passwordRecoveryUserdata").removeAttr("disabled");
  },
 
  passwordSaveSubmit : function() {
    // disable form fields
    $("#passwordSaveSubmit").attr("disabled", "disabled");
    $("#passwordSavePassword").attr("disabled", "disabled");
    
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/passwordrecovery/passwordRecoveryChangeMyPasswordRequest.json',
      data : ({
        c : $("#passwordRecoveryHash").val(),
        passwordSavePassword : $("#passwordSavePassword").val()
      }),
      
      timeout : (this.ajaxTimeout),
      success : jQuery.proxy(this.passwordSaveSuccess, this),
      error : jQuery.proxy(this.passwordSaveError, this)
      
    });
  },
  
  passwordSaveSuccess : function(result) {
    if(result.statusCode == 200) {
      $("#errorMsg").toggle(false);
      $("#passwordRecoveryFormAnswer").toggle(true);
      $("#passwordRecoveryFormDialog").toggle(false);
      //$("#okMsg").toggle(true);
      this.answer = result.answer_ok;
      this.saving_password = result.saving_password;
      $("#okMsg").toggle(true);
      $("#okMsg").html(this.saving_password);
      
      this.passwordRecoveryLoginSubmit(result.username);
    }
    else {
      $("#passwordRecoveryFormAnswer").toggle(true);
      $("#passwordRecoveryFormDialog").toggle(true);
      if(result.answer) {
        $("#okMsg").toggle(false);
        $("#errorMsg").toggle(true);
        $("#errorMsg").html(result.answer);
      }
      // enable form fields
      $("#passwordSaveSubmit").removeAttr("disabled");
      $("#passwordSavePassword").removeAttr("disabled");
    }
  },
  
  
  passwordSaveError : function() {
    if(errCode == "timeout") {
      window.location.reload(false);   
    }
  },
  
  
  passwordRecoveryLoginSubmit : function(username) {
    $.ajax({
      type: "POST",
      url: this.basePath + 'api/login/loginRequest.json',
      data : ({
        loginUsername : username,
        loginPassword : $("#passwordSavePassword").val()
      }),

      timeout : (this.ajaxTimeout),
      success : jQuery.proxy(this.passwordRecoveryLoginSuccess, this),
      error : jQuery.proxy(this.passwordRecoveryLoginError, this)
    });
  },


  passwordRecoveryLoginSuccess : function(result) {
    if(result.statusCode == 200) {
      $("#okMsg").html('');
      $("#okMsg").html(this.answer);
      $("#loginOk").toggle(true);
      $("#loginOkSubmit").toggle(true);
      
      var self = this;
      setTimeout(
        function() { self.redirectToUrl(self.basePath+'catroid/profile'); }, 
        5000
      );
    }
    else if(result.statusCode == 500) {
      $("#passwordRecoveryFormDialog").toggle(true);
      if(result.answer) {
        $("#okMsg").toggle(false);
        $("#errorMsg").toggle(true);
        $("#errorMsg").html(result.answer);
      }
    }
  },
  
  passwordRecoveryLoginError : function() {
    if(errCode == "timeout") {
      window.location.reload(false);   
    }
  },
  
  
  redirectToUrl : function(url) {
    location.href = url;
  },
  
  
  loginOkSubmit : function() {
    location.href = this.basePath+"catroid/profile";
  },
  
  
  passwordRecoveryUserdataCatchKeypress : function(event) {
    if(event.which == '13') {
      event.preventDefault();
      this.passwordRecoverySubmit();
    }
  },
  
  passwordSavePasswordCatchKeypress : function(event) {
    if(event.which == '13') {
      event.preventDefault();
      this.passwordSaveSubmit();
    }
  }
});
