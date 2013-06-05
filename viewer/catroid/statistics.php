<?php
/*
 * Catroid: An on-device visual programming system for Android devices
 * Copyright (C) 2010-2013 The Catrobat Team
 * (<http://developer.catrobat.org/credits>)
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * An additional term exception under section 7 of the GNU Affero
 * General Public License, version 3, is available at
 * http://developer.catrobat.org/license_additional_term
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

?>

      <article>
        <header><?php echo $this->title; ?></header>
        <p><?php echo $this->languageHandler->getString("numberOfUsers", "<strong>" . $this->numberOfUsers . "</strong>"); ?></p>
        <p>Every user created on average <strong><?php echo $this->projectsPerUser; ?></strong> projects.</p>
        <table cellpadding="10" width="100%" border="1">
          <tr>
            <th>User</th><th>Number of Projects</th>
          </tr>
<?php foreach($this->usersWithMostProjects as $item) {
  echo "<tr><td><a href='" . BASE_PATH . "profile/" . $item['username'] . "'>" . $item['username'] . "</a></td><td align='right'>" . $item['projects'] . "</td></tr>";
}?>
        </table>
        <br />
        <table id="results" cellpadding="10" width="100%" border="1">
          <tr>
            <th>Projectname</th><th>Downloads</th>
          </tr>
        </table>
        <div>
          <button id="loadProjects">load Projects</button>
        </div>
      </article>
      <script type="text/javascript">
        $(document).ready(function() {
          var languageObject = {'downloads' : '<?php echo $this->languageHandler->getString("downloads"); ?>'};
          new Statistics(languageObject);
        });
      </script>

