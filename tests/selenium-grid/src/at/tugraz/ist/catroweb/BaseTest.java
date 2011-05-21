package at.tugraz.ist.catroweb;

import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.closeSeleniumSession;
import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.session;
import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.startSeleniumSession;

import static org.testng.AssertJUnit.assertTrue;
import org.testng.Reporter;
import org.testng.annotations.AfterMethod;
import org.testng.annotations.BeforeMethod;
import org.testng.annotations.Parameters;

import at.tugraz.ist.catroweb.common.Config;
import at.tugraz.ist.catroweb.common.ProjectUploader;

public class BaseTest {
  protected ProjectUploader projectUploader;

  @BeforeMethod(alwaysRun = true)
  @Parameters( { "seleniumHost", "seleniumPort", "browser", "webSite" })
  protected void startSession(String seleniumHost, int seleniumPort, String browser, String webSite) {
    startSeleniumSession(seleniumHost, seleniumPort, browser, webSite);
    System.out.println("======================= START SESSION =====================");
    session().setSpeed(setSpeed());
    session().setTimeout(Config.TIMEOUT);
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
    session().waitForCondition("typeof selenium.browserbot.getCurrentWindow().jQuery == 'function'", Config.TIMEOUT_AJAX);
    session().waitForCondition("selenium.browserbot.getCurrentWindow().jQuery.active == 0", Config.TIMEOUT_AJAX);
  }

  public static void assertRegExp(String pattern, String string) {
    assertTrue(string.matches(pattern));
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
    session().waitForCondition("value = selenium.isElementPresent('" + locator.replace("'", "\\'") + "'); value == true", Config.WAIT_FOR_PAGE_TO_LOAD_LONG);
  }

  protected void waitForTextPresent(String text) {
    session().waitForCondition("value = selenium.isTextPresent('" + text + "'); value == true", Config.WAIT_FOR_PAGE_TO_LOAD_LONG);
  }

  protected void log(String message) {
    Reporter.log(message, Config.REPORTER_LOG_TO_STD_OUT);
  }
}
