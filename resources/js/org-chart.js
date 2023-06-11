Livewire.on('drawOrgChart', (data) => {
    google.charts.load('current', {packages:["orgchart"]});
    google.charts.setOnLoadCallback(drawChart);


    function drawChart() {
        var dt = new google.visualization.DataTable();
        dt.addColumn('string', 'NodeId');
        dt.addColumn('string', 'NodeParentId');
        dt.addColumn('string', 'ToolTip');

        // For each orgchart box, provide the node id, node parent id, and tooltip to show.
        dt.addRows(data);

        // Create the chart.
        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
        // Draw the chart, setting the allowHtml option to true for the tooltips.
        chart.draw(dt, {'allowHtml':true});
    }
})
