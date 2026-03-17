const workshopContainers = document.querySelectorAll('.workshops .workshop');

const workshopContainer = document.querySelector('.main .workshops');

const inRoundContainers = document.querySelectorAll('.rounds .round:nth-child(2)');

var roundAmountIds = [];
inRoundContainers.forEach(container => {
    var id = container.getAttribute('id');
    roundAmountIds.push(id)
})

document.addEventListener("DOMContentLoaded", () => {
    if (window.innerWidth > 800) {
        insertData()

        waitUntilApi()

        workshopCheckRules()
    }
});


async function fetchData() {
    workshopContainers.forEach(workshop => {
        var text = workshop.querySelector('.capacityText');

        text.classList.add('whiteLoader')
    })
    var response = await fetch("/viewCapacity")
    const data = await response.json();

    workshopContainers.forEach(workshop => {
        var text = workshop.querySelector('.capacityText');

        text.classList.remove('whiteLoader')
    })

    if (data.status === "success") {
        return data;
    }
    return;
}

async function insertData() {
    var data = await fetchData();

    var capacityText = document.querySelectorAll('.capacityText');

    // Create a map to store workshops by ID
    let workshopMap = {};

    // Populate the workshopMap
    data.data.forEach(workshop => {
        workshop.moments.forEach(entry => {
            if (!workshopMap[entry.workshop_id]) {
                workshopMap[entry.workshop_id] = [];
            }
            workshopMap[entry.workshop_id].push(entry);
        });
    });

    // Iterate over capacityText elements
    capacityText.forEach((element) => {
        let workshopId = element.id.replace('capacityText', '');

        if (workshopMap[workshopId]) {
            let rounds = workshopMap[workshopId];

            // Sort rounds by wm_id to ensure correct ordering
            rounds.sort((a, b) => a.wm_id - b.wm_id);

            let text = rounds
                .map(round => {
                    let spotsLeft = round.capacity - round.bookings;
                    var roundCount = round.wm_id % 3;
                    if (roundCount == 0) {
                        roundCount = roundCount + 3;
                    }
                    return `<p> Ronde ${roundCount}: ${spotsLeft > 0 ? spotsLeft + " plek(ken) over" : "workshop zit vol!"} </p>`;
                })
                .join("\n");

            element.innerHTML = text;
        }
    });

}

async function waitUntilApi() {

    handleMouseOver()

    var data = await fetchData();
    if (data.status === "success") {
    }
}

function handleMouseOver() {
    workshopContainers.forEach(container => {
        const capacityText = container.querySelector('.capacityText');
        const location = container.querySelector('.locationWorkshop');

        container.addEventListener("mouseenter", () => {
            capacityText.classList.add('hidden');
            location.classList.remove('hidden');
        });

        container.addEventListener("mouseleave", () => {
            capacityText.classList.remove('hidden');
            location.classList.add('hidden');
        });
    });
}

function workshopCheckRules() {
    const observer = new MutationObserver((mutationsList, observer) => {

        if (!document.cookie.match("workshopWhile=removed")) return;
        if (!inRoundContainers) return;

        ifFullWorkshop()

        ifEmptyRound()
    });

    observer.observe(document.body, { childList: true, subtree: true });
}

function ifFullWorkshop() {
    inRoundContainers.forEach(container => {

        let workshopElement = container.querySelector('.workshop'); // foreach container grabs the workshop.
        if (!workshopElement) return; // if any workshop exists

        var capacityElement = workshopElement.querySelectorAll('.capacityText p');
        if (!capacityElement) return; // if cap text exists inside workshop.

        var closeIcon = container.querySelector('.close-button');
        var disabledRounds = [];

        capacityElement.forEach(text => {
            if (!text.textContent) return;

            var numbersText = text.textContent.replace(/\D/g, "");

            if (numbersText.length === 1) {
                disabledRounds.push(numbersText)
            }
        })

        var roundID = container.getAttribute('id')

        for (let i = 0; i < disabledRounds.length; i++) {
            if (disabledRounds[i] === roundID) {
                showErrorPopup("Deze ronde zit vol!")
                var closeIcon = container.querySelector('.close-button');
                closeIcon.click()
                return;
            }
        }
    });
}

function ifEmptyRound() {
    inRoundContainers.forEach(container => {
        let workshopElement = container.querySelector('.workshop');

        if (!workshopElement) return;

        let capacityElements = workshopElement.querySelectorAll('.capacityText p');

        let uniqueRounds = new Set(); // Store unique round numbers

        capacityElements.forEach(text => {

            let content = text.textContent.trim();

            // Extract round number (e.g., "Ronde 1" -> 1)
            let match = content.match(/Ronde (\d+)/);
            if (match) {
                uniqueRounds.add(parseInt(match[1])); // Convert to integer and store
            }
        });

        var exampleFullRounds = [1, 2, 3];

        // Check if one or more values from exampleFullRounds do not exist in uniqueRounds
        exampleFullRounds.forEach(round => {
            if (!uniqueRounds.has(round)) {
                // Do something if one or more values from exampleFullRounds are missing

                var roundID = container.getAttribute('id')
                if (window.innerWidth < 800) return;

                if (round.toString() === roundID) {
                    showErrorPopup("De geselecteerde workshop heeft niet deze ronde!")
                    var closeIcon = container.querySelector('.close-button');
                    closeIcon.click()
                    return;
                }
            }
        });
    });
}
