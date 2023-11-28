<?php
session_start();
require_once "db.php"; // Ensure this path correctly points to your database connection script

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Query database for user details
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    echo 'Something went wrong, please log in again.';
    exit;
}

$username = $user['username'];

// Fetch income and expenses data for the logged-in user
try {
    $stmt = $pdo->prepare("SELECT * FROM Incomes WHERE user_id = ? ORDER BY date DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $incomes = $stmt->fetchAll();
    $total_income = array_sum(array_column($incomes, 'amount'));

    $stmt = $pdo->prepare("SELECT * FROM Expenses WHERE user_id = ? ORDER BY date DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $expenses = $stmt->fetchAll();
    $total_expenses = array_sum(array_column($expenses, 'amount')); // Calculate the total expenses
} catch (PDOException $e) {
    exit('Database error: ' . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh; /* Full viewport height */
            overflow: auto;
            position: relative; /* For positioning the pseudo-element */
        }

        /* Pseudo-element for background image with blur effect */
        body::before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            z-index: -1;
            background: url('bg.png') no-repeat center center fixed;
            background-size: cover;
            -webkit-filter: blur(10px);
            filter: blur(10px);
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #333;
            color: white;
            height: 100vh;
            position: fixed; /* Sidebar fixed on the left */
            overflow-y: auto; /* If content is more, it will scroll */
            overflow-x: hidden;
            z-index: 2; /* Above the pseudo-element */
        }

        /* Sidebar links */
        .sidebar a {
            padding: 20px;
            display: block;
            color: white;
            font-size: 25px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #555;
        }

        /* Welcome area on top left corner */
        .user-welcome {
            color: #333;
            background-color: #fff;
            padding: 20px;
            margin-bottom: 5px;
            border-radius: 0px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: fixed; /* Fixed at the top of the sidebar */
            width: calc(250px - 40px); /* Subtract the padding */
            top: 0;
            left: 0;
            z-index: 3; /* Above the sidebar */
        }

        .content-section {
            margin-left: 250px;
            padding: 1px 16px;
            display: none; /* Initially hide all sections */
        }
        /* Additional styles for the incomes form */
        #add-income-form input,
        #add-income-form select,
        #add-income-form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        #add-income-form button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }

        #add-income-form button:hover {
            background-color: #45a049;
        }

        /* Styles for the lists */
        #expenses-list,
        #incomes-list {
            list-style: none;
            padding: 0;
        }

        #expenses-list li,
        #incomes-list li {
            background: #f9f9f9;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 4px;
        }

        /* Styles for the total displays */
        .total-display {
            font-size: 1.5em;
            margin-top: 20px;
        }

        .total-expenses {
            color: #d9534f;
        }

        .total-income {
            color: #0b310b;
        }

    </style>
