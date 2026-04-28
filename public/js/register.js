const opleidingSelect = document.getElementById("opleiding");
const klasSelect = document.getElementById("klas");

const klassen = {
  opleiding1: ["MV2A", "MV2B", "MV2C"],
  opleiding2: ["BOW3B", "BOW3C", "BOW2V", "BOW3D", "BOW2A", "BOW2B",],
  opleiding3: ["P2.1", "P2.2"],
  opleiding4: ["AR2MZ",],
  opleiding5: ["AR2AC"],
  opleiding6: ["IT2A"],
  opleiding7: ["SD2A","SD2B",],
  opleiding8: ["AV3B",],
  opleiding9: ["AV3A"],
  opleiding10: ["2FR", "2FC", "2AFT", "2FT", "2FDA", "2FDB"],
  opleiding11: ["CD2A", "CD2B"],
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
