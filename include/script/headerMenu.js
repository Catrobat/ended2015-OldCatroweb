/*
 *Catroid: An on-device visual programming system for Android devices
 *Copyright (C) 2010-2013 The Catrobat Team
 *(<http://developer.catrobat.org/credits>)
 *
 *This program is free software: you can redistribute it and/or modify
 *it under the terms of the GNU Affero General Public License as
 *published by the Free Software Foundation, either version 3 of the
 *License, or (at your option) any later version.
 *
 *An additional term exception under section 7 of the GNU Affero
 *General Public License, version 3, is available at
 *http://developer.catrobat.org/license_additional_term
 *
 *This program is distributed in the hope that it will be useful,
 *but WITHOUT ANY WARRANTY; without even the implied warranty of
 *MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *GNU Affero General Public License for more details.
 *
 *You should have received a copy of the GNU Affero General Public License
 *along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

var HeaderMenu = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function(userId) {
    this.userId = userId;
    
    if($("#normalHeaderButtons").length != 0) {
      $("#normalHeaderButtons").toggle();
    }
    
    $("#headerMenuButton").click({url:"menu"}, jQuery.proxy(this.openLocation, this));
    $("#headerLoginButton").click({url:"login"}, jQuery.proxy(this.openLocation, this));
    $("#headerProfileButton").click(jQuery.proxy(this.toggleProfileMenu, this));
    $("#headerCancelButton" ).click(jQuery.proxy(this.toggleAllBoxes, this));
    $("#menuProfileButton").click(jQuery.proxy(this.toggleProfileBox, this));
    $("#menuProfileChangeButton").click(jQuery.proxy(this.toggleProfileBox, this));
    $("#menuLogoutButton").click(jQuery.proxy(this.logoutRequest, this));
  },
  
  openLocation : function(event) {
    location.href = this.basePath + event.data.url;
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
      location.href = this.basePath + "profile";
    }
  },
  
  toggleAllBoxes : function() {
    $("#normalHeaderButtons").toggle(true);
    $("#headerSearchBox").toggle(true);
    $("#cancelHeaderButton").toggle(false);
    $("#headerProfileBox").toggle(false);
  },

  toggleProfileMenu : function() {
    $("#profileMenuNavigation").toggle();
  },
    
  openProfileMenu : function() {
    $("#profileMenuNavigation").css("display", "block");
  },
  
  closeProfileMenu : function() {
    $("#profileMenuNavigation").css("display", "none");
  },  


  logoutRequest : function() {
    $.ajax({
      type : "POST",
      url : this.basePath + "login/logoutRequest.json",
      success : $.proxy(this.logoutRequestSuccess, this),
      error : alert('error')
    });
  },

  logoutRequestSuccess : function(result) {
    common.showPreHeaderMessages(result);
    if(result.statusCode == 200) {
      if(this.requestUri != '') {
        location.href = this.basePath + 'login?requestUri=' + this.requestUri;
      } else {
        location.reload();
      }
    }
  },
    
  updateMenu : function() {
/*  if(this.userLogin_userId > 0) {
      $("#firstRow").toggle(true);
      $("#secondRow").toggle(false);
      $("#thirdRow").toggle(false);
      $("#menuRegistrationButton").toggle(false);
      $("#menuProfileButton").removeClass('gray').addClass('green');
    } else {
      $("#firstRow").toggle(true);
      $("#secondRow").toggle(true);
      $("#thirdRow").toggle(false);
      $("#menuRegistrationButton").toggle(true);
      $("#menuProfileButton").removeClass('green').addClass('gray');
    }*/
    
  }
  
});
