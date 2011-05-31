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

package at.tugraz.ist.catroweb.admin;

import static com.thoughtworks.selenium.grid.tools.ThreadSafeSeleniumSessionStorage.session;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.sql.Statement;

import org.postgresql.Driver;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

@Test(groups = { "admin", "BadWordsFilterTests" })
public class BadWordsFilterTests extends BaseTest {

  @Test(groups = { "functionality", "upload" }, description = "approve an unapproved word as good")
  public void approveButtonGood() throws Throwable {
    String unapprovedWord = "donaudampfschiffahrtselektrizitaetenhauptbetriebswerkbauunterbeamtengesellschaft" + CommonData.getRandomShortString();
    String response = projectUploader.upload(CommonData.getUploadPayload(unapprovedWord, "", "", "", "", "", "", ""));
    assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));

    openAdminLocation();
    session().click("aAdministrationTools");
    waitForPageToLoad();
    session().click("aAdminToolsApproveWords");
    waitForPageToLoad();
    assertTrue(session().isTextPresent(unapprovedWord));
    session().select("id=meaning" + CommonFunctions.getUnapprovedWordId(unapprovedWord), "label=good");
    session().click("xpath=//input[@id='approve" + CommonFunctions.getUnapprovedWordId(unapprovedWord) + "']");
    session().getConfirmation();
    waitForPageToLoad();
    assertTrue(session().isTextPresent("The word was succesfully approved!"));

    openLocation();
    ajaxWait();
    assertTrue(session().isTextPresent(unapprovedWord));

    deletePreviouslyUploadedProjectAndUnapporvedWord(unapprovedWord);
  }

  @Test(groups = { "functionality", "upload" }, description = "approve an unapproved word as bad")
  public void approveButtonBad() throws Throwable {
    String unapprovedWord = "donaudampfschiffahrtselektrizitaetenhauptbetriebswerkbauunterbeamtengesellschaft" + CommonData.getRandomShortString();
    String response = projectUploader.upload(CommonData.getUploadPayload(unapprovedWord, "", "", "", "", "", "", ""));
    assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));

    openAdminLocation();
    session().click("aAdministrationTools");
    waitForPageToLoad();
    session().click("aAdminToolsApproveWords");
    waitForPageToLoad();
    assertTrue(session().isTextPresent(unapprovedWord));
    session().select("id=meaning" + CommonFunctions.getUnapprovedWordId(unapprovedWord), "label=bad");
    session().click("xpath=//input[@id='approve" + CommonFunctions.getUnapprovedWordId(unapprovedWord) + "']");
    session().getConfirmation();
    waitForPageToLoad();
    assertTrue(session().isTextPresent("The word was succesfully approved!"));

    openLocation();
    ajaxWait();
    assertFalse(session().isTextPresent(unapprovedWord));

    deletePreviouslyUploadedProjectAndUnapporvedWord(unapprovedWord);
  }

  @Test(groups = { "functionality", "upload" }, description = "approve an unapproved word with no selection")
  public void approveButtonNoSelection() throws Throwable {
    String unapprovedWord = "donaudampfschiffahrtselektrizitaetenhauptbetriebswerkbauunterbeamtengesellschaft" + CommonData.getRandomShortString();
    String response = projectUploader.upload(CommonData.getUploadPayload(unapprovedWord, "", "", "", "", "", "", ""));
    assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));

    openAdminLocation();
    session().click("aAdministrationTools");
    waitForPageToLoad();
    session().click("aAdminToolsApproveWords");
    waitForPageToLoad();
    assertTrue(session().isTextPresent(unapprovedWord));
    session().click("xpath=//input[@id='approve" + CommonFunctions.getUnapprovedWordId(unapprovedWord) + "']");
    session().getConfirmation();
    waitForPageToLoad();
    assertTrue(session().isTextPresent("Error: no word meaning selected!"));

    openLocation();
    waitForTextPresent(unapprovedWord);

    deletePreviouslyUploadedProjectAndUnapporvedWord(unapprovedWord);
  }

  @Test(groups = { "functionality", "upload" }, description = "remove word from database")
  public void testDeleteButton() throws Throwable {
    String unapprovedWord = "donaudampfschiffahrtselektrizitaetenhauptbetriebswerkbauunterbeamtengesellschaft" + CommonData.getRandomShortString();
    String response = projectUploader.upload(CommonData.getUploadPayload(unapprovedWord, "", "", "", "", "", "", ""));
    assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));

    openAdminLocation();
    session().click("aAdministrationTools");
    waitForPageToLoad();
    session().click("aAdminToolsApproveWords");
    waitForPageToLoad();
    assertTrue(session().isTextPresent(unapprovedWord));
    session().click("xpath=//input[@id='delete" + CommonFunctions.getUnapprovedWordId(unapprovedWord) + "']");
    session().getConfirmation();
    waitForPageToLoad();
    assertTrue(session().isTextPresent("The word was succesfully deleted!"));
    assertFalse(session().isTextPresent(unapprovedWord));

    openLocation();
    ajaxWait();
    waitForTextPresent(unapprovedWord);

    deletePreviouslyUploadedProjectAndUnapporvedWord(unapprovedWord);
  }

  private void deletePreviouslyUploadedProjectAndUnapporvedWord(String word) {
    projectUploader.remove(word);
    try {
      Driver driver = new Driver();
      DriverManager.registerDriver(driver);

      Connection connection = DriverManager.getConnection(Config.DB_HOST + Config.DB_NAME, Config.DB_USER, Config.DB_PASS);
      Statement statement = connection.createStatement();
      statement.executeUpdate("DELETE FROM wordlist WHERE word='" + word + "';");
      statement.close();
      connection.close();
      DriverManager.deregisterDriver(driver);
    } catch (SQLException e) {
      System.out.println("BadWordsFilterTests: deletePreviouslyUploadedProjectAndUnapporvedWord: SQL Exception couldn't execute sql query!");
    }
  }
}
