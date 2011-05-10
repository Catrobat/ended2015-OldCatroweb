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
  __init__ : function(basePath) {
    this.basePath = basePath;

    var self = this;
    $("#passwordRecoveryFormDialog").toggle(true);
    $("#passwordRecoverySaveFormDialog").toggle(true);
    $("#loginOk").toggle(false);
    
    $("#errorMsg").toggle(false);
    $("#okMsg").toggle(false);
    $("#passwordRecoveryFormAnswer").toggle(false);

    $("#passwordRecoverySubmit").click(
      $.proxy(this.passwordRecoverySubmit, this));
    $("#passwordRecoveryUserdata").keypress(
      $.proxy(this.passwordRecoveryUserdataCatchKeypress, this));
 
    $("#passwordSaveSubmit").click(
      $.proxy(this.passwordSaveSubmit, this));
    $("#passwordSavePassword").keypress(
      $.proxy(this.passwordSavePasswordCatchKeypress, this));

    $("#passwordLoginSubmit").click(
        $.proxy(this.passwordLoginSubmit, this));  
  },
  
  passwordRecoverySubmit : function() {
    // disable form fields
    $("#passwordRecoverySubmit").attr("disabled", "disabled");
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
    }
    $("#passwordRecoverySubmit").removeAttr("disabled");
    $("#passwordRecoveryUserdata").removeAttr("disabled");
  },
 
  passwordSaveSubmit : function() {
    // disable form fields
    $("#passwordSaveSubmit").attr("disabled", "disabled");
    $("#passwordSavePassword").attr("disabled", "disabled");
    
    var self = this;
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/passwordrecovery/passwordRecoveryChangeMyPasswordRequest.json',
      data: "c="+$("#c").val()+"&passwordSavePassword="+$("#passwordSavePassword").val(),
      timeout: (5000),
      
      success: function(result){
        if(result['statusCode'] == 200) {
          $("#passwordRecoveryFormDialog").toggle(false);
          $("#passwordRecoveryFormAnswer").toggle(true);
          $("#errorMsg").toggle(false);
          $("#okMsg").toggle(true);
          $("#okMsg").html(result.answer_ok);
          $("#loginOk").toggle(true);
        }
        else {
          $("#passwordRecoveryFormAnswer").toggle(true);
          $("#passwordRecoveryFormDialog").toggle(true);
          if(result.answer) {
            $("#okMsg").toggle(false);
            $("#errorMsg").toggle(true);
            $("#errorMsg").html(result.answer);
          }
        }
        // enable form fields
        $("#passwordSaveSubmit").removeAttr("disabled");
        $("#passwordSavePassword").removeAttr("disabled");
      },
      error : function(result, errCode) {
        if(errCode == "timeout") {
          window.location.reload(false);   
        }
      }
    });
  },
  
  passwordLoginSubmit : function() {
    var self = this;
    location.href = self.basePath+"catroid/login";
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
