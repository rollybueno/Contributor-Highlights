(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        // Handle error states for avatar images
        $('.contributor-profile img').on('error', function() {
            $(this).attr('src', 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y');
        });

        $('.contributor-bio-toggle').on('click', function() {
            var $toggle = $(this);
            var $section = $toggle.closest('.contributor-bio');
            var $preview = $section.find('.contributor-bio-preview');
            var $full = $section.find('.contributor-bio-full');
            var isExpanded = $toggle.attr('aria-expanded') === 'true';
            var readMore = $toggle.data('read-more') || 'Read more';
            var readLess = $toggle.data('read-less') || 'Read less';

            if (isExpanded) {
                $full.prop('hidden', true);
                $preview.prop('hidden', false);
                $toggle.attr('aria-expanded', 'false').text(readMore);
                return;
            }

            $preview.prop('hidden', true);
            $full.prop('hidden', false);
            $toggle.attr('aria-expanded', 'true').text(readLess);
        });
    });

})(jQuery); 