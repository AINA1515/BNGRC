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

insert into dons (idTypeDons, nom, date_, quantite, prixUnitaire) values
(1, 'Riz', '2023-10-01 10:00:00', 100, 100000.00),
(2, 'tole', '2023-10-02 11:00:00', 200, 200000.00),
(3, 'euro', '2023-10-03 12:00:00', 150, 10.00),
(1, 'huile', '2023-10-04 13:00:00', 100, 10000);

insert into besoinsVille (idVille, idDons, date_, quantite, prixUnitaire) values
(1, 1, '2023-10-01 10:00:00', 100, 100000.00),
(2, 2, '2023-10-02 11:00:00', 200, 200000.00),
(3, 3, '2023-10-03 12:00:00', 150, 10.00),
(4, 4, '2023-10-04 13:00:00', 300, 10000.00);

insert into historiqueDons (idDons, date_, idVille) values
(1, '2023-10-01 10:00:00', 1),
(2, '2023-10-02 11:00:00', 2),
(3, '2023-10-03 12:00:00', 3),
(4, '2023-10-04 13:00:00', null);