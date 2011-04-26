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
  var self = this;
    this.basePath = basePath;
    
    this.openLocation = {
      home: function() {
        location.href = self.basePath+'catroid/index';
      },        
      menu: function() {
        location.href = self.basePath+'catroid/menu';
      },
      login: function() {
        location.href = self.basePath+'catroid/login';
      }
    };

    if($("#normalHeaderButtons").length != 0) {
      $("#normalHeaderButtons").toggle();
    }

    $("#headerMenuButton").click(jQuery.proxy(this.openLocation, "menu"));
    $("#headerHomeButton").click(jQuery.proxy(this.openLocation, "home"));
    $("#headerSearchButton").click($.proxy(this.toggleSearchBox, this));
    $("#headerCancelSearchButton").click($.proxy(this.toggleSearchBox, this));
    $("#headerLoginButton").click(jQuery.proxy(this.openLocation, "login"));
  },

  toggleSearchBox : function() {
    $("#normalHeaderButtons").toggle();
    $("#cancelHeaderButton").toggle();
    $("#headerSearchBox").toggle();
    if($("#headerSearchBox").css("display") == "block") {
      $("#searchQuery").focus();
    }
  }
});

