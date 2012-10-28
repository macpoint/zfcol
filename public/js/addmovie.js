$(document).ready(function() {
    $('.jquery_tabs').accessibleTabs();
    
    $('[name=movie]').live('change', function() {
        var mov = $('[name=movie]:checked').val();
        var url = 'preview/url/' + mov;
        previewMovie(url);
    });
    
    function previewMovie(url) {
        $.ajax({
            beforeSend: function() { loading(); },
            url: url,
            type:'POST',
            dataType: 'html',
            success: function(msg){
                $("#selectedmovie").html(msg);
            }
        });
    }
    
    function loading() {
        $("#selectedmovie").html("<br><p style='text-align: center;'><img src='../images/ajax-loader.gif' /></p>");
    }
});

