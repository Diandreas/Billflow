document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des filtres avec TomSelect
    const clientFilter = new TomSelect('#client_filter', {
        placeholder: 'Sélectionner un client'
    });

    const statusFilter = new TomSelect('#status_filter', {
        placeholder: 'Sélectionner un statut'
    });

    const periodFilter = new TomSelect('#period_filter', {
        placeholder: 'Sélectionner une période'
    });

    // Fonction pour appliquer les filtres
    function applyFilters() {
        const client = clientFilter.getValue();
        const status = statusFilter.getValue();
        const period = periodFilter.getValue();
        const search = document.getElementById('search').value;

        // Construction de l'URL avec les paramètres
        const params = new URLSearchParams();
        if (client) params.append('client', client);
        if (status) params.append('status', status);
        if (period) params.append('period', period);
        if (search) params.append('search', search);

        // Redirection avec les paramètres
        window.location.href = `${window.location.pathname}?${params.toString()}`;
    }

    // Événements pour les filtres
    [clientFilter, statusFilter, periodFilter].forEach(filter => {
        filter.on('change', applyFilters);
    });

    // Recherche avec délai
    let searchTimeout;
    document.getElementById('search').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 500);
    });

    // Fonction pour le téléchargement de facture
    window.downloadBill = function(billId) {
        window.location.href = `/bills/${billId}/download`;
    }

    // Gestion des actions en masse (si nécessaire)
    const checkAll = document.getElementById('check-all');
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            document.querySelectorAll('.bill-checkbox').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
});
