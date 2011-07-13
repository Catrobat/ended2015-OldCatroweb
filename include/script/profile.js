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
    this.emailCount = 0;
    this.emailDeleteAlertTitle = '1';
    this.emailDeleteAlertText = '';
    this.emailDeleteAlertConfirm = '';
    this.emailDeleteAlertCancel = '';
    this.emailNotDeletableText = '';
    
    $("#profileFormAnswer").toggle(true);
    $("#errorMsg").toggle(false);
    $("#okMsg").toggle(false);
	
    $("#buttonProfileAddNewEmailField").toggle(true);
    $("#buttonProfileRemoveNewEmailField").toggle(false);
    $("#profileAddEmailButton").toggle(false);

    var email_count = $('#profileEmailTextDiv').children('a').size();
    var x=0;
    for(x; x<email_count; x++) {
      // html tag ID: case-sensitiv, must begin with a letter A-Z or a-z, can be followed by: letters (A-Za-z), digits (0-9), hyphens ("-"), underscores ("_"), colons (":"), and periods (".")
      $('#'+x).click(function(event) {
        self.profileChangeEmailOpen(event.target.id);
      });
    }
    jQuery.proxy(this.profileGetEmailCount(), this);
    
    $("#profileChangeEmailClose").click($.proxy(this.profileChangeEmailClose, this));
    
    $("#profilePasswordDiv").toggle(true);
    $("#profilePasswordDivOpened").toggle(false);
    $("#profileEmailTextDiv").toggle(true);
    $("#profileEmailChangeDiv").toggle(false);
    $("#profileCountryDiv").toggle(false);
    $("#profileCountryTextDiv").toggle(true);
    $("#countryLinkName").toggle(true);
    $("#countryLinkNameDyn").toggle(false);
    
    $("#buttonProfileAddNewEmailField").click($.proxy(this.addEmailInputField, this));
    $("#buttonProfileRemoveNewEmailField").click($.proxy(this.removeEmailInputField, this));
    $("#buttonProfileDeleteEmailAddress").click($.proxy(this.profileDeleteEmailAddress, this));
    
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
  
  profileGetEmailCount : function() {
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/profile/profileGetEmailCountRequestQuery.json',
      data : ({
      }),
      timeout : (this.ajaxTimeout),
      success : jQuery.proxy(this.profileGetEmailCountSuccess, this),
      error : jQuery.proxy(this.profileGetEmailCountError, this)
    });    
  },
  
  profileGetEmailCountSuccess : function(event) {
    this.emailCount = event.emailCount;
  },
  
  profileGetEmailCountError : function() {
    if(errCode == "timeout") {
      window.location.reload(false);   
    }
  },
  
  
  profilePasswordSubmit : function() {
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/profile/profilePasswordRequestQuery.json',
      data : ({
        profileOldPassword : $("#profileOldPassword").val(),
        profileNewPassword : $("#profileNewPassword").val()
      }),
      timeout : (this.ajaxTimeout),
      success : jQuery.proxy(this.profilePasswordSubmitSuccess, this),
      error : jQuery.proxy(this.profilePasswordSubmitError, this)
    });
  },
    
  profilePasswordSubmitSuccess : function(result) {
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
  
  profilePasswordSubmitError : function() {
    if(errCode == "timeout") {
      window.location.reload(false);   
    }
  },

  
  profileDeleteEmailAddress : function() {
    if(this.emailCount <= 1) {
      alert(profileDeleteEmailAddress+'emailCount <= 1');
      // do nothing if last mail
      return;
    }
    if(confirm(this.emailDeleteAlertTitle)){
      $.ajax({
        type: "POST",
        url: this.basePath + 'catroid/profile/profileAddRemoveEmailRequestQuery.json',
        data : ({
          emailRequest : 'delete',
          profileEmailOldValue : this.emailTextValue,
          profileEmailInputId : this.emailId,
          profileEmailCount : this.emailCount
        }),
        timeout : (this.ajaxTimeout),
        success : jQuery.proxy(this.profileDeleteEmailAddressSuccess, this),
        error : jQuery.proxy(this.profileDeleteEmailAddressError, this)
      });
    }
    else {
      jQuery.proxy(this.profileDeleteEmailAddressError, this);
    }
  },

  profileDeleteEmailAddressSuccess : function() {
    alert('OK');
  },
  
  profileDeleteEmailAddressCancel : function() {
    alert('Cancel');
  },
  
  profileDeleteEmailAddressError : function() {
    if(errCode == "timeout") {
      window.location.reload(false);   
    }
  },
  
  
  profileChangeEmailSubmit : function() {
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/profile/profileEmailRequestQuery.json',
      data : ({
        profileEmailNewValue : $("#profileEmail").val(),
        profileEmailOldValue : this.emailTextValue,
        //profileEmailInputId : this.emailId
      }),
      timeout : (this.ajaxTimeout),
      success : jQuery.proxy(this.profileChangeEmailSubmitSuccess, this),
      error : jQuery.proxy(this.profileChangeEmailSubmitError, this)
    });
    
  },
    
  profileChangeEmailSubmitSuccess : function(result) {
    if(result.statusCode == 200) {
      window.location.reload(false);
//      $("#profileFormAnswer").toggle(true);
//      $("#okMsg").toggle(true);
//      $("#errorMsg").toggle(false);
//      $("#okMsg").html(result.answer_ok);
//      $("#profileEmailTextDiv").toggle(true);
//      $("#profileEmailChangeDiv").toggle(false);
//      $("#profileEmail").val("");
    }
    else if(result.statusCode == 500) {
      $("#profileFormAnswer").toggle(true);
      $("#okMsg").toggle(false);
      $("#errorMsg").toggle(true);
      $("#errorMsg").html(result.answer);
    }
  },
  
  profileChangeEmailSubmitError : function() {
    if(errCode == "timeout") {
      window.location.reload(false);   
    }
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
    document.getElementById('emailTextFields').innerHTML = '<div id="emailTextFields"></div>'; 
    this.emailTextValue = $('#profileEmailTextDiv').children('a')[id].text;
    $("#profileAddEmailButton").toggle(false);
    $("#buttonProfileAddNewEmailField").removeAttr('disabled');
    $("#buttonProfileRemoveNewEmailField").toggle(false);
    $("#profileEmailTextDiv").toggle(false);
    $("#profileEmailChangeDiv").toggle(true);
    $("#profileEmail").val(this.emailTextValue);
    
    if(this.emailCount <= 1) {
      $("#buttonProfileDeleteEmailAddress").toggle(false);
    }
    else {
      $("#buttonProfileDeleteEmailAddress").toggle(true);
    }
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
  
  
  addEmailInputField : function() { 
    $("#buttonProfileAddNewEmailField").attr("disabled", "true");
    var self = this;
    $.ajax({
      type: "POST",
      url: self.basePath + 'catroid/profile/profileAddEmailTextFieldRequestQuery.json',
      data: "userName="+$("#profileUser").val(),
        
      success: function(result){
        if(result.statusCode == 200) {
          
          $("#profileAddEmailButton").toggle(true);
          var old = document.getElementById('emailTextFields').innerHTML; 
          var row_one = "<a href='javascript:;' class='profileText' id='profileChangeEmailClose'>" + result.addNewEmailLanguageString + "</a><br>";
          var row_two = "<input type='email' id='profileEmail'"+  +" name='profileEmail' value='' required='required' placeholder='" + result.addNewEmailPlaceholderLanguageString + "' ><br>"; 
          document.getElementById('emailTextFields').innerHTML = old + row_one + row_two; 
          
          $("#buttonProfileRemoveNewEmailField").toggle(true);
        }
        else if(result.statusCode == 300) {

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
      $("#buttonProfileAddNewEmailField").removeAttr('disabled');
      $("#buttonProfileRemoveNewEmailField").toggle(false);
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
