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

var currentSlide = 1;

$(".navigation"+1).css("color", "#FFFFFF");


if($(window).width() <= 840) {
  for(i=4;i<12;i++) {
    if(i === 1 || i === 11)
      continue;
    $(".navigation"+i).hide();
  }
}

//$(window).resize(function() {
//  if($(window).width() > 840) {
//    for(i=1;i<=12;i++) {
//      $(".navigation"+i).show();
//    }
//  }
//  else {
//    for(i=4;i<=12;i++) {
//      $(".navigation"+i).hide();
//    }
//  }
//});


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
   
    hideNavigations(1);

    $(".stepByStepMainContent"+(currentSlide-1)).css("display", "block");
    $(".navigation"+(currentSlide-1)).css("color", "#FFFFFF");
    
    $(".stepByStepMainContent"+currentSlide).css("display", "none");
    $(".navigation"+currentSlide).css("color", "#05222a");
    
    currentSlide -= 1;
  }
}

function incrementContainer() {  
  
  if(currentSlide < 11) {
    
    hideNavigations(2);    
    
    $(".stepByStepMainContent"+(currentSlide+1)).css("display", "block");
    $(".navigation"+(currentSlide+1)).css("color", "#FFFFFF");
    
    $(".stepByStepMainContent"+currentSlide).css("display", "none");
    $(".navigation"+currentSlide).css("color", "#05222a");
    
    currentSlide += 1;
  }
}

function hideNavigations(type) {
  
  for(i=2;i<=11;i++) {
    
    if(i === 11)
      continue;
    
    if(type === 1) {
      if((currentSlide <= 2 && i<=3))
        continue;      
        if(i === currentSlide-1-1 || i === currentSlide-1 || i === currentSlide+1-1)
          $(".navigation"+i).show();
        else
          $(".navigation"+i).hide();
    }
    else if(type === 2) {
        if(currentSlide >= 10 && i>=9)
          continue;
        if(i === currentSlide+1-1 || i === currentSlide+1 || i === currentSlide+1+1)
          $(".navigation"+i).show();
        else
          $(".navigation"+i).hide();

    }
    else if(type === 3){
      
      if(currentSlide === 11) {
        if(i === 11 || i === 10 || i === 9)
          $(".navigation"+i).show();
        else 
          $(".navigation"+i).hide();
        
        if(i === 9)
          $(".navigation"+i).before('<div class="blub" style="float: left;">...</div>');

      }
      else if(currentSlide === 1) {
        if(i === 1 || i === 2 || i === 3)
          $(".navigation"+i).show();
        else 
          $(".navigation"+i).hide();
        
        if(i === 3)
          $(".navigation"+i).after('<div class="blub" style="float: left;">...</div>');
      }
      else {
        if(currentSlide-1 === i || currentSlide === i || currentSlide+1 === i)
          $(".navigation"+i).show();
        else
          $(".navigation"+i).hide();
      }
    }
  }
}
