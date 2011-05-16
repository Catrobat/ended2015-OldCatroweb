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

package at.tugraz.ist.catroweb.catroid.menu;

import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.closeSeleniumSession;
import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.session;
import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.startSeleniumSession;
import static org.testng.AssertJUnit.*;

import org.testng.annotations.AfterMethod;
import org.testng.annotations.BeforeMethod;
import org.testng.annotations.Parameters;
import org.testng.annotations.Test;
import org.testng.Reporter;

import at.tugraz.ist.catroweb.common.*;


/**
 * Base class for all tests in Selenium Grid Java examples.
 */
public class MenuTests {

  public static final String TIMEOUT = "120000";

  @BeforeMethod(groups = { "default", "example" }, alwaysRun = true)
  @Parameters({ "seleniumHost", "seleniumPort", "browser", "webSite" })
  protected void startSession(String seleniumHost, int seleniumPort, String browser, String webSite) {
    startSeleniumSession(seleniumHost, seleniumPort, browser, webSite);
    session().setSpeed(CommonFunctions.setSpeed());
    session().setTimeout(CommonConfig.TIMEOUT);
  }

  @AfterMethod(groups = { "default", "example" }, alwaysRun = true)
  protected void closeSession() {
    closeSeleniumSession();
  }

  
  @Test(groups = {"menu", "firefox", "default"}, description = "check button visibility")
  public void buttonVisibility() throws Throwable {
    session().open(CommonConfig.TESTS_BASE_PATH);
    session().waitForPageToLoad(CommonConfig.TIMEOUT);   
    session().click("headerMenuButton");
    session().waitForPageToLoad(CommonConfig.TIMEOUT); 
    CommonAssertions.assertRegExp(".*/catroid/menu$", session().getLocation());
    
//    assertTrue(CommonAssertions.isMenuLocation(session().getLocation()));

    assertTrue(session().isVisible("menuProfileButton"));
    assertTrue(session().isVisible("menuForumButton"));
    assertTrue(session().isVisible("menuWikiButton"));

    assertTrue(session().isVisible("menuWallButton"));
    assertTrue(session().isVisible("menuLoginButton"));
    assertTrue(session().isVisible("menuSettingsButton"));

    assertFalse(session().isEditable("menuWallButton"));
    assertFalse(session().isEditable("menuSettingsButton"));

    session().click("menuLoginButton");
    session().waitForPageToLoad(CommonConfig.TIMEOUT);   

    //assertTrue(CommonAssertions.isLoginLocation(session().getLocation()));
    CommonAssertions.assertRegExp(".*/catroid/login[?]requesturi=catroid/menu",session().getLocation());
    session().type("xpath=//input[@name='loginUsername']", CommonDataProvider.getLoginUserDefault());
    session().type("xpath=//input[@name='loginPassword']", CommonDataProvider.getLoginPasswordDefault());
    session().click("xpath=//input[@name='loginSubmit']");
    session().waitForCondition(CommonFunctions.getAjaxWaitString(), CommonConfig.TIMEOUT_AJAX);
    session().waitForPageToLoad(CommonConfig.TIMEOUT);   

    //assertTrue(CommonAssertions.isMenuLocation(session().getLocation()));
    CommonAssertions.assertRegExp(".*/catroid/menu$",session().getLocation());
    assertTrue(session().isVisible("menuLogoutButton"));
    assertFalse(session().isVisible("menuLoginButton"));

    session().click("menuLogoutButton");
    session().waitForPageToLoad(CommonConfig.TIMEOUT);   
    // TODO UNDEFINED ?!
    // assertTrue(CommonAssertions.isIndexLocation(session().getLocation()));
   
    session().click("headerMenuButton");
    session().waitForPageToLoad(CommonConfig.TIMEOUT);   
   
    assertFalse(session().isVisible("menuLogoutButton"));
    assertTrue(session().isVisible("menuLoginButton"));
  }

