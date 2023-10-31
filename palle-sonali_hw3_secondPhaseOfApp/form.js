const firebaseConfig = {
    apiKey: "AIzaSyAMkAOJvyIJHx6TK8awD5nUxnhZFkogi50",
    authDomain: "detailsform-assignment3.firebaseapp.com",
    databaseURL: "https://detailsform-assignment3-default-rtdb.firebaseio.com",
    projectId: "detailsform-assignment3",
    storageBucket: "detailsform-assignment3.appspot.com",
    messagingSenderId: "97186651373",
    appId: "1:97186651373:web:097f7c48397f7a8ca080e4"
  };

  // Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Reference to the Firebase Realtime Database
var detailsFormDB = firebase.database().ref('detailsForm');

// Reference to Firebase Storage
var storage = firebase.storage();

// Handle form submission
document.getElementById('detailsForm').addEventListener('submit', submitForm);

function submitForm(e) {
    e.preventDefault();

    var firstName = getElementVal("firstName");
    var lastName = getElementVal("lastName");
    var age = getElementVal("age");
    var occupation = getElementVal("occupation");
    var dob = getElementVal("dob");
    var placeOfBirth = getElementVal("placeOfBirth");
    var currentLocation = getElementVal("currentLocation");
    var destination = getElementVal("destination");
    var familyOrigin = getElementVal("familyOrigin");
    var facts = getElementVal("facts");
    var hobbies = getElementVal("hobbies");
    var plans = getElementVal("plans");
    var places = getElementVal("places");
    var food = getElementVal("food");
    

    // Get the file input element
    var fileInput = document.getElementById("fileInput");
    var file = fileInput.files[0];

    if (file) {
        // Upload the file to Firebase Storage
        var storageRef = storage.ref("uploads/" + file.name);
        var task = storageRef.put(file);

        // Listen for state changes, errors, and completion of the upload.
        task.on(
            "state_changed",
            function progress(snapshot) {
                var percentage = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
                console.log("Upload is " + percentage + "% done");
            },
            function error(err) {
                console.error("Error uploading file:", err);
            },
            function complete() {
                console.log("File uploaded successfully!");
                // Get the download URL for the uploaded file
                storageRef
                    .getDownloadURL()
                    .then(function (downloadURL) {
                        // Save the user's information, including the file URL, to the database
                        saveMessages(
                            firstName,
                            lastName,
                            age,
                            occupation,
                            dob,
                            placeOfBirth,
                            currentLocation,
                            destination,
                            familyOrigin,
                            downloadURL,
                            facts,
                            hobbies,
                            plans,
                            places,
                            food,
                        );
                    })
                    .catch(function (error) {
                        console.error("Error getting file download URL:", error);
                    });
            }
        );
    } else {
        // If no file was selected, save user information without a file URL
        saveMessages(
            firstName,
            lastName,
            age,
            occupation,
            dob,
            placeOfBirth,
            currentLocation,
            destination,
            familyOrigin,
            fileUrl,
            facts,
            hobbies,
            plans,
            places,
            food,

        );
    }
    resetFormFields();
}

// Function to save user information to the database
const saveMessages = (
    firstName,
            lastName,
            age,
            occupation,
            dob,
            placeOfBirth,
            currentLocation,
            destination,
            familyOrigin,
            fileUrl,
            facts,
            hobbies,
            plans,
            places,
            food,
) => {
    var newDetailsForm = detailsFormDB.push();

    newDetailsForm.set({
        firstName: firstName,
        lastName: lastName,
        age: age,
        occupation: occupation,
        dob: dob,
        placeOfBirth: placeOfBirth,
        currentLocation: currentLocation,
        destination: destination,
        familyOrigin: familyOrigin,
        fileUrl: fileUrl, // Include the file URL in the database
        facts: facts,
        hobbies: hobbies,
        plans: plans,
        places: places,
        food: food,
    });
};

function resetFormFields() {
    document.getElementById("firstName").value = "";
    document.getElementById("lastName").value = "";
    document.getElementById("age").value = "";
    document.getElementById("occupation").value = "";
    document.getElementById("dob").value = "";
    document.getElementById("placeOfBirth").value = "";
    document.getElementById("currentLocation").value = "";
    document.getElementById("destination").value = "";
    document.getElementById("familyOrigin").value = "";
    document.getElementById("fileInput").value = "";
    document.getElementById("facts").value = "";
    document.getElementById("hobbies").value = "";
    document.getElementById("plans").value = "";
    document.getElementById("places").value = "";
    document.getElementById("food").value = "";
  }

const getElementVal = (id) => {
    return document.getElementById(id).value;
};