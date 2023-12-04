const sqlite3 = require('sqlite3').verbose();

// Initialize SQLite Database
const db = new sqlite3.Database('mydatabase.db');

// Create users table if it doesn't exist
db.run("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, email TEXT UNIQUE, password TEXT)", (err) => {
    if (err) {
        console.error(err.message);
    } else {
        console.log("Users table created");
    }
});

module.exports = db;
