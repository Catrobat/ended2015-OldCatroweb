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

currentSlide = 1;
$(".navigation"+1).css("color", "#FFFFFF");
$(".navigation3").after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');

for(i=4;i<=20;i++) {
  if(i === 1 || i === 20)
    continue;
  $(".navigation"+i).hide();
}

function initHourOfCode() {
  var overlay = $("#imageOverlay");
  var popup = $("#imagePopup");
  var outerContainer = $("#outerContainer");
  
  overlay.width($(document).width() - 1);
  overlay.height($(document).height());
  
  console.log($('html').offset().top);
  
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
  
  if(currentSlide > 1) {  
    
    currentSlide -= 1;
    hideNavigations(1);

    $("#content"+currentSlide).removeClass("hide");
    $(".navigation"+currentSlide).css("color", "#FFFFFF");
    
    $("#content"+(currentSlide+1)).addClass("hide");
    $(".navigation"+(currentSlide+1)).css("color", "#05222a");
  }
}

function next(index) {
  
  if(currentSlide < 20) {
    
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
  
  for(var i=1;i<=20;i++) {
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
    
    if(currentSlide == 1)
      $(".detailHeaderSide .arrow.left").addClass("hide");
    else if(currentSlide == 19)
      $(".detailHeaderSide .arrow.right").removeClass("hide");
    
    check = false;
    for(i=1;i<=20;i++) {
      
      if(currentSlide < 3 && i <= 3) {
        $(".navigationPoints").remove();
        $(".navigation3").after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        check = true;
      } else if(currentSlide > 18 && i >= 18) {
        $(".navigationPoints").remove();
        $(".navigation9").before('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        check = true;
      }
      
      if(i <= 3 && currentSlide <3)
        continue;
      
      if(check === false) 
        $(".navigationPoints").remove();
      
      check = true;
        
      if(i - 1 === currentSlide || i === currentSlide || i + 1 === currentSlide) {
        if((i - 1 === currentSlide) && currentSlide > 3 && currentSlide <= 18)
          $(".navigation"+(currentSlide-1)).before('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        if((i + 1 === currentSlide) && currentSlide >= 3 && currentSlide < 18)
          $(".navigation"+(currentSlide+1)).after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
          
        $(".navigation"+i).show();
      }
      else if(i !== 1 && i !== 20)
        $(".navigation"+i).hide();
    }
  }
  else if(type === 2) {
    
    if(currentSlide == 2)
      $(".detailHeaderSide .arrow.left").removeClass("hide");
    else if(currentSlide == 20)
      $(".detailHeaderSide .arrow.right").addClass("hide");
    
    check = false;
    
    for(i=1;i<=20;i++) {
      
      if(currentSlide < 3 && i <= 3) {
        $(".navigationPoints").remove();
        $(".navigation3").after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        check = true;
      }
      else if(currentSlide > 18 && i >= 18) {
        $(".navigationPoints").remove();
        $(".navigation9").before('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        check = true;
      }
      
      if(currentSlide > 18 && i >= 18)
        continue;

      if(check === false) 
        $(".navigationPoints").remove();
      
      check = true;
      
       if(i - 1 === currentSlide || i === currentSlide || i + 1 === currentSlide) {
        if(i - 1 === currentSlide  && currentSlide > 3 && currentSlide <= 18)
          $(".navigation"+(currentSlide-1)).before('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        if(i + 1 === currentSlide && currentSlide >= 3 && currentSlide < 18) 
          $(".navigation"+(currentSlide+1)).after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        
        $(".navigation"+i).show();
      }
      else if(i !== 1 && i !== 20)
        $(".navigation"+i).hide();
    }
    
  }
  else if(type === 3) {
    
    if(currentSlide == 1) {
      $(".detailHeaderSide .arrow.left").addClass("hide");
      $(".detailHeaderSide .arrow.right").removeClass("hide");
    }
    else if(currentSlide == 20) {
      $(".detailHeaderSide .arrow.left").removeClass("hide");
      $(".detailHeaderSide .arrow.right").addClass("hide");
    }
    else {
      $(".detailHeaderSide .arrow.left").removeClass("hide");
      $(".detailHeaderSide .arrow.right").removeClass("hide");
    }
    
    check = false;
    
    if(check === false) 
      $(".navigationPoints").remove();
    
    check = true;
    
    for(i=1;i<=20;i++) {
      
      if(currentSlide < 3 && i <= 3) {
        $(".navigationPoints").remove();
        $(".navigation3").after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        check = true;
      }    
      else if(currentSlide > 18 && i >= 18) {
        $(".navigationPoints").remove();
        $(".navigation9").before('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        check = true;
      }
      else {
        if(i - 1 === currentSlide  && currentSlide > 3 && currentSlide <= 18)
          $(".navigation"+(currentSlide-1)).before('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        if(i + 1 === currentSlide && currentSlide >= 3 && currentSlide < 18) 
          $(".navigation"+(currentSlide+1)).after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
      }
      
      if(currentSlide === 20) {
        if(i === 20 || i === 19 || i === 18 || i === 1) 
          $(".navigation"+i).show();
        else 
          $(".navigation"+i).hide();
      }
      else if(currentSlide === 1) {
        if(i === 1 || i === 2 || i === 3 || i === 20) 
          $(".navigation"+i).show();
        else 
          $(".navigation"+i).hide();
      }
      else {
        if(currentSlide-1 === i || currentSlide === i || currentSlide+1 === i || i === 1 || i === 20)
          $(".navigation"+i).show();
        else
          $(".navigation"+i).hide();
      }
    }
  }
}

function showImage(path, id, selector) {
  
  var container = $("#container");
  $(container).html('<img class="img" src="'+path+'images/hourOfCode/'+id+'_'+selector+'.jpg" />');
  
  $(".img").height($(window).height() - 108);
  
  $(document).ready(function() {
    $("#container img").load(function() {
      $("#outerContainer").width($(this).width() + 8);
    });
    
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
}

