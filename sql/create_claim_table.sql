CREATE TABLE IF NOT EXISTS `claim` (
    `claim_id` INT PRIMARY KEY AUTO_INCREMENT,
    `found_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `unique_features` TEXT NOT NULL,
    `proof_description` TEXT NOT NULL,
    `proof_image` VARCHAR(255),
    `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    `created_at` DATETIME NOT NULL,
    FOREIGN KEY (`found_id`) REFERENCES `found_item`(`found_id`),
    FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 