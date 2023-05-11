/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "../scss/app.scss";
import "../scss/agencies.scss";
import "../scss/campaigns.scss";
import "../scss/demos.scss";
import "../scss/targets.scss";
require("croppie/croppie.css");

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
const $ = require("jquery");
const m = require("moment");
//const d = require("datatables.net");

/*
window.jQuery = window.$ = $;
$.fn.dataTable = $.fn.DataTable = global.DataTable = require("datatables.net/js/jquery.dataTables.min")(
  window,
  $
);
*/
const imagesContext = require.context(
  "../img",
  true,
  /\.(png|jpg|jpeg|gif|ico|svg|webp)$/
);
imagesContext.keys().forEach(imagesContext);
