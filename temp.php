all i need is when clicked on placebet in play.js
keep the current functionality as it is but add to it too:

We will use mainly raking table for this.
this is the table i created for that:
    CREATE TABLE raking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    username VARCHAR(255) NOT NULL,
    total_wager DECIMAL(15, 2) DEFAULT 0,
    rank INT DEFAULT 0,
    claimable_rakeback DECIMAL(15, 2) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

for username userid balance rank etc we still use
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rsn VARCHAR(50),
    rank VARCHAR(20) DEFAULT 'User',
    balance DECIMAL(10, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

see bet amount.

add that to total wager amount of last bet of userid.
see username add that to username
see userid add that to userid
make a new id for the bet.

then if amount wager is > then a certain amount add +1 in the users table rank

    ['rank' => 0, 'minWager' => 0, 'rakeback' => 0.5],
    ['rank' => 1, 'minWager' => 10, 'rakeback' => 0.5],
    ['rank' => 2, 'minWager' => 25, 'rakeback' => 1.0],
    ['rank' => 3, 'minWager' => 50, 'rakeback' => 1.5],
    ['rank' => 4, 'minWager' => 250, 'rakeback' => 2.0],
    ['rank' => 5, 'minWager' => 500, 'rakeback' => 2.5],
    ['rank' => 6, 'minWager' => 1000, 'rakeback' => 3.0],
    ['rank' => 7, 'minWager' => 5000, 'rakeback' => 3.5],
    ['rank' => 8, 'minWager' => 10000, 'rakeback' => 4.0],
    ['rank' => 9, 'minWager' => 50000, 'rakeback' => 5.0]

    if user is rank do bet-amount * rakeback 
    add this number to previous amount of userid last claimable_rakeback
also give me an sql with timestamp that i can add to the table.