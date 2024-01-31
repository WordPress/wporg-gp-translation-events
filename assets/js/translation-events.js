( function( $, $gp ) {
jQuery(document).ready(function($) {
    $gp.notices.init(),
    $('#submit-event, #edit-translation-event').on('click', function(e) {
        e.preventDefault();
        var $form = $('.translation-event-form');
        $.ajax({
            type: 'POST',
            url: $translation_event.url,
            data:$form.serialize(),
            success: function(response) {
                $('.event-slug').text(response.data.post_name)
                history.replaceState('/events/edit/' + response.data.post_ID)
            },
            error: function(error) {
                $gp.notices.error(response.data);
            }
        });
    });

});
}( jQuery, $gp )
);