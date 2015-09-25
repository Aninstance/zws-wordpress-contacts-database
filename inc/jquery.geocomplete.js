jQuery(document).ready(function ($) {
    $('input#target_postcode').geocomplete({
        details: 'form',
        detailsAttribute: 'data-geo',
        componentRestrictions: {
            country: 'uk'
        }
    });
});