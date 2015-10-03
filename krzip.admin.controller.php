<?php

class krzipAdminController extends krzip
{
    public function init()
    {
        // no-op
    }

    public function procKrzipAdminInsertConfig()
    {
        $args = Context::gets(
            'krzip_server_url',
            'krzip_map_provider',
            'krzip_address_format',
            'krzip_display_postcode',
            'krzip_display_address',
            'krzip_display_details',
            'krzip_display_extra_info',
            'krzip_display_jibeon_address',
            'krzip_postcode_format',
            'krzip_server_request_format',
            'krzip_require_exact_query',
            'krzip_use_full_jibeon'
        );
        
        if (!$args->krzip_display_postcode) $args->krzip_display_postcode = 'N';
        if (!$args->krzip_display_address) $args->krzip_display_address = 'N';
        if (!$args->krzip_display_details) $args->krzip_display_details = 'N';
        if (!$args->krzip_display_extra_info) $args->krzip_display_extra_info = 'N';
        if (!$args->krzip_display_jibeon_address) $args->krzip_display_jibeon_address = 'N';
        
        $oModuleController = getController('module');
        $output = $oModuleController->insertModuleConfig('krzip', $args);
        if (!$output->toBool()) return $output;
        
        $this->setMessage('success_registed');
        
        if (Context::get('success_return_url'))
        {
            $this->setRedirectUrl(Context::get('success_return_url'));
        }
        else
        {
            $this->setRedirectUrl(getNotEncodedUrl('', 'module', 'krzip', 'act', 'dispKrzipAdminConfig'));
        }
    }
}
