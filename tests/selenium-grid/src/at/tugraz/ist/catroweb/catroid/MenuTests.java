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
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

public class MenuTests extends BaseTest{
  @Test(groups = {"menu", "firefox", "default"}, description = "check button visibility")
  public void buttonVisibility() throws Throwable {    
    this.session.open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();
    this.session.click("headerMenuButton");
    waitForPageToLoad(); 
    assertRegExp(".*/catroid/menu$", this.session.getLocation());
    
    assertTrue(this.session.isVisible("menuProfileButton"));
    assertTrue(this.session.isVisible("menuForumButton"));
    assertTrue(this.session.isVisible("menuWikiButton"));

    assertTrue(this.session.isVisible("menuWallButton"));
    assertTrue(this.session.isVisible("menuLoginButton"));
    assertTrue(this.session.isVisible("menuSettingsButton"));

    assertFalse(this.session.isEditable("menuWallButton"));
    assertFalse(this.session.isEditable("menuSettingsButton"));

    this.session.click("menuLoginButton");
    waitForPageToLoad();

    assertRegExp(".*/catroid/login[?]requesturi=catroid/menu",this.session.getLocation());
    this.session.type("xpath=//input[@name='loginUsername']", DataProvider.getLoginUserDefault());
    this.session.type("xpath=//input[@name='loginPassword']", DataProvider.getLoginPasswordDefault());
    this.session.click("xpath=//input[@name='loginSubmit']");
    ajaxWait();
    waitForPageToLoad();  

    assertRegExp(".*/catroid/menu$",this.session.getLocation());
    assertTrue(this.session.isVisible("menuLogoutButton"));
    assertFalse(this.session.isVisible("menuLoginButton"));

    this.session.click("menuLogoutButton");
    waitForPageToLoad();
    assertRegExp(".*/catroid/index(/[0-9]+)?",this.session.getLocation());
   
    this.session.click("headerMenuButton");
    waitForPageToLoad();
   
    assertFalse(this.session.isVisible("menuLogoutButton"));
    assertTrue(this.session.isVisible("menuLoginButton"));
  }

  @Test(groups = {"menu", "firefox", "default"}, description = "check board + wiki links; logged in/out")
  public void boardAndWikiLinks() throws Throwable {
    this.session.open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();
    this.session.click("headerMenuButton");
    waitForPageToLoad();
    assertRegExp(".*/catroid/menu$",this.session.getLocation());
    
    this.session.click("menuForumButton");
    this.session.waitForPopUp("board", Config.TIMEOUT);
    this.session.selectWindow("board");
    
    assertRegExp(".*/addons/board(/)?$",this.session.getLocation());
    assertTrue(this.session.isTextPresent(("Board index")));
    assertTrue(this.session.isTextPresent(("Login")));
    this.session.close();
    this.session.selectWindow(null);
    
    this.session.click("menuWikiButton");    
    this.session.waitForPopUp("wiki", Config.TIMEOUT);
    this.session.selectWindow("wiki");
    assertRegExp(".*/wiki/Main_Page$",this.session.getLocation());    
    
    assertTrue(this.session.isTextPresent(("Main Page")));
    assertFalse(this.session.isElementPresent("pt-userpage"));
    this.session.close();
    this.session.selectWindow(null);
    
    this.session.click("menuLoginButton");
    waitForPageToLoad();   
    this.session.type("xpath=//input[@name='loginUsername']", DataProvider.getLoginUserDefault());
    this.session.type("xpath=//input[@name='loginPassword']", DataProvider.getLoginPasswordDefault());
    this.session.click("xpath=//input[@name='loginSubmit']");
    ajaxWait();
    waitForPageToLoad();   

    this.session.click("menuForumButton");
    this.session.waitForPopUp("board", Config.TIMEOUT);
    this.session.selectWindow("board");
    assertRegExp(".*/addons/board(/)?$",this.session.getLocation());
    
    assertTrue(this.session.isTextPresent(("Board index")));
    assertTrue(this.session.isTextPresent(DataProvider.getLoginUserDefault()));
    this.session.close();
    this.session.selectWindow(null);

    this.session.click("menuWikiButton");
    this.session.waitForPopUp("wiki", Config.TIMEOUT);
    this.session.selectWindow("wiki");
    assertRegExp(".*/wiki/Main_Page[?]action=purge$",this.session.getLocation());
    
    assertTrue(this.session.isTextPresent(("Main Page")));
    assertTrue(this.session.isElementPresent("pt-userpage"));
    this.session.close();
    this.session.selectWindow(null);
 }
  
  @Test(groups = { "example", "firefox", "default" }, description = "check menu home button")
  public void homeButton() {
    this.session.open(Config.TESTS_BASE_PATH);
    waitForPageToLoad();   
    this.session.click("headerMenuButton");
    waitForPageToLoad();   
    assertRegExp(".*/catroid/menu$",this.session.getLocation());
     
    assertTrue(this.session.isElementPresent("headerHomeButton"));
    this.session.click("headerHomeButton");
    waitForPageToLoad();
    assertFalse(this.session.isElementPresent("headerHomeButton"));
    assertRegExp(".*/catroid/index(/[0-9]+)?",this.session.getLocation());
  }
}
