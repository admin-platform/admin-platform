import 'sylius/ui/sylius-api-login'
import 'sylius/ui/sylius-auto-complete'
import 'sylius/ui/sylius-api-toggle'
import 'sylius/ui/sylius-bulk-action-require-confirmation'
import 'sylius/ui/sylius-form-collection'
import 'sylius/ui/sylius-prototype-handler'
import 'sylius/ui/sylius-require-confirmation'
import 'sylius/ui/sylius-toggle'

import 'sylius/ui-resources/sass/main.scss';

$(document).ready(() => {
    $('[data-requires-confirmation]').requireConfirmation();
    $('[data-bulk-action-requires-confirmation]').bulkActionRequireConfirmation();
    $('[data-toggles]').toggleElement();

    $('.special.cards .image').dimmer({
        on: 'hover',
    });

    $('[data-form-type="collection"]').CollectionForm();
    $('.sylius-autocomplete').autoComplete();
});
