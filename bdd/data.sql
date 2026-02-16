insert into ville (nom, nbrSinistre, x, y, nbrPopulation) values
('Paris', 10, 2.35, 48.85, 2140526),
('Lyon', 5, 4.84, 45.76, 515695),
('Marseille', 8, 5.38, 43.30, 861635),
('Toulouse', 3, 1.44, 43.61, 479553),
('Nice', 2, 7.27, 43.70, 342522);

insert into typeDons (nom) values
('Alimentaire'),
('Hydratation'),
('Vêtements'),
('Autres');

insert into dons (idTypeDons, nom, quantite, prixUnitaire) values
(1, 'Riz', 100, 50.00),
(2, 'Haricot', 200, 30.00),
(3, 'Eau', 150, 20.00),
(4, 'Vêtements', 300, 15.00);

insert into besoinsVille (idVille, idDons, quantite, prixUnitaire) values
(1, 1, 100, 50.00),
(2, 2, 200, 30.00),
(3, 3, 150, 20.00),
(4, 4, 300, 15.00);

insert into historiqueDons (idDons, date_, idVille) values
(1, '2023-10-01 10:00:00', 1),
(2, '2023-10-02 11:00:00', 2),
(3, '2023-10-03 12:00:00', 3),
(4, '2023-10-04 13:00:00', null);