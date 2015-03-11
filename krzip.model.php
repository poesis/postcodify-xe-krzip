<?php

class krzipModel extends krzip
{
    public function init()
    {
        // no-op
    }
    
    public function getKrzipCodeList()
    {
        // no-op
    }
    
	// 기존 krzip 모듈이나 구버전 모듈이 저장한 값이 있을 경우 안정화 버전의 포맷에 맞추어 변환한다
	
	public function getKrzipStandardFormat($values)
	{
		// 배열 키를 정리한다
		
		$values = array_values($values);
		
		// Postcodify 안정화 버전의 포맷인 경우 그대로 반환한다
		
		if (is_array($values) && count($values) >= 4 && preg_match('/^[0-9a-z\x20-]{5,10}$/i', trim($values[0])))
		{
			return $values;
		}
		
		// Postcodify 구 버전의 포맷인 경우 순서를 바꾸어 반환한다
		
		if (is_array($values) && count($values) == 4 && preg_match('/^[0-9a-z\x20-]{5,10}$/i', trim($values[3])))
		{
			return array_map('trim', array($values[3], $values[0], $values[1], $values[2]));
		}
		
		// Postcodify 안정화 버전의 포맷이지만 XML 처리 과정에서 상세주소가 누락된 경우 상세주소를 채워서 반환한다
		
		if (is_array($values) && count($values) == 3 && preg_match('/^[0-9a-z\x20-]{5,10}$/i', trim($values[0])) && preg_match('/^\(.+\)$/', trim($values[2])))
		{
			return array_map('trim', array($values[0], $values[1], '', $values[2]));
		}
		
		// 기존 krzip 모듈 (#17ed81e 이후)
		
		if (is_array($values) && count($values) == 3 && preg_match('/^(.*)\(([0-9]{3}-[0-9]{3})\)\s*$/', $values[2], $matches))
		{
			$postcode = $matches[2];
			$values[2] = preg_replace('/,\s*\)/', ')', $matches[1]);
			return array_map('trim', array($postcode, $values[0], $values[1], $values[2]));
		}
		
		// 기존 krzip 모듈 (#3a932f6 이전)
		
		if (is_array($values) && count($values) == 2 && preg_match('/^(.*)\(([0-9]{3}-[0-9]{3})\)\s*$/', $values[0], $matches))
		{
			$postcode = $matches[2];
			$values[0] = $matches[1];
			if (preg_match('/^(.*)(\(.+\))\s*$/', $values[0], $exmatches))
			{
				$values[2] = preg_replace('/,\s*\)/', ')', $exmatches[2]);
				$values[0] = $exmatches[1];
			}
			else
			{
				$values[2] = '';
			}
			return array_map('trim', array($postcode, $values[0], $values[1], $values[2]));
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
		
		return array($postcode, $address, $details, $extra_info);
	}
    
    // 우편번호 검색 폼 HTML을 생성하여 반환한다
    
    public function getKrzipCodeSearchHtml($column_name, $values)
    {
        $config = getModel('module')->getModuleConfig('krzip');
        $server_url = $config->krzip_server_url ? $config->krzip_server_url : $this->freeapi_url;
        $map_provider = strval($config->krzip_map_provider);
        $postcode_format = $config->krzip_postcode_format == 5 ? 5 : 6;
        $require_exact_query = $config->krzip_require_exact_query == 'Y' ? 'Y' : 'N';
        $use_full_jibeon = $config->krzip_use_full_jibeon == 'Y' ? 'Y' : 'N';
    	
    	$values = $this->getKrzipStandardFormat($values);
    	foreach ($values as &$value)
    	{
    		$value = htmlspecialchars($value, ENT_COMPAT, 'UTF-8', false);
    	}
    	
        $krzip_config = new stdClass();
        $krzip_config->column_name = $column_name;
        $krzip_config->values = $values;
        $krzip_config->instance_id = ++self::$instance_sequence;
        $krzip_config->server_url = $server_url;
        $krzip_config->map_provider = $map_provider;
        $krzip_config->postcode_format = $postcode_format;
        $krzip_config->require_exact_query = $require_exact_query;
        $krzip_config->use_full_jibeon = $use_full_jibeon;
        Context::set('krzip', $krzip_config);
        
        $oTemplate = &TemplateHandler::getInstance();
        return $oTemplate->compile($this->module_path.'tpl', 'search');
    }
}
