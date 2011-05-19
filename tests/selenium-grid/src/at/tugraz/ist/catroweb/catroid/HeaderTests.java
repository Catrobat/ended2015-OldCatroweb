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

package at.tugraz.ist.catroweb.catroid;

import org.testng.annotations.Test;
import com.thoughtworks.selenium.Selenium;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

public class HeaderTests extends BaseTest{
  
  @Test(groups = {"catroid", "firefox", "default"}, description = "check menu home button")
 public void headerMenuButtons() throws Throwable {
    this.session.open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();
    assertFalse(this.session.isElementPresent("headerHomeButton"));
    assertTrue(this.session.isVisible("headerMenuButton"));
    
    this.session.click("headerMenuButton");
    waitForPageToLoad();
    assertTrue(this.session.isVisible("headerHomeButton"));
    assertFalse(this.session.isElementPresent("headerMenuButton"));
    
    this.session.click("headerHomeButton");
    waitForPageToLoad();
    ajaxWait();
    assertRegExp(".*/catroid/index(/[0-9]+)?",this.session.getLocation());
    assertTrue(this.session.isVisible("headerMenuButton"));
    assertFalse(this.session.isElementPresent("headerHomeButton"));
 }
  
  @Test(groups = {"catroid", "firefox", "default"}, description = "check header buttons, search bar visibility, etc.")
  public void  headerButtonsIndex()
  {   
    this.session.open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();

    assertFalse(this.session.isVisible("headerSearchBox"));
    assertFalse(this.session.isVisible("headerCancelSearchButton"));
    assertTrue(this.session.isVisible("headerSearchButton"));
    assertTrue(this.session.isVisible("headerMenuButton"));  
    
    this.session.click("headerSearchButton");
    ajaxWait();    
    assertTrue(this.session.isVisible("headerSearchBox"));
    assertTrue(this.session.isVisible("headerCancelSearchButton"));
    assertFalse(this.session.isVisible("headerSearchButton"));
    assertFalse(this.session.isVisible("headerMenuButton"));
    
    this.session.click("headerCancelSearchButton");
    ajaxWait();
    assertFalse(this.session.isVisible("headerSearchBox"));
    assertFalse(this.session.isVisible("headerCancelSearchButton"));
    assertTrue(this.session.isVisible("headerSearchButton"));
    assertTrue(this.session.isVisible("headerMenuButton"));
    
    this.session.click("headerMenuButton");
    waitForPageToLoad();
    assertRegExp(".*/catroid/menu$", this.session.getLocation());    
  } 

  @Test(groups = { "catroid", "firefox", "default" }, description = "home button: check button visibility")
  public void headerHomeButton() throws Throwable {
    this.session.open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();
    
    assertTrue(this.session.isVisible("headerMenuButton"));
    assertTrue(this.session.isVisible("headerSearchButton"));
    assertFalse(this.session.isElementPresent("headerHomeButton"));
    
    this.session.click("xpath=//a[@class='license'][4]");    
    waitForPageToLoad();
    
    assertTrue(this.session.isVisible("headerMenuButton"));
    assertTrue(this.session.isVisible("headerHomeButton"));
    assertFalse(this.session.isElementPresent("headerSearchButton"));    
    
    this.session.click("aIndexWebLogoLeft");    
    waitForPageToLoad();
    ajaxWait();
    
    assertTrue(this.session.isVisible("headerMenuButton"));
    assertTrue(this.session.isVisible("headerSearchButton"));
    assertFalse(this.session.isElementPresent("headerHomeButton"));        
  }
}
