document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[data-validate]').forEach(function (form) {
        const password = form.querySelector('input[name="password"]');
        const confirmation = form.querySelector('input[name="password_confirmation"]');

        function validatePasswordMatch() {
            if (!password || !confirmation) {
                return;
            }

            confirmation.setCustomValidity(
                confirmation.value && confirmation.value !== password.value
                    ? 'Passwords do not match.'
                    : ''
            );
        }

        if (password && confirmation) {
            password.addEventListener('input', validatePasswordMatch);
            confirmation.addEventListener('input', validatePasswordMatch);
        }

        form.addEventListener('submit', function (event) {
            validatePasswordMatch();
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
            form.classList.add('was-validated');
        });
    });

    document.querySelectorAll('[data-count-target]').forEach(function (field) {
        const counter = document.getElementById(field.dataset.countTarget);
        if (!counter) {
            return;
        }

        const updateCount = function () {
            counter.textContent = String(field.value.length);
        };
        field.addEventListener('input', updateCount);
        updateCount();
    });

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (element) {
        if (window.bootstrap && window.bootstrap.Tooltip) {
            new window.bootstrap.Tooltip(element);
        }
    });

    const workoutModal = document.getElementById('workoutModal');
    if (workoutModal && workoutModal.dataset.autoOpen === 'true' && window.bootstrap) {
        window.bootstrap.Modal.getOrCreateInstance(workoutModal).show();
    }

    const chartCanvas = document.getElementById('activityChart');
    if (chartCanvas && window.Chart) {
        const labels = JSON.parse(chartCanvas.dataset.labels || '[]');
        const values = JSON.parse(chartCanvas.dataset.values || '[]');
        const colors = ['#176c91', '#18a999', '#dd815f', '#73aeb7', '#8cbd83', '#e5bd68', '#6686a0', '#c87993'];

        new window.Chart(chartCanvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors.slice(0, labels.length),
                    borderColor: '#ffffff',
                    borderWidth: 4,
                    hoverOffset: 7
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutout: '66%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#52676d',
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 16,
                            font: { family: 'Manrope, sans-serif', size: 11 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return ' ' + context.label + ': ' + context.parsed + ' min';
                            }
                        }
                    }
                }
            }
        });
    }
});
