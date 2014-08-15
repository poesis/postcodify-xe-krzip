<?php

class krzipModel extends krzip
{
    public function init()
    {
        // no-op
    }
    
    public function getKrzipCodeSearchHtml($column_name, $values)
    {
        if (!is_array($values)) $values = array($values);
        while (count($values) < 4) $values[] = null;
        
        if ($values[3] === null && preg_match('/\(([0-9]{3}-[0-9]{3})\)\s*$/', $values[0], $matches))
        {
            $values[3] = $matches[1];
            $values[0] = trim(str_replace($matches[0], '', $values[0]));
        }
        if ($values[2] === null && ($parenpos = strpos($values[0], '(')) !== false)
        {
            $values[2] = trim(str_replace(', )', ')', substr($values[0], $parenpos)));
            $values[0] = trim(substr($values[0], 0, $parenpos));
        }
        
        $config = getModel('module')->getModuleConfig('krzip');
        $url = $config->krzip_server_url ? $config->krzip_server_url : $this->freeapi_url;
        $map_provider = strval($config->krzip_map_provider);
        $popup = $config->krzip_use_popup == 'Y' ? 'Y' : 'N';
        $use_full_jibeon = $config->krzip_use_full_jibeon == 'Y' ? 'Y' : 'N';
        
        $krzip_config = new stdClass();
        $krzip_config->column_name = $column_name;
        $krzip_config->values = $values;
        $krzip_config->url = $url;
        $krzip_config->map_provider = $map_provider;
        $krzip_config->popup = $popup;
        $krzip_config->use_full_jibeon = $use_full_jibeon;
        Context::set('krzip', $krzip_config);
        
        $oTemplate = &TemplateHandler::getInstance();
        return $oTemplate->compile($this->module_path.'tpl', 'search');
    }
    
    public function getKrzipCodeList()
    {
        // no-op
    }
}
