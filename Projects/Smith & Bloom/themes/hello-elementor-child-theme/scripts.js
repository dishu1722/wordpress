// Replacing the NOTES field to Custom Card message in checkout page

document.addEventListener("DOMContentLoaded", function () {
  const observer = new MutationObserver(() => {
    // Change placeholder text
    const textarea = document.querySelector('textarea[placeholder*="Notes about your order"]');
    if (textarea) {
      textarea.placeholder = "Write a message you want to say on the card...";
    }

    // Change label text
    const label = document.querySelector('.wc-block-checkout__add-note label span');
    if (label && label.innerText.includes("Add a note to your order")) {
      label.innerText = "Card Message *";
    }
  });

  observer.observe(document.body, { childList: true, subtree: true });
});

//Check if delivered in particular area using Postcode input field 
function checkPostcode() {
  const allowedPostcodes = ['2224', '2225', '2226', '2227', '2228',
    '2229', '2230', '2231', '2232', '2233', '2234']; 
  const userPostcode = document.getElementById('postcode').value.trim();
  const messageEl = document.getElementById('delivery-message');

  if (userPostcode === '') {
    messageEl.style.color = 'white';
    messageEl.innerText = 'Please enter your postcode.';
  } else if (allowedPostcodes.includes(userPostcode)) {
    messageEl.style.color = 'white';
    messageEl.innerText = 'Great news! We deliver to your area.';
  } else {
    messageEl.style.color = 'white';
    messageEl.innerText = 'Sorry, we do not currently deliver to that postcode.';
  }
}

// Get current year for copyright statement
  jQuery('#spanyear').html(new Date().getFullYear());