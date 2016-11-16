function htmlBlock(d) {
    var author = "<p></p>";
    if (d.author != "") {
        author = '                                <p>by <i>' + d.author + '</i></p>';
    }
    return '                <div align="left" class="well search-result">' +
        '                    <div class="row">' +
        '                        <a href="' + d.link + '">' +
        '                            <div class="col-xs-6 col-sm-3 col-md-3 col-lg-2">' +
        '                                <img class="img-responsive cover" src="' + d.cover + '" alt="' + d.title + '">' +
        '                            </div>' +
        '                            <div class="col-xs-6 col-sm-9 col-md-9 col-lg-10 title">' +
        '                                <h3>' + d.title + '</h3>' +
        author +
        '                            </div>' +
        '                        </a>' +
        '                    </div>' +
        '                </div>';

}

$(document).ready(function () {
    
    /* ------------------------
    
        Amazon API Parser Call.
    
       ------------------------ */
    
    $('#btn-search').on('click', function () {
        var txt = $('#search-txt').val();
        searching();
        $.get("c_amazon.php?s=" + txt, function (data, status) {
            if (status == "success") {
                var html = '';
                for (i = 0; i < data.length; i++) {
                    html += htmlBlock(data[i]);
                };
                $('#result').html(html);
            } else {
                $('#result').html('<p>Search error...<p>');

            }
        });
    });
});