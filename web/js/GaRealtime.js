var CONST_HISREALTIMES = 46;
var GaRealtime = (function(){
	// private propertise
	var _hisRealtimes = {};
	var _timeStatistics = {};
	var _charts = {};

	// private functions
	function setHisRealtimes(media, realTime) {
		// get history
		if ((typeof _hisRealtimes[media]) == 'undefined') {
			_hisRealtimes[media] = {};
		}
		_hisRealtimes[media].update = false;
		var history = _hisRealtimes[media].list;
		if ((typeof history) == 'undefined') {
			history = [];
		}

		var index = history.length - 1;
		if (index < CONST_HISREALTIMES - 1) {
			history[index + 1] = realTime;
		} else {
			for (var i = 0; i < index; i++) {
				history[i] = history[i + 1];
			}
			history[index] = realTime;
		}

		// back up history
		_hisRealtimes[media].list = history;
		_hisRealtimes[media].update = true;
	}
	function updateLineChart(chart, datasets, labels) {
		chart.scale.draw = function() {
			this.xLabelRotation = 0;
			this.endPoint = this.height - 30;
			Chart.Scale.prototype.draw.apply(this, arguments);
		};

		var maxindex = labels.length - 1;
		for (var i = 0; i < labels.length; i++) {
			for (var j = 0; j < datasets.length; j++) {
				chart.datasets[j].points[i].value = datasets[j][i];
				chart.datasets[j].points[i].label = labels[i];
			}

			if (i != 0 && i != 14 && i != maxindex) {
				chart.scale.xLabels[i] = '';
			} else {
				chart.scale.xLabels[i] = labels[i].substr(5);
			}
			if (i == maxindex) {
				chart.scale.xLabels[i-1] = chart.scale.xLabels[i];
				chart.scale.xLabels[i] = '';
			}
		}
		chart.update();
	}

	return {
		// public function
		deviceStatus : function(device){
			return ({'DESKTOP':'success', 'TABLET': 'warning', 'MOBILE': 'danger', 'OTHER':'info'})[device];
		},
		renderHisRealtimes : function(media, render) {
			if (_hisRealtimes[media].update) {
				var list = _hisRealtimes[media].list;
				var strlist = '';
				for (var i = 0; i < list.length; i++) {
					strlist += list[i] + ',';
				}
				strlist = strlist.substr(0, strlist.length - 1);

				render.html(strlist);
				//render.attr('xxx', list.length);
				render.sparkline('html',{height: '3em', width: '100%', lineColor: '#f00', fillColor: '#ffa', minSpotColor: false, maxSpotColor: false, spotColor: '#77f', spotRadius: 3});
			}
		},
		renderDevicePartition: function(template,data,target){
			var template = $(template).html();
			Mustache.parse(template);   // optional, speeds up future uses
			var rendered = Mustache.render(template, data);
			$(target).html(rendered);
		},
		getRealtime: function(url,medias,callable){
			//console.log(url,medias,callable);
			var evtSource = new EventSource(url);
			$.each(medias,function(id,key){
				evtSource.addEventListener('hasdata-'+id, function(e) {

					try {
						var obj = JSON.parse(e.data);
					} catch(err) {
						var str = e.data.replace("<!doctype html>", "");
						var obj = JSON.parse(str);
						console.log(id,key,e,err);
					}
					if(typeof obj == 'object'){
						var total = obj.rt_pvs_right_now.metricTotals[0];
						var total2 = 0;

						// add to history undefined
						setHisRealtimes(id, total);

						$.each(obj.rt_pvs_right_now.rows, function(index,value){
							obj.rt_pvs_right_now.rows[index].percent = Math.round((value.metricValues[0] / total) * 100);
							obj.rt_pvs_right_now.rows[index].device = value.dimensionValues[0];
							obj.rt_pvs_right_now.rows[index].color = GaRealtime.deviceStatus(value.dimensionValues[0]);
							total2+=obj.rt_pvs_right_now.rows[index].percent;
						});
						if(typeof obj.rt_pvs_right_now.rows[0] != 'undefined')
							obj.rt_pvs_right_now.rows[0].percent -= (total2 - 100);

						//				console.log(obj.rt_pvs_right_now.metricTotals[0]);
						if(typeof callable == 'function'){
							callable(obj.rt_pvs_right_now,id);
						}
					}else{
						console.log(obj);
					}

				}, false);
			});
		},
		getStatistics: function(url, medias, callable) {
			var evtSource = new EventSource(url);
			$.each(medias, function(id, key) {
				evtSource.addEventListener('statistics-hasdata-'+id, function(e) {
					try {
						var obj = JSON.parse(e.data);
					} catch(err) {
						var str = e.data.replace("<!doctype html>", "");
						var obj = JSON.parse(str);
						console.log(id, key, e, err);
					}
					if(typeof obj == 'object'){
						if(typeof callable == 'function'){
							callable(obj, id);
						}
					}else{
						console.log(obj);
					}
				}, false);
			});
		},
		renderStatistics: function(data, id) {
			if ((typeof _timeStatistics[id]) == 'undefined') {
				_timeStatistics[id] = 0;
			}
			if (_timeStatistics[id] == data.timestamp) {
				return;
			}
			_timeStatistics[id] = data.timestamp;

			if ((typeof _charts[id]) == 'undefined') {
				_charts[id] = {};
			}

			// show today Statistics
			var yesterday = data.dates[data.dates.length - 2];
			var today = data.today;
			var pageviews = '';
			var sessions = '';
			var users = '';
			var oldUsers = '';
			var newUsers = '';
			if (typeof today !== 'undefined') {
				pageviews = '&nbsp;' + accounting.formatNumber(yesterday.pageviews) + ' / ' + accounting.formatNumber(today.pageviews);
				sessions = '&nbsp;' + accounting.formatNumber(yesterday.sessions) + ' / ' + accounting.formatNumber(today.sessions);
				users = '&nbsp;' + accounting.formatNumber(yesterday.users) + ' / ' + accounting.formatNumber(today.users);
				newUsers = '&nbsp;' + accounting.formatNumber(yesterday.newUsers) + ' / ' + accounting.formatNumber(today.newUsers);
				oldUsers = '&nbsp;' + accounting.formatNumber(yesterday.users - yesterday.newUsers) + ' / ' + accounting.formatNumber(today.users - today.newUsers);
			}
			$('#today-pageviews-' + id).html(pageviews);
			$('#today-sessions-' + id).html(sessions);
			$('#today-users-' + id).html(users);
			$('#today-oldusers-' + id).html(oldUsers);
			$('#today-newusers-' + id).html(newUsers);

			// draw line chart PageViews & Sessions
			var lineChartOptions = {
				showScale: true,// Boolean - If we should show the scale at all
				scaleShowGridLines: true,//Boolean - Whether grid lines are shown across the chart
				scaleGridLineColor: "rgba(0,0,0,.05)",//String - Colour of the grid lines
				scaleGridLineWidth: 1,//Number - Width of the grid lines
				scaleShowHorizontalLines: true,//Boolean - Whether to show horizontal lines (except X axis)
				scaleShowVerticalLines: true,//Boolean - Whether to show vertical lines (except Y axis)
				bezierCurve: true,//Boolean - Whether the line is curved between points
				bezierCurveTension: 0.3,//Number - Tension of the bezier curve between points
				pointDot: true,//Boolean - Whether to show a dot for each point
				pointDotRadius: 3,//Number - Radius of each point dot in pixels
				pointDotStrokeWidth: 1,//Number - Pixel width of point dot stroke
				pointHitDetectionRadius: 5,//Number - amount extra to add to the radius to cater for hit detection outside the drawn point
				datasetStroke: true,//Boolean - Whether to show a stroke for datasets
				datasetStrokeWidth: 1,//Number - Pixel width of dataset stroke
				datasetFill: false,//Boolean - Whether to fill the dataset with a color
				legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",//String - A legend template
				maintainAspectRatio: true,//Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
				scaleShowXLabels : 1,
				responsive: true//Boolean - whether to make the chart responsive to window resizing
			};

			// draw pie chart Users
			var pieChartOptions = {
				segmentShowStroke : true,
				segmentStrokeColor : "#fff",
				segmentStrokeWidth : 2,
				percentageInnerCutout : 0,
				animationSteps : 100,
				animationEasing : "easeOutBounce",
				animateRotate : true,
				animateScale : false,
				tooltipTemplate: " <%=label%>: <%=value%>%",
				//toFixed
				legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
			};

			var lineLabels = [];
			var pageviewsData = [];
			var sessionsData = [];
			var newUsersData = [];
			var oldUsersData = [];
			$index = 0;
			$.each(data.dates, function(key, value) {
				lineLabels[$index] = value.date;
				pageviewsData[$index] = value.pageviews;
				sessionsData[$index] = value.sessions;
				newUsersData[$index] = value.newUsers;
				oldUsersData[$index] = value.users - value.newUsers;
				$index++;
				//console.log(labels);
			});

			var pageviewsLineChartData = {
				labels: lineLabels.slice(0),
				datasets: [
					{
						label: "Pageviews",
						fillColor: "rgba(210, 214, 222, 1)",
						strokeColor: "#dd4b39",
						pointColor: "#dd4b39",
						pointStrokeColor: "#c1c7d1",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(220,220,220,1)",
						data: pageviewsData
					},
					{
						label: "Sessions",
						fillColor: "rgba(60,141,188,0.9)",
						strokeColor: "#00a65a",
						pointColor: "#00a65a",
						pointStrokeColor: "rgba(60,141,188,1)",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(60,141,188,1)",
						data: sessionsData
					}
				]
			};

			var usersLineChartData = {
				labels: lineLabels.slice(0),
				datasets: [
					{
						label: "New User",
						fillColor: "rgba(60,141,188,0.9)",
						strokeColor: "#f39c12",
						pointColor: "#f39c12",
						pointStrokeColor: "rgba(60,141,188,1)",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(60,141,188,1)",
						data: newUsersData
					},
					{
						label: "Returning User",
						fillColor: "rgba(60,141,188,0.9)",
						strokeColor: "#00c0ef",
						pointColor: "#00c0ef",
						pointStrokeColor: "rgba(60,141,188,1)",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(60,141,188,1)",
						data: oldUsersData
					}
				]
			};

			var sumData = data.sum;
			var totalUsers = sumData.users;
			var totalNewUsersPer = 0;
			var totalReturningUsersPer = 0;
			var totalNewUsers = sumData.newUsers;
			var totalReturningUsers = sumData.users - totalNewUsers;
			if (totalUsers > 0) {
				totalNewUsersPer = accounting.toFixed(100 * totalNewUsers / totalUsers, 0);
				totalReturningUsersPer = 100 - totalNewUsersPer;
			}

			var sumtimes = data.dates[0].date + " ~ " + data.dates[data.dates.length - 1].date;
			$('#sum-times-' + id).html(sumtimes);
			$('#sum-pageviews-' + id).html(accounting.formatNumber(sumData.pageviews));
			$('#sum-sessions-' + id).html(accounting.formatNumber(sumData.sessions));
			$('#sum-users-' + id).html(accounting.formatNumber(sumData.users));
			$('#sum-newusers-' + id).html(accounting.formatNumber(totalNewUsers));
			$('#sum-oldusers-' + id).html(accounting.formatNumber(totalReturningUsers));
			var usersPieChartData = [
				{
					value: totalNewUsersPer,
					color:"#00c0ef",
					highlight: "#00c0ef",
					label: "New Users"
				},
				{
					value: totalReturningUsersPer,
					color: "#d81b60",
					highlight: "#d81b60",
					label: "Returned Users"
				}
			];

			// Modify chart
			/*Chart.types.Line.extend({
			 name: "AltLine",
			 initialize: function (data) {
			 Chart.types.Line.prototype.initialize.apply(this, arguments);
			 var xLabels = this.scale.xLabels;
			 var maxindex = xLabels.length - 1;
			 xLabels.forEach(function (label, i) {
			 if (i != 0 && i != 14 && i != maxindex) {
			 xLabels[i] = '';
			 } else {
			 xLabels[i] = xLabels[i].substr(5);
			 }
			 if (i == maxindex) {
			 xLabels[i-1] = xLabels[i];
			 xLabels[i] = '';
			 }
			 });
			 this.scale.draw = function() {
			 this.xLabelRotation = 0;
			 this.endPoint = this.height - 30;
			 Chart.Scale.prototype.draw.apply(this, arguments);
			 }
			 }
			 });*/

			// opening tab
			$('a[data-toggle="tab"]').each(function () {
				var openingtab = $(this);
				if (this.id == 'tabpageviews-' + id) {
					_charts[id].pageviewsData = {datasets:[pageviewsData, sessionsData], labels:lineLabels};
					if (openingtab.attr('aria-expanded') == 'true') {
						if ((typeof _charts[id].pageviewsLine) == 'undefined') {
							var pageviewsLineChartCanvas = $('#pageviewsLineChart-' + id).get(0).getContext("2d");
							var pageviewsLineChart = new Chart(pageviewsLineChartCanvas).Line(pageviewsLineChartData, lineChartOptions);
							_charts[id].pageviewsLine = pageviewsLineChart;
						}
						updateLineChart(_charts[id].pageviewsLine, _charts[id].pageviewsData.datasets, _charts[id].pageviewsData.labels);
						openingtab.data('draw', true);
					} else {
						openingtab.data('draw', false);
					}
				} else if (this.id == 'tabusers-' + id) {
					_charts[id].usersData = {datasets:[newUsersData, oldUsersData], labels:lineLabels};
					if (openingtab.attr('aria-expanded') == 'true') {
						if ((typeof _charts[id].usersLine) == 'undefined') {
							var usersLineChartCanvas = $('#usersLineChart-' + id).get(0).getContext("2d");
							var usersLineChart = new Chart(usersLineChartCanvas).Line(usersLineChartData, lineChartOptions);
							_charts[id].usersLine = usersLineChart;
						}
						updateLineChart(_charts[id].usersLine, _charts[id].usersData.datasets, _charts[id].usersData.labels);
						openingtab.data('draw', true);
					} else {
						openingtab.data('draw', false);
					}
				} else if (this.id == 'tabtotal-' + id) {
					if (openingtab.attr('aria-expanded') == 'true') {
						var usersPieChartCanvas = $('#totalusersPieChart-' + id).get(0).getContext("2d");
						new Chart(usersPieChartCanvas).Pie(usersPieChartData, pieChartOptions);
						openingtab.data('draw', true);
					} else {
						openingtab.data('draw', false);
					}
				}
			});

			var tabpageviews = $('#tabpageviews-' + id);
			tabpageviews.on('shown.bs.tab', function (e) {
				var currentTab = $(this);
				if (currentTab.data('draw') == false) {
					if ((typeof _charts[id].pageviewsLine) == 'undefined') {
						var pageviewsLineChartCanvas = $('#pageviewsLineChart-' + id).get(0).getContext("2d");
						var pageviewsLineChart = new Chart(pageviewsLineChartCanvas).Line(pageviewsLineChartData, lineChartOptions);
						_charts[id].pageviewsLine = pageviewsLineChart;
					}
					updateLineChart(_charts[id].pageviewsLine, _charts[id].pageviewsData.datasets, _charts[id].pageviewsData.labels);
					currentTab.data('draw', true);
				}
			});

			var tabusers = $('#tabusers-' + id);
			tabusers.on('shown.bs.tab', function (e) {
				var currentTab = $(this);
				if (currentTab.data('draw') == false) {
					if ((typeof _charts[id].usersLine) == 'undefined') {
						var usersLineChartCanvas = $('#usersLineChart-' + id).get(0).getContext("2d");
						var usersLineChart = new Chart(usersLineChartCanvas).Line(usersLineChartData, lineChartOptions);
						_charts[id].usersLine = usersLineChart;
					}
					updateLineChart(_charts[id].usersLine, _charts[id].usersData.datasets, _charts[id].usersData.labels);
					currentTab.data('draw', true);
				}
			});

			var tabtotal = $('#tabtotal-' + id);
			tabtotal.on('shown.bs.tab', function (e) {
				var currentTab = $(this);
				if (currentTab.data('draw') == false) {
					var usersPieChartCanvas = $('#totalusersPieChart-' + id).get(0).getContext("2d");
					new Chart(usersPieChartCanvas).Pie(usersPieChartData, pieChartOptions);
					currentTab.data('draw', true);
				}
			});
		}
	};
}());