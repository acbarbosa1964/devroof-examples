        var container = document.getElementById('json_editor');
        var options = {
            mode: 'code',
            modes: ['code', 'form', 'text', 'tree', 'view'],
            onError: function (err) {
                console.log(err.toString());
            },
            onModeChange: function (newMode, oldMode) {
                console.log('Mode switched from', oldMode, 'to', newMode);
            }
        };
        $.get("result.json", function (data, status) {
            if (status == "success") {
                var editor = new JSONEditor(container, options, data);
            } else {
                console.log('error on loading JSON result ...');

            }
        });
