<?php

class krzipAdminView extends krzip
{
    public function init()
    {
        // no-op
    }
    
    public function dispKrzipAdminConfig()
    {
        $config = getModel('module')->getModuleConfig('krzip');
        if (!$config->krzip_server_url) $config->krzip_server_url = $this->freeapi_url;
        
        Context::set('config', $config);
        $this->setTemplatePath($this->module_path.'tpl');
        $this->setTemplateFile('config');
    }
}
