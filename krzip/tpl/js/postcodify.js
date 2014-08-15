
jQuery(function() {
    
    // jQuery를 $로 사용할 수 있도록 한다.
    
    var $ = jQuery;
    
    // 무료 API 주소 기본값을 설정한다.
    
    var api_url = "//api.poesis.kr/post/search.php";
    var api_setup = false;
    var api_map_provider = "";
    var api_use_popup = "N";
    var api_use_full_jibeon = "N";
    
    // 현재 페이지가 주소 입력 화면인지 팝업창인지 파악한다.
    
    var target_document = $(document);
    var target_document_is_self = (target_document.find("#postcodify_search_button").size() > 0);
    
    // 주소 입력 화면인 경우 현재 페이지에서 설정을 읽어온다.
    
    if (target_document_is_self === true) {
        api_url = $("#postcodify_search_button").data("url");
        api_map_provider = $("#postcodify_search_button").data("map-provider");
        api_use_popup = $("#postcodify_search_button").data("popup");
        api_use_full_jibeon = $("#postcodify_search_button").data("use-full-jibeon");
    }
    
    // 팝업창인 경우 URL에서 설정을 읽어온다.
    
    else {
        target_document = $(window.opener.document);
        if ((api_url_match = /api=([^&?]+)/.exec(window.location.search))) {
            api_url = decodeURIComponent(api_url_match[1]);
        }
        if ((map_provider_match = /map=([^&?]+)/.exec(window.location.search))) {
            api_map_provider = decodeURIComponent(map_provider_match[1]);
        }
        if ((use_full_jibeon_match = /fjb=([^&?]+)/.exec(window.location.search))) {
            api_use_full_jibeon = decodeURIComponent(use_full_jibeon_match[1]);
        }
    }
    
    // Postcodify를 셋팅하는 함수.
    
    function setup_postcodify() {
        api_setup = true;
        if (!api_map_provider) {
        	$("#postcodify").addClass("nomap");
        }
        $("#postcodify").postcodify({
            api : api_url,
            insertPostcode6 : "#entry_postcode6",
            insertAddress : "#entry_address",
            insertDetails : "#entry_details",
            insertExtraInfo : "#entry_extra_info",
            insertEnglishAddress : "#entry_english_address",
            insertJibeonAddress : "#entry_jibeon_address",
            useFullJibeon : (api_use_full_jibeon === "Y"),
            focusDetails : false,
            hideSummary : true,
            mapLinkProvider : api_map_provider,
            afterSelect : function(selectedEntry) {
                target_document.find("#postcodify_addr1").val($("#entry_address").val());
                target_document.find("#postcodify_addr2").val("").focus();
                target_document.find("#postcodify_addr3").val($("#entry_extra_info").val());
                target_document.find("#postcodify_addr4").val($("#entry_postcode6").val());
                if (target_document_is_self) {
                    target_document.find("#postcodify_search_area").empty();
                } else {
                    window.open("", "_self", "");
                    window.close();
                }
            }
        });
    }
    
    // 현재 페이지가 주소 입력 화면인 경우...
    
    if (target_document_is_self) {
        
        // 이 화면에서 직접 검색을 수행하도록 설정되어 있는 경우, Postcodify 검색 스크립트를 로딩한다.
        
        if (api_use_popup !== "Y") {
            var script = $("<script></script>");
            script.attr("src", "//d1p7wdleee1q2z.cloudfront.net/post/search.min.js");
            script.insertBefore("#postcodify_search_area");
        }
        
        // 검색 단추 클릭 이벤트를 등록한다.
        
        $("#postcodify_search_button").click(function(e) {
            
            // 다른 이벤트 실행 금지...
            
            e.preventDefault();
            
            // 팝업창을 띄우도록 설정되어 있는 경우...
            
            if (api_use_popup === "Y") {
                var search_win_url = current_url.replace(/\/\?.*$/, '') + "/modules/krzip/tpl/popup.html";
                window.open(search_win_url + "?api=" + encodeURIComponent(api_url) + "&map=" +
                    encodeURIComponent(api_map_provider) + "&fjb=" + encodeURIComponent(api_use_full_jibeon),
                    "postcodify_popup", "width=680,height=540,resizable=yes,scrollbars=yes");
            }
            
            // 이 화면에서 직접 검색을 수행하도록 설정되어 있는 경우...
            
            else {
                $("#postcodify_search_area").slideDown();
                if (!api_setup) setup_postcodify();
            }
        });
    }
    
    // 현재 페이지가 팝업창인 경우...
    
    else {
        
        // common.php에서 검색창 내용을 가져와 문서에 삽입한 후 Postcodify를 셋팅한다.
        
        $.get("common.html", function(data) {
            $(data).insertAfter($("h1"));
            setup_postcodify();
        });
    }
    
});
