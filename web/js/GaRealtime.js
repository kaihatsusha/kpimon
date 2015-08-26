var CONST_HISREALTIMES = 46;
var GaRealtime = (function(){
	// private propertise
	var _hisRealtimes = {};
	
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
				render.attr('xxx', list.length);
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
			console.log(url,medias,callable);
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

		}
	};
	
}());