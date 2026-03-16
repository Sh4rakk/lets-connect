const opleidingSelect = document.getElementById("opleiding");
const klasSelect = document.getElementById("klas");

const klassen = {
  opleiding1: ["SIM2A", "SIM2B", "SIS3A", "SIA3A"],
  opleiding2: ["AR2MP",],
  opleiding3: ["P2.1", "P2.2", "P2.3"],
  opleiding4: ["2ARAC",],
  opleiding5: ["2AFT", "2FC", "2FR", "2FDA", "2FDB"],
  opleiding6: ["MV2A", "MV2B", "MV2C"],
  opleiding7: ["AV3A",],
  opleiding8: ["AV3B",],
  opleiding9: ["IT2A", "IT2B",],
  opleiding10: ["MI1A", "MI2A",],
  opleiding11: ["SD2A", "SD2B", "SD2O"],
  opleiding12: ["BOW2A", "BOW2B", "BOW2C", "BOW2D", "BOW2V"],
  opleiding13: ["CD2A", "CD2B"],
};

opleidingSelect.addEventListener("change", function() {
  
  klasSelect.innerHTML = "<option value=''>Kies een klas</option>";

  const selectedOpleiding = opleidingSelect.value;

  // Als er een opleiding is geselecteerd, vul de klas dropdown
  if (selectedOpleiding && klassen[selectedOpleiding]) {
    klassen[selectedOpleiding].forEach(function(klas) {
      const option = document.createElement("option");
      option.value = klas;
      option.textContent = klas;
      klasSelect.appendChild(option);
    });
  }
});

document.querySelector('.register-form').addEventListener('submit', function(e) {
  const emailInput = document.getElementById('email');
  const email = emailInput.value.trim();

  // Controleer of de eerste 8-9 tekens van het e-mailadres alleen uit cijfers bestaan
  const emailPrefix = email.split('@')[0];
  const firstPart = emailPrefix.substring(0, 9);
  
  if (!/^\d{8,9}$/.test(firstPart)) {
      e.preventDefault();
      alert('De eerste 8-9 tekens van je e-mailadres moeten alleen cijfers zijn.');
      emailInput.focus();
  }
});