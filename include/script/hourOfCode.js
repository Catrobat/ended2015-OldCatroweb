/*
 *Catroid: An on-device visual programming system for Android devices
 *Copyright (C) 2010-2014 The Catrobat Team
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

currentSlide = 0;
$(".navigation"+0).css("color", "#FFFFFF");
$(".navigation3").after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');

for(var i=3;i<=21;i++) {
  if(i === 0 || i === 21)
    continue;
  $(".navigation"+i).hide();
}

function initHourOfCode() {
  
  var overlay = $("#imageOverlay");
  var popup = $("#imagePopup");
  var outerContainer = $("#outerContainer");
  
  overlay.width($(document).width() - 1);
  overlay.height($(document).height());
  
  popup.css("top", "50px");
  outerContainer.height($(window).height() - 100);
  
  $(overlay).click(function() {
    $( overlay ).fadeToggle( 300, function() {});
    $( popup ).fadeToggle( 300, function() {});
  });
  
  $(popup).click(function() {
    $( overlay ).fadeToggle( 300, function() {});
    $( popup ).fadeToggle( 300, function() {});
  });
}

function prev(index) {
  
  if(currentSlide > 0) {  
    
    currentSlide -= 1;
    hideNavigations(1);

    $("#content"+currentSlide).removeClass("hide");
    $(".navigation"+currentSlide).css("color", "#FFFFFF");
    
    $("#content"+(currentSlide+1)).addClass("hide");
    $(".navigation"+(currentSlide+1)).css("color", "#05222a");
  }
}

function next(index) {
  
  if(currentSlide < 21) {
    
    currentSlide += 1;
    hideNavigations(2);    
    
    $("#content"+currentSlide).removeClass("hide");
    $(".navigation"+currentSlide).css("color", "#FFFFFF");
    
    $("#content"+(currentSlide-1)).addClass("hide");
    $(".navigation"+(currentSlide-1)).css("color", "#05222a");
  }
}

function changeContainer(slide) {
  
  currentSlide = slide;
  
  hideNavigations(3);
  
  for(var i=0;i<=21;i++) {
    if(i === slide) {
      $("#content"+i).removeClass("hide");
      $(".navigation"+i).css("color", "#FFFFFF");
    }
    else {
      $("#content"+i).addClass("hide");
      $(".navigation"+i).css("color", "#05222a");
    }

  }  
}

function hideNavigations(type) {
  
  if(type === 1) {
    
    if(currentSlide == 0)
      $(".detailHeaderSide .arrow1.left").addClass("hide");
    else if(currentSlide == 20)
      $(".detailHeaderSide .arrow1.right").removeClass("hide");
    
    check = false;
    for(i=0;i<=21;i++) {
      
      if(currentSlide < 2 && i <= 2) {
        $(".navigationPoints").remove();
        $(".navigation3").after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        check = true;
      } else if(currentSlide > 19 && i >= 19) {
        $(".navigationPoints").remove();
        $(".navigation9").before('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        check = true;
      }
      
      if(i <= 2 && currentSlide <2)
        continue;
      
      if(check === false) 
        $(".navigationPoints").remove();
      
      check = true;
        
      if(i - 1 === currentSlide || i === currentSlide || i + 1 === currentSlide) {
        if((i - 1 === currentSlide) && currentSlide > 2 && currentSlide <= 19)
          $(".navigation"+(currentSlide-1)).before('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        if((i + 1 === currentSlide) && currentSlide >= 2 && currentSlide < 19)
          $(".navigation"+(currentSlide+1)).after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
          
        $(".navigation"+i).show();
      }
      else if(i !== 0 && i !== 21)
        $(".navigation"+i).hide();
    }
  }
  else if(type === 2) {
    
    if(currentSlide == 1)
      $(".detailHeaderSide .arrow1.left").removeClass("hide");
    else if(currentSlide == 21)
      $(".detailHeaderSide .arrow1.right").addClass("hide");
    
    check = false;
    
    for(i=0;i<=21;i++) {
      
      if(currentSlide < 2 && i <= 2) {
        $(".navigationPoints").remove();
        $(".navigation3").after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        check = true;
      }
      else if(currentSlide > 19 && i >= 19) {
        $(".navigationPoints").remove();
        $(".navigation9").before('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        check = true;
      }
      
      if(currentSlide > 19 && i >= 19)
        continue;

      if(check === false) 
        $(".navigationPoints").remove();
      
      check = true;
      
       if(i - 1 === currentSlide || i === currentSlide || i + 1 === currentSlide) {
        if(i - 1 === currentSlide  && currentSlide > 2 && currentSlide <= 19)
          $(".navigation"+(currentSlide-1)).before('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        if(i + 1 === currentSlide && currentSlide >= 2 && currentSlide < 19) 
          $(".navigation"+(currentSlide+1)).after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        
        $(".navigation"+i).show();
      }
      else if(i !== 0 && i !== 21)
        $(".navigation"+i).hide();
    }
    
  }
  else if(type === 3) {
    
    if(currentSlide == 0) {
      $(".detailHeaderSide .arrow1.left").addClass("hide");
      $(".detailHeaderSide .arrow1.right").removeClass("hide");
    }
    else if(currentSlide == 21) {
      $(".detailHeaderSide .arrow1.left").removeClass("hide");
      $(".detailHeaderSide .arrow1.right").addClass("hide");
    }
    else {
      $(".detailHeaderSide .arrow1.left").removeClass("hide");
      $(".detailHeaderSide .arrow1.right").removeClass("hide");
    }
    
    check = false;
    
    if(check === false) 
      $(".navigationPoints").remove();
    
    check = true;
    
    for(i=0;i<=21;i++) {
      
      if(currentSlide < 2 && i <= 2) {
        $(".navigationPoints").remove();
        $(".navigation3").after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        check = true;
      }    
      else if(currentSlide > 19 && i >= 19) {
        $(".navigationPoints").remove();
        $(".navigation9").before('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        check = true;
      }
      else {
        if(i - 1 === currentSlide  && currentSlide > 2 && currentSlide <= 19)
          $(".navigation"+(currentSlide-1)).before('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        if(i + 1 === currentSlide && currentSlide >= 2 && currentSlide < 19) 
          $(".navigation"+(currentSlide+1)).after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
      }
      
      if(currentSlide === 21) {
        if(i === 21 || i === 20 || i === 19 || i === 0) 
          $(".navigation"+i).show();
        else 
          $(".navigation"+i).hide();
      }
      else if(currentSlide === 0) {
        if(i === 0 || i === 1 || i === 2 || i === 21) 
          $(".navigation"+i).show();
        else 
          $(".navigation"+i).hide();
      }
      else {
        if(currentSlide-1 === i || currentSlide === i || currentSlide+1 === i || i === 0 || i === 21)
          $(".navigation"+i).show();
        else
          $(".navigation"+i).hide();
      }
    }
  }
}

function showImage(path, id, selector, type) {
  
  var container = $("#container");
  $(container).html('<img class="img" src="'+path+'images/hourOfCode/'+id+'_'+selector+'.jpg" />');
  
  if(type !== 1)
    $(".img").height($(window).height() - 108);
  else {

    $(".img").css("height", "auto");
  }
  $(document).ready(function() {
    
    $(document).keyup(function(e) {
      if (e.keyCode == 27) { 
        $("#imageOverlay").fadeToggle( 300, function() {});
        $("#imagePopup").fadeToggle( 300, function() {}); 
      }   // esc
    });
    
  });
  
  $("#imageOverlay").fadeToggle( 300, function() {});
  $("#imagePopup").fadeToggle( 300, function() {});
  window.scrollTo(0, 0);
  
  var overlay = $("#imageOverlay");
  
  overlay.width($(document).width() - 1);
  overlay.height($(document).height());
  
}

/*
 *  Copyright (c) 2012 Alex Pankratov. All rights reserved.
 *
 *  http://swapped.cc/gif-player
 */
 
