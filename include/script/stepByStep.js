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

function changeContainer(slide) {
  
  currentSlide = slide;
  
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
  
  if(currentSlide >1) {  
    $(".stepByStepMainContent"+(currentSlide-1)).css("display", "block");
    $(".navigation"+(currentSlide-1)).css("color", "#FFFFFF");
    
    $(".stepByStepMainContent"+currentSlide).css("display", "none");
    $(".navigation"+currentSlide).css("color", "#05222a");
    
    currentSlide -= 1;
  }
}

function incrementContainer() {
  
  if(currentSlide < 11) {
    $(".stepByStepMainContent"+(currentSlide+1)).css("display", "block");
    $(".navigation"+(currentSlide+1)).css("color", "#FFFFFF");
    
    $(".stepByStepMainContent"+currentSlide).css("display", "none");
    $(".navigation"+currentSlide).css("color", "#05222a");
    
    currentSlide += 1;
  }
}
