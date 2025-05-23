# Guide Interactif pour BillFlow

Ce document identifie toutes les vues de l'application BillFlow qui nécessitent un guide interactif. Ces guides aideront les nouveaux utilisateurs à se familiariser avec l'interface et les fonctionnalités de l'application.

## Vues principales

### 1. Tableau de bord principal (dashboard.blade.php)
- Présentation générale du tableau de bord
- Explication des statistiques principales
- Navigation vers les différentes sections
- Graphiques et leur interprétation
- Activités récentes

### 2. Gestion des boutiques (shops/index.blade.php)
- Liste des boutiques
- Actions disponibles (voir, modifier, supprimer)
- Comment ajouter une nouvelle boutique
- Filtrage et recherche des boutiques

### 3. Tableau de bord de boutique (shops/dashboard.blade.php)
- Statistiques spécifiques à la boutique
- Filtres de période
- Graphiques de performance
- Section "Alertes de stock"
- Performance des vendeurs

### 4. Gestion des commissions (commissions/index.blade.php)
- Vue d'ensemble des commissions
- Filtres disponibles
- Comment marquer des commissions comme payées
- Exportation des données

### 5. Rapport de vendeur (commissions/vendor-report.blade.php)
- Informations du vendeur
- Graphiques de performance
- Filtres spécifiques
- Liste détaillée des commissions

### 6. Gestion des utilisateurs (users/index.blade.php)
- Liste des utilisateurs
- Filtrage par rôle et boutique
- Actions disponibles (voir détails, réinitialiser email)
- Permissions requises

### 7. Détails utilisateur (users/show.blade.php)
- Informations personnelles
- Boutiques assignées
- Commissions (pour les vendeurs)
- Options d'administration

### 8. Réinitialisation d'email (users/reset-email.blade.php)
- Formulaire de réinitialisation
- Informations de sécurité
- Confirmation requise

### 9. Gestion des produits (products/index.blade.php)
- Liste des produits
- Actions disponibles
- Filtrage et recherche
- Gestion du stock

### 10. Gestion des factures (bills/index.blade.php)
- Liste des factures
- Statuts des factures
- Actions disponibles
- Filtrage et exportation

### 11. Gestion des clients (clients/index.blade.php)
- Liste des clients
- Actions disponibles
- Comment ajouter un nouveau client
- Historique des achats

## Implémentation

Chaque guide sera implémenté à l'aide de la bibliothèque Intro.js. Les étapes suivantes seront suivies:

1. Ajouter l'attribut `data-page-name` au body de chaque vue
2. Créer une fonction spécifique dans `interactive-guide.js` pour chaque vue
3. Définir les étapes du guide pour chaque élément important de l'interface
4. Assurer que le guide s'affiche automatiquement lors de la première visite
5. Fournir un bouton d'aide flottant pour réactiver le guide

## Priorités d'implémentation

1. Tableau de bord principal
2. Gestion des boutiques
3. Gestion des commissions
4. Gestion des utilisateurs
5. Gestion des produits
6. Gestion des factures
7. Gestion des clients
