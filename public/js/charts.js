google.charts.load('current', {packages:['orgchart', 'bar', 'corechart']})
google.charts.setOnLoadCallback(drawChart)

function drawChart() {

    /**
     * Vérifie si un élément DOM est visible (non masqué par display:none sur lui ou ses parents).
     */
    function isVisible(el) {
        return !!(el && el.offsetWidth > 0 && el.offsetHeight > 0 && el.closest('[style*="display: none"]') === null)
    }

    /**
     * Redessine un graphique en attente dès que son conteneur devient visible.
     * Utilise un ResizeObserver pour détecter le moment où la largeur devient positive.
     *
     * @param {HTMLElement} el      Le conteneur cible du graphique.
     * @param {Function}    drawFn  La fonction de dessin à appeler quand el est visible.
     */
    function drawWhenVisible(el, drawFn) {
        if (isVisible(el)) {
            drawFn()
            return
        }

        const observer = new ResizeObserver(function(entries) {
            for (const entry of entries) {
                if (entry.contentRect.width > 0) {
                    observer.disconnect()
                    drawFn()
                    break
                }
            }
        })
        observer.observe(el)
    }

    // OrgChart (organigramme)
    Livewire.on('drawOrgChart', (targetId, data, options) => {
        const el = document.getElementById(targetId)
        drawWhenVisible(el, () => {
            var orgChart = new google.visualization.OrgChart(el)
            var dt = new google.visualization.DataTable(data)
            orgChart.draw(dt, options)
        })
    })

    // BarChart (graphique à colonnes)
    Livewire.on('drawBarChart', (targetId, data, options) => {
        const el = document.getElementById(targetId)
        drawWhenVisible(el, () => {
            var barChart = new google.charts.Bar(el)
            var dt = new google.visualization.DataTable(data)
            barChart.draw(dt, google.charts.Bar.convertOptions(options))
        })
    })

    // RingChart (graphique en anneau)
    Livewire.on('drawRingChart', (targetId, data, options) => {
        const el = document.getElementById(targetId)
        drawWhenVisible(el, () => {
            var ringChart = new google.visualization.PieChart(el)
            var dt = new google.visualization.DataTable(data)
            ringChart.draw(dt, options)
        })
    })
}
