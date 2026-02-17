drop database if exists BNGRC;
create database BNGRC;
use BNGRC;

drop table if exists distribution;
drop table if exists besoinsVille;
drop table if exists historiqueDons;
drop table if exists achat;
drop table if exists dons;
drop table if exists ville;
drop table if exists entrepot;
drop table if exists modeleDons;
drop table if exists typeDons;

create table ville(
    id int primary key auto_increment,
    nom varchar(50),
    nbrSinistre int,
    x decimal(6,2),
    y decimal(6,2),
    nbrPopulation int
);


create table typeDons(
    id int primary key auto_increment,
    nom varchar(50)
);

-- Table des modèles de dons (ex: riz, huile, farine, clou)
create table modeleDons(
    id int primary key auto_increment,
    nom varchar(50) not null,
    prixUnitaire decimal(8,2) not null,
    idTypeDons int not null,
    foreign key (idTypeDons) references typeDons(id)
);


create table dons(
    id int primary key auto_increment,
    idTypeDons int,
    idModeleDons int not null,
    date_ datetime,
    quantite int,
    prixUnitaire decimal(8,2),
    foreign key (idModeleDons) references modeleDons(id)
);

create table historiqueDons(
    id int primary key auto_increment,
    idDons int,
    date_ datetime,
    idVille int,
    quantite int,
    foreign key (idDons) references dons(id),
    foreign key (idVille) references ville(id)
);

create table besoinsVille(
    id int primary key auto_increment,
    ordre int,
    idVille int,
    idModeleDons int not null,
    date_ datetime,
    quantite int,
    prixUnitaire decimal(8,2),
    foreign key (idVille) references ville(id),
    foreign key (idModeleDons) references modeleDons(id)
);

create table achat(
    id int primary key auto_increment,
    idDons int,
    date_ datetime,
    quantite int,
    pourcentageAchat decimal(5,2),
    prixUnitaire decimal(8,2),
    foreign key (idDons) references dons(id)
);

create table distribution(
    id int primary key auto_increment,
    idBesoins int,
    idVille int,
    date_ datetime,
    quantiteBesoinDepart int,
    quantiteBesoinRestant int,
    quantiteDonsInitiale int,
    quantiteDonsDistribue int,
    prixUnitaire decimal(8,2),
    foreign key (idBesoins) references besoinsVille(id),
    foreign key (idVille) references ville(id)
);

create table entrepot(
    id int primary key auto_increment,
    idModeleDons int not null,
    quantite int,
    foreign key (idModeleDons) references modeleDons(id)
);

create view vue_besoins_par_ville as
select 
    v.id as idVille,
    v.nom as nomVille,
    td.nom as typeDon,
    md.nom as nomDon,
    bv.quantite,
    bv.prixUnitaire,
    (bv.quantite * bv.prixUnitaire) as montantTotal
from besoinsVille bv
join ville v on v.id = bv.idVille
join modeleDons md on md.id = bv.idModeleDons
join typeDons td on td.id = md.idTypeDons;

create view vue_dons_par_ville as
select
    v.id as idVille,
    v.nom as nomVille,
    td.nom as typeDon,
    md.nom as nomDon,
    h.date_ as dateDon
from historiqueDons h
join ville v on v.id = h.idVille
join dons d on d.id = h.idDons
join modeleDons md on md.id = d.idModeleDons
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
    md.nom as nomDon,
    h.date_ as dateDon
from historiqueDons h
join ville v on v.id = h.idVille
join dons d on d.id = h.idDons
join modeleDons md on md.id = d.idModeleDons
join typeDons td on td.id = d.idTypeDons;


/* Plus Tard */

alter table historiqueDons
add foreign key (idVille) references ville(id),
add foreign key (idDons) references dons(id);

alter table dons
add foreign key (idTypeDons) references typeDons(id);




