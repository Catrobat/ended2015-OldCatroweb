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

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.lang.reflect.Method;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.Collections;
import java.util.HashMap;
import java.util.Map;

import static org.testng.AssertJUnit.assertTrue;

import org.apache.commons.codec.binary.Base64;
import org.apache.commons.io.FileUtils;
import org.openqa.selenium.By;
import org.openqa.selenium.OutputType;
import org.openqa.selenium.TakesScreenshot;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebDriverBackedSelenium;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.firefox.FirefoxProfile;
import org.openqa.selenium.remote.DesiredCapabilities;
import org.openqa.selenium.remote.RemoteWebDriver;
import org.testng.Reporter;
import org.testng.annotations.BeforeClass;
import org.testng.annotations.AfterClass;
import org.testng.annotations.BeforeMethod;
import org.testng.annotations.AfterMethod;
import org.testng.annotations.Parameters;

import com.thoughtworks.selenium.DefaultSelenium;
import com.thoughtworks.selenium.Selenium;
import com.thoughtworks.selenium.SeleniumException;

import at.tugraz.ist.catroweb.common.CommonFunctions;
import at.tugraz.ist.catroweb.common.CommonStrings;
import at.tugraz.ist.catroweb.common.Config;
import at.tugraz.ist.catroweb.common.ProjectUploader;

public class BaseTest {
  protected ProjectUploader projectUploader;
  protected String webSite;
  protected Map<String, Selenium> seleniumSessions;
  protected Map<String, WebDriver> driverSessions;

  @BeforeClass(alwaysRun = true)
  @Parameters({ "webSite", "basedir" })
  protected void constructor(String webSite, String basedir) {
    this.webSite = webSite;
    Config.setSeleniumGridTestdata(basedir);
    projectUploader = new ProjectUploader(webSite);
    this.seleniumSessions = Collections.synchronizedMap(new HashMap<String, Selenium>());
    this.driverSessions = Collections.synchronizedMap(new HashMap<String, WebDriver>());
  }

  @AfterClass(alwaysRun = true)
  protected void destructor() {
    projectUploader.cleanup();
  }

  @BeforeMethod(alwaysRun = true)
  @Parameters({ "seleniumHost", "seleniumPort", "browser", "webSite" })
  synchronized protected void startSession(String seleniumHost, int seleniumPort, String browser, String webSite, Method method) {
    String methodName = method.getName();
    System.out.println("running " + methodName + "...");

    if(!this.seleniumSessions.containsKey(methodName)) {
      startFirefoxSession(seleniumHost, seleniumPort, browser, webSite, methodName);
    }
  }

  protected void startFirefoxSession(String seleniumHost, int seleniumPort, String browser, String webSite, String method) {
    FirefoxProfile profile = new FirefoxProfile();
    profile.setPreference("network.http.phishy-userpass-length", 255);
    WebDriver driver = new FirefoxDriver(profile);
    Selenium selenium = new WebDriverBackedSelenium(driver, webSite);
    selenium.setSpeed(setSpeed());
    selenium.setTimeout(Config.TIMEOUT);

    this.driverSessions.put(method, driver);
    this.seleniumSessions.put(method, selenium);
  }

  protected void startChromeSession(String seleniumHost, int seleniumPort, String browser, String webSite, String method) {
    System.setProperty("webdriver.chrome.bin", "/opt/google/chrome/google-chrome");
    System.setProperty("webdriver.chrome.driver", "/home/chris/.workspace/catroweb/tests/selenium-grid/chromedriver");

    WebDriver driver = new ChromeDriver();
    Selenium selenium = new WebDriverBackedSelenium(driver, webSite);
    selenium.setSpeed(setSpeed());
    selenium.setTimeout(Config.TIMEOUT);

    this.driverSessions.put(method, driver);
    this.seleniumSessions.put(method, selenium);
  }

  protected void startGridSession(String seleniumHost, int seleniumPort, String browser, String webSite, String method) {
    // Selenium selenium = new DefaultSelenium(seleniumHost, seleniumPort,
    // browser, webSite);
    // Selenium selenium = new DefaultSelenium("localhost", 4444, "*firefox",
    // "http://catroid.local/");
    // selenium.open(Config.TESTS_BASE_PATH);
    // this.seleniumSessions.put(method, selenium);

    try {

      DesiredCapabilities capability = DesiredCapabilities.firefox();
      WebDriver driver = new RemoteWebDriver(new URL("http://localhost:4444/wd/hub"), capability);

      driver.get(Config.TESTS_BASE_PATH);

      this.driverSessions.put(method, driver);
    } catch(MalformedURLException e) {
      // TODO Auto-generated catch block
      e.printStackTrace();
    }

  }

  @AfterMethod(alwaysRun = true)
  synchronized protected void closeSession(Method method) {
    String methodName = method.getName();

    getSeleniumObject(methodName).close();
    getDriverObject(methodName).quit();
    this.seleniumSessions.remove(methodName);
    this.driverSessions.remove(methodName);

    System.out.println("..." + methodName + " done");
  }

  protected Selenium selenium() {
    return getSeleniumObject(getCalleeName());
  }

  protected WebDriver driver() {
    return getDriverObject(getCalleeName());
  }

