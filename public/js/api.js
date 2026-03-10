let workshops;

function loadWorkshops_fetch() {
    let url = "http://localhost:4001/getData";
    fetch(url)
        .catch((e) => {
            workshops = "<div class='api-error'>De data kon niet worden opgehaald :(</div>";
            document.getElementById(4).innerHTML = workshops;
            throw new Error("Something went wrong trying to fetch data from the api");
        })
        .then(data => {
            return data.json();
        })
        .then(json => {
            fJson2Html(json);
        });
}

function fJson2Html(json) {
    workshops = "";
    for (let i = 0; i < json.Titles.length; i++) {
        let workshopId = json.IDs ? json.IDs[i] : i; // Use UUID if available, else fallback to index
        workshops +=
        "<div class='workshop' id='workshop" + workshopId + "' draggable='true' ondragstart='drag(event)'>" +
            "<div class='info' onclick='info(event)' id='info" + workshopId + "' tabindex='0'>i</div>" +
            "<div class='popup' id='popup" + workshopId + "' draggable='false'>" +
                "<button class='close' onclick='closePopup(\"" + workshopId + "\")'>x</button>" +
                "<a href='https://xerte.deltion.nl/play.php?template_id=8708#programma' class='popup-header' target='_blank'>Klik <span class='highlight'>hier</span> voor meer informatie</a>" +
                "<div class='description'>" +
                    "<div class='descriptionText'>";
                    for (let d = 0; d < json.Descriptions.length; d++) {
                        if (json.Descriptions[i].description[d] !== undefined) {
                            workshops +=
                            "<p>" + json.Descriptions[i].description[d] + "</p>";
                        }
                    }
                    workshops +=
                    "</div>" +
                    "<div class='descriptionImage'><img src='" + json.Images[i].image[1] + "'></div>" +
                "</div>" +
            "</div>" +
            "<div class='title' id='title" + workshopId + "'>" + json.Titles[i] + "</div>" +
        "</div>";
    }
    document.getElementById(4).innerHTML = workshops;
}

loadWorkshops_fetch();
