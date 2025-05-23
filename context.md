### Étapes d'implémentation du système de gestion de boutiques et vendeurs

## 1. Gestion des utilisateurs et rôles
- [x] Création des migrations pour ajouter les rôles aux utilisateurs
  - [x] Rôles: admin, manager, vendeur
  - [x] Taux de commission par vendeur
  - [x] Photo du vendeur
- [x] Mise à jour du modèle User avec les relations et méthodes d'autorisation
- [ ] Implémentation des tests d'autorisation
  - [ ] Tests pour les permissions d'admin
  - [ ] Tests pour les permissions de manager
  - [ ] Tests pour les permissions de vendeur
- [ ] Mise à jour des interfaces utilisateur selon les rôles
  - [ ] Interface admin pour gestion globale
  - [ ] Interface manager pour gestion de boutique
  - [ ] Interface vendeur avec accès limité

## 2. Gestion des boutiques
- [x] Création des tables pour les boutiques
- [x] Relation many-to-many entre boutiques et utilisateurs
- [x] Gestion des managers de boutique
- [ ] Interface d'administration des boutiques
  - [ ] CRUD complet des boutiques
  - [ ] Assignation des managers
  - [ ] Configuration des paramètres spécifiques
- [x] Association des vendeurs aux boutiques
- [ ] Tableau de bord spécifique par boutique
  - [ ] Résumé des ventes
  - [ ] Performance des vendeurs
  - [ ] État des stocks
- [x] Gestion des équipements des vendeurs
  - [x] Attribution d'équipement aux vendeurs
  - [x] Suivi de l'état des équipements
  - [x] Processus de retour d'équipement

## 3. Système de vente et attribution des ventes
- [x] Mise à jour de la table des factures pour inclure l'ID du vendeur
- [ ] Modifications de l'interface de vente pour sélectionner le vendeur
  - [ ] Dropdown de sélection du vendeur
  - [ ] Filtrage par boutique active
  - [ ] Option de recherche rapide
- [x] Ajout du champ boutique sur les factures
- [x] Enregistrement automatique du vendeur actuel lors de la création
- [ ] Statistiques de vente par vendeur
  - [ ] Graphiques de performance
  - [ ] Comparaison entre périodes
  - [ ] Classement des vendeurs
- [x] QR codes sur les factures pour validation et suivi

## 4. Système de commissions
- [x] Création de la table de commissions
- [x] Calcul automatique des commissions lors des ventes
  - [x] Commission sur les ventes standards
  - [ ] Commission sur les surplus
    - [ ] Définition des seuils de surplus
    - [ ] Taux progressifs selon performance
  - [x] Commission sur les trocs
- [ ] Interface de gestion des commissions
  - [ ] Vue d'ensemble des commissions
  - [ ] Approbation des commissions
  - [ ] Historique des paiements
- [ ] Rapport de commissions par vendeur
  - [ ] Ventilation par type de produit
  - [ ] Historique mensuel
  - [ ] Prévisions de commissions
- [ ] Système de paiement des commissions
  - [ ] Intégration avec la paie
  - [ ] Suivi des paiements
  - [ ] Reçus de commission

## 5. Gestion des trocs
- [x] Création des tables pour les trocs
- [x] Enregistrement des produits donnés par le client
  - [x] Support d'images pour les produits troqués
  - [x] Description détaillée des articles
  - [x] Estimation de la valeur
- [x] Gestion des produits reçus par le client
- [x] Calcul de l'équilibrage du troc
- [ ] Interface de saisie des trocs
  - [ ] Formulaire de saisie rapide
  - [ ] Calcul en temps réel de la valeur
  - [ ] Validation des produits acceptables
- [x] Signature électronique pour les trocs

## 6. Système de livraison
- [x] Création des tables pour les livraisons
- [x] Relation avec les factures et les trocs
- [x] Suivi d'état des livraisons
- [ ] Interface de gestion des livraisons
  - [ ] Planification des livraisons
  - [ ] Attribution automatique des zones
  - [ ] Notifications clients
- [x] Assignation des livreurs
- [x] Notification de changement d'état

## 7. Impression et réimpression des factures
- [x] Ajout des champs pour suivre les impressions
  - [x] Compteur de réimpressions
  - [x] Date de dernière impression
- [x] Marque visuelle pour les réimpressions
- [x] Génération de QR code sur les factures
  - [x] Encodage des informations essentielles (référence, montant, date)
  - [x] Validation de l'authenticité des factures
- [x] Support de signatures sur les factures
- [ ] Interface de réimpression
  - [ ] Recherche de facture
  - [ ] Prévisualisation avant impression
  - [ ] Journalisation des réimpressions

## 8. Tableau de bord et rapports
- [ ] Widget de performance des vendeurs
  - [ ] Ventes par jour/semaine/mois
  - [ ] Taux de conversion
  - [ ] Panier moyen
- [ ] Stats par boutique et par vendeur
  - [ ] Comparaison entre boutiques
  - [ ] Évolution temporelle
  - [ ] Analyse des produits phares
- [ ] Commissions à payer et historique
  - [ ] Vue consolidée par vendeur
  - [ ] Filtrage par période
  - [ ] Export pour la comptabilité
- [ ] Suivi des inventaires et alertes de stock
  - [ ] Seuils d'alerte paramétrables
  - [ ] Prévision des ruptures de stock
  - [ ] Suggestions de réapprovisionnement
- [ ] Rapports de trocs et de livraisons
  - [ ] Valeur des trocs par période
  - [ ] Performance des livraisons
  - [ ] Taux de satisfaction client
- [x] Exportation des données
  - [x] Exportation des clients
  - [x] Exportation des factures
  - [x] Exportation des statistiques

## 9. Optimisations et fonctionnalités avancées
- [ ] Système de recommandation produits
  - [ ] Basé sur l'historique client
  - [ ] Suggestions croisées entre produits
- [ ] Application mobile pour vendeurs
  - [ ] Consultation des commissions
  - [ ] Suivi des ventes en temps réel
- [ ] Intégration de l'IA pour analyse prédictive
  - [ ] Prévision des ventes
  - [ ] Optimisation du stock
- [ ] Système de fidélité multi-boutiques
  - [ ] Points cumulables
  - [ ] Avantages par palier
- [ ] Module de formation des vendeurs
  - [ ] Suivi des compétences
  - [ ] Parcours personnalisés