function filterTable(inputId, tableId) {
  const input = document.getElementById(inputId);
  const table = document.getElementById(tableId);
  if (!input || !table) return;

  const filter = (input.value || '').toLowerCase().trim();
  const rows = table.querySelectorAll('tbody tr');

  rows.forEach((row) => {
    const text = (row.textContent || '').toLowerCase();
    row.style.display = text.includes(filter) ? '' : 'none';
  });
}

function closePopup(saveState = true) {
  const popup = document.getElementById('portalPopup');
  if (!popup) return;

  popup.classList.remove('is-visible');

  if (saveState) {
    const popupId = popup.dataset.popupId || 'default';
    try {
      localStorage.setItem(`intranet_popup_closed_${popupId}`, '1');
    } catch (error) {
      console.warn('Não foi possível salvar o estado do popup.', error);
    }
  }
}

function initPopup() {
  const popup = document.getElementById('portalPopup');
  if (!popup) return;

  const popupId = popup.dataset.popupId || 'default';
  let alreadyClosed = false;

  try {
    alreadyClosed = localStorage.getItem(`intranet_popup_closed_${popupId}`) === '1';
  } catch (error) {
    alreadyClosed = false;
  }

  if (!alreadyClosed) {
    popup.classList.add('is-visible');
  }

  popup.addEventListener('click', function (event) {
    if (event.target === popup) {
      closePopup();
    }
  });
}

function initTableFilters() {
  document.querySelectorAll('.search-box input').forEach((input) => {
    const handler = input.getAttribute('onkeyup');
    if (!handler) return;

    const match = handler.match(/filterTable\('([^']+)'\s*,\s*'([^']+)'\)/);
    if (!match) return;

    const [, inputId, tableId] = match;
    input.removeAttribute('onkeyup');
    input.addEventListener('input', () => filterTable(inputId, tableId));
  });
}

document.addEventListener('DOMContentLoaded', function () {
  initPopup();
  initTableFilters();
});
