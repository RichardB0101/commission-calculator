## Commission calculator (Vue 3 + Laravel 11)

### Estimated time: 3-4 hours
### Actual time: 6 hours (yet not finished with api)
### Powered by: 2 energy drinks and late programming after work

![Screenshot_1](https://github.com/RichardB0101/commission-calculator/assets/86617931/76b23cde-68c5-4418-9113-c613ba7acb83)
![Screenshot_2](https://github.com/RichardB0101/commission-calculator/assets/86617931/9b793565-d870-49e0-917b-c8e98a1517e3)

-----------------------
# How to run: 
## Requirements

- Docker
- Docker Compose
- WSL (if you are using windows, to be able to use linux commands)

## Setup Instructions

Follow these steps to set up and run the project locally.

### 1. Clone the Repository
### 2. Install Dependencies

Run the following command to install the PHP dependencies:

```bash
composer install
```
### 3. Start Laravel Sail

Laravel Sail provides a simple way to set up a local development environment using Docker. Start the Sail environment:

```bash
./vendor/bin/sail up -d
```

### 4. Generate Application Key

Generate the Laravel application key:

```bash
./vendor/bin/sail artisan key:generate
```

### 5. Run Migrations

After starting the environment, run the database migrations:

```bash
./vendor/bin/sail artisan migrate
```

### 6. Install Frontend Dependencies

Install the frontend dependencies using npm:

```bash
./vendor/bin/sail npm install
```
### 7. Build Frontend Assets

Build the frontend assets using the following command:

```bash
./vendor/bin/sail npm run dev
```
### 8. Run Tests

To ensure everything is set up correctly, run the tests:

```bash
./vendor/bin/sail test
```
### Running the Application

With Sail running (`./vendor/bin/sail up -d`), you can access the application at:

```plaintext
http://localhost
```

-----------------------
# Task description:

## Situation

This application allows private and business clients to `deposit` and `withdraw` funds to and from accounts in multiple currencies. Clients may be charged a commission fee.

You need to create an application that handles operations provided in CSV format and calculates a commission fee based on defined rules.

## Commission Fee Calculation

- Commission fee is always calculated in the currency of the operation. For example, if you `withdraw` or `deposit` in US dollars, then the commission fee is also in US dollars.
- Commission fees are rounded up to the currency's decimal places. For example, `0.023 EUR` should be rounded up to `0.03 EUR`.

### Deposit Rule

All deposits are charged 0.03% of the deposit amount.

### Withdraw Rules

There are different calculation rules for `withdraw` of `private` and `business` clients.

**Private Clients**

- Commission fee - 0.3% of the withdrawn amount.
- 1000.00 EUR per week (from Monday to Sunday) is free of charge. Only for the first 3 withdraw operations per week. The 4th and subsequent operations are calculated using the rule above (0.3%). If the total free of charge amount is exceeded, the commission is calculated only for the exceeded amount (i.e., up to 1000.00 EUR no commission fee is applied).

For this rule, you will need to convert the operation amount if it's not in Euros. Please use rates provided by [https://api.exchangeratesapi.io/latest](https://api.exchangeratesapi.io/latest).

**Business Clients**

- Commission fee - 0.5% of the withdrawn amount.

## Input Data

Operations are given in a CSV file. In each line of the file, the following data is provided:

1. Operation date in format `Y-m-d`
2. User's identifier, number
3. User's type, one of `private` or `business`
4. Operation type, one of `deposit` or `withdraw`
5. Operation amount (for example `2.12` or `3`)
6. Operation currency, one of `EUR`, `USD`, `JPY`

## Expected Result

Output of calculated commission fees for each operation.

In each output line, only the final calculated commission fee for a specific operation must be provided without the currency.

## Example Usage

```plaintext
➜  cat input.csv 
2014-12-31,4,private,withdraw,1200.00,EUR
2015-01-01,4,private,withdraw,1000.00,EUR
2016-01-05,4,private,withdraw,1000.00,EUR
2016-01-05,1,private,deposit,200.00,EUR
2016-01-06,2,business,withdraw,300.00,EUR
2016-01-06,1,private,withdraw,30000,JPY
2016-01-07,1,private,withdraw,1000.00,EUR
2016-01-07,1,private,withdraw,100.00,USD
2016-01-10,1,private,withdraw,100.00,EUR
2016-01-10,2,business,deposit,10000.00,EUR
2016-01-10,3,private,withdraw,1000.00,EUR
2016-02-15,1,private,withdraw,300.00,EUR
2016-02-19,5,private,withdraw,3000000,JPY

➜  php script.php input.csv
0.60
3.00
0.00
0.06
1.50
0
0.70
0.30
0.30
3.00
0.00
0.00
8612
