<?php

class krzip extends ModuleObject
{
    public static $instance_sequence = 0;
    
    public function getKrzipConfig()
    {
        $defaults = array(
            'server_url' => 'https://api.poesis.kr/post/search.php',
            'map_provider' => '',
            'address_format' => 'postcodify',
            'postcode_format' => 6,
            'server_request_format' => 'CORS',
            'require_exact_query' => 'N',
            'use_full_jibeon' => 'N',
        );
        
        $config = getModel('module')->getModuleConfig('krzip');
        
        foreach ($defaults as $key => $value)
        {
            if (!$config->{'krzip_' . $key})
            {
                $config->{'krzip_' . $key} = $value;
            }
        }
        
        if ($config->krzip_server_url === substr($defaults['server_url'], 6))
        {
            $args->krzip_server_url = $defaults['server_url'];
        }
    	
        return $config;
    }
    
    public function moduleInstall()
    {
        return new Object();
    }
    
    public function checkUpdate()
    {
        return false;
    }
    
    public function moduleUpdate()
    {
        return new Object(0, 'success_updated');
    }
    
    public function recompileCache()
    {
        // no-op
    }
}
