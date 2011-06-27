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

var Login = Class.$extend({
  __include__ : [__baseClassVars],
  __init__ : function() {
    
    var uri = location.search;
    var vals = location.search.split("?requesturi=");
    if(vals.length == 2) {
      this.requestUri = vals[1];
    } else {
      this.requestUri = null;
    }

    $("#loginSubmitButton").click(jQuery.proxy(this.doLoginRequest, this));
    $("#logoutSubmitButton").click(jQuery.proxy(this.doLogoutRequest, this));
    $("#loginUsername").keypress($.proxy(this.loginCatchKeypress, this));
    $("#loginPassword").keypress($.proxy(this.loginCatchKeypress, this));
  },

  doLoginRequest : function() {
    $("#loginInfoText").toggle(false);
    this.disableForm();
    var url = this.basePath + 'catroid/login/loginRequest.json';
    $.ajax({
      type : "POST",
      url : url,
      data : ({
        loginUsername : $("#loginUsername").val(),
        loginPassword : $("#loginPassword").val()
      }),
      timeout : (this.ajaxTimeout),
      success : jQuery.proxy(this.loginSuccess, this),
      error : jQuery.proxy(this.loginError, this)
    });
  },

  loginSuccess : function(result) {
    if (result.statusCode == 200) {
      if(this.requestUri) {
        location.href = this.basePath+this.requestUri;
      } else {
        location.reload();
      }      
    } else {
      $("#loginInfoText").toggle(true);
      $("#loginErrorMsg").html(result.answer);
      this.enableForm();
    }
  },

  loginError : function(result, errCode) {
    //alert("loginError");
    this.enableForm();
  },

  doLogoutRequest : function(event) {
    $.ajax({
      url : this.basePath + "catroid/login/logoutRequest.json",
      async : false,
      success : jQuery.proxy(this.logoutSuccess, this),
      error : jQuery.proxy(this.logoutError, this)
    });
  },

  logoutSuccess : function(result) {
    location.reload();
  },

  logoutError : function(result, errCode) {
    //alert("logoutError");
  },

  loginCatchKeypress : function(event) {
    if (event.which == '13') {
      event.preventDefault();
      this.doLoginRequest();
    }
  },
  
  disableForm : function() {
    $("#loginSubmitButton").attr("disabled", "disabled");
    $("#loginUsername").attr("disabled", "disabled");
    $("#loginPassword").attr("disabled", "disabled");
  },
  
  enableForm : function() {
    $("#loginSubmitButton").removeAttr("disabled");
    $("#loginUsername").removeAttr("disabled");
    $("#loginPassword").removeAttr("disabled");
  }

});
