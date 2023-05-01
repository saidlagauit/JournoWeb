document.addEventListener('DOMContentLoaded', function () {

  const forms = document.querySelectorAll('form');
  forms.forEach(form => {
    const submitButton = form.querySelector('button[type="submit"]');
    const requiredInputs = form.querySelectorAll('input[required], textarea[required]');
    requiredInputs.forEach(input => {
      input.addEventListener('input', () => {
        const isFormValid = Array.from(requiredInputs).every(input => input.checkValidity());
        submitButton.disabled = !isFormValid;
      });
    });
  });

  var message = document.getElementById('message');
  if (message) {
    setTimeout(function () {
      message.style.display = 'none';
    }, 6000);
  }

  document.getElementById('search-form').addEventListener('submit', function (event) {
    event.preventDefault();
    var searchInput = document.getElementById('search-input').value;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'search.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
        var searchResults = document.getElementById('search-results');
        searchResults.innerHTML = xhr.responseText;
      }
    };
    xhr.send('search=' + encodeURIComponent(searchInput));
  });


});
