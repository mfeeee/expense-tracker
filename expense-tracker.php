<?php

$FILE_NAME = "expenses.json";
$currentTime = date('c');

if ($argc < 2) {
    cliGuide();
    exit(1);
} else {
    $command = $argv[1] ?? null;

    switch ($command) {
        case "add":
            $description = $argv[3] ?? null;
            $amount = $argv[5] ?? null;

            if (!$description || !$amount) {
                echo "Invalid command. Usage: php expense-tracker.php add --description \"<name>\" --amount <value>\n" . PHP_EOL;
                exit(1);
            }
            addExpense($description, $amount);
            break;
        case "update":
            $id = null;
            $description = null;
            $amount = null;

            foreach ($argv as $index => $arg) {
                switch ($arg) {
                    case '--id':
                        $id = $argv[$index + 1] ?? null;
                        break;
                    case '--description':
                        $description = $argv[$index + 1] ?? null;
                        break;
                    case '--amount':
                        $amount = $argv[$index + 1] ?? null;
                        break;
                }
            }

            if (!$id || (!$description && !$amount)) {
                echo "Invalid command. Usage: php expense-tracker.php update --id <id> --description \"<name>\" --amount <value>" . PHP_EOL;
                echo "                        php expense-tracker.php update --id <id> --description \"<name>\"" . PHP_EOL;
                echo "                        php expense-tracker.php update --id <id> --amount <value>\n" . PHP_EOL;
                exit(1);
            }
            updateExpense($id, $description, $amount);
            break;
        case "delete":
            $id = $argv[3] ?? null;

            if (!$id) {
                echo "Invalid command. Usage: php expense-tracker.php delete --id <id>\n" . PHP_EOL;
                exit(1);
            }
            deleteExpense($id);
            break;
        case "list":
            if ($argc > 2) {
                echo "Invalid command. Usage: php expense-tracker.php list\n" . PHP_EOL;
            }
            listExpenses();
            break;
        case "summary":
            $month = $argv[3] ?? null;

            if ($argc > 4) {
                echo "Invalid command. Usage: php expense-tracker.php summary\n" . PHP_EOL;
                echo "                        php expense-tracker.php summary --month <number>\n" . PHP_EOL;
                exit(1);
            }
            summaryExpenses($month);
            break;
        default:
            echo "Error: Unknown command - $command\n" . PHP_EOL;
            cliGuide();
            exit(1);
    }
}

function loadExpenses() {
    global $FILE_NAME;

    if (!file_exists($FILE_NAME)) {
        return [];
    } else {
        $jsonData = @file_get_contents($FILE_NAME);
        return json_decode($jsonData, true) ?? [];
    }
}

function saveExpenses(array $expense) {
    global $FILE_NAME;

    $jsonData = json_encode($expense, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($FILE_NAME, $jsonData);
}

function addExpense(string $description, string $value) {
    global $currentTime;
    $expenses = loadExpenses();
    $last_key = array_key_last($expenses);

    if (empty($expenses)) {
        $newId = 1;
    } else {
        $newId = $expenses[$last_key]["id"] + 1;
    }

    $newExpense = [
        "id" => $newId,
        "description" => $description,
        "amount" => $value,
        "createdAt" => $currentTime,
        "updatedAt" => $currentTime
    ];

    $expenses[] = $newExpense;
    saveExpenses($expenses);
    echo "Expense added successfully (ID: $newId)" . PHP_EOL;
}

function updateExpense(string $id, ?string $description = null , ?string $amount = null) {
    global $currentTime;
    $expenses = loadExpenses();
    $expenseFound = false;

    foreach ($expenses as &$expense) {
        if ($expense["id"] == $id) {
            if (!is_null($description)) {
                $expense["description"] = $description;
            }

            if (!is_null($amount)) {
                $expense["amount"] = $amount;
            }

            $expense["updatedAt"] = $currentTime;
            $expenseFound = true;
            break;
        }
    }

    unset($expense);

    if ($expenseFound) {
        saveExpenses($expenses);
        echo "Expense updated successfully (ID: $id)\n" . PHP_EOL;
    } else {
        echo "Expense not found (ID: $id)\n" . PHP_EOL;
    }
}

function deleteExpense(string $id) {
    $expenses = loadExpenses();
    $expenseFound = false;

    foreach ($expenses as $index => $expense) {
        if ($expense["id"] == $id) {
            unset($expenses[$index]);
            $expenseFound = true;
            break;
        }
    }

    if ($expenseFound) {
        $expenses = array_values($expenses);
        saveExpenses($expenses);
        echo "Expense deleted successfully.\n" . PHP_EOL;
    } else {
        echo "Expense not found (ID: $id)\n" . PHP_EOL;
    }
}

function listExpenses() {
    $expenses = loadExpenses();

    if (empty($expenses)) {
        echo "No expenses found." . PHP_EOL;
        exit(1);
    }

    echo "ID   Date         Description    Amount" . PHP_EOL;
    
    foreach ($expenses as $expense) {
        $date = date('Y-m-d', strtotime($expense['createdAt']));
        $space = "    ";

        echo $expense['id'] . $space . $date . $space . $expense['description'] . "          " . $expense['amount'] . PHP_EOL;
    }
}

function summaryExpenses(?string $month = null) {
    $expenses = loadExpenses();
    $total = 0;
    $months = [
        1  => "January",
        2  => "February",
        3  => "March",
        4  => "April",
        5  => "May",
        6  => "June",
        7  => "July",
        8  => "August",
        9  => "September",
        10 => "October",
        11 => "November",
        12 => "December"
    ];

    if (empty($expenses)) {
        echo "No expenses found. Total expenses: $0\n" . PHP_EOL;
        exit(1);
    }

    if (!is_null($month)) {
        foreach ($expenses as $expense) {
            $date = date('m', strtotime($expense['createdAt']));
            if ($month == (int)($date)) {
                $total += (int)($expense['amount']);
            }
        }
        echo "Total expenses for $months[$month]: $$total" . PHP_EOL;
    } else {
        foreach ($expenses as $expense) {
            $total += (int)($expense['amount']);
        }
        echo "Total expenses : $$total" . PHP_EOL;
    }
}

function cliGuide() {
    echo "=== Expense Tracker Guide ===\n" . PHP_EOL;
    echo "- Add an expense: php expense-tracker.php add --description \"<name>\" --amount <value>\n" . PHP_EOL;
    echo "- Update an expense: php expense-tracker.php update --id <id> --description \"<name>\" --amount <value>" . PHP_EOL;
    echo "                     php expense-tracker.php update --id <id> --description \"<name>\"" . PHP_EOL;
    echo "                     php expense-tracker.php update --id <id> --amount <value>\n" . PHP_EOL;
    echo "- Delete an expense: php expense-tracker.php delete --id <id>\n" . PHP_EOL;
    echo "- View all expenses: php expense-tracker.php list\n" . PHP_EOL;
    echo "- View a summary of all expenses: php expense-tracker.php summary\n" . PHP_EOL;
    echo "- View a summary of expenses for a specific month (of current year): php expense-tracker.php summary --month <number>\n" . PHP_EOL;
}

