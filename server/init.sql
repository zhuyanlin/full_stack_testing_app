CREATE DATABASE IF NOT EXISTS cart_db;
USE cart_db;

CREATE TABLE IF NOT EXISTS cart_items (
                                          id INT AUTO_INCREMENT PRIMARY KEY,
                                          title VARCHAR(255) NOT NULL,
                                          size VARCHAR(10) NOT NULL,
                                          price DECIMAL(10, 2) NOT NULL,
                                          quantity INT NOT NULL,
                                          image TEXT NOT NULL
);
