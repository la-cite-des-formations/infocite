    google.charts.load('current', {packages:['orgchart', 'bar', 'corechart']})
    google.charts.setOnLoadCallback(drawChart)

    function drawChart() {
        // Create the chart.

        // OrgChart (organigramme)
        Livewire.on('drawOrgChart', (targetId, data, options) => {
            var orgChart = new google.visualization.OrgChart(document.getElementById(targetId))
            var dt = new google.visualization.DataTable(data)

            orgChart.draw(dt, options)
        })

        // BarChart (graphique à colonnes)
        Livewire.on('drawBarChart', (targetId, data, options) => {
            var barChart = new google.charts.Bar(document.getElementById(targetId))
            var dt = new google.visualization.DataTable(data);

            barChart.draw(dt, google.charts.Bar.convertOptions(options))
        })

        // RingChart (graphique en anneau)
        Livewire.on('drawRingChart', (targetId, data, options) => {
            var ringChart = new google.visualization.PieChart(document.getElementById(targetId))
            var dt = new google.visualization.DataTable(data)

            ringChart.draw(dt, options)
        })
    }