/*
 *  This code is distributed under terms of BSD license. 
 *  You can obtain the copy of the license by visiting:
 *
 *  http://www.opensource.org/licenses/bsd-license.php
 */

function gifPlayer(cont, opts) {

  /* 
   *  parameters
   */
  var defaults = {
    autoplay: true,
    play:     function(s, m, cb) { s.hide(); m.show(); cb(); },
    stop:     function(m, s, cb) { m.hide(); s.show(); cb(); }
  };

  var opts = $.extend({}, defaults, opts);
  
  /*
   *  variables
   */
  var c = cont;
  var s = cont.find('.gif-still');
  var m = cont.find('.gif-movie');

  var state = 'e';
  var busy = false;

  var i = new Image;

  /* 
   *  functions
   */
  var setState = function(was, now) {
    c.removeClass('gif-player-' + was);
    c.addClass('gif-player-' + now);
    state = now;
  }

  var play = opts['play'];
  var stop = opts['stop'];
  var done = function() { busy = false; }

  /* 
   *  click() handler
   */
  this.act = function()
  {
    if (busy)
      return;

    switch (state)
    {

    case 'e': /* empty, ready to load */

      setState('e', 'l');
      m.load(function(){ 

        i.src = m.attr('src');
        m.unbind('load');

        if (! opts['autoplay'])
        {
          setState('l','s');
          return;
        }

        setState('l','p');
        busy = true;
        play(s, m, done);
      });
      m.attr('src', m.attr('gif'));
      break;

    case 'l': /* loading... */

      setState('l', 'e');
      m.unbind('load');
      m.attr('src', '');
      break;

    case 's': /* stopped, ready to play */

      /* this rewinds the gif, not in all browsers */
      m.attr('src', null).attr('src', i.src);

      setState('s', 'p');
      busy = true;
      play(s, m, done);
      break;
    
    case 'p': /* playing... */

      setState('p', 's');
      busy = true;
      stop(m, s, done);
      break;
    }
  }
  
  /*
   *  initialization
   */
  var that = this;

  c.find('.canvas').click( function(){ that.act(); });
  c.addClass('gif-player-e');
  
  m.hide(); s.show();
}

