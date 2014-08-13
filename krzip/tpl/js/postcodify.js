
jQuery(function() {
    
    var $ = jQuery;
    
    if ($("button.postcodify").size()) {
        $("button.postcodify").click(function(e) {
            e.preventDefault();
            var search_win_settings = "width=680,height=540,resizable=yes,scrollbars=yes";
            var search_win = window.open("./modules/krzip/tpl/popup.html?api=" + encodeURIComponent($(this).data("url")),
            	"postcodify_popup", search_win_settings);
        });
    }
    
    if ($("body.postcodify #postcodify").size()) {
    	var api_url = "//api.poesis.kr/post/search.php";
    	var api_url_match = /api=([^&?]+)/.exec(window.location.search);
    	if (api_url_match) {
    		api_url = decodeURIComponent(api_url_match[1]);
    	}
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
                var op = $(window.opener.document);
                op.find("#postcodify_addr1").val($("#entry_address").val());
                op.find("#postcodify_addr2").val("").focus();
                op.find("#postcodify_addr3").val($("#entry_extra_info").val());
                op.find("#postcodify_addr4").val($("#entry_postcode6").val());
                window.open("", "_self", "");
                window.close();
            }
        });
    }
});
