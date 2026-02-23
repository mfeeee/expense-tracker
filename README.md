# Expense Tracker

A simple command-line PHP application to track your expenses. This project is inspired by the [roadmap.sh Expense Tracker Project](https://roadmap.sh/projects/expense-tracker).

## Features
- Add, update, delete, and list expenses
- View total expenses
- View expenses summary by month
- Data stored in a local JSON file

## Usage

### Add an Expense
```
php expense-tracker.php add --description "<name>" --amount <value>
```

### Update an Expense
```
php expense-tracker.php update --id <id> --description "<name>" --amount <value>
php expense-tracker.php update --id <id> --description "<name>"
php expense-tracker.php update --id <id> --amount <value>
```

### Delete an Expense
```
php expense-tracker.php delete --id <id>
```

### List All Expenses
```
php expense-tracker.php list
```

### View Summary
```
php expense-tracker.php summary
php expense-tracker.php summary --month <number>
```

## Data Storage
All expenses are stored in `expenses.json` in the project directory.

## Future Features

- Add expense categories and allow users to filter expenses by category.
- Allow users to set a budget for each month and show a warning when the user exceeds the budget.
- Allow users to export expenses to a CSV file.

## Project Roadmap
For more ideas and features, see the [roadmap.sh Expense Tracker Project](https://roadmap.sh/projects/expense-tracker).

---

*This project is for learning and demonstration purposes.*
