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

package at.tugraz.ist.catroweb;

import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.closeSeleniumSession;
import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.session;
import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.startSeleniumSession;
import com.thoughtworks.selenium.Selenium;

import static org.testng.AssertJUnit.assertTrue;
import org.testng.Reporter;
import org.testng.annotations.AfterMethod;
import org.testng.annotations.BeforeMethod;
import org.testng.annotations.Parameters;

import at.tugraz.ist.catroweb.common.Config;
import at.tugraz.ist.catroweb.common.ProjectUploader;

public class BaseTest {
  protected Selenium session;
  protected ProjectUploader projectUploader;

  @BeforeMethod(alwaysRun = true)
  @Parameters( { "seleniumHost", "seleniumPort", "browser", "webSite" })
  protected void startSession(String seleniumHost, int seleniumPort, String browser, String webSite) {
    startSeleniumSession(seleniumHost, seleniumPort, browser, webSite);
    System.out.println("======================= START SESSION =====================");
    session().setSpeed(setSpeed());
    session().setTimeout(Config.TIMEOUT);
    session = session();
    System.out.println(" base path:\t" + webSite + Config.TESTS_BASE_PATH.substring(1));
    System.out.println("===========================================================");
    projectUploader = new ProjectUploader(webSite);
  }

  @AfterMethod(alwaysRun = true)
  protected void closeSession() {
    projectUploader.cleanup();
    closeSeleniumSession();
  }

  private String setSpeed() {
    if(Config.TESTS_SLOW_MODE) {
      System.out.println("***  WARNING:  You are running this test in slow mode!  ***");
      return String.valueOf(Config.TESTS_SLOW_SPEED);
    } else {
      return "1";
    }
  }

  protected void ajaxWait() {
    this.session.waitForCondition("typeof selenium.browserbot.getCurrentWindow().jQuery == 'function'", Config.TIMEOUT_AJAX);
    this.session.waitForCondition("selenium.browserbot.getCurrentWindow().jQuery.active == 0", Config.TIMEOUT_AJAX);
  }

  public static void assertRegExp(String pattern, String string) {
    assertTrue(string.matches(pattern));
  }

  protected void clickAndWaitForPopUp(String xpath, String windowname) {
    this.session.click(xpath);
    this.session.waitForPopUp(windowname, Config.TIMEOUT);
    this.session.selectPopUp(windowname);
  }

  protected void closePopUp() {
    this.session.close();
    this.session.selectWindow(null);
  }

  public void waitForPageToLoad() {
    this.session.waitForPageToLoad(Config.TIMEOUT);
  }

  public void waitForElementPresent(String locator) {
    this.session.waitForCondition("value = selenium.isElementPresent('" + locator.replace("'", "\\'") + "'); value == true", Config.WAIT_FOR_PAGE_TO_LOAD_LONG);
  }

  protected void waitForTextPresent(String text) {
    this.session.waitForCondition("value = selenium.isTextPresent('" + text + "'); value == true", Config.WAIT_FOR_PAGE_TO_LOAD_LONG);
  }

  protected void log(String message) {
    Reporter.log(message, Config.REPORTER_LOG_TO_STD_OUT);
  }
}
