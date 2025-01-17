document.getElementById('signupButton').addEventListener('click', function () {
  const firstName = document.getElementById('firstName').value.trim();
  const lastName = document.getElementById('lastName').value.trim();
  const email = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value.trim();
  const workAs = document.getElementById('workAs').value;

  // Validate all fields
  if (!firstName || !lastName || !email || !password || !workAs) {
    alert('Please fill out all required fields.');
    return;
  }

  
  window.location.href = 'login.html'; 
});