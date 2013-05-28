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

var Footer = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function() {
    this.isMoreMenuToggeled = false;
    
    $("#footerMoreButton").click($.proxy(this.toogleMoreMenu, this));
    $("#footerLessButton").click($.proxy(this.toogleMoreMenu, this));
  },
  
  toogleMoreMenu : function(event) {
    if(this.isMoreMenuToggeled) {
      $("#footerLessButton").hide();
      $("#footerMoreButton").show();
      
      $("#mobileFooterMenu ul").hide();
    } else {
      $("#footerLessButton").show();
      $("#footerMoreButton").hide();
      
      $("#mobileFooterMenu ul").show();
      $(window).scrollTop($(document).height());
    }
    
    this.isMoreMenuToggeled = !this.isMoreMenuToggeled;
    event.preventDefault();
  }
});
