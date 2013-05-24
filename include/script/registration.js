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

var Registration = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function() {
  	$("#registrationUsername").bind('keydown', { submit: false, prev: null, next: $("#registrationPassword") }, $.proxy(this.keydownHandler, this));
  	$("#registrationPassword").bind('keydown', { submit: false, prev: $("#registrationUsername"), next: $("#registrationEmail") }, $.proxy(this.keydownHandler, this));
  	$("#registrationEmail").bind('keydown', { submit: false, prev: $("#registrationPassword"), next: $("#registrationCountry") }, $.proxy(this.keydownHandler, this));
  	$("#registrationCountry").bind('keydown', { submit: false, prev: $("#registrationEmail"), next: $("#registrationCity") }, $.proxy(this.keydownHandler, this));
  	$("#registrationCity").bind('keydown', { submit: false, prev: $("#registrationCountry"), next: $("#registrationMonth") }, $.proxy(this.keydownHandler, this));
  	$("#registrationMonth").bind('keydown', { submit: false, prev: $("#registrationCity"), next: $("#registrationYear") }, $.proxy(this.keydownHandler, this));
  	$("#registrationYear").bind('keydown', { submit: false, prev: $("#registrationMonth"), next: $("#registrationGender") }, $.proxy(this.keydownHandler, this));
  	$("#registrationGender").bind('keydown', { submit: true, prev: $("#registrationYear"), next: null }, $.proxy(this.keydownHandler, this));
  	$("#registrationSubmit").bind('keydown', { submit: true }, $.proxy(this.keydownHandler, this));
  	$("#registrationSubmit").click($.proxy(this.submit, this));
	
  },
  
  toggleProfileBox : function() {
    $("#headerProfileBox").toggle(true);
    if($("#headerLoginBox").css("display") == "block") {
      $("#loginUsername").focus();
    }
    $(document).scrollTop(0);
  },
  
  registrationSubmit : function() {
    $("#registrationErrorMsg").toggle(false);
    var url = this.basePath + 'registration/registrationRequest.json';
    $.ajax({
      type : "POST",
      url : url,
      data : ({
        registrationUsername : $("#registrationUsername").val(),
        registrationPassword : $("#registrationPassword").val(),
        registrationEmail : $("#registrationEmail").val(),
        registrationCountry : $("#registrationCountry").val(),
        registrationCity : $("#registrationCity").val(),
        registrationMonth : $("#registrationMonth").val(),
        registrationYear : $("#registrationYear").val(),
        registrationGender : $("#registrationGender").val()
      }),
      timeout : $.proxy(this.registrationError, this),
      success : $.proxy(this.registrationSuccess, this),
      error : $.proxy(this.registrationError, this)
    });
  },

  registrationSuccess : function(response) {
    if(response.statusCode == 200) {
      location.href = this.basePath + 'profile';
    } else {
      alert(response.answer);
    }
  },
  
  registrationError : function(response) {
    console.log(response);
    alert(response);
  },

  keydownHandler : function(event) {
    if(event.which == '9') {
      if(event.shiftKey && event.data.prev != null) {
        event.preventDefault();
        this.focusAndScrollIntoView(event.data.prev);
      }
      if(!event.shiftKey && event.data.next != null) {
        event.preventDefault();
        this.focusAndScrollIntoView(event.data.next);
      }
    }
    
    if(event.which == '13') {
      if(!event.data.submit) {
        if(event.data.next != null) {
          event.preventDefault();
          this.focusAndScrollIntoView(event.data.next);
        }
      } else {
        this.submit();
        event.preventDefault();
      }
    }
  },

  focusAndScrollIntoView : function(element) {
    element.focus();
    $(document).scrollTop(element.offset().top - this.scrollMargin);
  },
  
  submit : function(event) {
    document.activeElement.blur();
    this.registrationSubmit();
    event.preventDefault();
  }
});
