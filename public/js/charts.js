window.onload = function() {

var chart = new CanvasJS.Chart("pie-chart", {
	animationEnabled: true,
	title: {
		text: "Desktop Search Engine Market Share - 2016"
	},
	data: [{
		type: "pie",
		startAngle: 240,
		yValueFormatString: "##0.00\"%\"",
		indexLabel: "{label} {y}",
		dataPoints: [
			{y: 79.45, label: "Google"},
			{y: 7.31, label: "Bing"},
			{y: 7.06, label: "Baidu"},
			{y: 4.91, label: "Yahoo"},
			{y: 1.26, label: "Others"}
		]
	}]
});
chart.render();

}
window.onload = function () {

var chart = new CanvasJS.Chart("dount-chart", {
	animationEnabled: true,
	title:{
		text: "Email Categories",
		horizontalAlign: "left"
	},
	data: [{
		type: "doughnut",
		startAngle: 60,
		//innerRadius: 60,
		indexLabelFontSize: 17,
		indexLabel: "{label} - #percent%",
		toolTipContent: "<b>{label}:</b> {y} (#percent%)",
		dataPoints: [
			{ y: 67, label: "Inbox" },
			{ y: 28, label: "Archives" },
			{ y: 10, label: "Labels" },
			{ y: 7, label: "Drafts"},
			{ y: 15, label: "Trash"},
			{ y: 6, label: "Spam"}
		]
	}]
});
chart.render();

}