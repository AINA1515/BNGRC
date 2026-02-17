drop database if exists BNGRC;
create database BNGRC;
use BNGRC;

create table ville(
    id int primary key auto_increment,
    nom varchar(50),
    nbrSinistre int,
    x decimal(6,2),
    y decimal(6,2),
    nbrPopulation int
);

create table dons(
    id int primary key auto_increment,
    idTypeDons int,
    nom varchar(50),
    date_ datetime,
    quantite int,
    prixUnitaire decimal(8,2)
);

create table typeDons(
    id int primary key auto_increment,
    nom varchar(50)
);

create table historiqueDons(
    id int primary key auto_increment,
    idDons int,
    date_ datetime,
    idVille int
);

create table besoinsVille(
    id int primary key auto_increment,
    idVille int,
    idDons int,
    date_ datetime,
    quantite int,
    prixUnitaire decimal(8,2)
);

create view vue_besoins_par_ville as
select 
    v.id as idVille,
    v.nom as nomVille,
    td.nom as typeDon,
    d.nom as nomDon,
    bv.quantite,
    bv.prixUnitaire,
    (bv.quantite * bv.prixUnitaire) as montantTotal
from besoinsVille bv
join ville v on v.id = bv.idVille
join dons d on d.id = bv.idDons
join typeDons td on td.id = bv.idTypeDons;

create view vue_dons_par_ville as
select
    v.id as idVille,
    v.nom as nomVille,
    td.nom as typeDon,
    d.nom as nomDon,
    h.date_ as dateDon
from historiqueDons h
join ville v on v.id = h.idVille
join dons d on d.id = h.idDons
join typeDons td on td.id = d.idTypeDons;

create view vue_historique_complet as
select
    h.id as idHistorique,
    v.nom as nomVille,
    v.nbrSinistre,
    v.nbrPopulation,
    v.x,
    v.y,
    td.nom as typeDon,
    d.nom as nomDon,
    h.date_ as dateDon
from historiqueDons h
join ville v on v.id = h.idVille
join dons d on d.id = h.idDons
join typeDons td on td.id = d.idTypeDons;


/* Plus Tard */

alter table historiqueDons
add foreign key (idVille) references ville(id),
add foreign key (idDons) references dons(id);

alter table dons
add foreign key (idTypeDons) references typeDons(id);

alter table besoinsVille
add foreign key (idVille) references ville(id),
add foreign key (idDons) references dons(id);


