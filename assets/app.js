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
const navLis = document.querySelectorAll("#nav_ul li");
navLis.forEach(li => {
    if (li.firstElementChild.classList.contains("current"))
    {
        li.classList.add('current-li');
    }
})

/*DarkMode setup*/
const switcher = document.getElementById('switch-toggle');
const html = document.querySelector('html');
const moon = `<i class="fa-solid fa-moon text-primary"></i>`;
const sun = `<i class="fa-solid fa-lightbulb text-primary"></i>`;
const btn = document.getElementById('btn');
let isDarkMode = false;

if (btn !== null)
{
    btn.addEventListener('click', () => {
        toggleTheme();
    })
}

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

if (btnModal !== null)
{
    btnModal.addEventListener("click", () => {
        modal.classList.remove('hidden');
        modal.classList.add("visible");
    })
}

if (span){
    span.addEventListener("click", () => {
        modal.classList.remove('visible');
        modal.classList.add("hidden");
    })
}


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
    if (document.querySelector('#type-effect') !== null)
    {
        document.querySelector('#type-effect').innerHTML = txt.substring(0,textPosition) + `<span class="anim">&#119078;</span>`;
        if (textPosition++ !== txt.length){
            setTimeout(typewriter, speed);
        }
    }
}

window.addEventListener('load', typewriter);

/*-------------------------User Settings/Logout------------------*/

const user = document.querySelector("#user");
const userModal = document.querySelector("#user-modal");

if (user !== null)
{
    user.addEventListener("mouseover", () => {
        userModal.classList.remove("invisible");
        userModal.classList.add("visible");
    })
}

if (userModal !== null)
{
    userModal.addEventListener("mouseover", () => {
        userModal.classList.remove("invisible");
        userModal.classList.add("visible");
    })

    userModal.addEventListener("mouseout", () => {
        userModal.classList.remove("visible");
        userModal.classList.add("invisible");
    })
}

/*-------------------------Load More Functionality------------------*/

let currentItem = 3;
const eventsLoader = document.querySelector("#load-more");
const boxes = document.querySelectorAll(".box");
// console.log(boxes)

for (let i = 0; i < 3; i++) {
    if (i < boxes.length)
    {
        boxes[i].classList.remove("hidden");
        boxes[i].classList.add("visible");
    }
}
if (boxes.length > 3)
{
    eventsLoader.classList.remove("hidden");
    eventsLoader.classList.add("visible");
}

if (eventsLoader !== null)
{
    eventsLoader.addEventListener("click", () => {
        more();
    });
}

function more() {
    console.log(currentItem);
    for (let i = currentItem; i < currentItem + 3; i++) {
        if (i < boxes.length)
        {
            boxes[i].classList.remove("hidden");
            boxes[i].classList.add("visible");
        }
    }
    currentItem += 3;
    console.log(currentItem);
    if (currentItem > boxes.length)
    {
        if (eventsLoader !== null)
        {
            eventsLoader.classList.remove("visible");
            eventsLoader.classList.add("hidden");
            eventsLoader.removeEventListener("click", more);
        }
    }
}

//---------------------------------------------------

const allTopicsBoxes = document.querySelectorAll(".all-topic-box");
const myTopicsBoxes = document.querySelectorAll(".my-topic-box");
const relatedTopicsBoxes = document.querySelectorAll(".related-topic-box");

const allTopicsLoader = document.querySelector("#all-topics-loader");
const myTopicsLoader = document.querySelector("#my-topics-loader");
const relatedTopicsLoader = document.querySelector("#related-topics-loader");

let allTopicsCurrentItem = 3;
let myTopicsCurrentItem = 3;
let relatedTopicsCurrentItem = 3;

/*--------------------------------------------------------------*/
if (allTopicsBoxes.length > 3)
{
    allTopicsLoader.classList.remove("hidden");
    allTopicsLoader.classList.add("visible");
}
for (let i = 0; i < 3; i++) {
    if (i < allTopicsBoxes.length)
    {
        allTopicsBoxes[i].classList.remove("hidden");
        allTopicsBoxes[i].classList.add("visible");
    }
}

if (allTopicsLoader !== null)
{
    allTopicsLoader.addEventListener("click", () => {
        showMoreAllTopics();
    });
}

function showMoreAllTopics() {
    console.log(allTopicsCurrentItem);
    for (let i = allTopicsCurrentItem; i < allTopicsCurrentItem + 3; i++) {
        if (i < allTopicsBoxes.length)
        {
            allTopicsBoxes[i].classList.remove("hidden");
            allTopicsBoxes[i].classList.add("visible");
        }
    }
    allTopicsCurrentItem += 3;
    console.log(allTopicsCurrentItem);
    if (allTopicsCurrentItem > allTopicsBoxes.length)
    {
        if (allTopicsLoader !== null)
        {
            allTopicsLoader.classList.remove("visible");
            allTopicsLoader.classList.add("hidden");
            allTopicsLoader.removeEventListener("click", more);
        }
    }
}

