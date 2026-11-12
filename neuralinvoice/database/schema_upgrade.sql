-- Neural Invoice - schema upgrade (run once via run-schema-upgrade.php)

-- Client logo
-- client_logo, tds_amount, received_net added via run-schema-upgrade.php if missing

-- Client projects (AMC / development) with payment milestones
CREATE TABLE IF NOT EXISTS client_projects (
  project_id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT NOT NULL,
  project_name VARCHAR(255) NOT NULL,
  payment_mode ENUM('percentage','module') NOT NULL DEFAULT 'percentage',
  total_value DECIMAL(15,2) NOT NULL DEFAULT 0,
  amc_start DATE NULL,
  amc_end DATE NULL,
  financial_year VARCHAR(12) NULL COMMENT 'e.g. 2025-26',
  amc_working_days INT NULL DEFAULT 0 COMMENT 'Mon-Fri count in FY',
  work_type VARCHAR(100) NULL,
  proposal_file VARCHAR(255) NULL,
  notes TEXT NULL,
  status TINYINT NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_client_projects_client (client_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS work_type_master (
  WorkTypeID INT AUTO_INCREMENT PRIMARY KEY,
  WorkTypeName VARCHAR(100) NOT NULL,
  DelStatus TINYINT NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_work_type_name (WorkTypeName)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS project_payment_milestones (
  milestone_id INT AUTO_INCREMENT PRIMARY KEY,
  project_id INT NOT NULL,
  milestone_label VARCHAR(255) NOT NULL,
  module_name VARCHAR(255) NULL,
  percentage DECIMAL(6,2) NULL,
  amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  due_date DATE NULL,
  is_paid TINYINT NOT NULL DEFAULT 0,
  paid_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  paid_date DATE NULL,
  sort_order INT NOT NULL DEFAULT 0,
  INDEX idx_milestones_project (project_id),
  CONSTRAINT fk_milestone_project FOREIGN KEY (project_id)
    REFERENCES client_projects(project_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
