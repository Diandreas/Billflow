// resources/js/bill-create.js

// Global variables
let BillManager = {
    selectedClient: null,
    nextItemId: 0,
    productRows: [],
    hasClientSelected: false,
    hasProductsAdded: false
};

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    console.log("Script de création de facture initialisé");
    
    // Initialize UI elements
    initClientSearch();
    initProductHandling();
    initCalculations();
    initModals();
    checkEmptyProductsState();
    updateProgressBar();
});

// Client search functionality
function initClientSearch() {
    const searchInput = document.getElementById('client_search');
    const resultsContainer = document.getElementById('clientSearchResults');
    
    if (!searchInput || !resultsContainer) {
        console.error("Éléments de recherche client manquants");
        return;
    }
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        if (searchTerm.length < 2) {
            resultsContainer.classList.add('hidden');
            return;
        }
        
        try {
            // Récupérer les clients depuis la variable globale
            if (typeof clients === 'undefined') {
                console.error("Variable 'clients' non définie");
                return;
            }
            
            const filteredClients = clients.filter(client => {
                const nameMatch = client.name && client.name.toLowerCase().includes(searchTerm);
                const emailMatch = client.email && client.email.toLowerCase().includes(searchTerm);
                const phoneMatch = client.phones && client.phones.some(phone => 
                    phone.number && phone.number.toLowerCase().includes(searchTerm)
                );
                
                return nameMatch || emailMatch || phoneMatch;
            });
            
            renderClientSearchResults(filteredClients);
        } catch (error) {
            console.error("Erreur lors de la recherche client:", error);
        }
    });
    
    // Cacher les résultats quand on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
            resultsContainer.classList.add('hidden');
        }
    });
    
    // Affichage au focus
    searchInput.addEventListener('focus', function() {
        if (this.value.length >= 2) {
            resultsContainer.classList.remove('hidden');
        }
    });
}

