(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var carrierSelect = document.getElementById('hdst_carrier');
        var customField = document.querySelector('.hdst-custom-url-field');
        if (carrierSelect && customField) {
            carrierSelect.addEventListener('change', function () {
                customField.style.display = 'other' === carrierSelect.value ? '' : 'none';
            });
        }
    });
})();
