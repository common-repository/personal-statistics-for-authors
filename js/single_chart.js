// data => wp_localize_script
var data_object = eval(data.evolution);

// chart data array init
var data_array = [
    ['Date', 'Taux de rebond (en %)', 'Visites']
];

// set chart data
for(index in data_object) {
    d = [data_object[index].date.toString(), parseInt(data_object[index].bounce_rate), parseInt(data_object[index].visits)]
    data_array.push(d);
}

google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);

function drawChart() {

    var data = google.visualization.arrayToDataTable(data_array);

    var options = {
        backgroundColor: 'transparent',
        areaOpacity: 0,
        colors:['#AADFF3','#21759B'],
        hAxis: {textStyle: {color: '#4F4F4F'}, showTextEvery: 7},
        chartArea: {width: '90%'},
        lineWidth: 3,
        pointSize: 0,
        legend: {position: 'top'},
        vAxis: {minValue: 0}
    };

    // Create and draw the visualization.
    var chart = new google.visualization.AreaChart(document.getElementById('chart'));
    chart.draw(data, options);
}