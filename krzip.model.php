<?php

class krzipModel extends krzip
{
    // 공식 모듈 및 구 버전 모듈과의 호환성을 위해 존재하는 메소드들
    
    public function init()
    {
        // no-op
    }
    
    public function getKrzipCodeList()
    {
        // no-op
    }
    
    public function getKrzipStandardFormat($values)
    {
        return $this->convertDataFormat($values);
    }
    
    public function getMigratedPostcode($values)
    {
        return $this->convertDataFormat($values);
    }
    
    // 기존 krzip 모듈이나 구버전 모듈이 저장한 값이 있을 경우 안정화 버전의 포맷에 맞추어 변환한다
    
    public function convertDataFormat($values)
    {
        // 배열 키를 정리한다
        
        $values = array_map('trim', array_values($values));
        
        // Postcodify 또는 새 krzip 모듈의 포맷을 인식한다
        
        if (is_array($values) && count($values))
        {
            // 반환할 배열을 준비한다
            
            $result = array(null, null, null, null, null);
            
            // 우편번호를 인식한다
            
            if (preg_match('/^([0-9]{5}|[0-9]{3}-[0-9]{3})$/i', trim($values[count($values) - 1])))
            {
                $result[0] = array_pop($values);
            }
            elseif (preg_match('/^([0-9]{5}|[0-9]{3}-[0-9]{3})$/i', trim($values[0])))
            {
                $result[0] = array_shift($values);
            }
            
            // 포맷 인식에 성공한 경우
            
            if ($result[0] !== null)
            {
                // 도로명주소를 인식한다
                
                if (preg_match('/^((?:서울|부산|인천|대[전구]|광주|울산|세종|경[기상남북]|전[라남북]|충[청남북]|강원|제주)[가-힣]*)\s+([가-힣]+)\s+/u', $values[0], $matches))
                {
                    $result[1] = array_shift($values);
                    
                    // 지번주소를 인식한다
                    
                    if (strpos($matches[1], '세종') === 0) $matches[2] = '';
                    if (preg_match('/^' . $matches[1] . '\s+' . $matches[2] . '.+/', $values[count($values) - 1]))
                    {
                        $result[4] = array_pop($values);
                    }
                    elseif (preg_match('/^\(' . $matches[1] . '\s+' . $matches[2] . '.+\)/', $values[0]))
                    {
                        $result[4] = trim(array_shift($values), ' ()');
                    }
                }
                
                // 참고항목을 인식한다
                
                if (preg_match('/^\([가-힣0-9]+[동리가로](,.*)?\)$/u', $values[count($values) - 1]))
                {
                    $result[3] = array_pop($values);
                }
                
                // 남은 항목들은 상세주소로 취급한다
                
                foreach ($values as $value)
                {
                    if ($result[1] === null && preg_match('/^((?:서울|부산|인천|대[전구]|광주|울산|세종|경[기상남북]|전[라남북]|충[청남북]|강원|제주)[가-힣]*)\s+([가-힣]+)\s+/u', $value, $matches))
                    {
                        $result[1] = $value;
                    }
                    elseif ($result[3] === null && preg_match('/^\([가-힣0-9]+[동리가로](,.*)?\)$/u', $value))
                    {
                        $result[3] = $value;
                    }
                    else
                    {
                        $result[2] = ($result[2] === null) ? $value : ($result[2] . ' ' . $value);
                    }
                }
                
                // 결과를 반환한다
                
                if ($result[1] !== null || $result[2] !== null || $result[3] !== null || $result[4] !== null)
                {
                    return $result;
                }
            }
        }
        
        // 기존 krzip 모듈 (#17ed81e 이후)
        
        if (is_array($values) && count($values) == 3 && preg_match('/^(.*)\(([0-9]{5}|[0-9]{3}-[0-9]{3})\)\s*$/', $values[2], $matches))
        {
            $postcode = $matches[2];
            $values[2] = preg_replace('/,\s*\)/', ')', $matches[1]);
            return array($postcode, $values[0], $values[1], $values[2], null);
        }
        
        // 기존 krzip 모듈 (#3a932f6 이전)
        
        if (is_array($values) && count($values) == 2 && preg_match('/^(.*)\(([0-9]{5}|[0-9]{3}-[0-9]{3})\)\s*$/', $values[0], $matches))
        {
            $postcode = $matches[2];
            $values[0] = $matches[1];
            if (preg_match('/^(.*)(\(.+\))\s*$/', $values[0], $exmatches))
            {
                $values[2] = preg_replace('/,\s*\)/', ')', $exmatches[2]);
                $values[0] = trim($exmatches[1]);
            }
            else
            {
                $values[2] = '';
            }
            return array($postcode, $values[0], $values[1], $values[2], null);
        }
        
        // 그 밖의 주소는 일정한 규칙에 따라 각각의 구성요소를 분리한다
        
        if (is_array($values)) $values = implode(' ', $values);
        $address = trim(preg_replace('/\s+/', ' ', $values));
        
        if (preg_match('/\(([0-9]{3}-[0-9]{3})\)/', $address, $matches))
        {
            $address = trim(preg_replace('/\s+/', ' ', str_replace($matches[0], '', $address)));
            $postcode = $matches[1];
        }
        elseif (preg_match('/^([0-9]{3}-[0-9]{3})\s/', $address, $matches))
        {
            $address = trim(preg_replace('/\s+/', ' ', str_replace($matches[0], '', $address)));
            $postcode = $matches[1];
        }
        else
        {
            $postcode = '';
        }
        
        if (preg_match('/\(.+[동리](?:,.*)?\)/u', $address, $matches))
        {
            $address = trim(preg_replace('/\s+/', ' ', str_replace($matches[0], '', $address)));
            $extra_info = $matches[0];
        }
        else
        {
            $extra_info = '';
        }
        
        if (preg_match('/^(.+ [가-힝]+[0-9]*[동리로길]\s*[0-9-]+(?:번지?)?),?\s+(.+)$/u', $address, $matches))
        {
            $address = trim($matches[1]);
            $details = trim($matches[2]);
        }
        else
        {
            $details = '';
        }
        
        return array($postcode, $address, $details, $extra_info, null);
    }
    
