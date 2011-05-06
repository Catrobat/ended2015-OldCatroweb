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

var Login = Class.$extend( {
  __init__ : function(basePath) {
    this.basePath = basePath;
	var self = this;
    $("#loginFormDialog").toggle(true);
    $("#loginFormAnswer").toggle(false);
    $("#loginSubmit").click(
      $.proxy(this.loginSubmit, this));
    $("#logoutSubmit").click(
      $.proxy(this.logoutSubmit, this));
    $("#loginUsername").keypress(
      $.proxy(this.loginCatchKeypress, this));
    $("#loginPassword").keypress(
      $.proxy(this.loginCatchKeypress, this));
  },
  
  loginSubmit : function() {
    var self = this;
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/login/loginRequest.json',
      data: "loginUsername="+$("#loginUsername").val()+"&loginPassword="+$("#loginPassword").val()+"&requesturi="+$("#requesturi").val(),
      success: function(result){
        if(result['statusCode'] == 200) {
          location.href = self.basePath+"catroid/index";
        }
        else {
          $("#loginFormAnswer").toggle(true);
          $("#errorMsg").html(result.answer);
        }
      }
    });
  },
  
  logoutSubmit : function() {
    var self = this;
    $.ajax({
      url: self.basePath+"catroid/login/logoutRequest.json",
      async: false,
      success: function() {
        location.href = self.basePath+"catroid/index";
      }
    });
  },
  
  loginCatchKeypress : function(event) {
    if(event.which == '13') {
      event.preventDefault();
      this.loginSubmit();
    }
  }

});
