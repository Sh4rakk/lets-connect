// var currentStepIndex = 0;
//
// // for buttons styling.
// let prevButton = document.getElementById('prevButton');
// let nextButton = document.getElementById('nextButton');
// let tutButtons = document.querySelector('.tutorial-buttons');
//
// // overlay
// let tutOverlay = document.querySelector('.tutorial-overlay');
//
// let tutStep = document.getElementById('tutorial-step')
//
// // first round
// let roundOne = document.querySelector('#round1 .round:nth-child(2)');
//
// var rounds = document.querySelector('.rounds .round');
//
// let tutorialSteps;
// if (window.innerWidth > 800) {
//     tutorialSteps = [
//         { text: "Welkom op het dashboard! Dit is het startpunt om je workshops te beheren. Hier leggen we stap voor stap uit hoe je de website gebruikt.", highlight: ".round:nth-child(1)" },
//         { text: "Klik op het 'i' icoon om meer informatie te krijgen over een workshop.", highlight: ".round:nth-child(1)" },
//         { text: "Dit is een ronde. Hier voeg je straks je workshops toe om de volgorde van jouw workshops in te stellen.", highlight: ".round:nth-child(1)" },
//         { text: "Hier is een workshop! Sleep deze workshop naar een ronde om je planning te starten.", highlight: ".workshop:nth-child(1)" },
//         { text: "Wil je een workshop verwijderen? Klik op de 'x' om deze uit de ronde te halen.", highlight: ".round:nth-child(1)" },
//         { text: "Gefeliciteerd! Je hebt de tutorial van Lets-connect voltooid. Nu weet je precies hoe je je kunt inschrijven voor een workshop!", highlight: ".round:nth-child(1)" },
//         { text: "", highlight: ".round:nth-child(1)" }
//     ];
//     var tutorialLength = tutorialSteps.length - 1;
// }
//
// if (window.innerWidth < 800) {
//     tutorialSteps = [
//         { text: "Welkom op het dashboard! Dit is het startpunt om je workshops te beheren. Hier leggen we stap voor stap uit hoe je de website gebruikt.", highlight: ".round:nth-child(1)" },
//         { text: "Dit is een ronde. Klik erop om de workshops te bekijken.", highlight: ".round:nth-child(1)" },
//         { text: "Klik op het 'i' icoon om meer informatie te krijgen over een workshop.", highlight: ".round:nth-child(1)" },
//         { text: "Hier is een workshop! Klik erop om je planning te starten.", highlight: ".workshop:nth-child(1)" },
//         { text: "Wil je een workshop verwijderen? Klik op de 'x' om deze uit de ronde te halen.", highlight: ".round:nth-child(1)" },
//         { text: "Gefeliciteerd! Je hebt de tutorial van Lets-connect voltooid. Nu weet je precies hoe je je kunt inschrijven voor een workshop!", highlight: ".round:nth-child(1)" },
//         { text: "", highlight: ".round:nth-child(1)" }
//     ];
//     var tutorialLength = tutorialSteps.length - 1;
// }
//
// document.addEventListener("DOMContentLoaded", () => {
//     var cookieSatus = cookieState();
//
//     if (!cookieSatus) {
//         startTutorial();
//         document.cookie = "render=loaded";
//     } else {
//         document.cookie = "workshopWhile=removed";
//     }
// });
//
// if (window.innerWidth > 800) {
//     const observerI = new MutationObserver((mutationsList, observer) => {
//         let iconOne = sendInfoIcon();
//
//         if (iconOne && currentStepIndex === 1) {
//             // If the icon exists, attach the click listener and disable the observer.
//             iconOne.addEventListener('click', function () {
//                 nextButton.disabled = false; // Enable the next button
//                 nextStep(); // Proceed to next step
//             });
//
//             // Disconnect the observer once we've attached the event listener
//             observer.disconnect();
//         }
//     });
//
//     // Start observing when the DOM is ready
//     observerI.observe(document.body, { childList: true, subtree: true });
//
//     // dragged workshop.
//     const observerDrag = new MutationObserver((mutationsList, observer) => {
//         let roundOneX = sendRoundX();
//         let workshopOne = sendWorkshop();
//
//         if (roundOneX) {
//             if (roundOne.contains(workshopOne)) {
//                 nextButton.disabled = false;
//                 nextStep();
//             }
//             observer.disconnect();
//         }
//     });
//     observerDrag.observe(document.body, { childList: true, subtree: true });
//
//     // remove workshop.
//     const observerX = new MutationObserver((mutationsList, observer) => {
//         let roundOneX = sendRoundX();
//         if (roundOneX && currentStepIndex === 4) {
//             // clicked on 'i' does things.
//             roundOneX.addEventListener('click', function () {
//                 nextButton.disabled = false;
//                 nextStep();
//             });
//             observer.disconnect(); // Stop observing once we find it
//         }
//     });
//     observerX.observe(document.body, { childList: true, subtree: true });
// }
//
// if (window.innerWidth < 800) {
//     const mobileTutObserver = new MutationObserver((mutationsList, mobileObserver) => {
//         var tutOverlay = document.querySelector('.tutorial-overlay');
//
//         if (roundOne && window.getComputedStyle(tutOverlay).display === "flex") {
//             // clicked on 'i' does things.
//             roundOne.addEventListener('click', function () {
//                 if (currentStepIndex === 1) {
//                     nextButton.disabled = false;
//                     nextStep();
//                 }
//             })
//             mobileObserver.disconnect(); // Stop observing once we find it
//         }
//     });
//     mobileTutObserver.observe(document.body, { childList: true, subtree: true });
//
//     const mobileInfoObserver = new MutationObserver((mutationsList, mobileObserver) => {
//         var infoIcons = document.querySelectorAll('#workshopsPopup .workshop .info');
//
//         if (infoIcons) {
//             // clicked on 'i' does things.
//             infoIcons.forEach(infoIcon => {
//                 infoIcon.addEventListener('click', function () {
//                     if (currentStepIndex === 2) {
//                         nextButton.disabled = false;
//                         nextStep();
//                     }
//                 })
//                 mobileObserver.disconnect(); // Stop observing once we find it
//             })
//         }
//
//     });
//     mobileInfoObserver.observe(document.body, { childList: true, subtree: true });
// }
//
// var rounds = document.querySelectorAll('.rounds .round');
// function clickedRound() {
//     rounds.forEach(round => {
//         if (window.innerWidth > 800) return;
//
//         round.addEventListener("click", (e) => {
//             var tutOver = document.querySelector('.tutorial-overlay')
//             var tutDisplay = window.getComputedStyle(tutOver).display;
//             if (e.target === round && tutDisplay === "none") {
//                 roundClick(round);
//             }
//             if (currentStepIndex === 1) {
//                 roundClick(round);
//             }
//         });
//     })
// }
//
// function startTutorial() {
//     // if ID exists, the popup becomes visible
//     tutOverlay.style.display = "flex";
//
//     document.getElementById('tutorial-text').textContent = tutorialSteps[currentStepIndex].text;
//
//     // styling buttons onload.
//     if (currentStepIndex === 0) {
//         prevButton.style.display = "none";
//         tutButtons.style.justifyContent = "center";
//     }
// }
//
// function nextStep() {
//     // gets the index out of a function and displays the right json on the index number.
//     currentStepIndex = hidePreviousOverlay();
//     document.getElementById('tutorial-text').textContent = tutorialSteps[currentStepIndex].text;
//
//     // styling buttons
//     if (currentStepIndex) { // if index is not zero. (true - false if value < 0),
//         prevButton.style.display = "flex";
//         tutButtons.style.justifyContent = "space-between";
//     }
//
//     // on tutorial round 1.
//     switch (currentStepIndex) {
//         case 1:
//             firstStep();
//             break;
//         case 2:
//             secondStep();
//             break;
//         case 3:
//             thirdStep();
//             break;
//         case 4:
//             fourthStep();
//             break;
//         case 6:
//             sixthStep()
//             break;
//         default:
//             defaultStyling();
//             break;
//     }
// }
//
// function prevStep() {
//     currentStepIndex = showNextOverlay();
//
//     document.getElementById('tutorial-text').textContent = tutorialSteps[currentStepIndex].text;
//
//     // styling buttons
//     if (currentStepIndex === 0) { // styling on the first tutorial step.
//         prevButton.style.display = "none";
//         tutButtons.style.justifyContent = "center";
//     } else { // styling other steps.
//         prevButton.style.display = "flex";
//         tutButtons.style.justifyContent = "space-between";
//     }
//
//     // on tutorial round 1.
//     switch (currentStepIndex) {
//         case 1:
//             firstStep();
//             break;
//         case 2:
//             secondStep();
//             break;
//         case 3:
//             thirdStep()
//             break;
//         case 4:
//             fourthStep()
//             break;
//         case 6:
//             sixthStep()
//             break;
//         default:
//             defaultStyling();
//             break;
//     }
// }
//
// // returning steo indexes
// function hidePreviousOverlay() {
//     // if index is lower than array length and is '0', the number increases.
//     if (currentStepIndex < tutorialLength || currentStepIndex === 0) {
//         currentStepIndex++;
//     }
//     // returning the index to other functions.
//     return currentStepIndex;
// }
//
// function showNextOverlay() {
//     // if the index is lower than array length and is higher than '0', the number decreases.
//     if (currentStepIndex < tutorialLength && currentStepIndex > 0 || currentStepIndex === tutorialLength) {
//         currentStepIndex--;
//     }
//     return currentStepIndex;
// }
//
// function sendWorkshop() {
//     // first workshop
//     let workshopOne = document.getElementById('workshop0')
//     return workshopOne;
// }
//
// function sendInfoIcon() {
//     // first icon
//     var iconOne;
//     if (window.innerWidth > 800) {
//         iconOne = document.getElementById('info0');
//     }
//     return iconOne;
// }
//
// function sendRoundX() {
//     // let roundX = document.querySelector('#round1 .round .workshop .close-button')
//
//     let workshopOne = sendWorkshop();
//
//     let roundOneX;
//     if (currentStepIndex && roundOne.contains(workshopOne)) {
//         roundOneX = document.querySelector('#round1 .round .workshop .close-button');
//     }
//     return roundOneX;
// }
//
// async function defaultStyling() {
//     document.cookie = "workshopWhile=removed";
//
//     nextButton.disabled = false;
//     if (nextButton.classList.contains("disabledStyle")) {
//         nextButton.classList.remove("disabledStyle")
//     }
//     // rounds style
//     roundOne.style.zIndex = "unset";
//
//     if (tutOverlay) tutOverlay.style.pointerEvents = "auto"
//
//     if (window.innerWidth > 800) {
//         // imported div.
//         let workshopOne = sendWorkshop();
//         // workshop style.
//         workshopOne.style.zIndex = "unset";
//
//         let iconOne = sendInfoIcon();
//         iconOne.style.zIndex = "unset";
//
//         let roundOneX = sendRoundX();
//         if (roundOneX && currentStepIndex !== 4) {
//             roundOneX.style.zIndex = "unset";
//         }
//         if (roundOne.contains(workshopOne) && currentStepIndex !== 4) {
//             roundOneX.click();
//         }
//
//         if (roundOne.classList.contains("tutorial-highlight")) {
//             roundOne.classList.remove("tutorial-highlight");
//         }
//         if (workshopOne.classList.contains("tutorial-highlight")) {
//             workshopOne.classList.remove("tutorial-highlight");
//         }
//     }
//
//     if (window.innerWidth < 800) {
//         if (roundOne.classList.contains("tutorial-highlight")) {
//             roundOne.classList.remove("tutorial-highlight");
//         }
//
//         tutOverlay.style.background = "rgba(0, 0, 0, 0.7)";
//         tutOverlay.style.display = "flex";
//
//         var popupWrapper = document.querySelector('.popupWrapper');
//         var workshops = document.querySelector('.popupWrapper .workshops');
//
//         if (popupWrapper) popupWrapper.style.top = "unset";
//         if (workshops) workshops.style.maxHeight = "unset";
//
//         var popup = document.getElementById('workshopsPopup');
//
//         if (popup) {
//             popup.style.background = "rgba(0, 0, 0, 0.7)";
//             popup.style.top = "0";
//             popup.style.alignItems = "center";
//         }
//
//         var workshopsWrapper = popup.querySelector('.popupWrapper .workshops')
//         if (workshopsWrapper) {
//
//             workshopsWrapper.style.background = "none";
//             workshopsWrapper.style.maxHeight = "250px";
//         }
//     }
//
//     tutStep.style.position = "unset";
//     tutStep.style.top = "unset";
// }
//
// function firstStep() {
//     defaultStyling() // resets previous styling.
//
//
//     nextButton.disabled = true; // disables next button
//     nextButton.classList.add("disabledStyle")
//
//     if (window.innerWidth > 800) {
//         let iconOne = sendInfoIcon();
//
//         iconOne.style.zIndex = "1004";
//
//         tutStep.style.position = "relative";
//         tutStep.style.top = "25%";
//     }
//     if (window.innerWidth < 800) {
//         roundOne.style.zIndex = "1004";
//
//         roundOne.classList.add("tutorial-highlight")
//
//         tutStep.style.position = "relative";
//         tutStep.style.top = "10%";
//     }
//
//
//     var closeIcon = document.querySelector('#workshopsPopup .close-button')
//     if (closeIcon) closeIcon.click()
// }
//
// // each step with styling.
// async function secondStep() {
//     defaultStyling() // resets previous styling.
//
//     if (window.innerWidth > 800) {
//         // rounds style
//         roundOne.style.zIndex = "1004";
//
//         roundOne.classList.add("tutorial-highlight")
//
//         tutStep.style.position = "relative";
//         tutStep.style.top = "25%";
//     }
//     if (window.innerWidth < 800) {
//
//         nextButton.disabled = true;
//         nextButton.classList.add("disabledStyle")
//
//         tutStep.style.position = "relative";
//         tutStep.style.top = "200px";
//
//         await fetchData();
//         var popup = document.getElementById('workshopsPopup');
//         var popupWrapper = popup.querySelector('.popupWrapper');
//         var workshops = popup.querySelector('.workshops');
//
//         popup.style.background = "unset";
//         popup.style.alignItems = "unset";
//         popupWrapper.style.top = "40px";
//         workshops.style.maxHeight = "250px"
//
//         if (tutOverlay) tutOverlay.style.pointerEvents = "none"
//     }
// }
//
// function thirdStep() {
//     defaultStyling() // resets previous styling.
//
//     nextButton.disabled = true;
//     nextButton.classList.add("disabledStyle")
//
//     if (window.innerWidth > 800) {
//         // imported div.
//         let workshopOne = sendWorkshop();
//
//         // rounds style.
//         roundOne.style.zIndex = "1004";
//
//         // workshop style.
//         workshopOne.style.zIndex = "1004";
//         workshopOne.classList.add("tutorial-highlight")
//
//         tutStep.style.position = "relative";
//         tutStep.style.top = "25%";
//     }
//     if (window.innerWidth < 800) {
//
//         nextButton.disabled = true;
//         nextButton.classList.add("disabledStyle")
//
//         tutStep.style.position = "relative";
//         tutStep.style.top = "200px";
//
//         var popup = document.getElementById('workshopsPopup');
//
//         var workshopsWrapper = popup.querySelector('.workshops');
//
//         popup.style.background = "unset";
//         popup.style.alignItems = "unset";
//         popupWrapper.style.top = "40px";
//         workshopsWrapper.style.maxHeight = "250px"
//
//         var workshops = workshopsWrapper.querySelectorAll('.workshop')
//
//         workshops[0].classList.add("tutorial-highlight")
//
//         tutOverlay.style.display = "grid";
//
//         var yesBtn = document.querySelector('.selectedWrapper .button-container .yes-button')
//         yesBtn.addEventListener('click', function (e) {
//             nextButton.disabled = true;
//             nextButton.classList.add("disabledStyle")
//             nextStep();
//         })
//     }
// }
//
// function fourthStep() {
//     defaultStyling() // resets previous styling.
//
//     if (window.innerWidth > 800) {
//         document.cookie = "workshopWhile=tutorial";
//
//         nextButton.disabled = true;
//         nextButton.classList.add("disabledStyle");
//
//         let roundOneX = sendRoundX();
//
//         if (roundOneX) { // Ensure it's not undefined
//             roundOneX.style.zIndex = "1004";
//         }
//
//         tutStep.style.position = "relative";
//         tutStep.style.top = "25%";
//     }
//     if (window.innerWidth < 800) {
//         nextButton.disabled = true;
//         nextButton.classList.add("disabledStyle")
//
//         tutStep.style.position = "relative";
//         tutStep.style.top = "25%";
//
//         const any = new MutationObserver(() => {
//             var removeIcon = roundOne.querySelector('#round1 .close-button')
//             if (removeIcon) {
//                 var workshop = document.querySelector('#round1 .workshop');
//                 if (workshop.classList.contains("tutorial-highlight")) {
//                     workshop.classList.remove("tutorial-highlight");
//                 }
//                 removeIcon.style.zIndex = "1004";
//
//                 removeIcon.addEventListener('click', function () {
//                     nextButton.disabled = true;
//                     nextButton.classList.add("disabledStyle")
//                     nextStep();
//                 })
//                 any.disconnect();
//             }
//         });
//
//         any.observe(document.body, { childList: true, subtree: true });
//     }
// }
//
// function sixthStep() {
//     defaultStyling();
//
//     tutOverlay.style.display = "none"; // hides the overlay.
// }
//
// function cookieState() {
//     var cookieSatus = false; // renderLoaded runs on first load, making status false on second load.
//     if (document.cookie.match("render=loaded")) {
//         cookieSatus = true;
//     } else {
//         cookieSatus = false;
//     }
//     return cookieSatus;
// }
//
// async function fetchData() {
//     var response = await fetch("/viewCapacity")
//     const data = await response.json();
//
//     if (data.status === "success") {
//         return data;
//     }
//     return;
// }
