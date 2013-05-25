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

var Header = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function(userId) {
    this.isSearchBarToggeled = false;
    this.isNavigationMenuToggeled = false;
    
    this.hideNavigationMenuTimer = null;

    this.userId = 0;
    if(typeof userId === 'number') {
      this.userId = userId;
    }

    this.requestUri = '';
    
    var fragments = location.search.split("?requestUri=");
    if(fragments.length > 1) {
      this.requestUri = fragments[1];
    }

    $(window).resize($.proxy(this.hideNavigationMenu, this));
    $(window).mousedown($.proxy(this.hideNavigationMenu, this));

    $("#mobileSearchButton").click($.proxy(this.toggleSearchBar, this));
    $("#mobileMenuButton").click($.proxy(this.pressProfileButton, this));
    $("#largeMenuButton").click($.proxy(this.pressProfileButton, this));
    $("#menuLogoutButton").click($.proxy(this.logoutRequest, this)); 
    $("#menuProfileButton").click($.proxy(this.openProfile, this)); 
  },

  pressProfileButton : function(event) {
    if(this.userId == 0) {
      location.href = this.basePath + 'login';
    } else {
      this.toggleNavigationMenu();
    }
    window.clearTimeout(this.hideNavigationMenuTimer);
  },
  
  hideNavigationMenu : function() {
    this.hideNavigationMenuTimer = window.setTimeout($.proxy(function() {
      $('#navigationMenu').hide();
      this.isNavigationMenuToggeled = false;
    }, this), 300);
  },
  
  toggleNavigationMenu : function() {
    if(this.isNavigationMenuToggeled) {
      $('#navigationMenu').hide();
    } else {
      var menu = $('#largeMenuButton');
      if($('#mobileMenuButton').is(':visible')) {
        menu = $('#mobileMenuButton');
      }
      
      $('#navigationMenu').css({right: $(window).width() - menu.offset().left - menu.outerWidth() + 5 });
      $('#navigationMenu').show();
    }
    this.isNavigationMenuToggeled = !this.isNavigationMenuToggeled;
  },

  toggleSearchBar : function(event) {
    if(this.isSearchBarToggeled) {
      $("#smallMenuBar").css("display", "table-cell");
      $("#smallSearchBar").hide();

      $("#mobileSearchButton button").addClass('img-magnifying-glass');
      $("#mobileSearchButton button").removeClass('img-undo-search');
    } else {
      $("#smallMenuBar").hide();
      $("#smallSearchBar").css("display", "table-cell");
      
      $("#mobileSearchButton button").removeClass('img-magnifying-glass');
      $("#mobileSearchButton button").addClass('img-undo-search');
      
      $("#smallSearchBar input").focus();
    }
    
    this.isSearchBarToggeled = !this.isSearchBarToggeled;
  },
  
  openProfile : function() {
    if(this.userId == 0) {
      location.href = this.basePath + 'login';
    } else {
      location.href = this.basePath + "profile"; 
    }    
  },

  logoutRequest : function() {
     $.ajax({
       type : "POST",
       context: this,
       url : this.basePath + "login/logoutRequest.json",
       success : $.proxy(this.logoutRequestSuccess, this),
       error : $.proxy(this.logoutRequestError, this)
     });
   },

   logoutRequestSuccess : function(result) {
     if(result.statusCode == 200) {
       if(this.requestUri != '') {
         location.href = this.basePath + 'login?requestUri=' + this.requestUri;
       } else {
         location.reload();
       }
     }
   },
   
   logoutRequestError : function(error) {
     alert(error);
   }
});
