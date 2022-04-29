/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';
import 'semantic-ui-css/semantic.css';

// start the Stimulus application
import './bootstrap';

// Sylius
import './js/shim/shim-jquery'
import './js/shim/shim-semantic-ui'
import './js/shim/shim-sylius'
