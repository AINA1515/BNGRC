

(function($) {
  'use strict';
  $(function() {

    if ($("#sinistreChart").length && typeof sinistreChartData !== 'undefined') {
      const ctx = document.getElementById('sinistreChart').getContext('2d');
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: sinistreChartData.labels,
          datasets: [{
            label: 'Nombre de sinistres',
            data: sinistreChartData.data,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
          }]
        },
        options: {
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    }
  });
})(jQuery);