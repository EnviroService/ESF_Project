/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)

import '../scss/app.scss';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';
const $ = require('jquery');
require('bootstrap');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});

// vitesse du carousel homepage
$('.carousel').carousel({
    interval: 6000
})

//Retour haut de page
/* POUR AFFICHER LE BOUTTON SUR VOS PAGES, INSERER A LA FIN DE VOTRE HTML LE BOUTTON SUIVANT:
 <button onclick="retourHaut()" id="haut" title="Retour haut de page">Haut de page</button>
*/
window.onscroll = function() {scrollFunction()};
function scrollFunction() {
    if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300)
    {
        document.getElementById("haut").style.display = "block";
    }
    else
    {
        document.getElementById("haut").style.display = "none";
    }
}

function retourHaut() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}


console.log('Hello Webpack Encore! Edit me in assets/js/app.js');
