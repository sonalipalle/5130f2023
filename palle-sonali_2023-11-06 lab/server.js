const express = require('express');
const bodyParser = require('body-parser');

const app = express();
const port = 3000;

// Middleware to parse JSON and URL-encoded form data
app.use(bodyParser.urlencoded({ extended: false }));
app.use(bodyParser.json());

// Serve static files from the 'public' directory
app.use(express.static('public'));

// Regular expressions for email, phone, and website validation
const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
const phoneRegex = /^\d{10}$/;
const websiteRegex = /^(https?:\/\/)?[a-z0-9-]+(\.[a-z0-9-]+)+([/?].*)?$/i;

// POST request handler
app.post('/validate', (req, res) => {
    const { email, phone, website } = req.body;
    const errors = [];

    if (!email.match(emailRegex)) {
        errors.push('Invalid email address');
    }

    if (!phone.match(phoneRegex)) {
        errors.push('Invalid phone number');
    }

    if (!website.match(websiteRegex)) {
        errors.push('Invalid website URL');
    }

    if (errors.length === 0) {
        res.send('Data is valid.');
    } else {
        res.status(400).send(errors.join(', '));
    }
});

app.listen(port, () => {
    console.log(`Server is listening on port ${port}`);
});
