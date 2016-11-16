function htmlBlock(d, i) {
    var author = "<p></p>";
    if (d.price_from != "") {
        price_from = '                                <p>From <b>' + d.price_from + '</b></p>';
    }
    var gps = d.gps.split(",");
    return '                <div align="left" class="well search-result">' +
        '                    <div class="row">' +
        '                        <a href="' + d.url + '">' +
        '                            <div class="col-xs-6 col-sm-3 col-md-3 col-lg-2">' +
        '                                <img class="img-responsive cover" src="' + d.image + '" alt="' + d.name + '">' +
        '                            </div>' +
        '                            <div class="col-xs-6 col-sm-9 col-md-9 col-lg-10 title">' +
        '                            <div class="row">' +
        '                               <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 title">' +
        '                                <h3>' + d.name + '</h3>' +
        '                                </div>' +
        '                               <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 rating">' +
        '                                <b>' + d.score_text + ' (' + d.score + ')</b>' +
        '                                </div>' +
        '                            </div>' +
        '                            <div class="row">' +
        '                               <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 title">' +
        '                                       <div class="popin">' +
        '                                       <div class="loc-map" id="map' + i + '"></div>' +
        '                                       </div>' +
        '                               </div>' +
        '                               <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 rating">' +
        '                                <b>' + price_from + '</b>' +
        '                                </div>' +
        '                            </div>' +
        '                            </div>' +
        '                        </a>' +
        '                    </div>' +
        '                </div>';

}

$(document).ready(function () {

    /* ------------------------
    
        Booking API Parser Call.
    
       ------------------------ */

    $('#btn-search').on('click', function () {
        var dates = $('input[name="daterange"]').val();
        var destination = $('#search-txt').val();
        searching();
        $.get("c_booking.php?s=" + destination + "&d=" + dates, function (data, status) {
            if (status == "success") {
                var html = '';
                for (i = 0; i < data.length; i++) {
                    var gps = data[i].gps.split(",");
                    html += htmlBlock(data[i], i);
                };
                $('#result').html(html);
                for (i = 0; i < data.length; i++) {
                    var gps = data[i].gps.split(",");
                    var mp = new GMaps({
                        div: '#map' + i,
                        lat: gps[1],
                        lng: gps[0]
                    });
                    mp.addMarker({
                        lat: gps[1],
                        lng: gps[0],
                        title: data[i].name,
                        click: function (e) {
                            e.preventDefault();
                        }
                    });
                }
            } else {
                $('#result').html('<p>Search error...<p>');
            }
        });
    });
});