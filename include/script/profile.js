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
  __init__ : function(languageStringsObject) {
    
    var self = this;
    this.emailCount = languageStringsObject.emailCount;
    this.emailText = '';
    this.id = 0;
    
    this.emailDeleteAlertTitle = languageStringsObject.emailDeleteAlertTitle;
    this.addNewEmailLanguageString = languageStringsObject.addNewEmailLanguageString;
    this.addNewEmailPlaceholderLanguageString = languageStringsObject.addNewEmailPlaceholderLanguageString;
    this.addNewEmailButtonLanguageString = languageStringsObject.addNewEmailButtonLanguageString;
    this.changeEmailLanguageString = languageStringsObject.changeEmailLanguageString;
    this.changeEmailDeleteButtonLanguageString = languageStringsObject.changeEmailDeleteButtonLanguageString;
    this.changeEmailSaveChangesLanguageString = languageStringsObject.changeEmailSaveChangesLanguageString;
    this.emailAddressStringChangedLanguageString = languageStringsObject.emailAddressStringChangedLanguageString;
        
    $("#profileFormAnswer").toggle(true);
    $("#errorMsg").toggle(false);
    $("#okMsg").toggle(false);
	
    $("#buttonProfileOpenAddNewEmailField").toggle(true);
    $("#buttonProfileCloseAddNewEmailField").toggle(false);
    $("#buttonProfileSaveNewEmailSubmit").toggle(false);

    var email_count = $('#profileEmailTextDiv').children('div').size();
    var x=0;
    for(x; x<email_count; x++) {
      // html tag ID: case-sensitiv, must begin with a letter A-Z or a-z, can be followed by: letters (A-Za-z), digits (0-9), hyphens ("-"), underscores ("_"), colons (":"), and periods (".")
      $('#'+x).click(function(event) {
        self.profileOpenChangeEmailInputField(event.target.id);
      });
    }
    
    $("#profilePasswordDiv").toggle(true);
    $("#profilePasswordDivOpened").toggle(false);
    $("#profileEmailTextDiv").toggle(true);
    $("#profileEmailChangeDiv").toggle(false);
    $("#profileCountryDiv").toggle(false);
    $("#profileCountryTextDiv").toggle(true);
    $("#countryLinkName").toggle(true);
    $("#countryLinkNameDyn").toggle(false);
    
    $("#buttonProfileOpenAddNewEmailField").click($.proxy(this.profileOpenAddEmailInputField, this));
    $("#buttonProfileCloseAddNewEmailField").click($.proxy(this.profileCloseAddEmailInputField, this));
    $("#buttonProfileSaveNewEmailSubmit").click($.proxy(this.profileAddEmailRequestSubmit, this));
    
    $("#profilePasswordSubmit").click($.proxy(this.profilePasswordRequestSubmit, this));
    $("#profileCountrySubmit").click($.proxy(this.profileCountryRequestSubmit, this));

    $("#profileChangePasswordOpen").click($.proxy(this.profileChangePasswordOpen, this));
    $("#profileChangePasswordClose").click($.proxy(this.profileChangePasswordClose, this));

    $("#profileChangeCountryOpen").click($.proxy(this.profileChangeCountryOpen, this));
    $("#profileChangeCountryClose").click($.proxy(this.profileChangeCountryClose, this));
    
    $("#profileCountry").keypress($.proxy(this.profileCountryCatchKeypress, this));
    $("#profileEmail").keypress($.proxy(this.profileEmailCatchKeypress, this));
    $("#profileOldPassword").keypress($.proxy(this.profilePasswordCatchKeypress, this));
    $("#profileNewPassword").keypress($.proxy(this.profilePasswordCatchKeypress, this));
    
  },
  
  
  profilePasswordRequestSubmit : function() {
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/profile/profilePasswordRequestQuery.json',
      data : ({
        profileOldPassword : $("#profileOldPassword").val(),
        profileNewPassword : $("#profileNewPassword").val()
      }),
      timeout : (this.ajaxTimeout),
      success : jQuery.proxy(this.profilePasswordRequestSubmitSuccess, this),
      error : jQuery.proxy(this.profilePasswordRequestSubmitError, this)
    });
  },
    
  profilePasswordRequestSubmitSuccess : function(result) {
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
  
  profilePasswordRequestSubmitError : function() {
    if(errCode == "timeout") {
      window.location.reload(false);   
    }
  },

  
  profileDeleteEmailRequestSubmit : function() {
    if ( $("#profileEmail").val() == this.emailText ) {
      if(confirm(this.emailDeleteAlertTitle)) {
        $.ajax({
          type: "POST",
          url: this.basePath + 'catroid/profile/profileEmailRequestQuery.json',
          data : ({
            requestType : 'delete',
            profileEmail : $("#profileEmail").val()
          }),
          timeout : (this.ajaxTimeout),
          success : jQuery.proxy(this.profileDeleteEmailRequestSubmitSuccess, this),
          error : jQuery.proxy(this.profileDeleteEmailRequestSubmitError, this)
        });
      }
      else {
        jQuery.proxy(this.profileDeleteEmailRequestSubmitCancel, this);
      }
    }
    else {
      $("#profileFormAnswer").toggle(true);
      $("#okMsg").toggle(false);
      $("#errorMsg").toggle(true);
      $("#errorMsg").html(this.emailAddressStringChangedLanguageString);
    }
  },

  profileDeleteEmailRequestSubmitSuccess : function(result) {
    if(result.statusCode == 200) {
      window.location.reload(false);
//      $("#profileFormAnswer").toggle(true);
//      $("#okMsg").toggle(true);
//      $("#okMsg").html(result.answer_ok);
    }
    else if(result.statusCode == 500) {
      //alert('failed 500');
      $("#profileFormAnswer").toggle(true);
      $("#okMsg").toggle(false);
      $("#errorMsg").toggle(true);
      $("#errorMsg").html(result.answer);
    }
    else {
      //alert('success ERROR');
      $("#profileFormAnswer").toggle(true);
      $("#okMsg").toggle(false);
      $("#errorMsg").toggle(true);
      $("#errorMsg").html(result.answer);
    }
  },
  
  profileDeleteEmailRequestSubmitCancel : function() {
    alert('Cancel');
  },
  
  profileDeleteEmailRequestSubmitError : function(result) {
    alert('ERROR timeout');
    $("#errorMsg").toggle(true);
    $("#errorMsg").html(result.answer);
    if(errCode == "timeout") {
      window.location.reload(false);   
    }
  },
  
  
  profileAddEmailRequestSubmit : function() {
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/profile/profileEmailRequestQuery.json',
      data : ({
        requestType : 'add',
        profileNewEmail : $("#profileNewEmail").val()
      }),
      timeout : this.ajaxTimeout,
      success : jQuery.proxy(this.profileAddEmailRequestSubmitSuccess, this),
      error : jQuery.proxy(this.profileAddEmailRequestSubmitError, this)
    });
    
  },
    
  profileAddEmailRequestSubmitSuccess : function(result) {
    if(result.statusCode == 200) {
      window.location.reload(false);
    }
    else if(result.statusCode == 500) {
      $("#profileFormAnswer").toggle(true);
      $("#okMsg").toggle(false);
      $("#errorMsg").toggle(true);
      $("#errorMsg").html(result.answer);
    }
  },
  
  profileAddEmailRequestSubmitError : function() {
    if(errCode == "timeout") {
      window.location.reload(false);   
    }
  },  
  
  
  profileChangeEmailRequestSubmit : function() {
    if ( $("#profileEmail").val() != this.emailText ) {
      $.ajax({
        type: "POST",
        url: this.basePath + 'catroid/profile/profileEmailRequestQuery.json',
        data : ({
          requestType : 'change',
          profileNewEmail : $("#profileEmail").val(),
          profileOldEmail : this.emailText,
        }),
        timeout : (this.ajaxTimeout),
        success : jQuery.proxy(this.profileChangeEmailRequestSubmitSuccess, this),
        error : jQuery.proxy(this.profileChangeEmailRequestSubmitError, this)
      });
    }
  },
    
  profileChangeEmailRequestSubmitSuccess : function(result) {
    if(result.statusCode == 200) {
      window.location.reload(false);
    }
    else if(result.statusCode == 500) {
      $("#profileFormAnswer").toggle(true);
      $("#okMsg").toggle(false);
      $("#errorMsg").toggle(true);
      $("#errorMsg").html(result.answer);
    }
    else {
      alert('success else');
      $("#profileFormAnswer").toggle(true);
      $("#okMsg").toggle(false);
      $("#errorMsg").toggle(true);
      $("#errorMsg").html(result.answer);
    }
  },
  
  profileChangeEmailRequestSubmitError : function() {
    alert('error');
    if(errCode == "timeout") {
      window.location.reload(false);   
    }
  },
  

  profileCountryRequestSubmit : function() {
    $("#buttonProfileOpenAddNewEmailField").attr("disabled", "true");
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/profile/profileCountryRequestQuery.json',
      data : ({
        profileCountry : $("#profileCountry").val()
      }),
      timeout : (this.ajaxTimeout),
      success : jQuery.proxy(this.profileCountryRequestSuccess, this),
      error : jQuery.proxy(this.profileCountryRequestError, this)
    });
  },
    
  profileCountryRequestSuccess : function(result) {
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
  
  profileCountryRequestError : function() {
    if(errCode == "timeout") {
      window.location.reload(false);
    }
  },
    
    
  profileChangePasswordOpen : function() {
    $("#profilePasswordDiv").toggle(false);
    $("#profilePasswordDivOpened").toggle(true);
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

  
  profileOpenChangeEmailInputField : function(id) {
    if(document.getElementById('changeEmailTextFieldDiv')) {
      jQuery.proxy(this.profileCloseChangeEmailInputField(), this);
    }
    jQuery.proxy(this.profileCloseAddEmailInputField(), this);
    $("#buttonProfileOpenAddNewEmailField").toggle(false);
    
    this.id = id;
    var div = document.createElement('div');
    div.setAttribute('id', 'changeEmailTextFieldDiv');
    var row_one = "<a href='javascript:;' class='profileText' id='profileChangeEmailClose'>"+this.changeEmailLanguageString+"</a><br>";
    var row_two = "<input type='email' id='profileEmail' name='profileEmail' value='' required='required' placeholder='' > <input type='button' name='buttonProfileDeleteEmailAddress' id='buttonProfileDeleteEmailAddress' value='" +this.changeEmailDeleteButtonLanguageString+ "' class='button white compact '><br>";
    var row_three = "<input type='button' name='buttonProfileChangeEmailSubmit' id='buttonProfileChangeEmailSubmit' value='"+this.changeEmailSaveChangesLanguageString+"' class='button orange compact profileSubmitButton'>";
    div.innerHTML = row_one + row_two + row_three; 
    document.getElementById('div'+this.id).appendChild(div);

    $("#"+this.id).toggle(false);
    this.emailText = $('#profileEmailTextDiv').find('a')[this.id].text;
    $("#profileEmailChangeDiv").toggle(true);
    $("#profileEmail").val(this.emailText);
    
    if(this.emailCount <= 1) {
      $("#buttonProfileDeleteEmailAddress").toggle(false);
    }
    else {
      $("#buttonProfileDeleteEmailAddress").toggle(true);
    }
    
    $("#profileChangeEmailClose").click($.proxy(this.profileCloseChangeEmailInputField, this));
    $("#buttonProfileDeleteEmailAddress").click($.proxy(this.profileDeleteEmailRequestSubmit, this));
    $("#buttonProfileChangeEmailSubmit").click($.proxy(this.profileChangeEmailRequestSubmit, this));
    $("#profileEmail").keypress($.proxy(this.profileEmailCatchKeypress, this));
  },

  profileCloseChangeEmailInputField : function() {
    $("#buttonProfileOpenAddNewEmailField").toggle(true);
    $("#profileEmail").val("");
    $("#profileFormAnswer").toggle(false);
    $("#errorMsg").toggle(false);
    $("#okMsg").toggle(false);
    $("#errorMsg").html("");
    $("#okMsg").html("");
    
    $("#"+this.id).toggle(true);
    var changeEmailDiv = document.getElementById('div'+this.id);
    var changeEmailTextFieldDiv = document.getElementById('changeEmailTextFieldDiv');
    changeEmailDiv.removeChild(changeEmailTextFieldDiv);
  },

  
  profileChangeCountryOpen : function() {
    $("#profileCountryTextDiv").toggle(false);
    $("#profileCountryDiv").toggle(true);
  },
  
  profileChangeCountryClose : function() {
    $("#profileCountryDiv").toggle(false);
    $("#profileCountryTextDiv").toggle(true);
  },
  
  
  profileChangeCityOpen : function() {
    $("#profileCityTextDiv").toggle(false);
    $("##profileCityDiv").toggle(true);
  },
  
  profileChangeCityClose : function() {
    $("#profileCityDiv").toggle(false);
    $("#profileCityTextDiv").toggle(true);
  },
  
  
  profileChangeBirthdayOpen : function() {
    $("#profileBirthdayTextDiv").toggle(false);
    $("#profileBirthdayDiv").toggle(true);
  },
  
  profileChangeBirthdayClose : function() {
    $("#profileBirthdayDiv").toggle(false);
    $("#profileBirthdayTextDiv").toggle(true);
  },
  
  
  profileChangeGenderOpen : function() {
    $("#profileGenderTextDiv").toggle(false);
    $("#profileGenderDiv").toggle(true);
  },
  
  profileChangeGenderClose : function() {
    $("#profileGenderDiv").toggle(false);
    $("#profileGenderTextDiv").toggle(true);
  },

  
  profileOpenAddEmailInputField : function() { 
    $("#emailTextFields").toggle(true);
    $("#buttonProfileOpenAddNewEmailField").attr("disabled", "true");
    $("#buttonProfileSaveNewEmailSubmit").toggle(true);

    var div = document.createElement('div');
    div.setAttribute('id', 'emailTextFieldsDiv');
    var row_one = "<a href='javascript:;' class='profileText' id='profileAddNewEmailInputField'>" + this.addNewEmailLanguageString + "</a><br>";
    var row_two = "<input type='email' id='profileNewEmail' name='profileNewEmail' value='' required='required' placeholder='" + this.addNewEmailPlaceholderLanguageString + "' ><br>";
    div.innerHTML = row_one + row_two; 
    document.getElementById('emailTextFields').appendChild(div);
    
    $("#buttonProfileOpenAddNewEmailField").toggle(false);
    $("#buttonProfileCloseAddNewEmailField").toggle(true);
    $("#profileAddNewEmailInputField").click($.proxy(this.profileCloseAddEmailInputField, this));
  },
    

  
  
  profileCloseAddEmailInputField : function() {
    if(document.getElementById('emailTextFieldsDiv')) {
      $("#buttonProfileSaveNewEmailSubmit").toggle(false);
      $("#buttonProfileOpenAddNewEmailField").toggle(true);
      $("#buttonProfileOpenAddNewEmailField").removeAttr('disabled');
      
      var addEmailDiv = document.getElementById('emailTextFields');
      var addEmailTextFieldDiv = document.getElementById('emailTextFieldsDiv');
      addEmailDiv.removeChild(addEmailTextFieldDiv);
      
      $("#buttonProfileCloseAddNewEmailField").toggle(false);
    }
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
      this.profileChangeEmailRequestSubmit();
    }
  },
  
  profileCountryCatchKeypress : function(event) {
    if(event.which == '13') {
      event.preventDefault();
      this.profileCountrySubmit();
    }
  }

});