</head>
<body>
    <div class="sidebar">
        <div class="user-welcome">Welcome, <?php echo htmlspecialchars($username); ?>!</div>

        <a href="#" id="dashboard-button">Dashboard</a>
        <a href="#" id="expenses-button">Expenses</a>
        <a href="#" id="incomes-button">Incomes</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <!-- Dashboard Content Section -->
    <div id="dashboard-content" class="content-section" style="display: none;">
        <h1>Dashboard</h1>
        <!-- Add more dashboard-specific content here -->
    </div>

    <!-- Expenses Content Section -->
    <!-- Expenses Content Section -->
    <div id="expenses-content" class="content-section">
        <h1>Expenses</h1>
        <!-- The action attribute should point to the PHP file that will process the form data -->
        <!-- The method should be 'post' to secure the data transfer -->
        <form id="add-expense-form" action="add_expense.php" method="post">
            <!-- Form inputs for expenses -->
            <!-- Added the 'name' attributes which are necessary for the server to process the form data -->
            <input type="text" id="expense-title" name="expense-title" placeholder="Expense Title" required>
            <input type="number" id="expense-amount" name="expense-amount" placeholder="Expense Amount" required>
            <input type="date" id="expense-date" name="expense-date" required>
            <select id="expense-category" name="expense-category" required>
                <option value="" disabled selected>Select Option</option>
                <option value="groceries">Groceries</option>
                <option value="utilities">Utilities</option>
                <!-- Add more options as needed -->
            </select>
            <button type="submit">Add Expense</button>
        </form>
        <!-- List of expenses -->
        <ul id="expenses-list">
            <?php foreach ($expenses as $expense): ?>
                <li id="expense-<?php echo $expense['id']; ?>">
                    <span>Title: <?php echo htmlspecialchars($expense['title']); ?></span><br>
                    <span>Amount: $<?php echo htmlspecialchars(number_format($expense['amount'], 2)); ?></span><br>
                    <span>Date: <?php echo htmlspecialchars($expense['date']); ?></span><br>
                    <span>Category: <?php echo htmlspecialchars($expense['category']); ?></span>
                    <button class="delete-expense" data-id="<?php echo $expense['id']; ?>">Delete</button>
                </li>
            <?php endforeach; ?>
        </ul>
        <div id="total-expenses" class="total-display total-expenses">
            Total Expenses: $<?php echo htmlspecialchars(number_format($total_expenses, 2)); ?>
        </div>

    </div>


    <!-- Incomes Content Section -->
    <div id="incomes-content" class="content-section">
        <h1>Incomes</h1>
        <form id="add-income-form" method="post" action="dashboard.php">
            <!-- Form inputs for incomes -->
            <input type="text" id="income-title" name="income-title" placeholder="Income Title" required>
            <input type="number" id="income-amount" name="income-amount" placeholder="Income Amount" required>
            <input type="date" id="income-date" name="income-date" required>
            <select id="income-category" name="income-category" required>
                <option value="" disabled selected>Select Option</option>
                <option value="salary">Salary</option>
                <option value="freelancing">Freelancing</option>
                <!-- Add more options as needed -->
            </select>
            <input type="text" id="income-reference" name="income-reference" placeholder="Reference/Note">
            <button type="submit">Add Income</button>
        </form>

        <!-- List of incomes -->
        <ul id="incomes-list">
            <?php foreach ($incomes as $income): ?>
                <li id="income-<?php echo $income['id']; ?>">
                    <span>Title: <?php echo htmlspecialchars($income['title']); ?></span><br>
                    <span>Amount: $<?php echo htmlspecialchars(number_format($income['amount'], 2)); ?></span><br>
                    <span>Date: <?php echo htmlspecialchars($income['date']); ?></span><br>
                    <span>Category: <?php echo htmlspecialchars($income['category']); ?></span><br>
                    <?php if (!empty($income['reference'])): ?>
                        <span>Reference: <?php echo htmlspecialchars($income['reference']); ?></span>
                    <?php endif; ?>
                    <button class="delete-income" data-id="<?php echo $income['id']; ?>">Delete</button>
                </li>
            <?php endforeach; ?>
        </ul>
        <div id="total-income" class="total-display total-income">
            Total Income: $<?php echo htmlspecialchars(number_format($total_income, 2)); ?>
        </div>

    </div>

    <script>
    // Function to hide all sections and show the selected one
    function showSection(sectionId) {
        // Hide all sections
        document.querySelectorAll('.content-section').forEach(function(section) {
            section.style.display = 'none';
        });

        // Show the selected section
        document.getElementById(sectionId).style.display = 'block';
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Event listener for the dashboard button
        document.getElementById('dashboard-button').addEventListener('click', function(event) {
            event.preventDefault();
            showSection('dashboard-content');
        });

        // Event listener for the expenses button
        document.getElementById('expenses-button').addEventListener('click', function(event) {
            event.preventDefault();
            showSection('expenses-content');
        });

        // Event listener for the incomes button
        document.getElementById('incomes-button').addEventListener('click', function(event) {
            event.preventDefault();
            showSection('incomes-content');
        });

        // Handle the expenses form submission
        document.getElementById('add-expense-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch('add_expense.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add the new expense to the expenses list
                    const expensesList = document.getElementById('expenses-list');
                    const newExpenseItem = document.createElement('li');
                    newExpenseItem.innerHTML = `
                        <span>Title: ${data.expense.title}</span><br>
                        <span>Amount: $${parseFloat(data.expense.amount).toFixed(2)}</span><br>
                        <span>Date: ${data.expense.date}</span><br>
                        <span>Category: ${data.expense.category}</span>
                        <button class="delete-expense" data-id="${data.expense.id}">Delete</button>
                    `;
                    expensesList.appendChild(newExpenseItem);

                    // Update the total expenses display
                    const totalExpensesDisplay = document.getElementById('total-expenses');
                    let currentTotal = parseFloat(totalExpensesDisplay.textContent.replace('Total Expenses: $', ''));
                    // Check if currentTotal is a number, otherwise set to 0
                    if (isNaN(currentTotal)) {
                        currentTotal = 0;
                    }
                    let newExpenseAmount = parseFloat(data.expense.amount);
                    // Check if newExpenseAmount is a number, otherwise set to 0
                    if (isNaN(newExpenseAmount)) {
                        newExpenseAmount = 0;
                    }
                    let newTotal = currentTotal + newExpenseAmount;
                    totalExpensesDisplay.textContent = `Total Expenses: $${newTotal.toFixed(2)}`;

                    // Reset the form fields
                    this.reset();
                } else {
                    // Handle failure
                    console.error('Error adding expense:', data.error);
                }
            })
            .catch(error => {
                console.error('Error adding expense:', error);
            });
        });






        // Handle the incomes form submission
        document.getElementById('add-income-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the normal form submission
            const formData = new FormData(this);

            fetch('add_income.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Expect a JSON response from 'add_income.php'
            .then(data => {
                if (data.success) {
                    // Add the new income to the incomes list
                    const incomesList = document.getElementById('incomes-list');
                    const newIncomeItem = document.createElement('li');
                    newIncomeItem.innerHTML = `
                        <span>Title: ${data.income.title}</span><br>
                        <span>Amount: $${parseFloat(data.income.amount).toFixed(2)}</span><br>
                        <span>Date: ${data.income.date}</span><br>
                        <span>Category: ${data.income.category}</span>
                        <button class="delete-income" data-id="${data.income.id}">Delete</button>
                        ${data.income.reference ? `<br><span>Reference: ${data.income.reference}</span>` : ''}
                    `;
                    incomesList.appendChild(newIncomeItem);

                    // Update the total income display
                    const totalIncomeDisplay = document.getElementById('total-income');
                    let currentTotal = parseFloat(totalIncomeDisplay.textContent.replace('Total Income: $', ''));
                    let newTotal = currentTotal + parseFloat(data.income.amount);
                    totalIncomeDisplay.textContent = `Total Income: $${newTotal.toFixed(2)}`;

                    // Reset the form fields
                    this.reset();
                } else {
                    // Handle failure
                    console.error('Error adding income:', data.error);
                }
            })
            .catch(error => {
                console.error('Error adding income:', error);
            });
        });
        document.querySelectorAll('.delete-expense, .delete-income').forEach(button => {
            button.addEventListener('click', function() {
                const recordId = this.getAttribute('data-id');
                const recordType = this.classList.contains('delete-expense') ? 'expense' : 'income';
                
                fetch('delete_record.php', {
                    method: 'POST',
                    body: JSON.stringify({ id: recordId, type: recordType }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the record from the UI
                        document.getElementById(`${recordType}-${recordId}`).remove();
                        // Optionally, update the total display
                        // ...
                    } else {
                        console.error('Error deleting record:', data.error);                    
                    }
                })
                .catch(error => {
                    console.error('Error deleting record:', error);
                });
            });
        });
        // Event delegation for deleting expenses
       // document.getElementById('expenses-list').addEventListener('click', function(event) {
          //  if (event.target && event.target.matches('.delete-expense')) {
            //    const recordId = event.target.getAttribute('data-id');
           //     deleteRecord(recordId, 'expense');
          //  }
        //});

        // Event delegation for deleting incomes
        document.getElementById('expenses-list').addEventListener('click', function(event) {
        if (event.target && event.target.matches('.delete-expense')) {
            const recordId = event.target.getAttribute('data-id');
            deleteRecord(recordId, 'expense');
        }
    });

    // Event delegation for deleting incomes
    document.getElementById('incomes-list').addEventListener('click', function(event) {
        if (event.target && event.target.matches('.delete-income')) {
            const recordId = event.target.getAttribute('data-id');
            deleteRecord(recordId, 'income');
        }
    });
    // Function to handle the deletion of a record
    function deleteRecord(recordId, type) {
        fetch('delete_record.php', {
            method: 'POST',
            body: JSON.stringify({ id: recordId, type: type }),
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const elementToDelete = document.getElementById(`${type}-${recordId}`);
                if (elementToDelete) {
                    elementToDelete.remove();
                    // Optionally, update the total display
                    // ... Add code here to update the total expenses or incomes
                } else {
                    console.error(`Element with ID ${type}-${recordId} not found.`);
                }
            } else {
                console.error('Error deleting record:', data.error);
            }
        })
        .catch(error => {
            console.error('Error deleting record:', error);
        });
    }




    });



</script>
</body>
</html>


