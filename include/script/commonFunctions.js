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


function bindAjaxLoader(basepath) {
  /*shows the loading div every time we have an Ajax call*/
  $(document).ajaxStart(function() {
    //$("body").append("<div class='webAjaxLoadingContainer' id='webAjaxLoadingContainer'><img class='webAjaxLoadingContainer' src='"+basepath+"images/symbols/ajax_loader_big.gif' /></div>");
    $("body").append($('<div>').attr('id', 'webAjaxLoadingContainer').
        addClass('webAjaxLoadingContainer').height($(document).height()));
  });
  
  $(document).ajaxStop(function() {
    $("#webAjaxLoadingContainer").remove();
  });
}

