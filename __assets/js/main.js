function searching() {
    $('#result').html('<p><img src="../__assets/images/search.gif"></p>');
}

function source_code(t) {
    $.ajax("source.php?s=" + t, {
        success: function (data) {
            //var ret = data.split("o|");
            var src_mode = data.mode;
            var data = data.source;
            var editor = ace.edit("editor");
            editor.setTheme("ace/theme/tomorrow_night_blue");
            editor.getSession().setValue(data);
            editor.getSession().setMode(src_mode);
        },
        error: function () {
            console.log('error');
        }
    });
}

$(document).ready(function () {
    source_code('PHP_EXAMPLE');
    $("#share").jsSocials({
        shares: ["email", "twitter", "facebook", "googleplus", "linkedin", "pinterest", "stumbleupon", "whatsapp"]
    });

    $(".list-group-item").on("click", function () {
        $(".list-group-item").removeClass("active");

        $(this).addClass("active");
        source_code($(this).attr("data-source"));
    });

    $("#search-txt").keypress(function (e) {
        if (e.which == 13) {
            $("#btn-search").click();
            return false;
        }
    });
});

(function (i, s, o, g, r, a, m) {
    i['GoogleAnalyticsObject'] = r;
    i[r] = i[r] || function () {
        (i[r].q = i[r].q || []).push(arguments)
    }, i[r].l = 1 * new Date();
    a = s.createElement(o),
        m = s.getElementsByTagName(o)[0];
    a.async = 1;
    a.src = g;
    m.parentNode.insertBefore(a, m)
})(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
ga('create', 'UA-87495445-1', 'auto');
ga('send', 'pageview');