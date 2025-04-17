/**
 * Guide interactif pour BillFlow
 */
class InteractiveGuide {
    constructor() {
        this.introJs = introJs();
        this.pageName = document.body.dataset.pageName;
        this.isFirstTime = localStorage.getItem('guideShown_' + this.pageName) !== 'true';
        this.setupButtons();
    }

    /**
     * Configurer les boutons d'aide
     */
    setupButtons() {
        // Ajouter le bouton d'aide flottant
        const helpButton = document.createElement('button');
        helpButton.id = 'help-button';
        helpButton.className = 'help-button';
        helpButton.innerHTML = '<i class="fas fa-question-circle"></i>';
        helpButton.title = 'Afficher le guide';
        document.body.appendChild(helpButton);

        // Écouter les clics sur le bouton d'aide
        helpButton.addEventListener('click', () => this.startGuide());

        // Afficher automatiquement le guide pour la première visite
        if (this.isFirstTime) {
            this.startGuide();
            localStorage.setItem('guideShown_' + this.pageName, 'true');
        }
    }

    /**
     * Lancer le guide approprié pour la page actuelle
     */
    startGuide() {
        switch (this.pageName) {
            case 'dashboard':
                this.dashboardGuide();
                break;
            case 'shops-index':
                this.shopsIndexGuide();
                break;
            case 'shop-dashboard':
                this.shopDashboardGuide();
                break;
            case 'commissions-index':
                this.commissionsIndexGuide();
                break;
            case 'bills-index':
                this.billsIndexGuide();
                break;
            case 'products-index':
                this.productsIndexGuide();
                break;
            case 'clients-index':
                this.clientsIndexGuide();
                break;
            default:
                this.genericGuide();
                break;
        }
    }

    /**
     * Guide générique pour les pages sans guide spécifique
     */
    genericGuide() {
        this.introJs.setOptions({
            steps: [
                {
                    title: 'Bienvenue sur BillFlow',
                    intro: 'Bienvenue dans l\'application BillFlow! Ce guide vous aidera à naviguer dans l\'interface.'
                },
                {
                    element: document.querySelector('nav'),
                    title: 'Navigation principale',
                    intro: 'Utilisez cette barre de navigation pour accéder aux différentes sections de l\'application.'
                },
                {
                    element: document.querySelector('.breadcrumb') || document.body,
                    title: 'Fil d\'Ariane',
                    intro: 'Le fil d\'Ariane vous montre votre position actuelle dans la hiérarchie de l\'application.'
                }
            ],
            showProgress: true,
            showBullets: true,
            exitOnOverlayClick: true,
            doneLabel: 'Terminé'
        }).start();
    }

    /**
     * Guide pour le tableau de bord principal
     */
    dashboardGuide() {
        this.introJs.setOptions({
            steps: [
                {
                    title: 'Tableau de bord principal',
                    intro: 'Bienvenue sur votre tableau de bord principal! Vous pouvez voir ici une vue d\'ensemble de votre activité.'
                },
                {
                    element: document.querySelector('.statistics-cards') || document.body,
                    title: 'Statistiques',
                    intro: 'Ces cartes affichent vos principales statistiques: ventes, clients, produits, etc.'
                },
                {
                    element: document.querySelector('.chart-container') || document.body,
                    title: 'Graphiques',
                    intro: 'Les graphiques vous donnent une représentation visuelle de vos données de vente.'
                },
                {
                    element: document.querySelector('.recent-activities') || document.body,
                    title: 'Activités récentes',
                    intro: 'Cette section affiche les activités récentes dans votre système.'
                }
            ],
            showProgress: true,
            showBullets: true,
            exitOnOverlayClick: true,
            doneLabel: 'Terminé'
        }).start();
    }

    /**
     * Guide pour la liste des boutiques
     */
    shopsIndexGuide() {
        this.introJs.setOptions({
            steps: [
                {
                    title: 'Gestion des boutiques',
                    intro: 'Bienvenue dans la gestion des boutiques! Ici, vous pouvez gérer toutes vos boutiques.'
                },
                {
                    element: document.querySelector('.card-header a.btn-primary') || document.body,
                    title: 'Ajouter une boutique',
                    intro: 'Cliquez ici pour ajouter une nouvelle boutique.'
                },
                {
                    element: document.querySelector('table') || document.body,
                    title: 'Liste des boutiques',
                    intro: 'Cette table liste toutes vos boutiques avec leurs informations principales.'
                },
                {
                    element: document.querySelector('.btn-group') || document.body,
                    title: 'Actions',
                    intro: 'Utilisez ces boutons pour voir le tableau de bord, les détails, modifier ou supprimer une boutique.'
                }
            ],
            showProgress: true,
            showBullets: true,
            exitOnOverlayClick: true,
            doneLabel: 'Terminé'
        }).start();
    }

    /**
     * Guide pour le tableau de bord d'une boutique
     */
    shopDashboardGuide() {
        this.introJs.setOptions({
            steps: [
                {
                    title: 'Tableau de bord boutique',
                    intro: 'Bienvenue sur le tableau de bord de cette boutique! Vous pouvez voir ici une vue d\'ensemble des activités de cette boutique.'
                },
                {
                    element: document.querySelector('.btn-group') || document.body,
                    title: 'Filtres de période',
                    intro: 'Utilisez ces boutons pour filtrer les données par période: jour, semaine, mois ou année.'
                },
                {
                    element: document.querySelector('#salesChart') || document.body,
                    title: 'Évolution des ventes',
                    intro: 'Ce graphique montre l\'évolution des ventes sur la période sélectionnée.'
                },
                {
                    element: document.querySelector('#categoriesChart') || document.body,
                    title: 'Ventes par catégorie',
                    intro: 'Ce graphique montre la répartition des ventes par catégorie de produits.'
                },
                {
                    element: document.querySelector('#stock-section') || document.body,
                    title: 'Gestion du stock',
                    intro: 'Cette section vous alerte sur les produits en rupture ou en stock faible.'
                }
            ],
            showProgress: true,
            showBullets: true,
            exitOnOverlayClick: true,
            doneLabel: 'Terminé'
        }).start();
    }