/*----------------------------------------------------------------*/

if (relatedTopicsBoxes.length > 3)
{
    relatedTopicsLoader.classList.remove("hidden");
    relatedTopicsLoader.classList.add("visible");
}
for (let i = 0; i < 3; i++) {
    if (i < relatedTopicsBoxes.length)
    {
        relatedTopicsBoxes[i].classList.remove("hidden");
        relatedTopicsBoxes[i].classList.add("visible");
    }
}

if (relatedTopicsLoader !== null)
{
    relatedTopicsLoader.addEventListener("click", () => {
        showMoreRelatedTopics();
    });
}

function showMoreRelatedTopics() {
    console.log(relatedTopicsCurrentItem);
    for (let i = relatedTopicsCurrentItem; i < relatedTopicsCurrentItem + 3; i++) {
        if (i < relatedTopicsBoxes.length)
        {
            relatedTopicsBoxes[i].classList.remove("hidden");
            relatedTopicsBoxes[i].classList.add("visible");
        }
    }
    relatedTopicsCurrentItem += 3;
    console.log(relatedTopicsCurrentItem);
    if (relatedTopicsCurrentItem > relatedTopicsBoxes.length)
    {
        if (relatedTopicsLoader !== null)
        {
            relatedTopicsLoader.classList.remove("visible");
            relatedTopicsLoader.classList.add("hidden");
            relatedTopicsLoader.removeEventListener("click", more);
        }
    }
}

/*----------------------------------------------------------------*/

if (myTopicsBoxes.length > 3)
{
    myTopicsLoader.classList.remove("hidden");
    myTopicsLoader.classList.add("visible");
}
for (let i = 0; i < 3; i++) {
    if (i < myTopicsBoxes.length)
    {
        myTopicsBoxes[i].classList.remove("hidden");
        myTopicsBoxes[i].classList.add("visible");
    }
}

if (myTopicsLoader !== null)
{
    myTopicsLoader.addEventListener("click", () => {
        showMoreMyTopics();
    });
}

function showMoreMyTopics() {
    console.log(myTopicsCurrentItem);
    for (let i = myTopicsCurrentItem; i < myTopicsCurrentItem + 3; i++) {
        if (i < myTopicsBoxes.length)
        {
            myTopicsBoxes[i].classList.remove("hidden");
            myTopicsBoxes[i].classList.add("visible");
        }
    }
    myTopicsCurrentItem += 3;
    console.log(myTopicsCurrentItem);
    if (myTopicsCurrentItem > myTopicsBoxes.length)
    {
        if (myTopicsLoader !== null)
        {
            myTopicsLoader.classList.remove("visible");
            myTopicsLoader.classList.add("hidden");
            myTopicsLoader.removeEventListener("click", more);
        }
    }
}

/*-----------------------AJAX------------------------*/

const form = document.querySelector("#comment-topic-form");

if (form !== null)
{
    form.addEventListener("submit", function f(e){
        e.preventDefault();

        const commentList = document.querySelector("#comment-list");

        const data = new FormData(e.target);
        //id of textarea (view console)
        const content = CKEDITOR.instances.comment_for_topics_form_content.getData();

        //deleting the content of textarea [name of textarea = comment_for_topics_form[content]] from data
        //then re-set its value and all will be added to data
        data.delete("comment_for_topics_form[content]")
        data.append("comment_for_topics_form[content]", content);


        fetch(this.action, {
            body: data,
            method: 'POST'
        })
            .then(response => response.json())
            .then(json => {
                console.log(json);
                handleResponse(json, commentList);
            })
    });
}

const handleResponse = function (response, commentList){
    console.log(response);
    switch (response.response) {
        case 'COMMENT_ADDED_SUCCESSFULLY':
            console.log(typeof response.html, response.html);
            commentList.innerHTML += response.html;
            commentList.innerHTML += response.html_flash;
            break;
        case 'COMMENT_UPDATED_SUCCESSFULLY':
            console.log(typeof response.html, response.html, response.html_flash);
            commentList.querySelector(`[data-id="${response.id}"]`).innerHTML = response.html;
            commentList.innerHTML += response.html_flash;
            break;
    }
}

/*-------------------------Menu modal for small screens-------------------------*/

const SmallScreenMenuBtn = document.querySelector("#MenuBtnForSmall");
if (SmallScreenMenuBtn !== null)
{
    SmallScreenMenuBtn.addEventListener("click", () => {
        modal.classList.remove('hidden');
        modal.classList.add("visible");
    })
}
