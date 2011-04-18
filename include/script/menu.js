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
  __init__ : function(basePath, userLogin_userId) {
	var self = this;
	this.userLogin_userId = userLogin_userId;
    this.basePath = basePath;
    
    this.openLocation = {
      home: function() {
        location.href = self.basePath+'catroid/index';
      },
      back: function() {
        history.back();
      },
      wall: function() {
        location.href = self.basePath+'catroid/wall';
      },
      profile: function() {
        if(userLogin_userId == 0) {
          location.href = self.basePath+'catroid/login';
        } else {
          location.href = self.basePath+'catroid/profile';
        }
      },
      settings: function() {
        if(userLogin_userId > 0) {
          location.href = self.basePath+'catroid/settings';
        } else {
          location.href = self.basePath+'catroid/login';
        }
      },
      forum: function() {
        if(userLogin_userId > 0) {
          location.href = self.basePath+'addons/board';
        } else {
          location.href = self.basePath+'catroid/login?requesturi=addons/board';
        }
      },
      wiki: function() {
        if(userLogin_userId > 0) {
          location.href = self.basePath+'wiki';
        } else {
          location.href = self.basePath+'catroid/login?requesturi=wiki';
        }
      },
      login: function() {
        location.href = self.basePath+'catroid/login?requesturi=catroid/menu';
      },
      logout: function() {
        var submitForm = "<form action='"+self.basePath+"catroid/login' method='POST'>";
        submitForm += "<input type='hidden' name='logoutSubmit' value='Logout'>";
        submitForm += "</form>";

        var $form = $(submitForm).appendTo('body');
        $form.submit();
      }
    };
    
    $("#headerHomeButton").click(jQuery.proxy(this.openLocation, "home"));
    $("#headerBackButton").click(jQuery.proxy(this.openLocation, "back"));
    $("#menuWallButton").click(jQuery.proxy(this.openLocation, "wall"));
    $("#menuProfileButton").click(jQuery.proxy(this.openLocation, "profile"));
    $("#menuSettingsButton").click(jQuery.proxy(this.openLocation, "settings"));
    $("#menuForumButton").click(jQuery.proxy(this.openLocation, "forum"));
    $("#menuWikiButton").click(jQuery.proxy(this.openLocation, "wiki"));
    $("#menuLoginButton").click(jQuery.proxy(this.openLocation, "login"));
    $("#menuLogoutButton").click(jQuery.proxy(this.openLocation, "logout"));
    
    $("#menuWallButton").attr('disabled', true).removeClass('green').addClass('gray');
    $("#menuProfileButton").attr('disabled', true).removeClass('pink').addClass('gray');
    $("#menuSettingsButton").attr('disabled', true).removeClass('rosy').addClass('gray');
    
    if(userLogin_userId > 0) {
      $("#menuLoginButton").toggle(false);
      $("#menuLogoutButton").toggle(true);
    } else {
      $("#menuLoginButton").toggle(true);
      $("#menuLogoutButton").toggle(false);
    }
  }
});
