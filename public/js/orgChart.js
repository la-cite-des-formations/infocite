    google.charts.load('current', {packages:["orgchart"]});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        // Create the chart.
        Livewire.on('drawOrgChart', (size, data, style) => {
            var dt = new google.visualization.DataTable();
            var orgChart = new google.visualization.OrgChart(document.getElementById('orgchart'));

            dt.addColumn('string', 'NodeId');
            dt.addColumn('string', 'NodeParentId');
            dt.addColumn('string', 'ToolTip');

            // For each orgchart box, provide the node id, node parent id, and tooltip to show.
            dt.addRows(data);

            for (let i = 0; i < dt.Wf.length; i++) {
                console.log(style[i])
                dt.setRowProperty(i, 'style', style[i])
            }

            // Draw the chart, setting the allowHtml option to true for the tooltips.
            orgChart.draw(dt, {
                'allowCollapse': true,
                'allowHtml': true,
                'size': size,
                'compactRows': true,
            });
        })
    }
