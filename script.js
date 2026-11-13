// Minimal front-end helper for hitest
document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form');
  if (!form) return;

  form.addEventListener('submit', (e) => {
    const input = form.querySelector('input[type="file"]');
    if (!input || !input.files.length) {
      e.preventDefault();
      alert('Please choose a file first.');
      return;
    }
    const maxBytes = 5 * 1024 * 1024; // 5 MB
    if (input.files[0].size > maxBytes) {
      e.preventDefault();
      alert('File is larger than 5 MB.');
    }
  });
});
