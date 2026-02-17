<!-- Comprehension du Sujet -->
# V1

    # Fonctionnalite 
        - formulaire :
                    -ville
                    -don
                    -type de don

    # objectif:
        tableau de bord
            -liste des villes + besoins
            -dons par villes

    # regle metier:
        -chaque besoin a un prix + quantite
        -prix unitaires fixe
        -Un besoin appartient a une ville

    # les entités principales :
        -Ville, Type de dons, Don, Besoin par ville, Historique des dons

    # Structure MVC Flight :
     -Creation Repo Git et inviter Chaque Membre
     -Chaque Membre travail dans une branche
 
# V2

    # Fonctionnalite:
    - Achat via dons en argent
        Utiliser les dons en argent pour acheter :
        besoins en nature
        besoins en matériaux
        Sélection achat depuis la page des besoins restants

    # objectif:
    Permettre transformation argent → besoins matériels

    # regle metier:
    Un don en argent peut financer un besoin en nature ou matériau
    montantAchat = (quantite × prixUnitaire)
    montantFinal = montantAchat + (montantAchat × frais%)
    frais achat est configurable
    On ne peut pas acheter si le montant argent restant est insuffisant

# V3

    # Fonctionnalite:
    On veut ajouter trois regle de distribution des dons au ville dans la simulation:
        -Le premier qui demande recoit le premier
        -Celui qui demande le mois recoit le premier
        -Regle de Proportionalite

    #regle metier: 
        -Premier demandeur(Trier les besoins par date croissante,Distribuer les dons jusqu’à épuisement)
        -Plus petit besoin d'abord(rier besoins par quantité restante croissante)
        -Proportionnalité(partVille = besoinVille / totalBesoinGlobal)


<!-- Partie code  To Do -->
<!-- ======================================================================================= -->
# V1

# Conception de base OK
Table a creer : ok -ville
                ok -typeDons
                ok -dons
                ok -besoinsVille
                ok -historiqueDons

Views a creer : ok -vue_besoins_par_ville
                ok -vue_dons_par_ville
                ok -vue_historique_complet

Donne de Test : dans data.sql ok

# Models a utiliser OK 

ok VilleModel : CRUD basique  

ok TypeDonsModel : getAll(),insert()

ok DonsModel : insert(), getAll(), getByType

ok BesoinVilleModel : insert(), getByVille(), getTotalBesoinByVille()

ok HistoriqueDonsModel : insert(), getByVille(), getByDate()

ok VueDonsParVilleModel : getDonParVille(), getView(), getByIdVilleAndTypeDon(), getByIdVilleAndNomDon()

ok VueHistoriqueModel() : getView(), getHistoriqueByTypeDon(), getHistoriqueByNomDon(), getHistoriqueByNomVille()

ok VueBesoinsParVilleModel : getView(), getByIdVille(), getByIdVilleAndTypeDon(), getByIdVilleAndNomDon(), getTotalMontantByVille(), getQuantiteParType()

# Controller OK

ok VilleController 
ok BesoinController
ok DonController
ok SimulationController

# DESIGN OK
    ok -Choix de Templates

# INTEGRATION OK 
    ok =>Definir les Routes get et post,Gerer recuperation parametres
    ok - dashboard.php {
        Affichage pour chaque ville => Besoins, Date, Quantite, PU, Prix Totale
    }
    ok -formBesoin.php
    ok -formDons.php
    ok -simulation.php

<!-- ======================================================================================= -->
# V2

# Conception de base OK

Table a creer :
    ok -achat
    ok -configuration

Modification table :
    ok -besoinsVille (ajout quantiteSatisfaite)
    ok -dons (ajout montantRestant si don argent)

Views a creer :
    ok -vue_besoins_restants
    ok -vue_achats_par_ville
    ok -vue_recapitulatif_global

Donne de Test : 
    ok -dons argent
    ok -besoins restants
    ok -frais achat par defaut

# Models a utiliser : OK

ok DonsModel :
    -getMontantRestantArgent()
    -decrementMontant()

ok BesoinVilleModel :
    -getBesoinsRestants()
    -updateQuantiteSatisfaite()

ok AchatModel :
    -insert()
    -getByVille()
    -getAll()
    -getTotalAchatByVille()

ok ConfigurationModel :
    -getFrais()
    -updateFrais()

ok VueBesoinsRestantsModel :
    -getByVille()

ok VueRecapitulatifModel :
    -getGlobalStats()

ok SimulationModel (V2 simple) :
    -simulerDispatchArgent()
    -validerDispatchArgent()

# Controller OK
    ok AchatController
    ok RecapController
    ok ConfigurationController
    ok SimulationController (maj logique V2)

# DESIGN OK
    ok -achat.php
    ok -besoins_restants.php
    ok -recap.php
    ok -bouton Simuler
    ok -bouton Valider
    ok -bouton Actualiser (Ajax)
    ok -message erreur dynamique

# INTEGRATION OK
    ok -route GET/POST achat
    ok -route GET recap
    ok -route GET recap/data (Ajax)
    ok -calcul frais achat automatique
    ok -verification montant insuffisant
    ok -verification depassement besoin
    ok -mise a jour dons argent apres validation
    ok -mise a jour quantite satisfaite
    
<!-- ======================================================================================= -->
# V3

# Conception de base OK

Table a creer :
    ok -distribution_log

Modification table :
    ok -historiqueDons (ajout regleDistribution)

Views a creer :
    ok -vue_distribution_par_regle
    ok -vue_simulation_detail

Donne de Test : dans data.sql ok
    ok -plusieurs villes
    ok -besoins avec dates differentes
    ok -cas proportionnel

# Models a utiliser OK
ok SimulationModel :
    -simulerFIFO()
    -simulerPlusPetitBesoin()
    -simulerProportionnel()
    -validerDistribution()

ok DistributionLogModel :
    -insert()
    -getByRegle()
    -getByVille()

ok BesoinVilleModel (maj) :
    -getAllBesoinsRestants()
    -getByDateDemande()

ok VueSimulationDetailModel :
    -getResultatSimulation()

# Controller OK

ok SimulationController (maj V3)
    -GET /simulation
    -POST /simulation/run
    -POST /simulation/validate

# INTEGRATION OK
    ok -tri par date pour Premier Demandeur
    ok -tri par quantite croissante pour petit besoin
    ok -calcul proportionnel
    ok -verifier somme distribuee correcte
    ok -verifier pas de depassement
    ok -log distribution apres validation
    ok -mise a jour dons
    ok -mise a jour besoins
