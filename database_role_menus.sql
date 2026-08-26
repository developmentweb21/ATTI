CREATE TABLE IF NOT EXISTS menus (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_menu VARCHAR(80) NOT NULL,
  url VARCHAR(120) NOT NULL UNIQUE,
  icon VARCHAR(20) NOT NULL DEFAULT '•',
  urutan INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1
);
CREATE TABLE IF NOT EXISTS role_menus (
  role_id INT NOT NULL,
  menu_id INT NOT NULL,
  PRIMARY KEY (role_id, menu_id),
  CONSTRAINT fk_role_menus_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
  CONSTRAINT fk_role_menus_menu FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE
);
INSERT IGNORE INTO menus (id,nama_menu,url,icon,urutan) VALUES
(1,'Dashboard','dashboard','▦',1),(2,'Semua Tiket','tickets','◫',2),(3,'Buat Tiket','tickets/create','＋',3),(4,'Meja Teknisi','technician','⌁',4),(5,'Persetujuan','approval','✓',5),(6,'Master Data','admin','⚙',6),(7,'Akses Menu Role','admin/rolemenus','⚿',7);
INSERT IGNORE INTO role_menus (role_id,menu_id) VALUES
(1,1),(1,2),(1,3),(2,1),(2,2),(2,3),(2,4),(3,1),(3,2),(3,5),(4,1),(4,2),(5,1),(5,2),(5,3),(5,6),(5,7);
