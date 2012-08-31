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

var MyProjects = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function(strings) {
    var basePath = this.basePath;
    
    $(document).ready(function(e) {
      $('#deleteButtons > div > button').each(function() {
        $(this).click(function(e) {
          if(confirm(strings.really_delete + " '" + e.target.name + "' ?")) {
            document.location = basePath + "catroid/myprojects/delete?id=" + e.target.id;
          }
        });
      });
    });
  }
});