  private String getCalleeName() {
    StackTraceElement[] stack = Thread.currentThread().getStackTrace();

    for(StackTraceElement item : stack) {
      String entry = item.toString();
      if(entry.matches("at.tugraz.ist.catroweb.*") && !entry.matches("at.tugraz.ist.catroweb.BaseTest.*")) {
        return item.getMethodName();
      }
    }

    return null;
  }

  private Selenium getSeleniumObject(String key) {
    if(this.seleniumSessions.containsKey(key)) {
      return this.seleniumSessions.get(key);
    }
    return null;
  }

  private WebDriver getDriverObject(String key) {
    if(this.driverSessions.containsKey(key)) {
      return this.driverSessions.get(key);
    }
    return null;
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
    selenium().waitForCondition("typeof selenium.browserbot.getCurrentWindow().jQuery == 'function'", Config.TIMEOUT_AJAX);
    selenium().waitForCondition("selenium.browserbot.getCurrentWindow().jQuery.active == 0", Config.TIMEOUT_AJAX);
    
//    PageFactory.initElements(new AjaxElementLocatorFactory(driver, 5), this); 
  }

  public static void assertRegExp(String pattern, String string) {
    assertTrue(string.matches(pattern));
  }

  public void assertTextPresent(String text) {
    assertTrue((driver().findElement(By.tagName("body"))).getText().contains(text));
  }

  public void assertElementPresent(String element) {
    assertTrue(driver().findElement(By.xpath(element)).isDisplayed());
  }

  protected void openLocation() {
    openLocation("");
  }

  protected void openLocation(String location) {
    openLocation(location, true);
  }

  protected void openLocation(String location, Boolean forceDefaultLanguage) {
    if(forceDefaultLanguage == true) {
      driver().get(this.webSite + Config.TESTS_BASE_PATH + location + "?userLanguage=" + Config.SITE_DEFAULT_LANGUAGE);
    } else {
      driver().get(this.webSite + Config.TESTS_BASE_PATH + location);
    }
    waitForPageToLoad();
  }

  protected void openAdminLocation() {
    openAdminLocation("");
  }

  protected void openAdminLocation(String location) {
    driver().get(CommonFunctions.getAdminPath(this.webSite) + location);
    waitForPageToLoad();
  }

  /**
   * @param xpath
   * @param windowname
   */
  protected void clickAndWaitForPopUp(String xpath, String windowname) {
    driver().findElement(By.xpath(xpath.replace("xpath=", ""))).click();

//    selenium().waitForPopUp(windowname, Config.TIMEOUT);
//    driver().switchTo().window(windowname);

    selenium().selectWindow(windowname);
  }

  protected void closePopUp() {
    selenium().close();
    selenium().selectWindow("null");
    waitForPageToLoad();
  }

  protected void assertProjectPresent(String project) {
    openLocation();
    selenium().click("headerSearchButton");
    selenium().type("searchQuery", project);
    selenium().click("webHeadSearchSubmit");
    ajaxWait();
    waitForTextPresent(project);
  }

  protected void assertProjectNotPresent(String project) {
    openLocation();
    selenium().click("headerSearchButton");
    selenium().type("searchQuery", project);
    selenium().click("webHeadSearchSubmit");
    ajaxWait();
    waitForTextPresent(CommonStrings.SEARCH_PROJECTS_PAGE_NO_RESULTS);
  }

  public void waitForPageToLoad() {
    selenium().waitForPageToLoad(Config.TIMEOUT);
  }

  public void waitForElementPresent(String locator) {
    selenium().waitForCondition("value = selenium.isElementPresent('" + locator.replace("'", "\\'") + "'); value == true", Config.WAIT_FOR_PAGE_TO_LOAD);
  }

  protected void waitForTextPresent(String text) {
    boolean wait = true;
    while(wait) {
      try {
        selenium().waitForCondition("value = selenium.isTextPresent('" + text + "'); value == true", Config.WAIT_FOR_PAGE_TO_LOAD);
        wait = false;
      } catch(SeleniumException e) {
        if(e.getMessage().matches(".*Timed out after.*")) {
          wait = false;
        }
      }
    }
    assertTrue(selenium().isTextPresent(text));
  }

  public void clickLastVisibleProject() {
    while(selenium().isVisible("moreProjects")) {
      selenium().click("moreProjects");
      ajaxWait();
    }
    String[] allLinks = selenium().getAllLinks();
    String lastLink = "";
    for(String link : allLinks) {
      try {
        if((link.matches("projectListDetailsLinkThumb.*")) && (selenium().isVisible(link))) {
          lastLink = link;
        }
      } catch(Exception e) {
      }
    }
    selenium().click(lastLink);
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
      File scrFile = ((TakesScreenshot) driver()).getScreenshotAs(OutputType.FILE);
      FileUtils.copyFile(scrFile, new File(Config.FILESYSTEM_BASE_PATH + imagePath));
      Reporter.log("<a href=\"" + this.webSite + Config.TESTS_BASE_PATH.substring(1) + imagePath + "\">Screenshot (" + imageName + ")</a>");
    } catch(IOException e) {
      e.printStackTrace();
    }
  }
}
