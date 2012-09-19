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

var Login = Class.$extend({
  __include__ : [__baseClassVars],
  __init__ : function() {
    this.requestUri = '';
    
    var fragments = location.search.split("?requestUri=");
    if(fragments.length != 0) {
      this.requestUri = fragments[1];
    }
    
    $("#loginSubmitButton").click($.proxy(this.loginRequest, this));
    $("#loginUsername").keypress($.proxy(this.loginCatchKeypress, this));
    $("#loginPassword").keypress($.proxy(this.loginCatchKeypress, this));

    $("#logoutSubmitButton").click($.proxy(this.logoutRequest, this));
  },

  loginCatchKeypress : function(event) {
    if (event.which == '13') {
      this.loginRequest();
      event.preventDefault();
    }
  },

  loginRequest : function() {
    $.ajax({
      type : "POST",
      url : this.basePath + 'catroid/login/loginRequest.json',
      data : ({
        loginUsername : $("#loginUsername").val(),
        loginPassword : $("#loginPassword").val()
      }),
      timeout : this.ajaxTimeout,
      success : $.proxy(this.loginRequestSuccess, this),
      error : $.proxy(common.ajaxTimedOut, this)
    });
  },

  loginRequestSuccess : function(result) {
    if(result.statusCode == 200) {
      location.href = this.basePath + 'catroid/login?requestUri=' + this.requestUri;
    } else {
      $("#loginHelperDiv").toggle(true);
      common.showPreHeaderMessages(result);
      common.showAjaxErrorMsg(result.answer);
    }
  },

  logoutRequest : function() {
    $.ajax({
      type : "POST",
      url : this.basePath + "catroid/login/logoutRequest.json",

      success : $.proxy(this.logoutRequestSuccess, this),
      error : $.proxy(common.ajaxTimedOut, this)
    });
  },

  logoutRequestSuccess : function(result) {
    common.showPreHeaderMessages(result);
    if(result.statusCode == 200) {
      location.href = this.basePath + 'catroid/index';
    }
  }
});
