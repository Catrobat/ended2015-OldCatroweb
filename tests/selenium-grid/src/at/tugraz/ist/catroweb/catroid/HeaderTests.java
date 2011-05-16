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

import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.closeSeleniumSession;
import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.session;
import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.startSeleniumSession;
import static org.testng.AssertJUnit.assertTrue;
import static org.testng.AssertJUnit.assertFalse;
import org.testng.annotations.AfterMethod;
import org.testng.annotations.BeforeMethod;
import org.testng.annotations.Parameters;
import org.testng.annotations.Test;

import at.tugraz.ist.catroweb.common.*;

public class HeaderTests {

  public static final String TIMEOUT = "120000";

  @BeforeMethod(groups = { "default", "catroid" }, alwaysRun = true)
  @Parameters({ "seleniumHost", "seleniumPort", "browser", "webSite" })
  protected void startSession(String seleniumHost, int seleniumPort, String browser, String webSite) {
    startSeleniumSession(seleniumHost, seleniumPort, browser, webSite);
    session().setTimeout(TIMEOUT);
  }

  @AfterMethod(groups = { "default", "catroid" }, alwaysRun = true)
  protected void closeSession() {
    closeSeleniumSession();
  }
 
  @Test(groups = {"catroid", "firefox", "default"}, description = "check menu home button")
 public void headerMenuButtons() throws Throwable {
    session().open(CommonConfig.TESTS_BASE_PATH);
    session().waitForPageToLoad(CommonConfig.TIMEOUT);
    assertFalse(session().isElementPresent("headerHomeButton"));
    assertTrue(session().isVisible("headerMenuButton"));
    
    session().click("headerMenuButton");
    session().waitForPageToLoad(CommonConfig.TIMEOUT);
    assertTrue(session().isVisible("headerHomeButton"));
    assertFalse(session().isElementPresent("headerMenuButton"));
    
    session().click("headerHomeButton");
    session().waitForPageToLoad(CommonConfig.TIMEOUT);
    session().waitForCondition(CommonFunctions.getAjaxWaitString(), CommonConfig.TIMEOUT_AJAX);
    CommonAssertions.assertRegExp(".*/catroid/index(/[0-9]+)?",session().getLocation());
    assertTrue(session().isVisible("headerMenuButton"));
    assertFalse(session().isElementPresent("headerHomeButton"));
 }
  
  @Test(groups = {"catroid", "firefox", "default"}, description = "check header buttons, search bar visibility, etc.")
  public void  headerButtonsIndex()
  {   
    session().open(CommonConfig.TESTS_BASE_PATH);
    session().waitForPageToLoad(CommonConfig.TIMEOUT);

    assertFalse(session().isVisible("headerSearchBox"));
    assertFalse(session().isVisible("headerCancelSearchButton"));
    assertTrue(session().isVisible("headerSearchButton"));
    assertTrue(session().isVisible("headerMenuButton"));  
    
    session().click("headerSearchButton");
    session().waitForCondition(CommonFunctions.getAjaxWaitString(), CommonConfig.TIMEOUT_AJAX);
    assertTrue(session().isVisible("headerSearchBox"));
    assertTrue(session().isVisible("headerCancelSearchButton"));
    assertFalse(session().isVisible("headerSearchButton"));
    assertFalse(session().isVisible("headerMenuButton"));
    
    session().click("headerCancelSearchButton");
    session().waitForCondition(CommonFunctions.getAjaxWaitString(), CommonConfig.TIMEOUT_AJAX);
    assertFalse(session().isVisible("headerSearchBox"));
    assertFalse(session().isVisible("headerCancelSearchButton"));
    assertTrue(session().isVisible("headerSearchButton"));
    assertTrue(session().isVisible("headerMenuButton"));
    
    session().click("headerMenuButton");
    session().waitForPageToLoad(CommonConfig.TIMEOUT);
    CommonAssertions.assertRegExp(".*/catroid/menu$", session().getLocation());    
  } 

  @Test(groups = { "catroid", "firefox", "default" }, description = "home button: check button visibility")
  public void headerHomeButton() throws Throwable {
    session().open(CommonConfig.TESTS_BASE_PATH);
    session().waitForPageToLoad(CommonConfig.TIMEOUT);
    
    assertTrue(session().isVisible("headerMenuButton"));
    assertTrue(session().isVisible("headerSearchButton"));
    assertFalse(session().isElementPresent("headerHomeButton"));
    
    session().click("xpath=//a[@class='license'][4]");    
    session().waitForPageToLoad(CommonConfig.TIMEOUT);
    
    assertTrue(session().isVisible("headerMenuButton"));
    assertTrue(session().isVisible("headerHomeButton"));
    assertFalse(session().isElementPresent("headerSearchButton"));    
    
    session().click("aIndexWebLogoLeft");    
    session().waitForPageToLoad(CommonConfig.TIMEOUT);
    session().waitForCondition(CommonFunctions.getAjaxWaitString(), CommonConfig.TIMEOUT_AJAX);
    
    assertTrue(session().isVisible("headerMenuButton"));
    assertTrue(session().isVisible("headerSearchButton"));
    assertFalse(session().isElementPresent("headerHomeButton"));        
  }
}
