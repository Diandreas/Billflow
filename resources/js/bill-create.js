// resources/js/bill-create.js

document.addEventListener('DOMContentLoaded', function() {
    // Modal pour création rapide de client
    const newClientModal = document.getElementById('newClientModal');
    const newClientForm = document.getElementById('newClientForm');

    // Modal pour création rapide de produit
    const newProductModal = document.getElementById('newProductModal');
    const newProductForm = document.getElementById('newProductForm');

    // Recherche de client avec auto-completion
    const clientSearch = document.getElementById('clientSearch');
    new TomSelect(clientSearch, {
        valueField: 'id',
        labelField: 'name',
        searchField: ['name'],
        load: function(query, callback) {
            fetch(`/clients/search?q=${query}`)
                .then(response => response.json())
                .then(json => callback(json));
        },
        render: {
            option: function(item) {
                return `<div>
                    <span class="font-bold">${item.name}</span>
                    ${item.phones.map(phone => `<span class="text-sm text-gray-600">${phone.number}</span>`).join(', ')}
                </div>`;
            }
        }
    });

    // Gestion des produits
    const productsContainer = document.getElementById('productsContainer');
    const addProductBtn = document.getElementById('addProductBtn');

    addProductBtn.addEventListener('click', function() {
        const productRow = document.createElement('div');
        productRow.className = 'product-row flex gap-4 mb-4';
        productRow.innerHTML = `
            <select class="product-select flex-1" name="products[]">
                <option value="">Sélectionner un produit</option>
            </select>
            <input type="number" name="quantities[]" class="quantity w-24" min="1" value="1">
            <input type="number" name="unit_prices[]" class="unit-price w-32" min="0" step="0.01">
            <span class="line-total w-32"></span>
            <button type="button" class="remove-product text-red-600">&times;</button>
        `;
        productsContainer.appendChild(productRow);

        // Initialiser TomSelect pour le nouveau select
        new TomSelect(productRow.querySelector('.product-select'), {
            valueField: 'id',
            labelField: 'name',
            searchField: ['name'],
            load: function(query, callback) {
                fetch(`/products/search?q=${query}`)
                    .then(response => response.json())
                    .then(json => callback(json));
            }
        });
    });

    // Calcul des totaux
    function calculateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
            const lineTotal = quantity * unitPrice;
            row.querySelector('.line-total').textContent = lineTotal.toFixed(2) + ' €';
            subtotal += lineTotal;
        });

        const taxRate = parseFloat(document.getElementById('taxRate').value) || 0;
        const taxAmount = subtotal * (taxRate / 100);
        const total = subtotal + taxAmount;

        document.getElementById('subtotal').textContent = subtotal.toFixed(2) + ' €';
        document.getElementById('taxAmount').textContent = taxAmount.toFixed(2) + ' €';
        document.getElementById('total').textContent = total.toFixed(2) + ' €';
    }

    // Event listeners pour les calculs
    productsContainer.addEventListener('input', calculateTotals);
    document.getElementById('taxRate').addEventListener('input', calculateTotals);
});
