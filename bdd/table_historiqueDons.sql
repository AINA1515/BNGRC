-- Table: historiqueDons
CREATE TABLE IF NOT EXISTS historiqueDons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idVille INT NOT NULL,
    idDons INT NOT NULL,
    quantite INT NOT NULL,
    date_ DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idVille) REFERENCES villes(id),
    FOREIGN KEY (idDons) REFERENCES dons(id)
);