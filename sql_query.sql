Hiten sql query :



ERROR FIX: travel_agencies.country cannot be null



Table: travel_agencies

Column: country



The app now defaults blank country to "United Kingdom" (same as existing rows).

You do NOT need to run SQL for the popup to work.



OPTIONAL (only if you want country to allow NULL in DB):


ALTER TABLE travel_agencies

  MODIFY country VARCHAR(191) NULL DEFAULT 'United Kingdom';


ALTER TABLE enquiries

  ADD COLUMN check_in DATE NULL AFTER day,

  ADD COLUMN check_out DATE NULL AFTER check_in;


ALTER TABLE enquiries

  ADD COLUMN has_tax TINYINT(1) NOT NULL DEFAULT 0 AFTER total_revenue,

  ADD COLUMN tax_percentage DECIMAL(5,2) NULL AFTER has_tax,

  ADD COLUMN tax_revenue DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER tax_percentage;



ALTER TABLE enquiries

  ADD COLUMN check_in_day VARCHAR(20) NULL AFTER check_in;



-- Status master module

CREATE TABLE IF NOT EXISTS status_masters (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(191) NOT NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY status_masters_title_unique (title)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO status_masters (title, status, created_at, updated_at) VALUES
  ('new', 'active', NOW(), NOW()),
  ('follow_up', 'active', NOW(), NOW()),
  ('quoted', 'active', NOW(), NOW()),
  ('confirmed', 'active', NOW(), NOW()),
  ('lost', 'active', NOW(), NOW()),
  ('cancelled', 'active', NOW(), NOW())
ON DUPLICATE KEY UPDATE status = VALUES(status);

ALTER TABLE enquiries
  MODIFY status VARCHAR(191) NOT NULL DEFAULT 'new';

INSERT INTO permissions (name, guard_name, created_at, updated_at) VALUES
  ('statuses.view', 'web', NOW(), NOW()),
  ('statuses.create', 'web', NOW(), NOW()),
  ('statuses.edit', 'web', NOW(), NOW()),
  ('statuses.delete', 'web', NOW(), NOW())
ON DUPLICATE KEY UPDATE name = VALUES(name);