  @Test(groups = {"menu", "firefox", "default"}, description = "check board + wiki links; logged in/out")
  public void boardAndWikiLinks() throws Throwable {
    session().open(CommonConfig.TESTS_BASE_PATH);
    session().waitForPageToLoad(CommonConfig.TIMEOUT);   
    session().click("headerMenuButton");
    session().waitForPageToLoad(CommonConfig.TIMEOUT);   
    //assertTrue(CommonAssertions.isMenuLocation(session().getLocation()));
    CommonAssertions.assertRegExp(".*/catroid/menu$",session().getLocation());
    
    
    session().click("menuForumButton");
    session().waitForPopUp("board", CommonConfig.TIMEOUT);
    session().selectWindow("board");
    //assertTrue(CommonAssertions.isBoardLocation(session().getLocation()));
    CommonAssertions.assertRegExp(".*/addons/board(/)?$",session().getLocation());
    
    assertTrue(session().isTextPresent(("Board index")));
    assertTrue(session().isTextPresent(("Login")));
    session().close();
    session().selectWindow(null);

    session().click("menuWikiButton");
    session().waitForPopUp("wiki", CommonConfig.TIMEOUT);
    session().selectWindow("wiki");
    //assertTrue(CommonAssertions.isWikiLocation(session().getLocation()));
    CommonAssertions.assertRegExp(".*/wiki/Main_Page$",session().getLocation());
    
    assertTrue(session().isTextPresent(("Main Page")));
    assertFalse(session().isElementPresent("pt-userpage"));
    session().close();
    session().selectWindow(null);
    
    session().click("menuLoginButton");
    session().waitForPageToLoad(CommonConfig.TIMEOUT);   
    session().type("xpath=//input[@name='loginUsername']", CommonDataProvider.getLoginUserDefault());
    session().type("xpath=//input[@name='loginPassword']", CommonDataProvider.getLoginPasswordDefault());
    session().click("xpath=//input[@name='loginSubmit']");
    session().waitForCondition(CommonFunctions.getAjaxWaitString(), CommonConfig.TIMEOUT_AJAX);
    session().waitForPageToLoad(CommonConfig.TIMEOUT);   

    session().click("menuForumButton");
    session().waitForPopUp("board", CommonConfig.TIMEOUT);
    session().selectWindow("board");
    //assertTrue(CommonAssertions.isBoardLocation(session().getLocation()));
    CommonAssertions.assertRegExp(".*/addons/board(/)?$",session().getLocation());
    
    assertTrue(session().isTextPresent(("Board index")));
    assertTrue(session().isTextPresent(CommonDataProvider.getLoginUserDefault()));
    session().close();
    session().selectWindow(null);

    session().click("menuWikiButton");
    session().waitForPopUp("wiki", CommonConfig.TIMEOUT);
    session().selectWindow("wiki");
    //assertTrue(CommonAssertions.isWikiLocation(session().getLocation()));
    CommonAssertions.assertRegExp(".*/wiki/Main_Page[?]action=purge$",session().getLocation());
    
    assertTrue(session().isTextPresent(("Main Page")));
    assertTrue(session().isElementPresent("pt-userpage"));
    session().close();
    session().selectWindow(null);
 }
  
  @Test(groups = { "example", "firefox", "default" }, description = "check menu home button")
  public void homeButton() {
    session().open(CommonConfig.TESTS_BASE_PATH);
    session().waitForPageToLoad(CommonConfig.TIMEOUT);   
    session().click("headerMenuButton");
    session().waitForPageToLoad(CommonConfig.TIMEOUT);   
 //   assertTrue(CommonAssertions.isMenuLocation(session().getLocation()));
    CommonAssertions.assertRegExp(".*/catroid/menu$",session().getLocation());
     
    assertTrue(session().isElementPresent("headerHomeButton"));
    session().click("headerHomeButton");
    session().waitForPageToLoad(CommonConfig.TIMEOUT);
    assertFalse(session().isElementPresent("headerHomeButton"));
 //   assertTrue(CommonAssertions.isIndexLocation(session().getLocation()));    
    CommonAssertions.assertRegExp(".*/catroid/index(/[0-9]+)?",session().getLocation());
    
  }
}
