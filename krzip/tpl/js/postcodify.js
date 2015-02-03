
// Postcodify 플러그인 및 팝업 레이어 스크립트를 로딩한다.

var cdnPrefix = navigator.userAgent.match(/MSIE [5-7]\./) ? "http://cdn.poesis.kr" : "//cdn.poesis.kr";
document.write('<script type="text/javascript" src="' + cdnPrefix + '/post/search.min.js"></script>');
document.write('<script type="text/javascript" src="' + cdnPrefix + '/post/popup.min.js"></script>');

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
        
        var server_url = container.data("server-url");
        var map_provider = container.data("map-provider");
        var postcode_format = parseInt(container.data("postcode-format"), 10);
        var require_exact_query = container.data("require-exact-query");
        var use_full_jibeon = container.data("use-full-jibeon");
        
        if (postcode_format == 5) {
            container.find("input.postcodify.postcode").addClass("postcodify_postcode5");
        } else {
            container.find("input.postcodify.postcode").addClass("postcodify_postcode6");
        }
        
        // 팝업 레이어 플러그인을 셋팅한다.
        
        $(this).postcodifyPopUp({
            api : server_url,
            inputParent : container,
            mapLinkProvider : map_provider,
            useFullJibeon : (use_full_jibeon === "Y"),
            requireExactQuery : (require_exact_query === "Y"),
            forceDisplayPostcode5 : (postcode_format == 5),
            onSelect : function() {
                container.find("div.postcodify_hidden_fields").show();
                container.find("input.postcodify.postcode").removeAttr("readonly").off("click");
            }
        });
        
        // 주소를 처음 입력하는 경우 우편번호 입력란을 클릭하면 자동으로 팝업 레이어가 나타나도록 한다.
        
        container.find("input.postcodify.postcode").each(function() {
        	if ($(this).attr("readonly")) {
        		$(this).on("click", function() {
        			container.find("button.postcodify_search_button").click();
        		});
        	}
        });
    });
});
