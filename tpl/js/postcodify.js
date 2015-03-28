
jQuery(function() {
    
    // jQuery를 $로 사용할 수 있도록 한다.
    
    var $ = jQuery;
    
    // Postcodify 팝업 레이어 플러그인을 로딩한다.
    
    var cdnPrefix = navigator.userAgent.match(/MSIE [56]\./) ? "http:" : "https:";
    $.getScript(cdnPrefix + "//cdn.poesis.kr/post/popup.min.js");
    
    // 검색 단추에 이벤트를 부착한다.
    
    $("button.postcodify_search_button").each(function() {
        
        // 초기화가 완료될 때까지 검색 버튼 클릭을 금지한다.
        
        var clickButton = $(this).data("initialized", "N");
        clickButton.on("click", function() {
            alert($(this).data("not-loaded-yet"));
        });
        
        // 부모 <div>의 크기에 따라 검색창의 크기를 조절한다.
        
        var container = $(this).parents("div.postcodify_address_area");
        var pcinput = container.find("input.postcodify.postcode");
        var inputs = container.find("input.postcodify").not(".postcode");
        var labels = container.find("label.postcodify").not(".postcode");
        var windowResizeCallback = function() {
            var width = container.width();
            pcinput.width(Math.min(206, width - 100));
            if (width < 360) {
                inputs.width(width - 16);
                labels.hide();
            } else {
                inputs.width(Math.min(540, width - 100));
                labels.show();
            }
        };
        $(window).resize(windowResizeCallback);
        windowResizeCallback();
        
        // 설정을 가져온다.
        
        var server_url = container.data("server-url");
        var map_provider = container.data("map-provider");
        var postcode_format = parseInt(container.data("postcode-format"), 10);
        var server_request_format = container.data("server-request-format");
        var require_exact_query = container.data("require-exact-query");
        var use_full_jibeon = container.data("use-full-jibeon");
        
        if (postcode_format == 5) {
            container.find("input.postcodify.postcode").addClass("postcodify_postcode5");
        } else {
            container.find("input.postcodify.postcode").addClass("postcodify_postcode6");
        }
        
        // 팝업 레이어 플러그인 로딩이 끝날 때까지 기다렸다가 셋팅한다.
        
        var waitInterval;
        waitInterval = setInterval(function() {
            if (typeof $.fn.postcodify === "undefined" || typeof $.fn.postcodifyPopUp === "undefined") {
                return;
            } else {
                clearInterval(waitInterval);
            }
            if (clickButton.data("initialized") !== "Y") {
                clickButton.data("initialized", "Y").off("click").postcodifyPopUp({
                    api : server_url,
                    inputParent : container,
                    mapLinkProvider : map_provider,
                    useCors : (server_request_format !== "JSONP"),
                    useFullJibeon : (use_full_jibeon === "Y"),
                    requireExactQuery : (require_exact_query === "Y"),
                    forceDisplayPostcode5 : (postcode_format == 5),
                    onSelect : function() {
                        container.find("div.postcodify_hidden_fields").show();
                        container.find("input.postcodify.postcode").removeAttr("readonly").off("click");
                    }
                });
            }
        }, 100);
        
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
