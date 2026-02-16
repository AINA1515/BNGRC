# Fonctionnalite
    - formulaire 
        -ville
        -don
            -type de don

# objectif:
    tableau de bord
        -liste des villes + besoins
        -dons par villes

# regle:
    -chaque besoin a un prix + quantite(prix unitaires fixe)


#  BASE :( ok ): BRYAN
    -table
        -ville
            -nom
            -nbr sinistre
            -x
            -y
            -nbr total personne

        -dons
            -idTypeDons
            -nom

        -typeDons
            -nom

        -historiquesDons
            -idDons
            -idTypeDons
            -date
            -idVille

        -besoinsVille
            -idVille
            -idDons
            -idTypeDons
            -quantite
            -prixUnitaire
    -vues :
        -besoin par ville 
        -dons par ville
        -historique avec ville avec dons


# FONCTIONS : AINA
    -forAll:
        -CRUD
    -ville:
    -dons
    -typeDons
    -historiqueDons
    -besoinsVille

# DESIGN : TIANIAINA
    -getTemplates

# INTEGRATION : TIANIAINA
    -dashboard
    -formulaire
        -BESOIN
        -interface CRUD
        