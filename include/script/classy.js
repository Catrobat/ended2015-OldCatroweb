/**
 * Classy - classy classes for JavaScript
 * 
 * :copyright: (c) 2010 by Armin Ronacher. :license: BSD.
 * 
 * Classy is written and maintained by Armin Ronacher.
 *  - Armin Ronacher <armin.ronacher@active-4.com>
 * 
 * Implementation is heavily inspired by John Resig's simple JavaScript
 * inheritance: http://ejohn.org/blog/simple-javascript-inheritance/
 * 
 * Copyright (c) 2010 by Armin Ronacher, see AUTHORS for more details.
 * 
 * Some rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 
 * Redistributions of source code must retain the above copyright notice, this
 * list of conditions and the following disclaimer.
 * 
 * Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 * 
 * The names of the contributors may not be used to endorse or promote products
 * derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

;
(function(undefined) {
  var CLASSY_VERSION = '1.3', root = this, old_class = Class, disable_constructor = false;

  /*
   * we check if $super is in use by a class if we can. But first we have to
   * check if the JavaScript interpreter supports that. This also matches to
   * false positives later, but that does not do any harm besides slightly
   * slowing calls down.
   */
  var probe_super = (function() {
    $super();
  }).toString().indexOf('$super') > 0;
  function usesSuper(obj) {
    return !probe_super || /\B\$super\b/.test(obj.toString());
  }

  /*
   * helper function to set the attribute of something to a value or removes it
   * if the value is undefined.
   */
  function setOrUnset(obj, key, value) {
    if (value === undefined)
      delete obj[key];
    else
      obj[key] = value;
  }

  /* gets the own property of an object */
  function getOwnProperty(obj, name) {
    return Object.prototype.hasOwnProperty.call(obj, name) ? obj[name]
        : undefined;
  }

  /* instanciate a class without calling the constructor */
  function cheapNew(cls) {
    disable_constructor = true;
    var rv = new cls;
    disable_constructor = false;
    return rv;
  }

  /* the base class we export */
  var Class = function() {
  };

  /*
   * restore the global Class name and pass it to a function. This allows
   * different versions of the classy library to be used side by side and in
   * combination with other libraries.
   */
  Class.$noConflict = function() {
    try {
      setOrUnset(root, 'Class', old_class);
    } catch (e) {
      // fix for IE that does not support delete on window
      root.Class = old_class;
    }
    return Class;
  };

  /* what version of classy are we using? */
  Class.$classyVersion = CLASSY_VERSION;

  /* extend functionality */
  Class.$extend = function(properties) {
    var super_prototype = this.prototype;

    /*
     * disable constructors and instanciate prototype. Because the prototype
     * can't raise an exception when created, we are safe without a try/finally
     * here.
     */
    var prototype = cheapNew(this);

    /* copy all properties of the includes over if there are any */
    if (properties.__include__)
      for ( var i = 0, n = properties.__include__.length; i != n; ++i) {
        var mixin = properties.__include__[i];
        for ( var name in mixin) {
          var value = getOwnProperty(mixin, name);
          if (value !== undefined)
            prototype[name] = mixin[name];
        }
      }

    /* copy all properties over to the new prototype */
    for ( var name in properties) {
      var value = getOwnProperty(properties, name);
      if (name === '__include__' || name === '__classvars__'
          || value === undefined)
        continue;

      prototype[name] = typeof value === 'function' && usesSuper(value) ? (function(
          meth, name) {
        return function() {
          var old_super = getOwnProperty(this, '$super');
          this.$super = super_prototype[name];
          try {
            return meth.apply(this, arguments);
          } finally {
            setOrUnset(this, '$super', old_super);
          }
        };
      })(value, name)
          : value
    }

    /* dummy constructor */
    var rv = function() {
      if (disable_constructor)
        return;
      var proper_this = root === this ? cheapNew(arguments.callee) : this;
      if (proper_this.__init__)
        proper_this.__init__.apply(proper_this, arguments);
      proper_this.$class = rv;
      return proper_this;
    }

    /* copy all class vars over of any */
    if (properties.__classvars__)
      for ( var key in properties.__classvars__) {
        var value = getOwnProperty(properties.__classvars__, key);
        if (value !== undefined)
          rv[key] = value;
      }

    /*
     * copy prototype and constructor over, reattach $extend and return the
     * class
     */
    rv.prototype = prototype;
    rv.constructor = rv;
    rv.$extend = Class.$extend;
    rv.$withData = Class.$withData;
    return rv;
  };

  /* instanciate with data functionality */
  Class.$withData = function(data) {
    var rv = cheapNew(this);
    for ( var key in data) {
      var value = getOwnProperty(data, key);
      if (value !== undefined)
        rv[key] = value;
    }
    return rv;
  };

  /* export the class */
  root.Class = Class;
})();
