jQuery(function($) {
    const bengaliRegex = /[\u0980-\u09FF]/;
    
    function applyBanglaFont() {
        $('body *:not(script, style, code, pre, input, textarea, .no-bangla-font *)').each(function() {
            const $el = $(this);
            if (!$el.hasClass('bfl-processed') && bengaliRegex.test($el.text())) {
                $el.addClass('bfl-processed')
                   .css('font-family', `${bflSettings.font}, ${$el.css('font-family')}`);
            }
        });
    }
    
    // Initial run
    applyBanglaFont();
    
    // Run on AJAX complete
    $(document).ajaxComplete(applyBanglaFont);
    
    // Use MutationObserver for dynamic content
    if (typeof MutationObserver !== 'undefined') {
        new MutationObserver(function() {
            applyBanglaFont();
        }).observe(document.body, {
            childList: true,
            subtree: true
        });
    }
});