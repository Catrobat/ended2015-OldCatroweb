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


var HeaderMenu = Class.$extend( {
  __init__ : function(basePath) {
    this.basePath = basePath;
    
    if($("#normalHeaderButtons").length != 0) {
      $("#normalHeaderButtons").toggle();
    }
    $("#headerHomeButton").click({url:"catroid/index"}, jQuery.proxy(this.openLocation, this));
    $("#headerMenuButton").click({url:"catroid/menu"}, jQuery.proxy(this.openLocation, this));
    $("#headerSearchButton").click(jQuery.proxy(this.toggleSearchBox, this));
    $("#headerProfileButton").click(jQuery.proxy(this.toggleProfileBox, this));
    $("#headerCancelButton").click(jQuery.proxy(this.toggleAllBoxes, this));
    $("#loginSubmitButton").click(jQuery.proxy(this.doLoginRequest, this));
    $("#logoutSubmitButton").click(jQuery.proxy(this.doLogoutRequest, this));
    $("#loginUsername").keypress($.proxy(this.loginCatchKeypress, this));
    $("#loginPassword").keypress($.proxy(this.loginCatchKeypress, this));
  },
  
  openLocation : function(event) {
    location.href = this.basePath+event.data.url;
  },
  
  toggleSearchBox : function() {
    $("#normalHeaderButtons").toggle(false);
    $("#cancelHeaderButton").toggle(true);
    $("#headerSearchBox").toggle(true);
    if($("#headerSearchBox").css("display") == "block") {
      $("#searchQuery").focus();
    }
  },

  toggleProfileBox : function() {
    $("#normalHeaderButtons").toggle(false);
    $("#cancelHeaderButton").toggle(true);
    $("#headerProfileBox").toggle(true);
    if($("#headerLoginBox").css("display") == "block") {
      $("#loginUsername").focus();
    }
  },

  doLoginRequest : function() {
    $("#loginSubmitButton").attr("disabled", "disabled");
    $("#loginUsername").attr("disabled", "disabled");
    $("#loginPassword").attr("disabled", "disabled");
    var url = this.basePath + 'catroid/login/loginRequest.json';
    $.ajax({
      type: "POST",
      url: url,
      data: ({
          loginUsername: $("#loginUsername").val(),
          loginPassword: $("#loginPassword").val()
      }),
      timeout: (5000),
      success : jQuery.proxy(this.loginSuccess, this),
      error : jQuery.proxy(this.loginError, this)
    });
  },
  
  loginSuccess : function(result) {
	if(result.statusCode == 200) {
      location.reload();
    } else {
      alert("error: "+result.answer);
    }
	$("#loginSubmitButton").removeAttr("disabled");
    $("#loginUsername").removeAttr("disabled");
    $("#loginPassword").removeAttr("disabled");
  },
  
  loginError : function(result, errCode) {
	alert("loginError");
  },
  
  doLogoutRequest : function(event) {
    $.ajax({ 
      url: this.basePath+"catroid/login/logoutRequest.json", 
      async: false,
      success: jQuery.proxy(this.logoutSuccess, this),
      error: jQuery.proxy(this.logoutError, this)
    });
  },
  
  logoutSuccess: function(result) {
    location.reload();
  },
  
  logoutError: function(result, errCode) {
	alert("logoutError"); 
  },
  
  toggleAllBoxes : function() {
    $("#normalHeaderButtons").toggle(true);
    $("#cancelHeaderButton").toggle(false);
    $("#headerSearchBox").toggle(false);
    $("#headerProfileBox").toggle(false);
  },
  
  loginCatchKeypress : function(event) {
	if(event.which == '13') {
	  event.preventDefault();
	  this.doLoginRequest();
	}
  }
  
});

