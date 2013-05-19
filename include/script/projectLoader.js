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
 
var ProjectLoader = Class.$extend({
  __include__ : [__baseClassVars],
  __init__ : function(cbParams, cbOnSuccess, cbOnError) {
    this.initialized = false;
    this.cbParams = cbParams;
    this.cbOnSuccess = cbOnSuccess;
    this.cbOnError = cbOnError;    
    
    this.params = cbParams.call(this);
    this.ajaxRequestMutex = false;
    this.initialize();
  },
  
  initialize : function() {
    if(!this.initialized) {
      if(this.params.content == null) {
        this.loadPage();
      }
      this.initialized = true;
    }
  },

  tryAcquireAjaxMutex : function() {
    if(!this.ajaxRequestMutex){
      this.ajaxRequestMutex = true;
      return true;
    }
    return false;
  },

  releaseAjaxMutex : function() {
    this.ajaxRequestMutex = false;
  },
  
  loadPage : function() {
    if(this.tryAcquireAjaxMutex()){
      this.loadPageRequest();
    }
  },

  loadPageRequest : function() {
    this.params = this.cbParams.call(this);
    $(this.params.loader).show();
    $(this.params.buttons.next).hide();

    $.ajax({
      url : this.basePath + "api/projects/list.json",
      type : "GET",
      context: this,
      data : {
        offset : this.params.page.numProjectsPerPage * (this.params.page.number - 1),
        limit : this.params.page.numProjectsPerPage,
        mask : this.params.mask,
        order : this.params.sort,
        query: this.params.filter.query,
        user : this.params.filter.author
      },
      success : function(result) {
        $(this.params.loader).hide();
        $(this.params.buttons.next).show();
        this.releaseAjaxMutex();

        if(typeof result === "object") {
          this.cbOnSuccess.call(this, result);
        } else {
          this.cbOnError.call(this, "Error: unknown response type: " + typeof(error), '');
        }
      },
      error : function(result, errCode) {
        $(this.params.loader).hide();
        $(this.params.buttons.next).show();
        this.releaseAjaxMutex();        
        this.cbOnError.call(this, result, errCode);
      }
    });
  }
});
