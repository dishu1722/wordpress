document.addEventListener('DOMContentLoaded', function() {

    // --- 1. Update swatches with term name + description ---
    document.querySelectorAll('.bricks-variation-swatches li').forEach(li => {
        const valueName = li.getAttribute('data-balloon'); // term name
        const valueSlug = li.getAttribute('data-value');   // term slug

        const ul = li.closest('ul.bricks-variation-swatches');
        if (!ul) return;

        const input = ul.querySelector('input.variation-select');
        if (!input) return;

        const taxonomy = input.getAttribute('name'); // e.g., attribute_pa_sedak
        const key = taxonomy.replace('attribute_', '') + ':' + valueSlug; // match PHP object

        const description = termDescriptions[key] || '';

        // Create swatch-text container
        let textDiv = li.querySelector('.swatch-text');
        if (!textDiv) {
            textDiv = document.createElement('div');
            textDiv.classList.add('swatch-text');
            li.appendChild(textDiv);
        }

        // Term name
        let nameSpan = textDiv.querySelector('span');
        if (!nameSpan) {
            nameSpan = document.createElement('span');
            textDiv.appendChild(nameSpan);
        }
        nameSpan.textContent = valueName;

        // Term description
        let descSmall = textDiv.querySelector('small');
        if (!descSmall) {
            descSmall = document.createElement('small');
            textDiv.appendChild(descSmall);
        }
        descSmall.textContent = description;
        descSmall.style.display = 'block';
        descSmall.style.color = '#555';
        descSmall.style.marginTop = '2px';

        // Flex layout
        li.style.display = 'flex';
        li.style.alignItems = 'center';
        li.style.gap = '10px';
    });

    // --- 2. Update the top label dynamically ---
    function updateLabel(row) {
        const label = row.querySelector('th.label label');
        const ul = row.querySelector('ul.bricks-variation-swatches');
        if (!label || !ul) return;

        const selectedLi = ul.querySelector('li.bricks-swatch-selected') || ul.querySelector('li');
        if (!selectedLi) return;

        const valueName = selectedLi.getAttribute('data-balloon');

        // Remove old span if exists
        let span = label.querySelector('span.selected-variation');
        if (!span) {
            span = document.createElement('span');
            span.className = 'selected-variation';
            label.appendChild(span);
        }

        // Only term name in label
        span.textContent = `: ${valueName}`;
    }

    // Initialize labels on page load
    document.querySelectorAll('tr').forEach(row => updateLabel(row));

    // Update label on swatch click
    document.querySelectorAll('.bricks-variation-swatches li').forEach(li => {
        li.addEventListener('click', function() {
            // mark selected
            const siblings = li.parentNode.querySelectorAll('li');
            siblings.forEach(sib => sib.classList.remove('bricks-swatch-selected'));
            li.classList.add('bricks-swatch-selected');

            const row = li.closest('tr');
            updateLabel(row);
        });
    });

});


document.querySelectorAll('.bricks-variation-swatches li img').forEach(img => {
    let src = img.getAttribute('src');
    if(src.includes('-150x150')) {
        // Replace with full size version
        src = src.replace(/-150x150/, '');
        img.setAttribute('src', src);
    }
});

