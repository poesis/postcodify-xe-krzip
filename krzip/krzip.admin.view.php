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
        if (!$config->krzip_plugin_url) $config->krzip_plugin_url = $this->plugin_url;
        if (!$config->krzip_postcode_format) $config->krzip_postcode_format = 6;
        if (!$config->krzip_require_exact_query) $config->krzip_require_exact_query = 'N';
        if (!$config->krzip_use_full_jibeon) $config->krzip_use_full_jibeon = 'N';
        
        Context::set('krzip_config', $config);
        $this->setTemplatePath($this->module_path.'tpl');
        $this->setTemplateFile('config');
    }
}
