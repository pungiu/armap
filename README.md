
# Project Title

A brief description of what this project does and who it's for


## SETUP

Setup database for the project

```bash
  sudo mysql -u root -p
  
  CREATE DATABASE ar_location_db;
CREATE USER 'ar_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON ar_location_db.* TO 'ar_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

CREATE TABLE models (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    latitude DECIMAL(10, 7) NOT NULL,
    longitude DECIMAL(11, 7) NOT NULL,
    model_url VARCHAR(255) NOT NULL,
    base_scale FLOAT DEFAULT 5.0,
    min_scale FLOAT DEFAULT 0.5,
    max_scale FLOAT DEFAULT 15.0,
    target_altitude FLOAT DEFAULT 0.0,
    reference_distance FLOAT DEFAULT 25.0,
    visibility_threshold FLOAT DEFAULT 200.0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO models (name, latitude, longitude, model_url, base_scale, target_altitude, reference_distance, visibility_threshold) VALUES ('test', 46.3620104, 15.1134535, 'assets/untitled.glb', 1, 400, 50, 500);

mysql -u ar_user -p ar_location_db

```
    
## 🔗 Links
[![website](https://img.shields.io/website?url=http%3A//pungi.org/)](https://pungi.org/)


