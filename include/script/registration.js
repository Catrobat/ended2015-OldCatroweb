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

var Registration = Class.$extend( {
  __init__ : function(basePath) {
    this.basePath = basePath;
	var self = this;
    $("#registrationFormDialog").toggle(true);
    $("#registrationFormAnswer").toggle(false);
   
    $("#registrationSubmit").click(
      $.proxy(this.registrationSubmit, this));
    
    $("#registrationUsername").keypress(
      $.proxy(this.registrationCatchKeypress, this));
    $("#registrationPassword").keypress(
      $.proxy(this.registrationCatchKeypress, this));
    $("#registrationEmail").keypress(
      $.proxy(this.registrationCatchKeypress, this));
    $("#registrationCountry").keypress(
      $.proxy(this.registrationCatchKeypress, this));
    $("#registrationCity").keypress(
      $.proxy(this.registrationCatchKeypress, this));
    $("#registrationMonth").keypress(
      $.proxy(this.registrationCatchKeypress, this));
    $("#registrationYear").keypress(
        $.proxy(this.registrationCatchKeypress, this));
    $("#registrationGender").keypress(
        $.proxy(this.registrationCatchKeypress, this));    
  },
  
  registrationSubmit : function() {
    // disable form fields
    $("#registrationUsername").attr("disabled", "disabled");
    $("#registrationPassword").attr("disabled", "disabled");
    $("#registrationEmail").attr("disabled", "disabled");
    $("#registrationCountry").attr("disabled", "disabled");
    $("#registrationCity").attr("disabled", "disabled");
    $("#registrationMonth").attr("disabled", "disabled");
    $("#registrationYear").attr("disabled", "disabled");
    $("#registrationGender").attr("disabled", "disabled");
    $("#registrationSubmit").attr("disabled", "disabled");

    var url = this.basePath + 'catroid/registration/registrationRequest.json';
    $.post(url, {
      registrationUsername : $("#registrationUsername").val(),
      registrationPassword : $("#registrationPassword").val(),
      registrationEmail : $("#registrationEmail").val(),
      registrationCountry : $("#registrationCountry").val(),
      registrationCity : $("#registrationCity").val(),
      registrationMonth : $("#registrationMonth").val(),
      registrationYear : $("#registrationYear").val(),
      registrationGender : $("#registrationGender").val()
    }, $.proxy(this.registrationSuccess, this), "json");
  },

  registrationSuccess : function(response) {
    $("#registrationFormAnswer").toggle(true);
	$("#errorMsg").html(response.answer);

	if(response.statusCode == 200) {
      location.href = self.basePath+'catroid/login';
    }
    $("#registrationUsername").removeAttr("disabled");
    $("#registrationPassword").removeAttr("disabled");
    $("#registrationEmail").removeAttr("disabled");
    $("#registrationCountry").removeAttr("disabled");
    $("#registrationCity").removeAttr("disabled");
    $("#registrationMonth").removeAttr("disabled");
    $("#registrationYear").removeAttr("disabled");
    $("#registrationGender").removeAttr("disabled");
    $("#registrationSubmit").removeAttr("disabled");

  },
  
  registrationCatchKeypress : function(event) {
    if(event.which == '13') {
      event.preventDefault();
      this.registrationSubmit();
    }
  }

});
