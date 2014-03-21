/**
  *Catroid: An on-device visual programming system for Android devices
  *Copyright (C) 2010-2014 The Catrobat Team
  *(<http://developer.catrobat.org/credits>)
  *
  *This program is free software: you can redistribute it and/or modify
  *it under the terms of the GNU Affero General Public License as
  *published by the Free Software Foundation, either version 3 of the
  *License, or (at your option) any later version.
  *
  *An additional term exception under section 7 of the GNU Affero
  *General Public License, version 3, is available at
  *http://developer.catrobat.org/license_additional_term
  *
  *This program is distributed in the hope that it will be useful,
  *but WITHOUT ANY WARRANTY; without even the implied warranty of
  *MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  *GNU Affero General Public License for more details.
  *
  *You should have received a copy of the GNU Affero General Public License
  *along with this program. If not, see <http://www.gnu.org/licenses/>.
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
  
  @Test(groups = { "functionality", "upload" }, description = "check the scrolling function from unapproved word")
  public void checkScrollingFunction() throws Throwable {
    deleteAllUnapprovedWords();
    try {
      String response = "";
      String [] unapprovedWord = new String[20];
      for (int i = 0; i < 20; i++) {
        unapprovedWord[i] = "badwordfiltertestsscrollingfunction" + CommonData.getRandomShortString(10);
        response = projectUploader.upload(CommonData.getUploadPayload(unapprovedWord[i], "", "", "", "", "", "", ""));
        assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));
      }
      
      openAdminLocation();
      driver().findElement(By.id("aAdministrationTools")).click();
      ajaxWait();
      driver().findElement(By.id("aAdminToolsApproveWords")).click();
      ajaxWait();   
      driver().findElement(By.id("Projects10")).click();
      ajaxWait();
      assertTrue(isElementPresent(By.id("site2")));
      assertTrue(isElementPresent(By.id("greaterThen")));
      
      for (int i = 19; i > 9; i--) {
        assertTrue(isTextPresent(unapprovedWord[i]));
      }
      
      assertTrue(!isTextPresent(unapprovedWord[9]));
      assertTrue(!isTextPresent(unapprovedWord[0]));
      
      for (int i = 0; i < 20; i++) {
        deletePreviouslyUploadedProjectAndUnapprovedWord(unapprovedWord[i]);
      }
      
    } catch(AssertionError e) {
      captureScreen("BadWordsFilterTests.checkScrollingFunction");
      throw e;
    } catch(Exception e) {
      captureScreen("BadWordsFilterTests.checkScrollingFunction");
      throw e;
    }
  }

  @Test(groups = { "functionality", "upload" }, description = "approve an unapproved word as good") 
  public void approveButtonGood() throws Throwable {
    deleteAllUnapprovedWords();
    try {
      String unapprovedWord = "badwordsfiltertestsapprovebuttongood" + CommonData.getRandomShortString(10);
      String response = projectUploader.upload(CommonData.getUploadPayload(unapprovedWord, "", "", "", "", "", "", ""));
      assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));

      openAdminLocation();
      driver().findElement(By.id("aAdministrationTools")).click();
      ajaxWait();
      driver().findElement(By.id("aAdminToolsApproveWords")).click();
      ajaxWait();
      assertTrue(isTextPresent(unapprovedWord));

      (new Select(driver().findElement(By.id("meaning" + CommonFunctions.getUnapprovedWordId(unapprovedWord))))).selectByVisibleText("good");
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("approve" + CommonFunctions.getUnapprovedWordId(unapprovedWord))).click();
      assertTrue(isTextPresent("The word was succesfully approved!"));

      assertProjectPresent(unapprovedWord);

      deletePreviouslyUploadedProjectAndUnapprovedWord(unapprovedWord);
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
    deleteAllUnapprovedWords();
    try {
      String unapprovedWord = "badwordsfiltertestsapprovebuttonbad" + CommonData.getRandomShortString(10);
      String response = projectUploader.upload(CommonData.getUploadPayload(unapprovedWord, "", "", "", "", "", "", ""));
      assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));
      
      openAdminLocation();
      driver().findElement(By.id("aAdministrationTools")).click();
      ajaxWait();
      driver().findElement(By.id("aAdminToolsApproveWords")).click();
      ajaxWait();
      driver().findElement(By.id("allProjects")).click();
      ajaxWait();
      assertTrue(isTextPresent(unapprovedWord));
      (new Select(driver().findElement(By.id("meaning" + CommonFunctions.getUnapprovedWordId(unapprovedWord))))).selectByVisibleText("bad");
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("approve" + CommonFunctions.getUnapprovedWordId(unapprovedWord))).click();
      assertTrue(isTextPresent("The word was succesfully approved!"));

      assertProjectNotPresent(unapprovedWord);

      deletePreviouslyUploadedProjectAndUnapprovedWord(unapprovedWord);
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
	  deleteAllUnapprovedWords();
    try {
      String unapprovedWord = "badwordsfiltertestsapprovebuttonnoselection" + CommonData.getRandomShortString(10);
      String response = projectUploader.upload(CommonData.getUploadPayload(unapprovedWord, "", "", "", "", "", "", ""));
      assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));

      openAdminLocation();
      driver().findElement(By.id("aAdministrationTools")).click();
      ajaxWait();
      driver().findElement(By.id("aAdminToolsApproveWords")).click();
      assertTrue(isTextPresent(unapprovedWord));
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("approve" + CommonFunctions.getUnapprovedWordId(unapprovedWord))).click();
      assertTrue(isTextPresent("Error: no word meaning selected!"));

      assertProjectPresent(unapprovedWord);

      deletePreviouslyUploadedProjectAndUnapprovedWord(unapprovedWord);
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
	  deleteAllUnapprovedWords();
    try {
      String unapprovedWord = "badwordsfilterteststestdeletebutton" + CommonData.getRandomShortString(10);
      String response = projectUploader.upload(CommonData.getUploadPayload(unapprovedWord, "", "", "", "", "", "", ""));
      assertEquals("200", CommonFunctions.getValueFromJSONobject(response, "statusCode"));

      openAdminLocation();
      driver().findElement(By.id("aAdministrationTools")).click();
      ajaxWait();
      driver().findElement(By.id("aAdminToolsApproveWords")).click();
      assertTrue(isTextPresent(unapprovedWord));
      clickOkOnNextConfirmationBox();
      driver().findElement(By.id("delete" + CommonFunctions.getUnapprovedWordId(unapprovedWord))).click();
      assertTrue(isTextPresent("The word was succesfully deleted!"));
      assertFalse(isTextPresent(unapprovedWord));

      assertProjectPresent(unapprovedWord);

      deletePreviouslyUploadedProjectAndUnapprovedWord(unapprovedWord);
    } catch(AssertionError e) {
      captureScreen("BadWordsFilterTests.testDeleteButton");
      throw e;
    } catch(Exception e) {
      captureScreen("BadWordsFilterTests.testDeleteButton");
      throw e;
    }
  }

  private void deleteAllUnapprovedWords() {
	    try {
	      Driver driver = new Driver();
	      DriverManager.registerDriver(driver);
	      Connection connection = DriverManager.getConnection(Config.DB_HOST + Config.DB_NAME, Config.DB_USER, Config.DB_PASS);
	      Statement statement = connection.createStatement();
	      statement.executeUpdate("delete from wordlist where approved != true;");
	      statement.close();
	      connection.close();
	      DriverManager.deregisterDriver(driver);
	    } catch(SQLException e) {
	      System.out.println("BadWordsFilterTests: deleteAllUnapprovedWords: SQL Exception couldn't execute sql query!");
	    }
	  }

  
  private void deletePreviouslyUploadedProjectAndUnapprovedWord(String word) {
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
