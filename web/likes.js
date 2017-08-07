jQuery(function ($) {

    /**
     *
     */
    $('[data-hartjes]').on('click', function(){
        var t = jQuery(this);
        t.data('value', 1 - t.data('value'));

        if (t.data('value')) {
            t.addClass('hartjes-active');
        } else {
            t.removeClass('hartjes-active');
        }

        likesUpdate(
            t.data('hartjes'),
            t.data('contenttype'),
            t.data('id'),
            t.data('value')
        );

        t.blur();
        return false;
    });

    /**
     *
     */
    function likesUpdate(type, contenttype, id, value)
    {
        var data = {
            'type': type,
            'contenttype': contenttype,
            'id': id,
            'value': value
        };

        console.log('Update', $data);

        jQuery.ajax({
            type: "POST",
            url: '/async/likes',
            data: data,
            success: function() {
                console.log("Successfully updated Likes.");
            },
            error: function() {
                console.log("Could not update Likes.");
            },
            dataType: 'json'
        });
    }

});
