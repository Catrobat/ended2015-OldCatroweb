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
    this.year = 0;
    this.month = 0;
    
    this.emailDeleteAlertTitle = languageStringsObject.emailDeleteAlertTitle;
    this.addNewEmailLanguageString = languageStringsObject.addNewEmailLanguageString;
    this.addNewEmailPlaceholderLanguageString = languageStringsObject.addNewEmailPlaceholderLanguageString;
    this.addNewEmailButtonLanguageString = languageStringsObject.addNewEmailButtonLanguageString;
    this.changeEmailLanguageString = languageStringsObject.changeEmailLanguageString;
    this.changeEmailDeleteButtonLanguageString = languageStringsObject.changeEmailDeleteButtonLanguageString;
    this.changeEmailSaveChangesLanguageString = languageStringsObject.changeEmailSaveChangesLanguageString;
    this.emailAddressStringChangedLanguageString = languageStringsObject.emailAddressStringChangedLanguageString;
    this.birthdayChangeLanguageString = languageStringsObject.birthdayChangeLanguageString;
        
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
    if(document.getElementById('profileEmptyCityDiv')) {
      $("#profileEmptyCityDiv").toggle(true);
      $("#profileEmptyCityDivOpened").toggle(false);
    }
    if(document.getElementById('profileCityDiv')) {
      $("#profileCityDiv").toggle(true);
      $("#profileCityDivOpened").toggle(false);
    }
    $("#profileBirthDivOpened").toggle(false);
    $("#profileBirthDiv").toggle(true);
    $("#profileGenderDivOpened").toggle(false);
    $("#profileGenderDiv").toggle(true);
    
    $("#countryLinkName").toggle(true);
    $("#countryLinkNameDyn").toggle(false);
    
    $("#buttonProfileOpenAddNewEmailField").click($.proxy(this.profileOpenAddEmailInputField, this));
    $("#buttonProfileCloseAddNewEmailField").click($.proxy(this.profileAddEmailInputFieldClose, this));
    $("#buttonProfileSaveNewEmailSubmit").click($.proxy(this.profileAddEmailRequestSubmit, this));
    
    $("#profilePasswordSubmit").click($.proxy(this.profilePasswordRequestSubmit, this));
    $("#profileCountrySubmit").click($.proxy(this.profileCountryRequestSubmit, this));
    $("#profileCitySubmit").click($.proxy(this.profileCountryRequestSubmit, this));
    $("#profileBirthSubmit").click($.proxy(this.profileBirthRequestSubmit, this));
    $("#profileGenderSubmit").click($.proxy(this.profileGenderRequestSubmit, this));
        
    $("#profileChangePasswordOpen").click($.proxy(this.profileChangePasswordOpen, this));
    $("#profileChangePasswordClose").click($.proxy(this.profileChangePasswordClose, this));

    $("#profileChangeCountryOpen").click($.proxy(this.profileChangeCountryOpen, this));
    $("#profileChangeCountryClose").click($.proxy(this.profileChangeCountryClose, this));

    $("#profileChangeCityOpen").click($.proxy(this.profileChangeCityOpen, this));
    $("#profileChangeCityClose").click($.proxy(this.profileChangeCityClose, this));
    
    $("#profileChangeBirthOpen").click($.proxy(this.profileChangeBirthOpen, this));
    $("#profileChangeBirthClose").click($.proxy(this.profileChangeBirthClose, this));
    
    $("#profileChangeGenderOpen").click($.proxy(this.profileChangeGenderOpen, this));
    $("#profileChangeGenderClose").click($.proxy(this.profileChangeGenderClose, this));
    
    //$("#profileCountry").keypress($.proxy(this.profileCountryCatchKeypress, this));
    $("#profileCity").keypress($.proxy(this.profileCityCatchKeypress, this));
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
    else {
      jQuery.proxy(this.profileChangeEmailInputFieldClose(), this);
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


  profileCityRequestSubmit : function() {
    $("#buttonProfileOpenAddNewEmailField").attr("disabled", "true");
    $("#profileCity").attr("disabled", "true");
    $("#profileCitySubmit").attr("disabled", "true");
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/profile/profileCityRequestQuery.json',
      data : ({
        profileCity : $("#profileCity").val()
      }),
      timeout : (this.ajaxTimeout),
      success : jQuery.proxy(this.profileCityRequestSuccess, this),
      error : jQuery.proxy(this.profileCityRequestError, this)
    });
  },
    
  profileCityRequestSuccess : function(result) {
    if(result.statusCode == 200) {
      $("#profileCity").removeAttr("disabled");
      $("#profileCitySubmit").removeAttr("disabled");
      window.location.reload(false);
    }
    else {
      $("#profileCity").removeAttr("disabled");
      $("#profileCitySubmit").removeAttr("disabled");
      $("#profileFormAnswer").toggle(true);
      $("#okMsg").toggle(false);
      $("#errorMsg").toggle(true);
      $("#errorMsg").html(result.answer);
    }
  },
  
  profileCityRequestError : function() {
    if(errCode == "timeout") {
      window.location.reload(false);
    }
  },
  
  
  profileBirthRequestSubmit : function() {
    if($("#profileMonth").val() && $("#profileYear").val() || !($("#profileMonth").val()) && !($("#profileYear").val()) ) {
      $("#buttonProfileOpenAddNewEmailField").attr("disabled", "true");
      $("#profileMonth").attr("disabled", "true");
      $("#profileYear").attr("disabled", "true");
      $("#profileBirthSubmit").attr("disabled", "true");
      $.ajax({
        type: "POST",
        url: this.basePath + 'catroid/profile/profileBirthRequestQuery.json',
        data : ({
          profileMonth : $("#profileMonth").val(),
          profileYear : $("#profileYear").val()
        }),
        timeout : (this.ajaxTimeout),
        success : jQuery.proxy(this.profileBirthRequestSuccess, this),
        error : jQuery.proxy(this.profileBirthRequestError, this)
      });
    }
    else {
      $("#profileFormAnswer").toggle(true);
      $("#errorMsg").toggle(true);
      $("#errorMsg").html(this.birthdayChangeLanguageString);
    }
  },
  
  profileBirthRequestSuccess : function(result) {
    if(result.statusCode == 200) {
      $("#buttonProfileOpenAddNewEmailField").removeAttr("disabled");
      $("#profileMonth").removeAttr("disabled");
      $("#profileYear").removeAttr("disabled");
      $("#profileBirthSubmit").removeAttr("disabled");
      window.location.reload(false);
    }
    else {
      $("#buttonProfileOpenAddNewEmailField").removeAttr("disabled");
      $("#profileMonth").removeAttr("disabled");
      $("#profileYear").removeAttr("disabled");
      $("#profileBirthSubmit").removeAttr("disabled");
      $("#profileFormAnswer").toggle(true);
      $("#okMsg").toggle(false);
      $("#errorMsg").toggle(true);
      $("#errorMsg").html(result.answer);
    }
  },
  
  profileBirthRequestError : function() {
    if(errCode == "timeout") {
      window.location.reload(false);
    }
  },
  
  profileGenderRequestSubmit : function() {
    $("#buttonProfileOpenAddNewEmailField").attr("disabled", "true");
    $("#profileGender").attr("disabled", "true");
    $("#profileGenderSubmit").attr("disabled", "true");
    $.ajax({
      type: "POST",
      url: this.basePath + 'catroid/profile/profileGenderRequestQuery.json',
      data : ({
        profileGender : $("#profileGender").val(),
      }),
      timeout : (this.ajaxTimeout),
      success : jQuery.proxy(this.profileGenderRequestSuccess, this),
      error : jQuery.proxy(this.profileGenderRequestError, this)
    });
  },
  
  profileGenderRequestSuccess : function(result) {
    if(result.statusCode == 200) {
      $("#buttonProfileOpenAddNewEmailField").removeAttr("disabled");
      $("#profileGender").removeAttr("disabled");
      $("#profileGenderSubmit").removeAttr("disabled");
      window.location.reload(false);
    }
    else {
      $("#buttonProfileOpenAddNewEmailField").attr("disabled", "false");
      $("#profileGender").removeAttr("disabled");
      $("#profileGenderSubmit").removeAttr("disabled");
      $("#profileFormAnswer").toggle(true);
      $("#okMsg").toggle(false);
      $("#errorMsg").toggle(true);
      $("#errorMsg").html(result.answer);
    }
  },
  
  profileGenderRequestError : function() {
    if(errCode == "timeout") {
      window.location.reload(false);
    }
  },
  
  profileChangePasswordOpen : function() {
    $("#profilePasswordDiv").toggle(false);
    $("#profilePasswordDivOpened").toggle(true);
    
    jQuery.proxy(this.profileAddEmailInputFieldClose(), this);
    jQuery.proxy(this.profileChangeEmailInputFieldClose(), this);
    jQuery.proxy(this.profileChangeCountryClose(), this);
    jQuery.proxy(this.profileChangeCityClose(), this);
    jQuery.proxy(this.profileChangeBirthClose(), this);
    jQuery.proxy(this.profileChangeGenderClose(), this);
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


  profileChangeCountryOpen : function() {
    $("#profileCountryTextDiv").toggle(false);
    $("#profileCountryDiv").toggle(true);
    
    jQuery.proxy(this.profileAddEmailInputFieldClose(), this);
    jQuery.proxy(this.profileChangeEmailInputFieldClose(), this);
    jQuery.proxy(this.profileChangePasswordClose(), this);
    jQuery.proxy(this.profileChangeCityClose(), this);
    jQuery.proxy(this.profileChangeBirthClose(), this);
    jQuery.proxy(this.profileChangeGenderClose(), this);
  },
  
  profileChangeCountryClose : function() {
    $("#profileCountryDiv").toggle(false);
    $("#profileCountryTextDiv").toggle(true);
  },
  
  
  profileChangeCityOpen : function() {
    if(document.getElementById('profileEmptyCityDiv')) {
      $("#profileEmptyCityDiv").toggle(false);
      $("#profileEmptyCityDivOpened").toggle(true);
    }
    if(document.getElementById('profileCityDiv')) {
      $("#profileCityDiv").toggle(false);
      $("#profileCityDivOpened").toggle(true);
      
      var city = $("#profileCityDiv : first-child").text();
      $("#profileCity").val(city);
    }
    
    jQuery.proxy(this.profileAddEmailInputFieldClose(), this);
    jQuery.proxy(this.profileChangeEmailInputFieldClose(), this);
    jQuery.proxy(this.profileChangePasswordClose(), this);
    jQuery.proxy(this.profileChangeCountryClose(), this);
    jQuery.proxy(this.profileChangeBirthClose(), this);
    jQuery.proxy(this.profileChangeGenderClose(), this);
  },
  
  profileChangeCityClose : function() {
    $("#profileCity").val("");
    if(document.getElementById('profileEmptyCityDiv')) {
      $("#profileEmptyCityDiv").toggle(true);
      $("#profileEmptyCityDivOpened").toggle(false);
    }
    if(document.getElementById('profileCityDiv')) {
      $("#profileCityDiv").toggle(true);
      $("#profileCityDivOpened").toggle(false);
    }
  },
  
  
  profileChangeBirthOpen : function() {
    $("#profileBirthDiv").toggle(false);
    $("#profileBirthDivOpened").toggle(true);
    this.year = $("#profileYear").val();
    this.month = $("#profileMonth").val();
        
    jQuery.proxy(this.profileAddEmailInputFieldClose(), this);
    jQuery.proxy(this.profileChangeEmailInputFieldClose(), this);
    jQuery.proxy(this.profileChangePasswordClose(), this);
    jQuery.proxy(this.profileChangeCountryClose(), this);
    jQuery.proxy(this.profileChangeCityClose(), this);
    jQuery.proxy(this.profileChangeGenderClose(), this);
  },
  
  profileChangeBirthClose : function() {
    if($("#profileYear").val()) {
      $("#profileYear").val(this.year);
    }
    if($("#profileMonth").val()) {
      $("#profileMonth").val(this.month);
    }

    $("#profileBirthDivOpened").toggle(false);
    $("#profileBirthDiv").toggle(true);
  },
  
  
  profileChangeGenderOpen : function() {
    $("#profileGenderDiv").toggle(false);
    $("#profileGenderDivOpened").toggle(true);
    
    jQuery.proxy(this.profileAddEmailInputFieldClose(), this);
    jQuery.proxy(this.profileChangeEmailInputFieldClose(), this);
    jQuery.proxy(this.profileChangePasswordClose(), this);
    jQuery.proxy(this.profileChangeCountryClose(), this);
    jQuery.proxy(this.profileChangeCityClose(), this);
    jQuery.proxy(this.profileChangeBirthClose(), this);
  },
  
  profileChangeGenderClose : function() {
    $("#profileGenderDivOpened").toggle(false);
    $("#profileGenderDiv").toggle(true);
  },

  
  profileOpenAddEmailInputField : function() { 
    $("#emailTextFields").toggle(true);
    $("#buttonProfileOpenAddNewEmailField").attr("disabled", "true");
    $("#buttonProfileOpenAddNewEmailFieldDiv").toggle(false);
    $("#buttonProfileSaveNewEmailSubmit").toggle(true);

    var div = document.createElement('div');
    div.setAttribute('id', 'emailTextFieldsDiv');
    var row_zero = '<br>';
    var row_one = "<a href='javascript:;' class='profileText' id='profileAddNewEmailInputField'>" + this.addNewEmailLanguageString + "</a><br>";
    var row_two = "<input type='email' id='profileNewEmail' name='profileNewEmail' value='' required='required' placeholder='" + this.addNewEmailPlaceholderLanguageString + "' ><br>";
    div.innerHTML = row_zero + row_one + row_two; 
    document.getElementById('emailTextFields').appendChild(div);
    
    $("#buttonProfileCloseAddNewEmailField").toggle(true);
    $("#profileAddNewEmailInputField").click($.proxy(this.profileAddEmailInputFieldClose, this));
    
    jQuery.proxy(this.profileChangeEmailInputFieldClose(), this);
    jQuery.proxy(this.profileChangePasswordClose(), this);
    jQuery.proxy(this.profileChangeCountryClose(), this);
    jQuery.proxy(this.profileChangeCityClose(), this);
    jQuery.proxy(this.profileChangeBirthClose(), this);
    jQuery.proxy(this.profileChangeGenderClose(), this);
    
  },
    

  profileAddEmailInputFieldClose : function() {
    if(document.getElementById('emailTextFieldsDiv')) {
      $("#buttonProfileSaveNewEmailSubmit").toggle(false);
      $("#buttonProfileOpenAddNewEmailFieldDiv").toggle(true);
      $("#buttonProfileOpenAddNewEmailField").removeAttr('disabled');
      
      var addEmailDiv = document.getElementById('emailTextFields');
      var addEmailTextFieldDiv = document.getElementById('emailTextFieldsDiv');
      if(addEmailTextFieldDiv) {
        addEmailDiv.removeChild(addEmailTextFieldDiv);
      }      
      $("#buttonProfileCloseAddNewEmailField").toggle(false);
    }
  },  
    
  
  profileOpenChangeEmailInputField : function(id) {
    if(document.getElementById('changeEmailTextFieldDiv')) {
      jQuery.proxy(this.profileChangeEmailInputFieldClose(), this);
    }
    jQuery.proxy(this.profileAddEmailInputFieldClose(), this);
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
    
    $("#profileChangeEmailClose").click($.proxy(this.profileChangeEmailInputFieldClose, this));
    $("#buttonProfileDeleteEmailAddress").click($.proxy(this.profileDeleteEmailRequestSubmit, this));
    $("#buttonProfileChangeEmailSubmit").click($.proxy(this.profileChangeEmailRequestSubmit, this));
    $("#profileEmail").keypress($.proxy(this.profileEmailCatchKeypress, this));
    
    jQuery.proxy(this.profileAddEmailInputFieldClose(), this);   
    jQuery.proxy(this.profileChangePasswordClose(), this);
    jQuery.proxy(this.profileChangeCountryClose(), this);
    jQuery.proxy(this.profileChangeCityClose(), this);
    jQuery.proxy(this.profileChangeBirthClose(), this);
    jQuery.proxy(this.profileChangeGenderClose(), this);
  },

  profileChangeEmailInputFieldClose : function() {
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
    if(changeEmailTextFieldDiv) {
      changeEmailDiv.removeChild(changeEmailTextFieldDiv);
    }
  },

  
  profileCancel : function() {
    $("#errorMsg").toggle(false);
    $("#okMsg").toggle(false);
    $("#errorMsg").html("");
    $("#okMsg").html("");
    
    jQuery.proxy(this.profileAddEmailInputFieldClose(), this);
    jQuery.proxy(this.profileChangeEmailInputFieldClose(), this);
    jQuery.proxy(this.profileChangePasswordClose(), this);
    jQuery.proxy(this.profileChangeCountryClose(), this);
    jQuery.proxy(this.profileChangeCityClose(), this);
    jQuery.proxy(this.profileChangeBirthClose(), this);
    jQuery.proxy(this.profileChangeGenderClose(), this);
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
      this.profileCountryRequestSubmit();
    }
  },
  
  profileCityCatchKeypress : function(event) {
    if(event.which == '13') {
      event.preventDefault();
      this.profileCityRequestSubmit();
    }
  }

});
