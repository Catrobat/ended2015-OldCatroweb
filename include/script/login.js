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

var Login = Class.$extend({
  __include__ : [__baseClassVars],
  __init__ : function() {
    this.requestUri = '';
    
    var fragments = location.search.split("?requestUri=");
    if(fragments.length > 1) {
      this.requestUri = fragments[1];
    }
    
    $("#loginSubmitButton").click($.proxy(this.loginRequest, this));
    $("#loginUsername").keypress($.proxy(this.loginCatchKeypress, this));
    $("#loginPassword").keypress($.proxy(this.loginCatchKeypress, this));
  },

  loginCatchKeypress : function(event) {
    if(event.which == '13') {
      this.loginRequest(event);
      event.preventDefault();
    }
  },

  loginRequest : function(event) {
    if($("#loginUsername").val() != "" &&  $("#loginPassword").val() != "") {
      $("#loginSubmitButton").hide();
      $("#loginError").hide();
      $("#loginLoader").css('display', 'block');
  
      $("#loginUsername").blur();
      $("#loginPassword").blur();

      $.ajax({
        type : "POST",
        url : this.basePath + 'login/loginRequest.json',
        data : ({
          loginUsername : $("#loginUsername").val(),
          loginPassword : $("#loginPassword").val()
        }),
        success : $.proxy(this.loginRequestSuccess, this),
        error : $.proxy(this.loginRequestError, this)
      });
    }
    event.preventDefault();
  },

  loginRequestSuccess : function(result) {
    $("#loginLoader").hide();
    if(result.statusCode == 200) {
      if(this.requestUri == '') {
        location.href = this.basePath + 'profile';
      } else {
        location.href = this.basePath + this.requestUri;
      }
    } else {
      $("#loginSubmitButton").show();
      $("#loginError").show().text(result.answer);
    }
  },

  loginRequestError : function(error) {
    $("#loginSubmitButton").show();
    $("#loginLoader").hide();
    $("#loginError").show().text(error);   
  }
});
