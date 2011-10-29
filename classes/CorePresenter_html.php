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

class CorePresenter_html extends CorePresenterCommon {
    private $viewer = null;
    private $header = null;
    private $footer = null;
    private $data;
    private $languageHandler = null;

    public function __construct(CoreModule $module) {
        parent::__construct($module);
        $this->data = $this->module->getData();
        $this->languageHandler = $this->module->languageHandler;
    }

    public function display() {
        if($this->module->htmlFile == null) {
            $htmlFile = $this->module->name.'.php';
        } else {
            $htmlFile = $this->module->htmlFile;
        }
        $this->viewer = CORE_BASE_PATH.'viewer/'.$this->module->moduleName.'/'.$htmlFile;
        
        if($this->module->htmlHeaderFile == null) {
            $htmlHeaderFile = DEFAULT_HTML_HEADER_TEMPLATE_NAME;
        } else {
            $htmlHeaderFile = $this->module->htmlHeaderFile;
        }
        $this->header = CORE_BASE_PATH.'viewer/'.$this->module->moduleName.'/'.$htmlHeaderFile;
        
        if($this->module->htmlFooterFile == null) {
            $htmlFooterFile = DEFAULT_HTML_FOOTER_TEMPLATE_NAME;
        } else {
            $htmlFooterFile = $this->module->htmlFooterFile;
        }
        $this->footer = CORE_BASE_PATH.'viewer/'.$this->module->moduleName.'/'.$htmlFooterFile;
        
        if(!file_exists($this->viewer)) {
          return $this->module->errorHandler->showErrorPage('viewer', 'html_file_not_found', $this->viewer);
        }

        if($this->module->pageTemplateFile == null) {
            $pageTemplateFile = DEFAULT_HTML_TEMPLATE_NAME;
        } else {
            $pageTemplateFile = $this->module->pageTemplateFile;
        }
        
        header('Content-language: '.$this->languageHandler->getLanguage());
        include(CORE_BASE_PATH.'viewer/'.$this->module->moduleName.'/'.$pageTemplateFile);

        return true;
    }

    public function __get($property) {
        if(isset($this->data[$property])) {
            return $this->data[$property];
        }
        return null;
    }
}

?>