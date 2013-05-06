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
    setTimeout($.proxy(function() { this.initialize(this); }, this), 50);
  },
  
  initialize : function() {
    if(!this.initialized) {
      if(this.params.firstPage == null) {
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
      this.params = this.cbParams.call(this);
      this.loadPageRequest(this.params.page.number);
    }
  },

  loadPageRequest : function(pageNr) {
    var self = this;
    $.ajax({
      url : self.basePath + "api/projects/list.json",
      type : "GET",
      data : {
        offset : self.params.page.numProjectsPerPage * (pageNr - 1),
        limit : self.params.page.numProjectsPerPage,
        mask : self.params.mask,
        order : self.params.sort,
        query: self.params.filter.query,
        user : self.params.filter.author
      },
      timeout : (this.ajaxTimeout),
      success : function(result) {
        if(typeof result === "object") {
          self.cbOnSuccess.call(this, result);
          self.releaseAjaxMutex();        
        } else {
          alert("error");
        }
      },
      error : function(result, errCode) {
        if(errCode == "timeout") {
          alert('timed out');
        }
        self.cbOnError.call(this, result, errCode);
      }
    });
  }
});
