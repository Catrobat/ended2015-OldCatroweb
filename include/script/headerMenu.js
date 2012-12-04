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

var HeaderMenu = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function(userId) {
    this.userId = userId;
    
    if($("#normalHeaderButtons").length != 0) {
      $("#normalHeaderButtons").toggle();
    }
    
    $("#headerMenuButton").click({url:"catroid/menu"}, jQuery.proxy(this.openLocation, this));
    $("#headerProfileButton").click(jQuery.proxy(this.toggleProfileBox, this));
    $("#headerCancelButton" ).click(jQuery.proxy(this.toggleAllBoxes, this));
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
    if(this.userId == 0) {
      $("#normalHeaderButtons").toggle(false);
      $("#cancelHeaderButton").toggle(true);
      $("#headerProfileBox").toggle(true);
      $("#headerSearchBox").toggle(false);
      if($("#headerLoginBox").css("display") == "block") {
        $("#loginUsername").focus();
      }
    } else {
      location.href = this.basePath + "catroid/profile";
    }
  },
  
  toggleAllBoxes : function() {
    $("#normalHeaderButtons").toggle(true);
    $("#headerSearchBox").toggle(true);
    $("#cancelHeaderButton").toggle(false);
    $("#headerProfileBox").toggle(false);
  }
  
});
