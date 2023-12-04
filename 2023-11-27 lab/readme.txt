I have included screenshots showcasing the functionality of the application.
index.html: Frontend interface with forms for user sign-up, sign-in, and guest access, along with dynamic content display like location-based weather, time, location,currency,local language,local facts.
script.js: Handles client-side logic, including form submissions, password validation, and fetching dynamic content based on user location.
app.js: Node.js/Express server setup with endpoints for user authentication, leveraging bcrypt for password encryption and SQLite (via db.js) for data persistence.
service-worker.js: Implements Progressive Web App (PWA) functionalities, enabling offline capabilities, caching of resources, and an enhanced user experience.
db.js: Manages database operations, including the creation of user tables and execution of SQLite queries, integral for user management.

