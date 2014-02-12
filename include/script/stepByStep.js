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

var currentSlide = 1;

$(".navigation"+1).css("color", "#FFFFFF");
$(".navigation3").after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');

  for(i=4;i<12;i++) {
    if(i === 1 || i === 11)
      continue;
    $(".navigation"+i).hide();
  }

function changeContainer(slide) {
  
  currentSlide = slide;
  
  hideNavigations(3);
  
  for(var i=1;i<=12;i++) {
    if(i === slide) {
      $(".stepByStepMainContent"+i).css("display", "block");
      $(".navigation"+i).css("color", "#FFFFFF");
    }
    else {
      $(".stepByStepMainContent"+i).css("display", "none");
      $(".navigation"+i).css("color", "#05222a");
    }

  }  
}

function decrementContainer() {

  if(currentSlide > 1) {  
   
    currentSlide -= 1;
    hideNavigations(1);

    $(".stepByStepMainContent"+currentSlide).css("display", "block");
    $(".navigation"+currentSlide).css("color", "#FFFFFF");
    
    $(".stepByStepMainContent"+(currentSlide+1)).css("display", "none");
    $(".navigation"+(currentSlide+1)).css("color", "#05222a");
    
  }
}

function incrementContainer() {  
  
  if(currentSlide < 11) {
    
    currentSlide += 1;
    hideNavigations(2);    
    
    $(".stepByStepMainContent"+currentSlide).css("display", "block");
    $(".navigation"+currentSlide).css("color", "#FFFFFF");
    
    $(".stepByStepMainContent"+(currentSlide-1)).css("display", "none");
    $(".navigation"+(currentSlide-1)).css("color", "#05222a");
    
  }
}

function hideNavigations(type) {
  
  if(type === 1) {
    
    check = false;
    console.log("currentSlide: "+currentSlide);
    for(i=1;i<=11;i++) {
      
      if(currentSlide < 3 && i <= 3) {
        $(".navigationPoints").remove();
        $(".navigation3").after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        check = true;
      } else if(currentSlide > 9 && i >= 9) {
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
        if((i - 1 === currentSlide) && currentSlide > 3 && currentSlide <= 9)
          $(".navigation"+(currentSlide-1)).before('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        if((i + 1 === currentSlide) && currentSlide >= 3 && currentSlide < 9)
          $(".navigation"+(currentSlide+1)).after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
          
        $(".navigation"+i).show();
      }
      else if(i !== 1 && i !== 11)
        $(".navigation"+i).hide();
    }
  }
  else if(type === 2) {
    
    check = false;
    
    for(i=1;i<=11;i++) {
      
      if(currentSlide < 3 && i <= 3) {
        $(".navigationPoints").remove();
        $(".navigation3").after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        check = true;
      }
      else if(currentSlide > 9 && i >= 9) {
        $(".navigationPoints").remove();
        $(".navigation9").before('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        check = true;
      }
      
      if(currentSlide > 9 && i >= 9)
        continue;

      if(check === false) 
        $(".navigationPoints").remove();
      
      check = true;
      
       if(i - 1 === currentSlide || i === currentSlide || i + 1 === currentSlide) {
        if(i - 1 === currentSlide  && currentSlide > 3 && currentSlide <= 9)
          $(".navigation"+(currentSlide-1)).before('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        if(i + 1 === currentSlide && currentSlide >= 3 && currentSlide < 9) 
          $(".navigation"+(currentSlide+1)).after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        
        $(".navigation"+i).show();
      }
      else if(i !== 1 && i !== 11)
        $(".navigation"+i).hide();
    }
    
  }
  else if(type === 3) {
    
    check = false;
    
    if(check === false) 
      $(".navigationPoints").remove();
    
    check = true;
    
    for(i=1;i<=11;i++) {
      
      if(currentSlide < 3 && i <= 3) {
        $(".navigationPoints").remove();
        $(".navigation3").after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        check = true;
      }    
      else if(currentSlide > 9 && i >= 9) {
        $(".navigationPoints").remove();
        $(".navigation9").before('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        check = true;
      }
      else {
        if(i - 1 === currentSlide  && currentSlide > 3 && currentSlide <= 9)
          $(".navigation"+(currentSlide-1)).before('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
        if(i + 1 === currentSlide && currentSlide >= 3 && currentSlide < 9) 
          $(".navigation"+(currentSlide+1)).after('<div class="navigationPoints" style="float: left; color: #05222a;">...</div>');
      }
      
      if(currentSlide === 11) {
        if(i === 11 || i === 10 || i === 9 || i === 1) 
          $(".navigation"+i).show();
        else 
          $(".navigation"+i).hide();
      }
      else if(currentSlide === 1) {
        if(i === 1 || i === 2 || i === 3 || i === 11) 
          $(".navigation"+i).show();
        else 
          $(".navigation"+i).hide();
      }
      else {
        if(currentSlide-1 === i || currentSlide === i || currentSlide+1 === i || i === 1 || i === 11)
          $(".navigation"+i).show();
        else
          $(".navigation"+i).hide();
      }
    }
  }
  
  $('html, body').animate({ scrollTop: $('.header').offset().top }, 'medium');
  
}
