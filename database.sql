-- users table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    currency TEXT NOT NULL,
    starting_balance REAL DEFAULT 0
);

-- transactions table
CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    date TEXT NOT NULL,
    description TEXT NOT NULL,
    amount REAL NOT NULL,
    category TEXT,
    type TEXT CHECK(type IN ('income', 'expense')) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- goals table
CREATE TABLE IF NOT EXISTS goals (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    target_savings REAL,
    preferred_minimum_balance REAL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
