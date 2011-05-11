package at.tugraz.ist.catroweb.catroid.license;

import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.closeSeleniumSession;
import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.session;
import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.startSeleniumSession;
import static org.testng.AssertJUnit.assertTrue;
import org.testng.annotations.AfterMethod;
import org.testng.annotations.BeforeMethod;
import org.testng.annotations.Parameters;
import org.testng.annotations.Test;


/**
 * Base class for all tests in Selenium Grid Java examples.
 */
public class LicenseTests {

    public static final String TIMEOUT = "120000";

    @BeforeMethod(groups = {"default", "licence"}, alwaysRun = true)
    @Parameters({"seleniumHost", "seleniumPort", "browser", "webSite"})
    protected void startSession(String seleniumHost, int seleniumPort, String browser, String webSite) {
        startSeleniumSession(seleniumHost, seleniumPort, browser, webSite);
        session().setTimeout(TIMEOUT);
    }

    @AfterMethod(groups = {"default", "licence"}, alwaysRun = true)
    protected void closeSession() {
        closeSeleniumSession();
    }

    @Test(groups = {"licence", "firefox", "default"}, description = "Imprint")
    public void imprint() throws Throwable {
        session().setSpeed("1000");
        session().open("/");
        session().waitForPageToLoad(TIMEOUT);
        session().click("xpath=//a[@class='license'][5]");
        session().waitForPageToLoad(TIMEOUT);
        assertTrue(session().isTextPresent(("Contact us")));
        session().isElementPresent("xpath=//p[@class='licenseText']/a");
        session().goBack();
        session().waitForPageToLoad(TIMEOUT);
    }
}
