<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2011 The Catroid Team
 *    (<http://code.google.com/p/catroid/wiki/Credits>)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    An additional term exception under section 7 of the GNU Affero
 *    General Public License, version 3, is available at
 *    http://www.catroid.org/catroid/licenseadditionalterm
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>

<body>
  <script type="text/javascript">
      $(document).ready(function() {
        new AdminLanguageManagement();
      });
    </script>
  <h2>Administration Tools - Language Management</h2>
  <div>
    <p>supported languages:</p>
    <select id="supportedLanguageSelect">
    <?php
    $supportedLanguages = getSupportedLanguagesArray($this->languageHandler);
    foreach($supportedLanguages as $lang => $details) {
      if($details['supported'] && $lang != SITE_DEFAULT_LANGUAGE) {
        ?>
      <option value="<?php echo $lang?>">
      <?php echo $lang.' - '.$details['name'].' - '.$details['nameNative']?>
      </option>
      <?php }?>
      <?php }?>
    </select>
    <a href="javascript:;" id="doUpdateLink">update</a>
    <span id="doUpdateLoadingMessage" style="display: none;">updating -
      please wait..</span> 
    <br>
    <div id="adminAnswer" class="adminAnswerMessage"></div>
    <div id="adminError" class="adminAnswerError"></div>
    <br><br>
    <a id="aAdminToolsBackToTools" href="<?php echo BASE_PATH;?>admin/tools">&lt;- back</a>
  </div>
</body>
