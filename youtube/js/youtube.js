function htmlArtist(d) {
    return '                <div align="left" class="well search-result">' +
        '                    <div class="row">' +
        '                            <div class="col-xs-6 col-sm-3 col-md-3 col-lg-2">' +
        '                                <img class="img-responsive cover" src="' + d.image + '" alt="' + d.artist + '">' +
        '                            </div>' +
        '                            <div class="col-xs-6 col-sm-9 col-md-9 col-lg-10 title">' +
        '                                <h2>' + d.artist + '</h2>' +
        '                            </div>' +
        '                    </div>' +
        '                </div>';
    
}

function htmlBlock(d) {
    return '                <div align="left" class="well search-result">' +
        '                    <div class="row">' +
        '                        <a href="https://www.youtube.com/watch?v=' + d.id + '">' +
        '                            <div class="col-xs-6 col-sm-3 col-md-3 col-lg-2">' +
        '                                <img class="img-responsive cover" src="' + d.thumb + '" alt="' + d.title + '">' +
        '                            </div>' +
        '                            <div class="col-xs-6 col-sm-9 col-md-9 col-lg-10 title">' +
        '                                <h3>' + d.title + '</h3>' +
        '                                <p>by <i>' + d.user + '</i></p>' +
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
        $.get("c_youtube_artist.php?s=" + txt, function (data, status) {
            if (status == "success") {
                var videos = data.videos;
                var html = htmlArtist(data);
                for (i = 0; i < videos.length; i++) {
                    html += htmlBlock(videos[i]);
                };
                $('#result').html(html);
            } else {
                $('#result').html('<p>Search error...<p>');

            }
        });
    });
});