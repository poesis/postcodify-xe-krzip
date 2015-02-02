
jQuery(function() {
    
    // jQuery를 $로 사용할 수 있도록 한다.
    
    var $ = jQuery;
    
    // 검색 단추에 이벤트를 부착한다.
    
    $("button.postcodify_search_button").each(function() {
        
        // 부모 <div>의 크기에 따라 검색창의 크기를 조절한다.
        
        var container = $(this).parents("div.postcodify_address_area");
        var width = container.width();
        if (width < 700) {
            container.find("input.postcodify").not(".postcode").each(function() {
                $(this).width(width - 100);
            });
        }
        
        // 설정을 가져온다.
        
        var url = $(this).data("url");
        var map_provider = $(this).data("map-provider");
        var postcode_format = parseInt($(this).data("postcode-format"), 10);
        var require_exact_query = $(this).data("require-exact-query");
        var use_full_jibeon = $(this).data("use-full-jibeon");
        
        if (postcode_format == 5) {
            container.find("input.postcodify.postcode").addClass("postcodify_postcode5");
        } else {
            container.find("input.postcodify.postcode").addClass("postcodify_postcode6");
        }
        
        // 팝업 레이어 플러그인을 셋팅한다.
        
        $(this).postcodifyPopUp({
            api : url,
            inputParent : container,
            mapLinkProvider : map_provider,
            useFullJibeon : (use_full_jibeon === "Y"),
            requireExactQuery : (require_exact_query === "Y"),
            forceDisplayPostcode5 : (postcode_format == 5),
            onSelect : function() {
                container.find("div.postcodify_hidden_fields").show();
                container.find("input.postcodify.postcode").removeAttr("readonly");
            }
        });
    });
});
