CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('guru', 'siswa')
);
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    code VARCHAR(10) UNIQUE,
    teacher_id INT,
    FOREIGN KEY (teacher_id) REFERENCES users(id)
);

CREATE TABLE class_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT,
    student_id INT,
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (student_id) REFERENCES users(id)
);
CREATE TABLE materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT,
    title VARCHAR(255),
    file_url VARCHAR(255), -- bisa berupa nama file atau URL
    is_link BOOLEAN DEFAULT 0, -- 0 = file, 1 = link
    uploaded_by INT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
);
