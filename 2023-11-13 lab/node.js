const express = require('express');
const bodyParser = require('body-parser');
const mongoose = require('mongoose');
const bcrypt = require('bcryptjs');
const nodemailer = require('nodemailer');
const axios = require('axios');
const cors = require('cors');

const app = express();
app.use(bodyParser.json());
app.use(cors());

// Connect to MongoDB
mongoose.connect('mongodb://localhost/user-auth', { useNewUrlParser: true, useUnifiedTopology: true });

const User = mongoose.model('User', new mongoose.Schema({
    email: String,
    password: String,
    name: String,
    latitude: Number,
    longitude: Number,
    weather: Object
    // ... other user data
}));

app.post('/signup', async (req, res) => {
    const { email, password, name, latitude, longitude } = req.body;

    try {
        const hashedPassword = await bcrypt.hash(password, 10);

        const weatherResponse = await axios.get(`https://api.openweathermap.org/data/2.5/weather?lat=${latitude}&lon=${longitude}&appid=YOUR_OPENWEATHERMAP_API_KEY`);
        const weatherData = weatherResponse.data;

        const user = new User({ email, password: hashedPassword, name, latitude, longitude, weather: weatherData });
        await user.save();

        const transporter = nodemailer.createTransport({
            service: 'Gmail',
            auth: {
                user: 'your-email@gmail.com',
                pass: 'your-email-password'
            }
        });

        const mailOptions = {
            from: 'your-email@gmail.com',
            to: email,
            subject: 'Email Verification',
            text: `Thank you for registering. Please verify your email by clicking on this link: http://localhost:3000/verify/${email}`,
        };

        transporter.sendMail(mailOptions, (error, info) => {
            if (error) {
                console.log(error);
                res.json({ message: 'Error sending verification email' });
            } else {
                console.log('Email sent: ' + info.response);
                res.json({ message: 'Verification email sent', weather: weatherData });
            }
        });
    } catch (error) {
        console.error(error);
        res.json({ message: 'Error registering user' });
    }
});

// Define other routes...

app.listen(3000, () => {
    console.log('Server is running on port 3000');
});