    /**
     * Guide pour la liste des commissions
     */
    commissionsIndexGuide() {
        this.introJs.setOptions({
            steps: [
                {
                    title: 'Gestion des commissions',
                    intro: 'Bienvenue dans la gestion des commissions! Ici, vous pouvez suivre et gérer les commissions des vendeurs.'
                },
                {
                    element: document.querySelector('.card-header.bg-primary') || document.body,
                    title: 'Récapitulatif',
                    intro: 'Ce récapitulatif vous montre les totaux des commissions, payées et en attente.'
                },
                {
                    element: document.querySelector('form.row.g-3') || document.body,
                    title: 'Filtres',
                    intro: 'Utilisez ces filtres pour affiner la liste des commissions par vendeur, boutique, statut ou période.'
                },
                {
                    element: document.querySelector('#markSelectedAsPaid') || document.body,
                    title: 'Actions en masse',
                    intro: 'Sélectionnez plusieurs commissions et utilisez ce bouton pour les marquer comme payées en une seule opération.'
                },
                {
                    element: document.querySelector('#commissionsTable') || document.body,
                    title: 'Liste des commissions',
                    intro: 'Cette table liste toutes les commissions avec leurs détails.'
                }
            ],
            showProgress: true,
            showBullets: true,
            exitOnOverlayClick: true,
            doneLabel: 'Terminé'
        }).start();
    }

    /**
     * Guide pour la liste des factures
     */
    billsIndexGuide() {
        this.introJs.setOptions({
            steps: [
                {
                    title: 'Gestion des factures',
                    intro: 'Bienvenue dans la gestion des factures! Vous pouvez gérer ici toutes vos factures.'
                },
                {
                    element: document.querySelector('.card-header a.btn-primary') || document.body,
                    title: 'Nouvelle facture',
                    intro: 'Cliquez ici pour créer une nouvelle facture.'
                },
                {
                    element: document.querySelector('form.filters') || document.body,
                    title: 'Filtres',
                    intro: 'Utilisez ces filtres pour rechercher des factures spécifiques.'
                },
                {
                    element: document.querySelector('table') || document.body,
                    title: 'Liste des factures',
                    intro: 'Cette table liste toutes vos factures avec leurs informations principales.'
                }
            ],
            showProgress: true,
            showBullets: true,
            exitOnOverlayClick: true,
            doneLabel: 'Terminé'
        }).start();
    }

    /**
     * Guide pour la liste des produits
     */
    productsIndexGuide() {
        this.introJs.setOptions({
            steps: [
                {
                    title: 'Gestion des produits',
                    intro: 'Bienvenue dans la gestion des produits! Vous pouvez gérer ici votre catalogue de produits.'
                },
                {
                    element: document.querySelector('.card-header a.btn-primary') || document.body,
                    title: 'Nouveau produit',
                    intro: 'Cliquez ici pour ajouter un nouveau produit.'
                },
                {
                    element: document.querySelector('form.filters') || document.body,
                    title: 'Filtres',
                    intro: 'Utilisez ces filtres pour rechercher des produits spécifiques.'
                },
                {
                    element: document.querySelector('table') || document.body,
                    title: 'Liste des produits',
                    intro: 'Cette table liste tous vos produits avec leurs informations principales.'
                }
            ],
            showProgress: true,
            showBullets: true,
            exitOnOverlayClick: true,
            doneLabel: 'Terminé'
        }).start();
    }

    /**
     * Guide pour la liste des clients
     */
    clientsIndexGuide() {
        this.introJs.setOptions({
            steps: [
                {
                    title: 'Gestion des clients',
                    intro: 'Bienvenue dans la gestion des clients! Vous pouvez gérer ici votre base de clients.'
                },
                {
                    element: document.querySelector('.card-header a.btn-primary') || document.body,
                    title: 'Nouveau client',
                    intro: 'Cliquez ici pour ajouter un nouveau client.'
                },
                {
                    element: document.querySelector('form.filters') || document.body,
                    title: 'Filtres',
                    intro: 'Utilisez ces filtres pour rechercher des clients spécifiques.'
                },
                {
                    element: document.querySelector('table') || document.body,
                    title: 'Liste des clients',
                    intro: 'Cette table liste tous vos clients avec leurs informations principales.'
                }
            ],
            showProgress: true,
            showBullets: true,
            exitOnOverlayClick: true,
            doneLabel: 'Terminé'
        }).start();
    }
}

// Styles pour le bouton d'aide flottant
document.head.insertAdjacentHTML('beforeend', `
    <style>
        .help-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #0d6efd;
            color: white;
            border: none;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        
        .help-button:hover {
            transform: scale(1.1);
            background-color: #0b5ed7;
        }
        
        /* Styles additionnels pour Intro.js */
        .introjs-tooltip {
            min-width: 300px;
        }
        
        .introjs-helperLayer {
            box-shadow: 0 0 0 1000px rgba(0, 0, 0, 0.5) !important;
            background-color: rgba(255, 255, 255, 0.9) !important;
        }
    </style>
`);

// Initialiser le guide lorsque le DOM est chargé
document.addEventListener('DOMContentLoaded', function () {
    new InteractiveGuide();
}); 