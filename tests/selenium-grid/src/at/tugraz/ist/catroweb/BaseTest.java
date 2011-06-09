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

import java.io.File;
import java.io.FileOutputStream;

import static org.testng.AssertJUnit.assertTrue;

import org.apache.commons.codec.binary.Base64;
import org.testng.Reporter;
import org.testng.annotations.BeforeClass;
import org.testng.annotations.AfterClass;
import org.testng.annotations.BeforeMethod;
import org.testng.annotations.AfterMethod;
import org.testng.annotations.Parameters;

import com.thoughtworks.selenium.SeleniumException;

import at.tugraz.ist.catroweb.common.CommonFunctions;
import at.tugraz.ist.catroweb.common.Config;
import at.tugraz.ist.catroweb.common.ProjectUploader;

public class BaseTest {
  protected ProjectUploader projectUploader;
  protected String webSite;

  @BeforeClass(alwaysRun = true)
  @Parameters( { "webSite", "basedir" })
  protected void constructor(String webSite, String basedir) {
    this.webSite = webSite;
    Config.setSeleniumGridTestdata(basedir);
    projectUploader = new ProjectUploader(webSite);
  }

  @AfterClass(alwaysRun = true)
  protected void destructor() {
    projectUploader.cleanup();
  }

  @BeforeMethod(alwaysRun = true)
  @Parameters( { "seleniumHost", "seleniumPort", "browser", "webSite" })
  protected void startSession(String seleniumHost, int seleniumPort, String browser, String webSite) {
    startSeleniumSession(seleniumHost, seleniumPort, browser, webSite);
    System.out.println("====================== START SESSION ===================");
    session().setSpeed(setSpeed());
    session().setTimeout(Config.TIMEOUT);
    System.out.println(" base path:\t" + webSite + Config.TESTS_BASE_PATH.substring(1));
    System.out.println("========================================================");
  }

  @AfterMethod(alwaysRun = true)
  protected void closeSession() {
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
    session().waitForCondition("typeof selenium.browserbot.getCurrentWindow().jQuery == 'function'", Config.TIMEOUT_AJAX);
    session().waitForCondition("selenium.browserbot.getCurrentWindow().jQuery.active == 0", Config.TIMEOUT_AJAX);
  }

  public static void assertRegExp(String pattern, String string) {
    assertTrue(string.matches(pattern));
  }

  protected void openLocation() {
    openLocation("");
  }

  protected void openLocation(String location) {
    session().open(Config.TESTS_BASE_PATH + location);
    waitForPageToLoad();
  }

  protected void openAdminLocation() {
    session().open(CommonFunctions.getAdminPath(this.webSite));
    waitForPageToLoad();
  }

  protected void openAdminLocation(String location) {
    session().open(CommonFunctions.getAdminPath(this.webSite) + location);
    waitForPageToLoad();
  }

  protected void clickAndWaitForPopUp(String xpath, String windowname) {
    session().click(xpath);
    session().waitForPopUp(windowname, Config.TIMEOUT);
    session().selectPopUp(windowname);
  }

  protected void closePopUp() {
    session().close();
    session().selectWindow(null);
  }

  public void waitForPageToLoad() {
    session().waitForPageToLoad(Config.TIMEOUT);
  }

  public void waitForElementPresent(String locator) {
    session().waitForCondition("value = selenium.isElementPresent('" + locator.replace("'", "\\'") + "'); value == true", Config.WAIT_FOR_PAGE_TO_LOAD);
  }

  protected void waitForTextPresent(String text) {
    boolean wait = true;
    while(wait) {
      try {
        session().waitForCondition("value = selenium.isTextPresent('" + text + "'); value == true", Config.WAIT_FOR_PAGE_TO_LOAD);
        wait = false;
      } catch(SeleniumException e) {
        if(e.getMessage().matches(".*Timed out after.*")) {
          wait = false;
        }
      }
    }
    assertTrue(session().isTextPresent(text));
  }

  public void clickLastVisibleProject() {
    while(session().isVisible("moreProjects")) {
      session().click("moreProjects");
      ajaxWait();
    }
    String[] allLinks = session().getAllLinks();
    String lastLink = "";
    for(String link : allLinks) {
      try {
        if((link.matches("projectListDetailsLinkThumb.*")) && (session().isVisible(link))) {
          lastLink = link;
        }
      } catch(Exception e) {
      }
    }
    session().click(lastLink);
    waitForPageToLoad();
  }

  protected void log(int message) {
    Reporter.log(String.valueOf(message), Config.REPORTER_LOG_TO_STD_OUT);
  }

  protected void log(String message) {
    Reporter.log(message, Config.REPORTER_LOG_TO_STD_OUT);
  }

  protected void captureScreen(String imageName) {
    String imageExtension = ".png";
    String imagePath = Config.SELENIUM_GRID_TARGET + imageName + imageExtension;
    try {
      String base64Screenshot = session().captureEntirePageScreenshotToString("");
      byte[] decodedScreenshot = Base64.decodeBase64(base64Screenshot.getBytes());
      FileOutputStream fos = new FileOutputStream(new File(Config.FILESYSTEM_BASE_PATH + imagePath));
      fos.write(decodedScreenshot);
      fos.close();
      Reporter.log("<a href=\"" + this.webSite + Config.TESTS_BASE_PATH.substring(1) + imagePath + "\">Screenshot (" + imageName + ")</a>");
    } catch(Exception e) {
      e.printStackTrace();
    }
  }
}
