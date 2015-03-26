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
        if (!$config->krzip_postcode_format) $config->krzip_postcode_format = 6;
        if (!$config->krzip_require_exact_query) $config->krzip_require_exact_query = 'N';
        if (!$config->krzip_use_full_jibeon) $config->krzip_use_full_jibeon = 'N';
        
    	if (!strncasecmp($args->krzip_server_url, '//api.poesis.kr/', 16))
    	{
    	    $args->krzip_server_url = 'https:' . $args->krzip_server_url;
    	}
    	
        Context::set('krzip_config', $config);
        $this->setTemplatePath($this->module_path.'tpl');
        $this->setTemplateFile('config');
    }
}
