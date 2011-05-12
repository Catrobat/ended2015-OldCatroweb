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

    @BeforeMethod(groups = {"default", "license"}, alwaysRun = true)
    @Parameters({"seleniumHost", "seleniumPort", "browser", "webSite"})
    protected void startSession(String seleniumHost, int seleniumPort, String browser, String webSite) {
        startSeleniumSession(seleniumHost, seleniumPort, browser, webSite);
        session().setTimeout(TIMEOUT);
    }

    @AfterMethod(groups = {"default", "license"}, alwaysRun = true)
    protected void closeSession() {
        closeSeleniumSession();
    }

    @Test(groups = {"license", "firefox", "default"}, description = "Imprint")
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

    @Test(groups = {"license", "firefox", "default"}, description = "Imprint")
    public void imprint2() throws Throwable {
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