// Render client search results
function renderClientSearchResults(filteredClients) {
    const resultsContainer = document.getElementById('clientSearchResults');
    
    if (!resultsContainer) return;
    
    // Effacer les résultats précédents
    resultsContainer.innerHTML = '';
    
    if (filteredClients.length === 0) {
        resultsContainer.innerHTML = `
            <div class="p-4 text-center">
                <p class="text-sm text-gray-500">Aucun client trouvé</p>
                <button type="button" onclick="toggleNewClientModal()" class="mt-2 text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    Ajouter un nouveau client
                </button>
            </div>
        `;
        resultsContainer.classList.remove('hidden');
        return;
    }
    
    // Créer les résultats
    filteredClients.forEach(client => {
        const resultItem = document.createElement('div');
        resultItem.className = 'search-result p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100';
        
        const phoneNumbers = client.phones && client.phones.length > 0 
            ? client.phones.map(p => p.number).join(', ') 
            : 'Pas de téléphone';
        
        resultItem.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                    ${client.name.substring(0, 2).toUpperCase()}
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">${client.name}</p>
                    <p class="text-xs text-gray-500">${phoneNumbers}</p>
                </div>
            </div>
        `;
        
        resultItem.addEventListener('click', function() {
            selectClient(client);
        });
        
        resultsContainer.appendChild(resultItem);
    });
    
    resultsContainer.classList.remove('hidden');
}

// Select a client
function selectClient(client) {
    const clientIdInput = document.getElementById('client_id');
    const clientCard = document.getElementById('selectedClientCard');
    const clientSearch = document.getElementById('client_search');
    const clientSearchResults = document.getElementById('clientSearchResults');
    const summaryClientName = document.getElementById('summaryClientName');
    
    if (!clientIdInput || !clientCard || !clientSearch || !clientSearchResults) {
        console.error("Éléments client manquants");
        return;
    }
    
    BillManager.selectedClient = client;
    clientIdInput.value = client.id;
    
    // Mettre à jour l'affichage du client sélectionné
    clientCard.classList.remove('hidden');
    
    // Définir les infos du client
    const initialDiv = clientCard.querySelector('.flex-shrink-0');
    if (initialDiv) initialDiv.textContent = client.name.substring(0, 2).toUpperCase();
    
    const clientNameElement = document.getElementById('selectedClientName');
    if (clientNameElement) clientNameElement.textContent = client.name;
    
    const phoneNumbers = client.phones && client.phones.length > 0 
        ? client.phones.map(p => p.number).join(', ') 
        : 'Pas de téléphone';
        
    const clientDetailsElement = document.getElementById('selectedClientDetails');
    if (clientDetailsElement) clientDetailsElement.textContent = phoneNumbers;
    
    // Cacher l'input de recherche
    clientSearch.value = '';
    clientSearchResults.classList.add('hidden');
    clientSearch.classList.add('hidden');
    
    // Mettre à jour le récapitulatif
    if (summaryClientName) summaryClientName.textContent = client.name;
    
    // Mettre à jour le statut
    BillManager.hasClientSelected = true;
    updateClientStatus();
    updateProgressBar();
}

// Initialize product handling
function initProductHandling() {
    // Ajouter un produit
    const addProductBtn = document.getElementById('addProductBtn');
    if (addProductBtn) {
        addProductBtn.addEventListener('click', function() {
            addProductRow();
        });
    }
    
    // Bouton Rechercher produits
    const openProductSearchModal = document.getElementById('openProductSearchModal');
    if (openProductSearchModal) {
        openProductSearchModal.addEventListener('click', function() {
            toggleProductSearchModal();
        });
    }
}

// Add product row
function addProductRow() {
    try {
        // Utiliser le template de produit
        const template = document.getElementById('productTemplate');
        const productsContainer = document.getElementById('productsContainer');
        
        if (!template || !productsContainer) {
            console.error("Template de produit ou conteneur manquant");
            return;
        }
        
        // Cloner le contenu du template
        const clone = template.content.cloneNode(true);
        
        // Générer un index unique pour les produits
        const rowIndex = BillManager.nextItemId++;
        
        // Mettre à jour les noms de champs avec l'index correct
        const selectField = clone.querySelector('.product-select');
        if (selectField) {
            selectField.name = `products[${rowIndex}][id]`;
            selectField.id = `product-${rowIndex}`;
        }
        
        const quantityField = clone.querySelector('.product-quantity');
        if (quantityField) {
            quantityField.name = `products[${rowIndex}][quantity]`;
            quantityField.id = `quantity-${rowIndex}`;
        }
        
        const priceField = clone.querySelector('.product-price');
        if (priceField) {
            priceField.name = `products[${rowIndex}][price]`;
            priceField.id = `price-${rowIndex}`;
        }
        
        // Ajouter la ligne au tableau
        productsContainer.appendChild(clone);
        
        // Récupérer l'élément nouvellement ajouté (le dernier enfant)
        const newRow = productsContainer.lastElementChild;
        
        // Ajouter les événements
        const productSelect = newRow.querySelector('.product-select');
        if (productSelect) {
            productSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const defaultPrice = selectedOption.dataset.price || 0;
                const priceInput = newRow.querySelector('.product-price');
                if (priceInput) priceInput.value = defaultPrice;
                calculateTotals();
            });
        }
        
        // Set up quantity and price change handlers
        const quantityInput = newRow.querySelector('.product-quantity');
        if (quantityInput) {
            quantityInput.addEventListener('input', calculateTotals);
        }
        
        const priceInput = newRow.querySelector('.product-price');
        if (priceInput) {
            priceInput.addEventListener('input', calculateTotals);
        }
        
        // Set up remove button
        const removeBtn = newRow.querySelector('.remove-product-btn');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                newRow.remove();
                calculateTotals();
                checkEmptyProductsState();
            });
        }
        
        // Ajouter à la liste des lignes
        BillManager.productRows.push({
            id: rowIndex,
            row: newRow
        });
        
        // Mettre à jour les totaux
        calculateTotals();
        
        // Mettre à jour l'état des produits
        BillManager.hasProductsAdded = true;
        updateProductsStatus();
        updateProgressBar();
        
        // Afficher le tableau si caché
        const tableContainer = document.getElementById('productsTableContainer');
        const emptyState = document.getElementById('emptyProductsState');
        
        if (tableContainer) tableContainer.classList.remove('hidden');
        if (emptyState) emptyState.classList.add('hidden');
        
    } catch (error) {
        console.error("Erreur lors de l'ajout d'une ligne produit:", error);
    }
}

// Initialize calculations
function initCalculations() {
    // Taux de TVA
    const taxRateInput = document.getElementById('tax_rate');
    if (taxRateInput) {
        taxRateInput.addEventListener('input', calculateTotals);
    }
    
    // Remises
    const discountPercentInput = document.getElementById('discountPercent');
    if (discountPercentInput) {
        discountPercentInput.addEventListener('input', function() {
            if (this.value > 0) {
                const discountAmountInput = document.getElementById('discountAmount');
                if (discountAmountInput) discountAmountInput.value = 0;
            }
            calculateTotals();
        });
    }
    
    const discountAmountInput = document.getElementById('discountAmount');
    if (discountAmountInput) {
        discountAmountInput.addEventListener('input', function() {
            if (this.value > 0) {
                const discountPercentInput = document.getElementById('discountPercent');
                if (discountPercentInput) discountPercentInput.value = 0;
            }
            calculateTotals();
        });
    }
}

// Calculate totals
function calculateTotals() {
    try {
        let subtotal = 0;
        let itemCount = 0;
        const productRows = document.querySelectorAll('#productsContainer tr');
        
        productRows.forEach(row => {
            // Utiliser querySelector sur la ligne pour trouver les éléments par classe
            const quantityInput = row.querySelector('.product-quantity');
            const priceInput = row.querySelector('.product-price');
            const totalSpan = row.querySelector('.product-total');
            
            if (quantityInput && priceInput) {
                const quantity = parseFloat(quantityInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const total = quantity * price;
                
                // Mettre à jour le total de la ligne
                if (totalSpan) {
                    totalSpan.textContent = formatPrice(total);
                }
                
                subtotal += total;
                itemCount += quantity;
            }
        });
        
        // Gérer les remises
        let discountValue = 0;
        const discountPercentInput = document.getElementById('discountPercent');
        const discountAmountInput = document.getElementById('discountAmount');
        
        if (discountPercentInput && discountPercentInput.value > 0) {
            const discountPercent = parseFloat(discountPercentInput.value) || 0;
            discountValue = subtotal * (discountPercent / 100);
        }
        else if (discountAmountInput && discountAmountInput.value > 0) {
            discountValue = parseFloat(discountAmountInput.value) || 0;
        }
        
        // Calculer le montant après remise
        const afterDiscount = subtotal - discountValue;
        
        // Calculer la TVA
        const taxRateInput = document.getElementById('tax_rate');
        const taxRate = taxRateInput ? (parseFloat(taxRateInput.value) || 0) : 0;
        const taxAmount = afterDiscount * (taxRate / 100);
        
        // Calculer le total
        const total = afterDiscount + taxAmount;
        
        // Mettre à jour l'affichage des totaux
        updateSummaryElement('summarySubtotal', formatPrice(subtotal));
        updateSummaryElement('summaryTaxAmount', formatPrice(taxAmount));
        updateSummaryElement('summaryTotal', formatPrice(total));
        updateSummaryElement('summaryItems', itemCount.toString());
        updateSummaryElement('summaryTaxRate', taxRate.toString());
        
        // Afficher ou masquer la ligne de remise
        const discountRow = document.getElementById('discountSummaryRow');
        if (discountRow) {
            if (discountValue > 0) {
                discountRow.classList.remove('hidden');
                updateSummaryElement('summaryDiscount', '-' + formatPrice(discountValue));
            } else {
                discountRow.classList.add('hidden');
            }
        }
        
        // Mise à jour du statut des produits
        BillManager.hasProductsAdded = productRows.length > 0 && subtotal > 0;
        updateProductsStatus();
        updateProgressBar();
        
    } catch (error) {
        console.error("Erreur lors du calcul des totaux:", error);
    }
}

// Check if products table is empty
function checkEmptyProductsState() {
    const productRows = document.querySelectorAll('#productsContainer tr');
    const emptyState = document.getElementById('emptyProductsState');
    const tableContainer = document.getElementById('productsTableContainer');
    
    if (!emptyState || !tableContainer) return;
    
    if (productRows.length === 0) {
        emptyState.classList.remove('hidden');
        tableContainer.classList.add('hidden');
    } else {
        emptyState.classList.add('hidden');
        tableContainer.classList.remove('hidden');
    }
}

// Initialize modals
function initModals() {
    console.log("Initialisation des modals");
    
    // Modal Client
    document.querySelectorAll('[data-action="toggle-client-modal"]').forEach(btn => {
        btn.addEventListener('click', toggleNewClientModal);
    });
    
    const newClientForm = document.getElementById('quickClientForm');
    if (newClientForm) {
        newClientForm.addEventListener('submit', function(e) {
            e.preventDefault();
            addQuickClient();
        });
    }
    
    // Modal Produit
    document.querySelectorAll('[data-action="toggle-product-modal"]').forEach(btn => {
        btn.addEventListener('click', toggleProductSearchModal);
    });
}

// Add a client quickly
function addQuickClient() {
    const nameInput = document.getElementById('quick_client_name');
    const emailInput = document.getElementById('quick_client_email');
    const phoneInput = document.getElementById('quick_client_phone');
    
    if (!nameInput || !emailInput || !phoneInput) return;
    
    const name = nameInput.value;
    const email = emailInput.value;
    const phone = phoneInput.value;
    
    // Simuler l'ajout d'un client (normalement ce serait un appel AJAX)
    // Pour la démonstration, nous allons simplement créer un objet client et l'utiliser
    const newClient = {
        id: 'new_' + Date.now(),
        name: name,
        email: email,
        phones: [{ number: phone }]
    };
    
    // Sélectionner le nouveau client
    selectClient(newClient);
    
    // Fermer le modal
    toggleNewClientModal();
}

// Update client status
function updateClientStatus() {
    const clientStep = document.getElementById('clientStepStatus');
    if (!clientStep) return;
    
    if (BillManager.hasClientSelected) {
        clientStep.innerHTML = `
            <div class="flex items-center justify-center h-6 w-6 rounded-full bg-green-100 text-green-600">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </div>
            <span class="ml-2 text-sm font-medium text-gray-900">Client sélectionné</span>
        `;
    } else {
        clientStep.innerHTML = `
            <div class="flex items-center justify-center h-6 w-6 rounded-full bg-gray-100 text-gray-400">
                <span class="text-xs font-medium">2</span>
            </div>
            <span class="ml-2 text-sm font-medium text-gray-500">Client sélectionné</span>
        `;
    }
}

// Update products status
function updateProductsStatus() {
    const productsStep = document.getElementById('productsStepStatus');
    if (!productsStep) return;
    
    if (BillManager.hasProductsAdded) {
        productsStep.innerHTML = `
            <div class="flex items-center justify-center h-6 w-6 rounded-full bg-green-100 text-green-600">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </div>
            <span class="ml-2 text-sm font-medium text-gray-900">Produits ajoutés</span>
        `;
    } else {
        productsStep.innerHTML = `
            <div class="flex items-center justify-center h-6 w-6 rounded-full bg-gray-100 text-gray-400">
                <span class="text-xs font-medium">3</span>
            </div>
            <span class="ml-2 text-sm font-medium text-gray-500">Produits ajoutés</span>
        `;
    }
}

// Update the progress bar
function updateProgressBar() {
    const progressBar = document.getElementById('progressBar');
    if (!progressBar) return;
    
    let progress = 33; // Base progress - step 1 completed
    
    if (BillManager.hasClientSelected) progress += 33;
    if (BillManager.hasProductsAdded) progress += 34;
    
    progressBar.style.width = progress + '%';
}

// Utility functions
function updateSummaryElement(id, value) {
    const element = document.getElementById(id);
    if (element) element.textContent = value;
}

function formatPrice(price) {
    return new Intl.NumberFormat('fr-FR').format(price) + ' FCFA';
}

// Helper function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Modal functions exposed globally
function toggleNewClientModal() {
    const modal = document.getElementById('newClientModal');
    if (!modal) return;
    
    modal.classList.toggle('hidden');
    
    if (!modal.classList.contains('hidden')) {
        // Réinitialiser le formulaire
        const form = document.getElementById('quickClientForm');
        if (form) form.reset();
        // Focus sur le premier champ
        setTimeout(() => {
            const nameField = document.getElementById('quick_client_name');
            if (nameField) nameField.focus();
        }, 100);
    }
}

function toggleProductSearchModal() {
    const modal = document.getElementById('productSearchModal');
    if (!modal) return;
    
    modal.classList.toggle('hidden');
    
    if (!modal.classList.contains('hidden')) {
        // Focus sur le champ de recherche
        setTimeout(() => {
            const searchField = document.getElementById('quickProductSearch');
            if (searchField) searchField.focus();
        }, 100);
    }
}

function applyDiscount() {
    const discountRow = document.getElementById('discountRow');
    if (!discountRow) return;
    
    discountRow.classList.remove('hidden');
    
    // Mettre le focus sur le pourcentage de remise
    setTimeout(() => {
        const discountPercent = document.getElementById('discountPercent');
        if (discountPercent) discountPercent.focus();
    }, 300);
}

function removeDiscount() {
    const discountRow = document.getElementById('discountRow');
    if (!discountRow) return;
    
    const discountPercent = document.getElementById('discountPercent');
    const discountAmount = document.getElementById('discountAmount');
    
    // Réinitialiser les valeurs
    if (discountPercent) discountPercent.value = 0;
    if (discountAmount) discountAmount.value = 0;
    
    // Cacher la ligne
    discountRow.classList.add('hidden');
    
    // Recalculer les totaux
    calculateTotals();
}

// Definir les fonctions globales nécessaires
window.__ = function(text) {
    // Cette fonction devrait être remplacée par une vraie fonction de traduction
    // Pour l'instant, on retourne simplement le texte
    return text;
};

// Export functions to global scope
window.addProductRow = addProductRow;
window.toggleProductSearchModal = toggleProductSearchModal;
window.toggleNewClientModal = toggleNewClientModal;
window.applyDiscount = applyDiscount;
window.removeDiscount = removeDiscount;
window.calculateTotals = calculateTotals;
window.selectClient = selectClient;
