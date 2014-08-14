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
        if (!$config->krzip_use_popup) $config->krzip_use_popup = 'N';
        
        Context::set('krzip_config', $config);
        $this->setTemplatePath($this->module_path.'tpl');
        $this->setTemplateFile('config');
    }
}
