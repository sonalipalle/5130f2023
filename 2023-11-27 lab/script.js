// Register Service Worker
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
    .then(function(registration) {
        console.log('Service Worker registered with scope:', registration.scope);
    })
    .catch(function(error) {
        console.log('Service Worker registration failed:', error);
    });
}


// Password Validation Function
function validatePassword(password) {
    const passwordCriteria = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}/;
    return passwordCriteria.test(password);
}

// Event listener for signup form submission
document.getElementById('signupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const password = formData.get('password');
    if (!validatePassword(password)) {
        alert('Password must be at least 8 characters long and include numbers, uppercase and lowercase letters, and special characters.');
        return;
    }
    fetch('/signup', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData)),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'welcome.html';
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('signup Successful');
        console.error(error);
    });
});

// Event listener for signin form submission
document.getElementById('signinForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const password = formData.get('password');
    fetch('/signin', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData)),
    })
    .then(response => {
        if (!response.ok) {
            throw response;
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.location.href = 'signin.html';
        } else {
            alert(data.message);
        }
    })
    .catch(errorResponse => {
        if (errorResponse.json) {
            // Handle JSON responses
            errorResponse.json().then(errorData => {
                if (errorResponse.status === 404) {
                    alert('User not found. Please sign up.');
                } else if (errorResponse.status === 401) {
                    alert('Incorrect password.');
                } else {
                    alert('An error occurred: ' + errorData.message);
                }
            });
        } else {
            // Handle non-JSON responses or other fetch errors
            alert('Signin successful');
        }
    });
});
// Event listener for guest access form submission
document.getElementById('guestForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('/guest', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData)),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'guestlogin.html';
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('Guest access successful');
        console.error(error);
    });
});
