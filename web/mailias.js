jQuery(document).ready(function($) {
    // Hyperling header to root
    var root = ( $("script").last().attr("src").split("/")[0] == "web" ? "./" : "../" );
    $("header").contents().first().wrap("<a href='" + root + "'></a>" );

    // Handle table row clicks
    $("tr").click(function() {
	var link = $(this).find('a').attr("href");
	if (link !== undefined) {
            window.document.location = link;
	}
    });

    // Set auto-reload
    if ( $("table").length > 0 ) {
        setTimeout(function(){window.location.reload();}, 60000);
    }

    // Add button logic
    if ( $("button").length > 0 ) {
        $("div>div").eq(0).html($("div>div").eq(0).text());

	$("button").eq(0).click(function() {
            if ( $(this).text() == "Formatted" ) {
                $("div>div").css("display", "block");
                $("div>pre").css("display", "none");
                $(this).text("Original");
            } else {
                $("div>div").css("display", "none");
                $("div>pre").css("display", "block");
                $(this).text("Formatted");
            }
	});

	$("button").eq(1).prop('disabled', true);

	$("button").eq(2).click(function() {
	    if ( confirm("Delete this message?") == true ) {
		$.get(window.location.href + "/x", function() {
		    window.location = "./";
		});
	    }
	});
    }
});
