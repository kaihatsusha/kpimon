///////////////////////////
// JS for GA widgets     //
// Author: Nhan          //
// Created: 2015/07/30   //
///////////////////////////
var WidgetJs = (function (/*contructVar*/){
	// PRIVATE ZONE :D
	// Private varibles
	var template = $('#template').html();
	var status1 = new Array("success", "warning", "danger","info","success", "warning", "danger","info");
	
	console.log(status1);
	// Private method
	var renderDevicePartition = function(data,target){
		var rendered = Mustache.render(template, data);
		$(target).html(rendered);
	}
	// CONTRUCTOR ZONE
	Mustache.parse(template);   // optional, speeds up future uses

	// PUBLIC ZONE !!!
	return { 
		// public var
		publicvar : 'publicvar',
		// public function
		start : function(url,id,key){
		var evtSource = new EventSource(url);
		console.log('aaa',this);
		evtSource.addEventListener('hasdata', function(e) {
			var obj = JSON.parse(e.data);
//			console.log(obj.rt_pvs_right_now);
			var listDevice = new Array();
			var total = obj.rt_pvs_right_now.metricTotals;
			var total2 = 0;
			$.each(obj.rt_pvs_right_now.rows, function(index,value){
//				listDevice[index] = value.dimensionValues[0];
				obj.rt_pvs_right_now.rows[index].percent = Math.round((value.metricValues[0] / total) * 100);
				obj.rt_pvs_right_now.rows[index].device = value.dimensionValues[0];
				obj.rt_pvs_right_now.rows[index].color = status1[index];
				total2+=obj.rt_pvs_right_now.rows[index].percent;
			});
			if(typeof obj.rt_pvs_right_now.rows[0] != 'undefined')
				obj.rt_pvs_right_now.rows[0].percent -= (total2 - 100);

//				console.log(obj.rt_pvs_right_now.metricTotals[0]);
			renderDevicePartition(obj.rt_pvs_right_now,'.progress-legend-'+id);
			
			$('#eventList-'+id).text(obj.rt_pvs_right_now.metricTotals[0]?obj.rt_pvs_right_now.metricTotals[0]:0);
		}, false);
			}
	}
}(/*contructValue*/)); // Widgetjs