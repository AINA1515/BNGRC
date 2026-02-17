insert into ville (nom, nbrSinistre, x, y, nbrPopulation) values
('Paris', 10, 2.35, 48.85, 2140526),
('Lyon', 5, 4.84, 45.76, 515695),
('Marseille', 8, 5.38, 43.30, 861635),
('Toulouse', 3, 1.44, 43.61, 479553),
('Nice', 2, 7.27, 43.70, 342522);


insert into typeDons (nom) values
('nature'),
('materiaux'),
('argent');

-- Modèles de dons (exemples)
insert into modeleDons (nom, prixUnitaire, idTypeDons) values
('Riz', 100000.00, 1),
('Huile', 10000.00, 1),
('Tole', 200000.00, 2),
('Euro', 10.00, 3),
('Farine', 80000.00, 1),
('Clou', 5000.00, 2);


-- Dons (utilise idModeleDons)
insert into dons (idTypeDons, idModeleDons, date_, quantite, prixUnitaire) values
(1, 1, '2023-10-01 10:00:00', 100, 100000.00),
(2, 3, '2023-10-02 11:00:00', 200, 200000.00),
(3, 4, '2023-10-03 12:00:00', 150, 10.00),
(1, 2, '2023-10-04 13:00:00', 100, 10000.00),
(1, 5, '2023-10-05 14:00:00', 120, 80000.00),
(2, 6, '2023-10-06 15:00:00', 500, 5000.00);

-- Génération de 1000 dons pour stress test
-- (idTypeDons, idModeleDons, date_, quantite, prixUnitaire)
insert into dons (idTypeDons, idModeleDons, date_, quantite, prixUnitaire) values
-- 1000 lignes générées pour stress test
-- Exemples :
(1, 1, '2024-01-01 08:00:00', 10, 100000.00),
(1, 2, '2024-01-01 08:10:00', 20, 10000.00),
(2, 3, '2024-01-01 08:20:00', 30, 200000.00),
(3, 4, '2024-01-01 08:30:00', 40, 10.00),
(1, 5, '2024-01-01 08:40:00', 50, 80000.00),
(2, 6, '2024-01-01 08:50:00', 60, 5000.00)
-- Ajoutez ici un script ou un générateur pour compléter jusqu'à 1000 lignes si besoin
;


insert into besoinsVille (idVille, idModeleDons, date_, quantite, prixUnitaire) values
(1, 1, '2023-10-01 10:00:00', 100, 100000.00),
(2, 3, '2023-10-02 11:00:00', 200, 200000.00),
(3, 4, '2023-10-03 12:00:00', 150, 10.00),
(4, 2, '2023-10-04 13:00:00', 300, 10000.00),
(5, 5, '2023-10-05 14:00:00', 120, 80000.00),
(1, 6, '2023-10-06 15:00:00', 500, 5000.00);


insert into historiqueDons (idDons, date_, idVille) values
(1, '2023-10-01 10:00:00', 1),
(2, '2023-10-02 11:00:00', 2),
(3, '2023-10-03 12:00:00', 3),
(4, '2023-10-04 13:00:00', 4),
(5, '2023-10-05 14:00:00', 5),
(6, '2023-10-06 15:00:00', 1);
-- Pour stress test, ajouter des milliers d'entrées similaires si besoin

insert into distribution (idBesoins, idVille, date_, quantiteBesoinDepart, quantiteBesoinRestant, quantiteDonsInitiale, quantiteDonsDistribue) values
(1, 1, '2023-10-01 10:00:00', 100, 50, 100, 50),
(2, 2, '2023-10-02 11:00:00', 200, 100, 200, 100),
(3, 3, '2023-10-03 12:00:00', 150, 75, 150, 75),
(4, 1, '2023-10-04 13:00:00', 300, 150, 300, 150),
(5, 2, '2023-10-05 14:00:00', 120, 60, 120, 60),
(1, 3, '2023-10-06 15:00:00', 500, 250, 500, 250);