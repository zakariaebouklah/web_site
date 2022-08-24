/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

/* any CSS you import will output into a single css file (app.css in this case)*/
import './styles/app.css';

/* start the Stimulus application*/
import './bootstrap';

/*active page transitions*/

const activePage = window.location.pathname;
const navLinks = document.querySelectorAll("#nav_ul a");
navLinks.forEach(link => {
    if(link.href.includes(`${activePage}`)){
        link.classList.add('current');
        link.classList.remove('text-secondary');
    }
})

/*DarkMode setup*/
const switcher = document.getElementById('switch-toggle');
const html = document.querySelector('html');
const moon = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0e166c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
              </svg>`;
const sun = `<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0e166c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/>
                <path d="M12 1v2M12 21v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M1 12h2M21 12h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4"/>
             </svg>`;
const btn = document.getElementById('btn');
let isDarkMode = false;

btn.addEventListener('click', () => {
    toggleTheme();
})

function toggleTheme() {
    isDarkMode = !isDarkMode;
    switchTheme();
}

function switchTheme() {
    if(isDarkMode){
        html.classList.add('dark');
        switcher.classList.remove('-translate-x-2');
        switcher.classList.add('translate-x-full');
        setTimeout(() => {
            switcher.innerHTML = moon;
        }, 250);
    }else {
        html.classList.remove('dark');
        switcher.classList.add('-translate-x-2');
        switcher.classList.remove('translate-x-full');
        setTimeout(() => {
            switcher.innerHTML = sun;
        }, 250);
    }
}

/*------------------------The Menu Modal-----------------------*/

const modal = document.getElementById("myModal");
const btnModal = document.getElementById("myBtn");
const span = document.getElementsByClassName("close")[0];

btnModal.addEventListener("click", () => {
    modal.classList.remove('hidden');
    modal.classList.add("visible");
})

span.addEventListener("click", () => {
    modal.classList.remove('visible');
    modal.classList.add("hidden");
})

window.addEventListener("click", e => {
    if(e.target === modal){
        modal.classList.remove('visible');
        modal.classList.add("hidden");
    }
})

/*-------------------------Typing Effect------------------*/

const txt = "Laboratoire Universitaire de recherche en Instrumentations et Gestion des Organisations.";
const speed = 100;
let textPosition = 0;

const typewriter = () => {
    document.querySelector('#type-effect')
        .innerHTML = txt.substring(0,textPosition) + `<span class="anim">&#119078;</span>`;
    if (textPosition++ !== txt.length){
        setTimeout(typewriter, speed);
    }
}

window.addEventListener('load', typewriter);

/*-----------------------------*/
