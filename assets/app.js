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
const moon = `<i class="fa-solid fa-moon text-primary"></i>`;
const sun = `<i class="fa-solid fa-lightbulb text-primary"></i>`;
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
    document.querySelector('#type-effect').innerHTML = txt.substring(0,textPosition) + `<span class="anim">&#119078;</span>`;
    if (textPosition++ !== txt.length){
        setTimeout(typewriter, speed);
    }
}

window.addEventListener('load', typewriter);

/*-------------------------User Settings/Logout------------------*/

const user = document.querySelector("#user");
const userModal = document.querySelector("#user-modal");

user.addEventListener("mouseover", () => {
    userModal.classList.remove("invisible");
    userModal.classList.add("visible");
    console.log("yes")
})

userModal.addEventListener("mouseover", () => {
    userModal.classList.remove("invisible");
    userModal.classList.add("visible");
    console.log("yes")
})

userModal.addEventListener("mouseout", () => {
    userModal.classList.remove("visible");
    userModal.classList.add("invisible");
    console.log("ok")
})
