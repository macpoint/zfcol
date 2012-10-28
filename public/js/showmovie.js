$(document).ready(function() {
    $('#moreinfo').hide();
    var moreinfo = false;
   
    $('#morebutton').click(function() {
        if (moreinfo == false) {
            $('#moreinfo').show();
            moreinfo = true;
        } else {
            $('#moreinfo').hide();
            moreinfo = false;
        }
    });
});

