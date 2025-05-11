# 🌍 AR Map

AR Map je PHP projekt z integriranim A-Frame in LeafletJS za prikaz razširjene resničnosti in geolokacijskih 3D modelov (.glb) na spletni mapi. Aplikacija omogoča prijavo, registracijo, ustvarjanje in pridruževanje skupinam ter nalaganje 3D modelov, ki se nato prikažejo kot markerji na zemljevidu.

![GitHub Screenshot](https://raw.githubusercontent.com/pungiu/armap/main/assets/github_preview.png)

---

## 📦 Funkcionalnosti

- 🌐 LeafletJS zemljevid z AR integracijo (A-Frame, AR.js)
- 📍 Prikaz markerjev iz baze (tabela `models`)
- 🧑‍🤝‍🧑 Registracija, prijava uporabnikov
- 👥 Ustvarjanje in pridruževanje skupinam (z geslom)
- 📤 Nalaganje `.glb` modelov z lokacijo
- 🔐 Varnostno shranjevanje gesel (password_hash)
- 🧭 Responsive dizajn (TailwindCSS + minimalističen UI)

---

## 🛠️ Namestitev in zagon

### 1. Kloniraj repozitorij

```bash

git clone https://github.com/pungiu/armap.git
cd armap

CREATE DATABASE ar_location_db;
USE ar_location_db;

CREATE TABLE usr (
  id INT(11) NOT NULL AUTO_INCREMENT,
  username VARCHAR(256) NOT NULL,
  mail VARCHAR(256) NOT NULL,
  password VARCHAR(1024) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE groups (
  id INT(11) NOT NULL AUTO_INCREMENT,
  group_name VARCHAR(100) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  description TEXT DEFAULT NULL,
  created_by_user_id INT(11) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY (created_by_user_id),
  CONSTRAINT groups_ibfk_1 FOREIGN KEY (created_by_user_id) REFERENCES usr(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE group_members (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  group_id INT(11) NOT NULL,
  joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY (user_id),
  KEY (group_id),
  CONSTRAINT group_members_ibfk_1 FOREIGN KEY (user_id) REFERENCES usr(id),
  CONSTRAINT group_members_ibfk_2 FOREIGN KEY (group_id) REFERENCES groups(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE models (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  latitude DOUBLE NOT NULL,
  longitude DOUBLE NOT NULL,
  model_url VARCHAR(512) NOT NULL,
  base_scale FLOAT DEFAULT 1.0,
  min_scale FLOAT DEFAULT 0.5,
  max_scale FLOAT DEFAULT 2.0,
  reference_distance FLOAT DEFAULT 10.0,
  visibility_threshold FLOAT DEFAULT 100.0,
  target_altitude FLOAT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  created_by_user_id INT(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_created_by_user_id (created_by_user_id),
  CONSTRAINT models_fk_user FOREIGN KEY (created_by_user_id) REFERENCES usr(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

