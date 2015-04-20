<?php

class krzipAdminView extends krzip
{
    public function init()
    {
        // no-op
    }
    
    public function dispKrzipAdminConfig()
    {
        Context::set('krzip_config', $this->getKrzipConfig());
        $this->setTemplatePath($this->module_path.'tpl');
        $this->setTemplateFile('config');
    }
}
