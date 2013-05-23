/**
  *Catroid: An on-device visual programming system for Android devices
  *Copyright (C) 2010-2013 The Catrobat Team
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

package at.tugraz.ist.catroweb.catroid;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.sql.Statement;

import org.openqa.selenium.By;
import org.postgresql.Driver;
import org.testng.annotations.DataProvider;
import org.testng.annotations.Test;
import static org.testng.AssertJUnit.*;

import at.tugraz.ist.catroweb.BaseTest;
import at.tugraz.ist.catroweb.common.*;

@Test(groups = { "non-parallelizable", "BlockedIpTests" })
public class BlockedIpTests extends BaseTest {

  @Test(dataProvider = "blockedIps", groups = { "functionality" }, description = "test blocked ips")
  public void blockedIps(String projectId, String blockedIp) throws Throwable {
    try {
    	unblockAllIPs();
      openLocation();
      blockIp(blockedIp);
      openLocation("details/" + projectId);
      ajaxWait();
      assertTrue(containsElementText(By.xpath("//*[@id='wrapper']/article/div"), "Error".toUpperCase()));
      assertTrue(isTextPresent("Your IP-Address has been blocked."));

      openLocation();
      ajaxWait();
      assertFalse(isTextPresent("Your IP-Address has been blocked."));
      unblockIp(blockedIp);
    } catch(AssertionError e) {
      captureScreen("BlockedIpTests.blockedIps." + blockedIp);
      throw e;
    } catch(Exception e) {
      captureScreen("BlockedIpTests.blockedIps." + blockedIp);
      throw e;
    }
  }

  @Test(dataProvider = "unblockedIps", dependsOnMethods = { "blockedIps" }, groups = { "functionality" }, description = "test unblocked ips")
  public void unblockedIps(String projectId, String unblockedIp) throws Throwable {
    try {
    	unblockAllIPs();
      blockIp(unblockedIp);
      openLocation("details/" + projectId);
      assertFalse(containsElementText(By.xpath("//*[@id='wrapper']/article/div"), "Error".toUpperCase()));
      assertFalse(isTextPresent("Your IP-Address has been blocked."));

      openLocation();
      assertFalse(containsElementText(By.xpath("//*[@id='wrapper']/article/div"), "Error".toUpperCase()));
      assertFalse(isTextPresent("Your IP-Address has been blocked."));
      unblockIp(unblockedIp);
    } catch(AssertionError e) {
      captureScreen("BlockedIpTests.unblockedIps." + unblockedIp);
      throw e;
    } catch(Exception e) {
      captureScreen("BlockedIpTests.unblockedIps." + unblockedIp);
      throw e;
    }
  }

  @DataProvider(name = "blockedIps")
  public Object[][] blockedIpsData() {
    Object[][] returnArray = new Object[][] { { "1", "127.0.0.1" }, { "1", "127.0.0." }, { "1", "127.0." }, { "1", "127." } };
    return returnArray;
  }

  @DataProvider(name = "unblockedIps")
  public Object[][] unblockedIpsData() {
    Object[][] returnArray = new Object[][] { { "1", "127.0.0.2" }, { "1", "127.12.0." }, { "1", "127.12." }, { "1", "129.0.0.1" } };
    return returnArray;
  }

  private void blockIp(String ip) {
    try {
      Driver driver = new Driver();
      DriverManager.registerDriver(driver);

      Connection connection = DriverManager.getConnection(Config.DB_HOST + Config.DB_NAME, Config.DB_USER, Config.DB_PASS);
      Statement statement = connection.createStatement();
      statement.executeUpdate("INSERT INTO blocked_ips(ip_address) " + "VALUES ('" + ip + "')");
      statement.close();
      connection.close();
      DriverManager.deregisterDriver(driver);
    } catch(SQLException e) {
      System.out.println("BlockedIpTests: blockIp: SQL Exception couldn't execute sql query!");
      System.out.println(e.getMessage());
    }
  }

  private void unblockIp(String ip) {
    try {
      Driver driver = new Driver();
      DriverManager.registerDriver(driver);

      Connection connection = DriverManager.getConnection(Config.DB_HOST + Config.DB_NAME, Config.DB_USER, Config.DB_PASS);
      Statement statement = connection.createStatement();
      statement.executeUpdate("DELETE FROM blocked_ips WHERE ip_address='" + ip + "';");
      statement.close();
      connection.close();
      DriverManager.deregisterDriver(driver);
    } catch(SQLException e) {
      System.out.println("BlockedIpTests: unblockIp: SQL Exception couldn't execute sql query!");
      System.out.println(e.getMessage());
    }
  }
  
 private void unblockAllIPs() {
	    try {
	      Driver driver = new Driver();
	      DriverManager.registerDriver(driver);

	      Connection connection = DriverManager.getConnection(Config.DB_HOST + Config.DB_NAME, Config.DB_USER, Config.DB_PASS);
	      Statement statement = connection.createStatement();
	      statement.executeUpdate("DELETE FROM blocked_ips;");
	      statement.close();
	      connection.close();
	      DriverManager.deregisterDriver(driver);
	    } catch(SQLException e) {
	      System.out.println("BlockedIpTests: unblockAllIPs: SQL Exception couldn't execute sql query!");
	      System.out.println(e.getMessage());
	    }
	  }
  }
