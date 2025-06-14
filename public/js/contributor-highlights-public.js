(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        // Handle error states for avatar images
        $('.contributor-profile img').on('error', function() {
            $(this).attr('src', 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y');
        });
    });

})(jQuery); 