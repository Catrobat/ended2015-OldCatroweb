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


var Menu = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function(userLogin_userId) {

    this.userLogin_userId = userLogin_userId;
    
    $("#menuForumButton").click({url:"addons/board",windowName:"board"}, jQuery.proxy(this.openWindow, this));
    $("#headerHomeButton").click({url:"catroid/index"}, jQuery.proxy(this.openLocation, this));
    $("#headerBackButton").click(jQuery.proxy(this.goBack, this));
    $("#menuWallButton").click({url:"catroid/wall"}, jQuery.proxy(this.openLocation, this));
    $("#menuSettingsButton").click({url:"catroid/settings"}, jQuery.proxy(this.openLocation, this));
    $("#menuLoginButton").click({url:"catroid/login?requesturi=catroid/menu"}, jQuery.proxy(this.openLocation, this));
    $("#menuLogoutButton").click(jQuery.proxy(this.doLogoutRequest, this));
    if(this.userLogin_userId == 0) {
    	$("#menuWikiButton").click({url:"wiki",windowName:"wiki"}, jQuery.proxy(this.openWindow, this));
    } else {
    	$("#menuWikiButton").click({url:"wiki/Main_Page?action=purge",windowName:"wiki"}, jQuery.proxy(this.openWindow, this));
    }
    if(this.userLogin_userId == 0) {
    	$("#menuProfileButton").click({url:"catroid/login"}, jQuery.proxy(this.openLocation, this));
    } else {
    	$("#menuProfileButton").click({url:"catroid/profile"}, jQuery.proxy(this.openLocation, this));
    }
    
    $("#menuWallButton").attr('disabled', true).removeClass('green').addClass('gray');
    $("#menuSettingsButton").attr('disabled', true).removeClass('rosy').addClass('gray');
    if(this.userLogin_userId > 0) {
      $("#menuLoginButton").toggle(false);
      $("#menuLogoutButton").toggle(true);
    } else {
      $("#menuLoginButton").toggle(true);
      $("#menuLogoutButton").toggle(false);
    }
    
    $("#headerLoginButton").click($.proxy(this.toggleProfileBox, this));
    $("#headerCancelButton").click($.proxy(this.toggleAllBoxes, this));
    
  },
  
  goBack : function(event) {
	  history.back();
  },
    
  openLocation : function(event) {
	  location.href = this.basePath+event.data.url;
  },
    
  openWindow : function(event) {
  	 window.open(this.basePath+event.data.url, event.data.windowName);
  },
    
  doLogoutRequest : function(event) {
	$.ajax({ 
      url: this.basePath+"catroid/login/logoutRequest.json", 
  	  async: false,
 		success: jQuery.proxy(this.doLogout, this)
 	});
  },
    
  doLogout: function(event) {
	  window.location.href = this.basePath+"catroid/login"; 
  },

  toggleProfileBox : function() {
    $("#normalHeaderButtons").toggle(false);
    $("#cancelHeaderButton").toggle(true);
    $("#headerProfileBox").toggle(true);
    if($("#headerLoginBox").css("display") == "block") {
      $("#loginUsrname").focus();
    }
  },

  toggleAllBoxes : function() {
    $("#normalHeaderButtons").toggle(true);
    $("#cancelHeaderButton").toggle(false);
    $("#headerProfileBox").toggle(false);
  }
  
});
