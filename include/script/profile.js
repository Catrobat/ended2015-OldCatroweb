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

var Profile = Class.$extend( {
  __include__ : [__baseClassVars],
  __init__ : function() {
    var self = this;
    $("#profileFormAnswer").toggle(false);
    $("#errorMsg").toggle(false);
    $("#okMsg").toggle(false);
	
    $("#profileAddNewEmailField").toggle(true);
    $("#profileRemoveNewEmailField").toggle(false);
    $("#profileAddEmailButton").toggle(false);

    var email_count = $('#profileEmailTextDiv').children('a').size();
    var x=0;
    for(x; x<email_count; x++) {
      // how can I set the click event to the ID as emailaddress
      // ID: case-sensitiv, must begin with a letter A-Z or a-z, can be followed by: letters (A-Za-z), digits (0-9), hyphens ("-"), underscores ("_"), colons (":"), and periods (".")
      $('#'+x).click(function(event) {
        self.profileChangeEmailOpen(event.target.id);
      });
    }
    $("#profileChangeEmailClose").click($.proxy(this.profileChangeEmailClose, this));
    
    $("#profilePasswordDiv").toggle(true);
    $("#profilePasswordDivOpened").toggle(false);
    $("#profileEmailTextDiv").toggle(true);
    $("#profileEmailChangeDiv").toggle(false);
    $("#profileCountryDiv").toggle(false);
    $("#profileCountryTextDiv").toggle(true);
    $("#countryLinkName").toggle(true);
    $("#countryLinkNameDyn").toggle(false);
    
    $("#profileAddNewEmailField").click($.proxy(this.addEmailInputField, this));
    $("#profileRemoveNewEmailField").click($.proxy(this.removeEmailInputField, this));
    $("#profilePasswordSubmit").click($.proxy(this.profilePasswordSubmit, this));
    $("#profileEmailSubmit").click($.proxy(this.profileEmailSubmit, this)); 
    $("#profileCountrySubmit").click($.proxy(this.profileCountrySubmit, this));

    $("#profileChangePasswordOpen").click($.proxy(this.profileChangePasswordOpen, this));
    $("#profileChangePasswordClose").click($.proxy(this.profileChangePasswordClose, this));

    $("#profileChangeCountryOpen").click($.proxy(this.profileChangeCountryOpen, this));
    $("#profileChangeCountryClose").click($.proxy(this.profileChangeCountryClose, this));
    
    $("#profileCountry").keypress($.proxy(this.profileCountryCatchKeypress, this));
    $("#profileEmail").keypress($.proxy(this.profileEmailCatchKeypress, this));
    $("#profileOldPassword").keypress($.proxy(this.profilePasswordCatchKeypress, this));
    $("#profileNewPassword").keypress($.proxy(this.profilePasswordCatchKeypress, this));
  },
  
  profilePasswordSubmit : function() {
    var self = this;
    $.ajax({
      type: "POST",
      url: self.basePath + 'catroid/profile/profilePasswordRequestQuery.json',
      data: "profileOldPassword="+$("#profileOldPassword").val()+"&profileNewPassword="+$("#profileNewPassword").val(),
      timeout: (this.ajaxTimeout),

      success: function(result){
        if(result.statusCode == 200) {
          $("#profileFormAnswer").toggle(true);
          $("#errorMsg").toggle(false);
          $("#okMsg").toggle(true);
          $("#okMsg").html(result.answer_ok);
          $("#profileOldPassword").val("");
          $("#profileNewPassword").val("");
          $("#profilePasswordDiv").toggle(false);
        }
        else {
          $("#profileFormAnswer").toggle(true);
          $("#okMsg").toggle(false);
          $("#errorMsg").toggle(true);
          $("#errorMsg").html(result.answer);
        }
      },
      error : function(result, errCode) {
        if(errCode == "timeout") {
          window.location.reload(false);   
        }
      }
    });
  },
  
  profileEmailSubmit : function() {
    var self = this;
    $.ajax({
      type: "POST",
      url: self.basePath + 'catroid/profile/profileEmailRequestQuery.json',
      data: "profileEmail="+$("#profileEmail").val(),
        
      success: function(result){
        if(result.statusCode == 200) {
          window.location.reload(false);
        }
        else {
          $("#profileFormAnswer").toggle(true);
          $("#okMsg").toggle(false);
          $("#errorMsg").toggle(true);
          $("#errorMsg").html(result.answer);
        }
      },
      error : function(result, errCode) {
        if(errCode == "timeout") {
          window.location.reload(false);   
        }
      }
    });
  },
  
  addEmailInputField : function() { 
    var self = this;
    $.ajax({
      type: "POST",
      url: self.basePath + 'catroid/profile/profileAddEmailTextFieldRequestQuery.json',
      data: "userName="+$("#profileUser").val(),
        
      success: function(result){
        if(result.statusCode == 200) {
          $("#profileAddEmailButton").toggle(true);
          
          var old = document.getElementById('emailTextFields').innerHTML; 
          var row_one = "<a href='javascript:;' class='profileText' id='profileChangeEmailClose'>" + result.addEmailLanguageString + "</a><br>";
          var row_two = "<input type='email' id='profileEmail' name='profileEmail' value='' required='required' placeholder='" + result.addEmailPlaceholderLanguageString + "' ><br>"; 
          document.getElementById('emailTextFields').innerHTML = old + row_one + row_two; 
          $("#profileAddNewEmailField").attr("disabled", "disabled");
          $("#profileRemoveNewEmailField").toggle(true);
        }
        else {
          $("#profileFormAnswer").toggle(true);
          $("#errorMsg").toggle(true);
          $("#errorMsg").html(result.answer);
          $("#okMsg").toggle(false);
        }
      },
      error : function(result, errCode) {
        if(errCode == "timeout") {
          window.location.reload(false);   
        }
      }
    });
  },
  
  
  removeEmailInputField : function() { 
      $("#profileAddEmailButton").toggle(false);
      document.getElementById('emailTextFields').innerHTML = '<div id="emailTextFields"></div>'; 
      $("#profileAddNewEmailField").removeAttr('disabled');
      $("#profileRemoveNewEmailField").toggle(false);
  },
   
  
  profileCountrySubmit : function() {
    var self = this;
    $.ajax({
      type: "POST",
      url: self.basePath + 'catroid/profile/profileCountryRequestQuery.json',
      data: "profileCountry="+$("#profileCountry").val(),
      timeout: (this.ajaxTimeout),
      
      success: function(result){
        if(result.statusCode == 200) {
          $("#profileCountry").selectedIndex = "#profileCountry";
          $("#profileFormAnswer").toggle(false);
          $("#errorMsg").toggle(false);
          window.location.reload(false);
          $("#okMsg").toggle(true);
          $("#okMsg").html(result.answer_ok);
        }
        else {
          $("#profileFormAnswer").toggle(true);
          $("#okMsg").toggle(false);
          $("#errorMsg").toggle(true);
          $("#errorMsg").html(result.answer);
        }
      },
      error : function(result, errCode) {
        if(errCode == "timeout") {
          window.location.reload(false);
        }
      }
    });
  },
 
  profileChangePasswordOpen : function() {
    $("#profilePasswordDiv").toggle(false);
    $("#profilePasswordDivOpened").toggle(true);
    $("#profileOldPassword").focus(true);
  },
  
  profileChangePasswordClose : function() {
    $("#profileOldPassword").val("");
    $("#profileNewPassword").val("");
    $("#profilePasswordDivOpened").toggle(false);
    $("#profilePasswordDiv").toggle(true);
    $("#profileFormAnswer").toggle(false);
    $("#errorMsg").toggle(false);
    $("#okMsg").toggle(false);
    $("#errorMsg").html("");
    $("#okMsg").html("");
  },

  profileChangeEmailOpen : function(id) {
    $("#profileEmailTextDiv").toggle(false);
    $("#profileEmailChangeDiv").toggle(true);
    $("#profileEmail").val($('#profileEmailTextDiv').children('a')[id].text);
  },

  profileChangeEmailClose : function() {
    $("#profileEmailTextDiv").toggle(true);
    $("#profileEmailChangeDiv").toggle(false);
    $("#profileEmail").val("");
    $("#profileFormAnswer").toggle(false);
    $("#errorMsg").toggle(false);
    $("#okMsg").toggle(false);
    $("#errorMsg").html("");
    $("#okMsg").html("");
  },

  
  profileChangeCountryOpen : function() {
    $("#profileCountryTextDiv").toggle(false);
    $("#profileCountryDiv").toggle(true);
    $("#profileCountry").focus();
  },
  
  profileChangeCountryClose : function() {
    $("#profileCountryDiv").toggle(false);
    $("#profileCountryTextDiv").toggle(true);
  },
  
  profileCancel : function() {
    $("#errorMsg").toggle(false);
    $("#okMsg").toggle(false);
    $("#errorMsg").html("");
    $("#okMsg").html("");
    
    $("#profileFormAnswer").toggle(false);
    $("#profileCancelDiv").toggle();
    $("#profilePassword").val("");
    $("#profileEmail").val("");
    $("#profileOldPassword").val("");
    $("#profileNewPassword").val("");
    $("#profilePasswordDiv").toggle(false);
    $("#profileEmailTextDiv").toggle(true);
    $("#profileEmailChangeDiv").toggle(false);
    $("#profileCountryDiv").toggle(false);
    $("#profileCountryTextDiv").toggle(true);
  },
  
  profilePasswordCatchKeypress : function(event) {
    if(event.which == '13') {
      event.preventDefault();
      this.profilePasswordSubmit();
    }
  },
  
  profileEmailCatchKeypress : function(event) {
    if(event.which == '13') {
      event.preventDefault();
      this.profileEmailSubmit();
    }
  },
  
  profileCountryCatchKeypress : function(event) {
    if(event.which == '13') {
      event.preventDefault();
      this.profileCountrySubmit();
    }
  }

});