    // 우편번호 검색 폼 HTML을 생성하여 반환한다
    
    public function getKrzipCodeSearchHtml($column_name, $values)
    {
        $config = $this->getKrzipConfig();
        
        $values = $this->convertDataFormat($values);
        if ($config->krzip_address_format === 'newkrzip' && strlen($values[4]) > 0)
        {
        	$values[4] = '(' . $values[4] . ')';
        }
        foreach ($values as &$value)
        {
            $value = htmlspecialchars($value, ENT_COMPAT, 'UTF-8', false);
        }
        if (!strncasecmp($server_url, 'https://api.poesis.kr/', 22) && preg_match('/MSIE [56]\./', $_SERVER['HTTP_USER_AGENT']))
        {
            $server_url = 'http:' . substr($server_url, 6);
        }
        
        $krzip_config = new stdClass();
        $krzip_config->column_name = $column_name;
        $krzip_config->values = $values;
        $krzip_config->instance_id = ++self::$instance_sequence;
        $krzip_config->server_url = $config->krzip_server_url;
        $krzip_config->map_provider = $config->krzip_map_provider;
        $krzip_config->address_format = $config->krzip_address_format;
        $krzip_config->display_postcode = $config->krzip_display_postcode;
        $krzip_config->display_address = $config->krzip_display_address;
        $krzip_config->display_details = $config->krzip_display_details;
        $krzip_config->display_extra_info = $config->krzip_display_extra_info;
        $krzip_config->display_jibeon_address = $config->krzip_display_jibeon_address;
        $krzip_config->postcode_format = $config->krzip_postcode_format;
        $krzip_config->server_request_format = $config->krzip_server_request_format;
        $krzip_config->require_exact_query = $config->krzip_require_exact_query;
        $krzip_config->use_full_jibeon = $config->krzip_use_full_jibeon;
        Context::set('krzip', $krzip_config);
        
        $oTemplate = &TemplateHandler::getInstance();
        return $oTemplate->compile($this->module_path.'tpl', 'search');
    }
}
