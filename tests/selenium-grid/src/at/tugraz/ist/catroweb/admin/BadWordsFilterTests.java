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

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.sql.Statement;

import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.Select;
import org.postgresql.Driver;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

@Test(groups = { "admin", "BadWordsFilterTests" })
public class BadWordsFilterTests extends BaseTest {

  @Test(groups = { "functionality", "upload" }, description = "approve an unapproved word as good")
  public void approveButtonGood() throws Throwable {
    try {
      String unapprovedWord = "badwordsfiltertestsapprovebuttongood" + CommonData.getRandomShortString(10);
      String response = projectUploader.upload(CommonData.getUploadPayload(unapprovedWord, "", "", "", "", "", ""));
      assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));

      openAdminLocation();
      driver().findElement(By.id("aAdministrationTools")).click();
      driver().findElement(By.id("aAdminToolsApproveWords")).click();
      assertTrue(isTextPresent(unapprovedWord));

      (new Select(driver().findElement(By.id("meaning" + CommonFunctions.getUnapprovedWordId(unapprovedWord))))).selectByVisibleText("good");
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("approve" + CommonFunctions.getUnapprovedWordId(unapprovedWord))).click();
      assertTrue(isTextPresent("The word was succesfully approved!"));

      assertProjectPresent(unapprovedWord);

      deletePreviouslyUploadedProjectAndUnapporvedWord(unapprovedWord);
    } catch(AssertionError e) {
      captureScreen("BadWordsFilterTests.approveButtonGood");
      throw e;
    } catch(Exception e) {
      captureScreen("BadWordsFilterTests.approveButtonGood");
      throw e;
    }
  }

  @Test(groups = { "functionality", "upload" }, description = "approve an unapproved word as bad")
  public void approveButtonBad() throws Throwable {
    try {
      String unapprovedWord = "badwordsfiltertestsapprovebuttonbad" + CommonData.getRandomShortString(10);
      String response = projectUploader.upload(CommonData.getUploadPayload(unapprovedWord, "", "", "", "", "", ""));
      assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));
      
      openAdminLocation();
      driver().findElement(By.id("aAdministrationTools")).click();
      driver().findElement(By.id("aAdminToolsApproveWords")).click();
      assertTrue(isTextPresent(unapprovedWord));
      (new Select(driver().findElement(By.id("meaning" + CommonFunctions.getUnapprovedWordId(unapprovedWord))))).selectByVisibleText("bad");
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("approve" + CommonFunctions.getUnapprovedWordId(unapprovedWord))).click();
      assertTrue(isTextPresent("The word was succesfully approved!"));

      assertProjectNotPresent(unapprovedWord);

      deletePreviouslyUploadedProjectAndUnapporvedWord(unapprovedWord);
    } catch(AssertionError e) {
      captureScreen("BadWordsFilterTests.approveButtonBad");
      throw e;
    } catch(Exception e) {
      captureScreen("BadWordsFilterTests.approveButtonBad");
      throw e;
    }
  }

  @Test(groups = { "functionality", "upload" }, description = "approve an unapproved word with no selection")
  public void approveButtonNoSelection() throws Throwable {
    try {
      String unapprovedWord = "badwordsfiltertestsapprovebuttonnoselection" + CommonData.getRandomShortString(10);
      String response = projectUploader.upload(CommonData.getUploadPayload(unapprovedWord, "", "", "", "", "", ""));
      assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));

      openAdminLocation();
      driver().findElement(By.id("aAdministrationTools")).click();
      driver().findElement(By.id("aAdminToolsApproveWords")).click();
      assertTrue(isTextPresent(unapprovedWord));
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("approve" + CommonFunctions.getUnapprovedWordId(unapprovedWord))).click();
      assertTrue(isTextPresent("Error: no word meaning selected!"));

      assertProjectPresent(unapprovedWord);

      deletePreviouslyUploadedProjectAndUnapporvedWord(unapprovedWord);
    } catch(AssertionError e) {
      captureScreen("BadWordsFilterTests.approveButtonNoSelection");
      throw e;
    } catch(Exception e) {
      captureScreen("BadWordsFilterTests.approveButtonNoSelection");
      throw e;
    }
  }

  @Test(groups = { "functionality", "upload" }, description = "remove word from database")
  public void testDeleteButton() throws Throwable {
    try {
      String unapprovedWord = "badwordsfilterteststestdeletebutton" + CommonData.getRandomShortString(10);
      String response = projectUploader.upload(CommonData.getUploadPayload(unapprovedWord, "", "", "", "", "", ""));
      assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));

      openAdminLocation();
      driver().findElement(By.id("aAdministrationTools")).click();
      driver().findElement(By.id("aAdminToolsApproveWords")).click();
      assertTrue(isTextPresent(unapprovedWord));
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("delete" + CommonFunctions.getUnapprovedWordId(unapprovedWord))).click();
      assertTrue(isTextPresent("The word was succesfully deleted!"));
      assertFalse(isTextPresent(unapprovedWord));

      assertProjectPresent(unapprovedWord);

      deletePreviouslyUploadedProjectAndUnapporvedWord(unapprovedWord);
    } catch(AssertionError e) {
      captureScreen("BadWordsFilterTests.testDeleteButton");
      throw e;
    } catch(Exception e) {
      captureScreen("BadWordsFilterTests.testDeleteButton");
      throw e;
    }
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
    } catch(SQLException e) {
      System.out.println("BadWordsFilterTests: deletePreviouslyUploadedProjectAndUnapporvedWord: SQL Exception couldn't execute sql query!");
    }
  }
}
