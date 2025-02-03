var inputs = document.querySelectorAll('.wpcf7-form-control');
function updateLabel(event) {
  var input = event.target;
  var label = input.parentNode.parentNode.querySelector('.form-label');

  if (input.value || input === document.activeElement) {
    label.classList.add('active');
  } else {
    label.classList.remove('active');
  }
}

inputs.forEach(function(input) {
  input.addEventListener('input', updateLabel);
  input.addEventListener('focus', updateLabel);
  input.addEventListener('blur-sm', updateLabel);
});
