-- Add any missing columns if they don't exist
ALTER TABLE `claim`
ADD COLUMN IF NOT EXISTS `unique_features` TEXT,
ADD COLUMN IF NOT EXISTS `proof_description` TEXT,
ADD COLUMN IF NOT EXISTS `proof_image` VARCHAR(255),
ADD COLUMN IF NOT EXISTS `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS `claimant_name` VARCHAR(100),
ADD COLUMN IF NOT EXISTS `claimant_email` VARCHAR(100),
ADD COLUMN IF NOT EXISTS `claimant_phone` VARCHAR(20);

-- Add foreign key constraints (without IF NOT EXISTS)
-- Note: These will fail if the constraints already exist, which is fine
-- You can run these statements one by one if you encounter errors
ALTER TABLE `claim`
ADD CONSTRAINT `fk_claim_found_item` FOREIGN KEY (`found_id`) REFERENCES `found_item`(`found_id`);

ALTER TABLE `claim`
ADD CONSTRAINT `fk_claim_lost_item` FOREIGN KEY (`lost_id`) REFERENCES `lost_item`(`lost_id`);

ALTER TABLE `claim`
ADD CONSTRAINT `fk_claim_user` FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`); 