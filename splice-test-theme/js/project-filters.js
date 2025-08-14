document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('project-filter-form');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const resetButton = document.querySelector('.filter-reset');

    // Handle form reset
    resetButton.addEventListener('click', function(e) {
        e.preventDefault();
        startDateInput.value = '';
        endDateInput.value = '';
        filterForm.submit();
    });

    // Add loading state to form submission
    filterForm.addEventListener('submit', function() {
        document.body.classList.add('filtering');
    });
});