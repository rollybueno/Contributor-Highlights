(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        // Add loading state to profile container
        $('.contributor-profile').each(function() {
            var $profile = $(this);
            
            // Add loading indicator
            $profile.prepend('<div class="contributor-loading" style="display: none;">Loading...</div>');
            
            // Handle error states
            $profile.on('error', 'img', function() {
                $(this).attr('src', 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y');
            });
        });
    });

})(jQuery); 