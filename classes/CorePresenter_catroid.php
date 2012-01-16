<?php
/*    Catroid: An on-device graphical programming language for Android devices
 *    Copyright (C) 2010-2012 The Catroid Team
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

  class CorePresenter_catroid extends CorePresenterCommon
  {
      public function __construct(CoreModule $module)
      {
          parent::__construct($module);
      }

      public function display()
      {
          $data = $this->module->getData();
          $file = CORE_BASE_PATH.PROJECTS_DIRECTORY.$data['source_file'];
          $filename = $data['file_name'];
          if(is_file($file)) {
            header("Content-type: application/zip");
            header('Content-Disposition: attachment; filename="'.urlencode(utf8_encode($filename)).PROJECTS_EXTENSION.'"');
            readfile($file);
          } else {
            $this->module->errorHandler->showErrorPage('download', 'file_not_found', $file);
            exit();
          }

          return true;
      }

      public function __destruct()
      {
          parent::__destruct();
      }
  }

?>
