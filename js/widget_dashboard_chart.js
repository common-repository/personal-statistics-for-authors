// chart data array init
var data_array = [
    ['Date', 'Taux de rebond (en %)', 'Visites']
];

// Set datas for draw chart

var keys = [];
for(var k in data) keys.push(k);

var index = 0,
    length = keys.length;
    correct_display = 80;
    
var gap = Math.ceil( length / correct_display );

for (key in data) {
    
    if (length < correct_display) {
        d = [data[key].date.toString(), parseInt(data[key].bounce_rate), parseInt(data[key].visits)];
        data_array.push(d);
    } else {
        if (index % gap == 0) {
            d = [data[key].date.toString(), parseInt(data[key].bounce_rate), parseInt(data[key].visits)];
            data_array.push(d);
        }
    }

    index++;
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

    var chart = new google.visualization.AreaChart(document.getElementById('chart'));
    chart.draw(data, options);
}

