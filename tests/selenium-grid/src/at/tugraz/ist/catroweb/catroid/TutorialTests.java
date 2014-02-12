/**
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

package at.tugraz.ist.catroweb.catroid;

import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.Select;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

@Test(groups = { "catroid", "TutorialTests" })
public class TutorialTests extends BaseTest {

  @Test(groups = { "visibility", "popupwindows" }, description = "try to navigate through stepByStep page ")
  public void stepbystep() throws Throwable {
    try {
      openLargeLocation("tutorial", false);
      ajaxWait();
      
      assertTrue(isVisible(By.xpath("//div[@class='tutorialStepByStep1']")));
      assertFalse(isVisible(By.xpath("//div[@class='tutorialStepByStep2']")));
      
      assertTrue(isVisible(By.xpath("//div[@class='tutorialDiscuss1']")));
      assertFalse(isVisible(By.xpath("//div[@class='tutorialDiscuss2']")));
      
      // test page title and header title
      assertTrue(driver().getTitle().matches("^Pocket Code Website.*"));

      assertRegExp(".*/tutorial", driver().getCurrentUrl());
      
      driver().findElement(By.xpath("//div[@class='tutorialStepByStep1']")).click();
      assertRegExp(".*/stepByStep", driver().getCurrentUrl());
      ajaxWait();
      
      assertTrue(isTextPresent("STEP-BY-STEP INTRO"));
      assertTrue(isTextPresent("1. Make a new program"));
      
      driver().findElement(By.xpath("//a[@class='stepLinks navigation2']")).click();
      ajaxWait();
      
      assertTrue(isTextPresent("STEP-BY-STEP INTRO"));
      assertTrue(isTextPresent("2. Create a new object"));
      
      assertTrue(isVisible(By.xpath("//a[@class='stepLinks navigation1']")));
      assertTrue(isVisible(By.xpath("//a[@class='stepLinks navigation2']")));
      assertTrue(isVisible(By.xpath("//a[@class='stepLinks navigation3']")));
      assertFalse(isVisible(By.xpath("//a[@class='stepLinks navigation4']")));
      assertTrue(isVisible(By.xpath("//a[@class='stepLinks navigation11']")));
      
      driver().findElement(By.xpath("//a[@class='stepLinks']")).click();
      ajaxWait();
      
      assertTrue(isTextPresent("STEP-BY-STEP INTRO"));
      assertTrue(isTextPresent("3. Start Moving"));
      
      driver().findElement(By.xpath("//a[@class='stepLinks']")).click();
      ajaxWait();
      
      assertTrue(isTextPresent("STEP-BY-STEP INTRO"));
      assertTrue(isTextPresent("4. Change Look"));
      
      assertTrue(isVisible(By.xpath("//a[@class='stepLinks navigation1']")));
      assertFalse(isVisible(By.xpath("//a[@class='stepLinks navigation2']")));
      assertTrue(isVisible(By.xpath("//a[@class='stepLinks navigation3']")));
      assertTrue(isVisible(By.xpath("//a[@class='stepLinks navigation4']")));
      assertTrue(isVisible(By.xpath("//a[@class='stepLinks navigation5']")));
      assertTrue(isVisible(By.xpath("//a[@class='stepLinks navigation11']")));
      
      driver().findElement(By.xpath("//a[@class='stepLinks navigation11']")).click();
      ajaxWait();
      
      assertTrue(isTextPresent("STEP-BY-STEP INTRO"));
      assertTrue(isTextPresent("11. Main Menu"));
      
      assertTrue(isVisible(By.xpath("//a[@class='stepLinks navigation1']")));
      assertTrue(isVisible(By.xpath("//a[@class='stepLinks navigation11']")));
      assertTrue(isVisible(By.xpath("//a[@class='stepLinks navigation10']")));
      assertTrue(isVisible(By.xpath("//a[@class='stepLinks navigation9']")));
      assertFalse(isVisible(By.xpath("//a[@class='stepLinks navigation8']")));
      
      driver().findElement(By.xpath("//a[@class='stepLinks arrow']")).click();
      ajaxWait();
      
      assertTrue(isTextPresent("STEP-BY-STEP INTRO"));
      assertTrue(isTextPresent("10. Change the background"));
      
      driver().findElement(By.xpath("//a[@class='stepLinks arrow']")).click();
      ajaxWait();
      
      assertTrue(isTextPresent("STEP-BY-STEP INTRO"));
      assertTrue(isTextPresent("9. Navigation in the app"));
      
      assertTrue(isVisible(By.xpath("//a[@class='stepLinks navigation1']")));
      assertTrue(isVisible(By.xpath("//a[@class='stepLinks navigation11']")));
      assertTrue(isVisible(By.xpath("//a[@class='stepLinks navigation10']")));
      assertTrue(isVisible(By.xpath("//a[@class='stepLinks navigation9']")));
      assertTrue(isVisible(By.xpath("//a[@class='stepLinks navigation8']")));
      assertFalse(isVisible(By.xpath("//a[@class='stepLinks navigation7']")));
      
      driver().navigate().back();
      ajaxWait();
      assertRegExp(".*/tutorial", driver().getCurrentUrl());
      
    } catch(AssertionError e) {
      captureScreen("TutorialTests.tutorial");
      throw e;
    } catch(Exception e) {
      captureScreen("TutorialTests.tutorial");
      throw e;
    }
  }
  
  @Test(groups = { "visibility", "popupwindows" }, description = "try to navigate through tutorials page ")
  public void tutorials() throws Throwable {
    try {
      openLargeLocation("tutorial", false);
      ajaxWait();
      
      assertTrue(isVisible(By.xpath("//div[@class='tutorialStepByStep1']")));
      assertFalse(isVisible(By.xpath("//div[@class='tutorialStepByStep2']")));
      
      assertTrue(isVisible(By.xpath("//div[@class='tutorialDiscuss1']")));
      assertFalse(isVisible(By.xpath("//div[@class='tutorialDiscuss2']")));
      
      // test page title and header title
      assertTrue(driver().getTitle().matches("^Pocket Code Website.*"));

      assertRegExp(".*/tutorial", driver().getCurrentUrl());

      driver().findElement(By.xpath("//div[@class='tutorialTutorials']")).click();
      assertRegExp(".*/tutorials", driver().getCurrentUrl());
      ajaxWait();
      
      assertTrue(isTextPresent("TUTORIALS"));
      assertTrue(isTextPresent("This tutorials show you how to use effective tricks in"));
      assertTrue(isTextPresent("POCKET CODE."));
      
      assertTrue(isTextPresent("Change Size"));
      assertTrue(isTextPresent("Change Look"));
      assertTrue(isTextPresent("Moving Animation"));
      assertTrue(isTextPresent("Glide"));
      assertTrue(isTextPresent("Play Sound"));
      assertTrue(isTextPresent("Speak Something"));
      assertTrue(isTextPresent("GSensor"));
      assertTrue(isTextPresent("Compass"));
      assertTrue(isTextPresent("Broadcast"));
      
      driver().findElement(By.xpath("//a[@class='tutorialCardsLinkStyle']")).click();
      ajaxWait();
      
      assertRegExp(".*/tutorialCard.?id=1", driver().getCurrentUrl());
      assertTrue(isTextPresent("CHANGE SIZE"));
      driver().navigate().back();
      ajaxWait();
      assertRegExp(".*/tutorials", driver().getCurrentUrl());
      
      
      driver().findElement(By.xpath("//*[@class='tutorialLeftContainer']/div[3]/a[1]")).click();
      assertRegExp(".*/tutorialCard.?id=2", driver().getCurrentUrl());
      assertTrue(isTextPresent("CHANGE LOOK"));
      driver().navigate().back();
      ajaxWait();
      assertRegExp(".*/tutorials", driver().getCurrentUrl());
      
      driver().findElement(By.xpath("//*[@class='tutorialLeftContainer']/div[4]/a[1]")).click();
      assertRegExp(".*/tutorialCard.?id=3", driver().getCurrentUrl());
      assertTrue(isTextPresent("MOVING ANIMATION"));
      driver().navigate().back();
      ajaxWait();
      assertRegExp(".*/tutorials", driver().getCurrentUrl());
      
      driver().findElement(By.xpath("//*[@class='tutorialLeftContainer']/div[5]/a[1]")).click();
      assertRegExp(".*/tutorialCard.?id=4", driver().getCurrentUrl());
      assertTrue(isTextPresent("GLIDE"));
      driver().navigate().back();
      ajaxWait();
      assertRegExp(".*/tutorials", driver().getCurrentUrl());
      
      driver().findElement(By.xpath("//*[@class='tutorialLeftContainer']/div[6]/a[1]")).click();
      assertRegExp(".*/tutorialCard.?id=5", driver().getCurrentUrl());
      assertTrue(isTextPresent("PLAY SOUND"));
      driver().navigate().back();
      ajaxWait();
      assertRegExp(".*/tutorials", driver().getCurrentUrl());
      
      driver().findElement(By.xpath("//*[@class='tutorialRightContainer']/div[1]/a[1]")).click();
      assertRegExp(".*/tutorialCard.?id=6", driver().getCurrentUrl());
      assertTrue(isTextPresent("SPEAK SOMETHING"));
      driver().navigate().back();
      ajaxWait();
      assertRegExp(".*/tutorials", driver().getCurrentUrl());
      
      driver().findElement(By.xpath("//*[@class='tutorialRightContainer']/div[2]/a[1]")).click();
      assertRegExp(".*/tutorialCard.?id=7", driver().getCurrentUrl());
      assertTrue(isTextPresent("GSENSOR"));
      driver().navigate().back();
      ajaxWait();
      assertRegExp(".*/tutorials", driver().getCurrentUrl());
      
      driver().findElement(By.xpath("//*[@class='tutorialRightContainer']/div[3]/a[1]")).click();
      assertRegExp(".*/tutorialCard.?id=8", driver().getCurrentUrl());
      assertTrue(isTextPresent("COMPASS"));
      driver().navigate().back();
      ajaxWait();
      assertRegExp(".*/tutorials", driver().getCurrentUrl());
      
      driver().findElement(By.xpath("//*[@class='tutorialRightContainer']/div[4]/a[1]")).click();
      assertRegExp(".*/tutorialCard.?id=9", driver().getCurrentUrl());
      assertTrue(isTextPresent("BROADCAST"));
      driver().navigate().back();
      ajaxWait();
      assertRegExp(".*/tutorials", driver().getCurrentUrl());
      
      driver().navigate().back();
      ajaxWait();
      assertRegExp(".*/tutorial", driver().getCurrentUrl());
      
    } catch(AssertionError e) {
      captureScreen("TutorialTests.tutorial");
      throw e;
    } catch(Exception e) {
      captureScreen("TutorialTests.tutorial");
      throw e;
    }
  }
  
  @Test(groups = { "visibility", "popupwindows" }, description = "try to navigate through starters page ")
  public void starter() throws Throwable {
    try {
      openLargeLocation("tutorial", false);
      ajaxWait();
      
      assertTrue(isVisible(By.xpath("//div[@class='tutorialStepByStep1']")));
      assertFalse(isVisible(By.xpath("//div[@class='tutorialStepByStep2']")));
      
      assertTrue(isVisible(By.xpath("//div[@class='tutorialDiscuss1']")));
      assertFalse(isVisible(By.xpath("//div[@class='tutorialDiscuss2']")));
      
      // test page title and header title
      assertTrue(driver().getTitle().matches("^Pocket Code Website.*"));

      assertRegExp(".*/tutorial", driver().getCurrentUrl());

      driver().findElement(By.xpath("//div[@class='tutorialStarters']")).click();
      assertRegExp(".*/starterPrograms", driver().getCurrentUrl());
      ajaxWait();
      
      assertTrue(isTextPresent("STARTER PROGRAMS"));
      assertTrue(isTextPresent("GAMES"));
      assertTrue(isTextPresent("ANIMATIONS"));
      assertTrue(isTextPresent("INTERACTIVE ART AND STORIES"));
      assertTrue(isTextPresent("MUSIC AND DANCE"));

      assertTrue(isTextPresent("Try out these starter programs. Look inside to make changes and add your ideas."));
      
      assertTrue(isTextPresent("Games"));
      assertTrue(isTextPresent("Animations"));
      assertTrue(isTextPresent("Interactive Art and Stories"));
      assertTrue(isTextPresent("Music and Dance"));
      
      driver().navigate().back();
      ajaxWait();
      assertRegExp(".*/tutorial", driver().getCurrentUrl());
 
    } catch(AssertionError e) {
      captureScreen("TutorialTests.tutorial");
      throw e;
    } catch(Exception e) {
      captureScreen("TutorialTests.tutorial");
      throw e;
    }
  }
  
  @Test(groups = { "visibility", "popupwindows" }, description = "check mobile view ")
  public void mobile() throws Throwable {
    try {
      openMobileLocation("tutorial", false);
      ajaxWait();
      // test page title and header title
      assertTrue(driver().getTitle().matches("^Pocket Code Website.*"));

      assertRegExp(".*/tutorial", driver().getCurrentUrl());
      ajaxWait();
      
      assertFalse(isVisible(By.xpath("//div[@class='tutorialStepByStep1']")));
      assertTrue(isVisible(By.xpath("//div[@class='tutorialStepByStep2']")));
      
      assertFalse(isVisible(By.xpath("//div[@class='tutorialDiscuss1']")));
      assertTrue(isVisible(By.xpath("//div[@class='tutorialDiscuss2']")));
 
    } catch(AssertionError e) {
      captureScreen("TutorialTests.tutorial");
      throw e;
    } catch(Exception e) {
      captureScreen("TutorialTests.tutorial");
      throw e;
    }
  }
}
