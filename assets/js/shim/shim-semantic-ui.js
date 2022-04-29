import 'semantic-ui-css/components/accordion';
import 'semantic-ui-css/components/api';
import 'semantic-ui-css/components/checkbox';
import 'semantic-ui-css/components/colorize';
import 'semantic-ui-css/components/dimmer';
import 'semantic-ui-css/components/dropdown';
import 'semantic-ui-css/components/embed';
import 'semantic-ui-css/components/form';
import 'semantic-ui-css/components/modal';
import 'semantic-ui-css/components/nag';
import 'semantic-ui-css/components/popup';
import 'semantic-ui-css/components/progress';
import 'semantic-ui-css/components/rating';
import 'semantic-ui-css/components/search';
import 'semantic-ui-css/components/shape';
import 'semantic-ui-css/components/sidebar';
import 'semantic-ui-css/components/site';
import 'semantic-ui-css/components/state';
import 'semantic-ui-css/components/sticky';
import 'semantic-ui-css/components/tab';
import 'semantic-ui-css/components/transition';
import 'semantic-ui-css/components/video';
import 'semantic-ui-css/components/visibility';
import 'semantic-ui-css/components/visit';

$(document).ready(() => {
    // ### SYLIUS ###
    $('#sidebar').addClass('visible');
    $('#sidebar').sidebar('attach events', '#sidebar-toggle', 'toggle');
    $('#sidebar').sidebar('setting', {
        dimPage: false,
        closable: false,
    });

    $('.ui.checkbox').checkbox();
    $('.ui.accordion').accordion();
    $('.ui.menu .dropdown').dropdown({ action: 'hide' });
    $('.ui.inline.dropdown').dropdown();
    $('.link.ui.dropdown').dropdown({ action: 'hide' });
    $('.button.ui.dropdown').dropdown({ action: 'hide' });
    $('.ui.fluid.search.selection.ui.dropdown').dropdown();
    $('.ui.tabular.menu .item, .sylius-tabular-form .menu .item').tab();
    $('.ui.card .dimmable.image, .ui.cards .card .dimmable.image').dimmer({ on: 'hover' });
    $('.ui.rating').rating('disable');
    $('.tabular.menu .item').tab();
    $('.ui.selection.dropdown').dropdown();

    $('form.loadable button[type=submit]').on('click', (event) => {
        $(event.currentTarget).closest('form').addClass('loading');
    });
    $('.loadable.button').on('click', (event) => {
        $(event.currentTarget).addClass('loading');
    });
    $('.message .close').on('click', (event) => {
        $(event.currentTarget).closest('.message').transition('fade');
    });
});
