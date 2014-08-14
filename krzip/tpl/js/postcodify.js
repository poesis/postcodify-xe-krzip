
jQuery(function() {
    
    var $ = jQuery;
    var api_url = "//api.poesis.kr/post/search.php";
    var target_document = $(document);
    var target_document_is_self = (target_document.find("#postcodify_search_button").size() > 0);
    if (target_document_is_self === false) {
        target_document = $(window.opener.document);
    }
    
    function setup_postcodify() {
        $("#postcodify").postcodify({
            api : api_url,
            insertPostcode6 : "#entry_postcode6",
            insertAddress : "#entry_address",
            insertDetails : "#entry_details",
            insertExtraInfo : "#entry_extra_info",
            insertEnglishAddress : "#entry_english_address",
            insertJibeonAddress : "#entry_jibeon_address",
            useFullJibeon : false,
            focusDetails : false,
            hideSummary : true,
            mapLinkProvider : "naver",
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
    
    if (target_document_is_self) {
        api_url = $("#postcodify_search_button").data("url");
        if ($("#postcodify_search_button").data("popup") !== "Y") {
            var script = $("<script></script>");
            script.attr("src", "//d1p7wdleee1q2z.cloudfront.net/post/search.min.js");
            script.insertBefore("#postcodify_search_area");
        }
        $("#postcodify_search_button").click(function(e) {
            e.preventDefault();
            var search_win_url = current_url.replace(/\/\?.*$/, '') + "/modules/krzip/tpl/popup.html";
            if ($(this).data("popup") == "Y") {
                window.open(search_win_url + "?api=" + encodeURIComponent($(this).data("url")),
                    "postcodify_popup", "width=680,height=540,resizable=yes,scrollbars=yes");
            } else {
                $("#postcodify_search_area").load(search_win_url + " #postcodify_search_area", function() {
                    setup_postcodify();
                });
            }
        });
    } else {
        var api_url_match = /api=([^&?]+)/.exec(window.location.search);
        if (api_url_match) {
            api_url = decodeURIComponent(api_url_match[1]);
        }
        setup_postcodify();
    }
    
});
