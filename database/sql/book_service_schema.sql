-- ============================================
-- BookService Schema + Dummy Data
-- ============================================

-- Users
CREATE TABLE IF NOT EXISTS `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `phone` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Books
CREATE TABLE IF NOT EXISTS `books` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `available` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Loans
CREATE TABLE IF NOT EXISTS `loans` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `book_id` BIGINT UNSIGNED NOT NULL,
    `loan_date` DATE NOT NULL,
    `due_date` DATE NULL,
    `status` VARCHAR(255) NOT NULL DEFAULT 'borrowed',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    CONSTRAINT `loans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `loans_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Loan Histories
CREATE TABLE IF NOT EXISTS `loan_histories` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `book_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `borrow_date` DATE NOT NULL,
    `return_date` DATE NULL,
    `status` VARCHAR(255) NOT NULL DEFAULT 'completed',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    CONSTRAINT `loan_histories_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
    CONSTRAINT `loan_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Dummy Data
-- ============================================

INSERT INTO `users` (`name`, `email`, `phone`, `created_at`, `updated_at`) VALUES
('Citra', 'citra@gmail.com', '08123456789', NOW(), NOW());

INSERT INTO `books` (`title`, `available`, `created_at`, `updated_at`) VALUES
('Laravel for Beginners', 0, NOW(), NOW()),
('Clean Code', 1, NOW(), NOW()),
('Design Patterns', 1, NOW(), NOW()),
('The Pragmatic Programmer', 1, NOW(), NOW()),
('Refactoring', 1, NOW(), NOW());

INSERT INTO `loans` (`user_id`, `book_id`, `loan_date`, `due_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'borrowed', NOW(), NOW());

INSERT INTO `loan_histories` (`book_id`, `user_id`, `borrow_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-04-10', '2026-04-17', 'completed', NOW(), NOW()),
(2, 1, '2026-04-05', '2026-04-12', 'completed', NOW(), NOW()),
(3, 1, '2026-04-20', NULL, 'active', NOW(), NOW());
