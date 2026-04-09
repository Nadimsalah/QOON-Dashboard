<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(function() {
    const ctx = document.getElementById('canvas').getContext('2d');
    
    const labels = {
        'YEAR': ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        'MONTH': [<?= implode(',', range(1, 31)) ?>],
        'DAY': [<?= implode(',', range(0, 23)) ?>],
        'WEEK': ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
    };

    const datasets = {
        'Traffic': {
            'YEAR': [<?= "$junNum1,$febNum1,$MarchNum1,$AprilNum1,$MayNum1,$JunNum1,$JulyNum1,$AugNum1,$SepNum1,$OctNum1,$NovNum1,$DecNum1" ?>],
            'MONTH': [<?= implode(',', array_map(function($i) use ($con) { global ${"Days".$i}; return ${"Days".$i}; }, range(1, 31))) ?>],
            'DAY': [<?= "$oneclock,$twoclock,$threeclock,$fourclock,$fiveclock,$sexclock,$sevenclock,$eightclock,$nineclock,$tenclock,$tenoneclock,$tentwoclock,$tenthreeclock,$tenfourclock,$tenfiveclock,$tensexclock,$tensevenclock,$teneightclock,$tennineclock,$tentenclock,$twentyoneclock,$twentytwoclock,$twentythreeclock,$twentyfourclock" ?>],
            'WEEK': [<?= "$Mon,$Tues,$Wednes,$Thurs,$Fri,$Satur,$Sun" ?>]
        },
        'Orders': {
            'YEAR': [<?= "$junNum,$febNum,$MarchNum,$AprilNum,$MayNum,$JunNum,$JulyNum,$AugNum,$SepNum,$OctNum,$NovNum,$DecNum" ?>],
            'MONTH': [<?= implode(',', array_map(function($i) { global ${"Day".$i}; return ${"Day".$i}; }, range(1, 31))) ?>],
            'DAY': [<?= "$oneclock1,$twoclock1,$threeclock1,$fourclock1,$fiveclock1,$sexclock1,$sevenclock1,$eightclock1,$nineclock1,$tenclock1,$tenoneclock1,$tentwoclock1,$tenthreeclock1,$tenfourclock1,$tenfiveclock1,$tensexclock1,$tensevenclock1,$teneightclock1,$tennineclock1,$tentenclock1,$twentyoneclock1,$twentytwoclock1,$twentythreeclock1,$twentyfourclock1" ?>],
            'WEEK': [<?= "$MonOrder,$TuesOrder,$WednesOrder,$ThursOrder,$FriOrder,$SaturOrder,$SunOrder" ?>]
        }
    };

    const type = '<?= $Type ?>';
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels[type],
            datasets: [
                {
                    label: 'Traffic',
                    data: datasets['Traffic'][type],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Orders',
                    data: datasets['Orders'][type],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    padding: 12,
                    backgroundColor: '#1e293b',
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
                    },
                    ticks: {
                        color: '#64748b',
                        font: { size: 11 }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#64748b',
                        font: { size: 11 }
                    }
                }
            }
        }
    });
});
</script>
