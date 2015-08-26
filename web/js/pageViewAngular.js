/**
 * Created by Loi Dang on 7/30/2015.
 */
angular.module('pageViewApp', [])
	.controller('pageViewController', function($scope, $http, EventSource) {
		$scope.media_list = media_list;
		$scope.media_count = [];
		$scope.media_item = [];
		$scope.media_device = [];
		var device_class_list = new Array("success", "warning", "danger","info","success", "warning", "danger","info");
		angular.forEach($scope.media_list, function(media, key) {
			$scope.media_count[media.media_id] = 0;
			var evtSource = new EventSource('api/ga/ga/get-page-view?mediaId='+media.media_id+'&key='+media.rt_keyds);
			evtSource.addEventListener('hasdata-'+media.media_id, function(e) {
				var obj = JSON.parse(e.data);
				var total = obj.rt_pvs_right_now.metricTotals[0];
				var total2 = 0;
				$scope.media_count[media.media_id] = total;
				$scope.media_device[media.media_id] = [];
				angular.forEach(obj.rt_pvs_right_now.rows, function(device, index) {
					$scope.media_device[media.media_id][index] = {name: device.dimensionValues[0], count: Math.round((device.metricValues[0] / total) * 100), color: device_class_list[index]};
				});
				if(typeof obj.rt_pvs_right_now.rows[0] != 'undefined')
					obj.rt_pvs_right_now.rows[0].percent -= (total2 - 100);
			}, false);
		});
	})
	.directive("refocusOn", refocusOn)
	.factory("EventSource", EventSourceNg);
function EventSourceNg($rootScope) {
	function EventSourceNg(url) {
		this._source = new EventSource(url);
	}
	EventSourceNg.prototype = {
		addEventListener: function(x, fn) {
			this._source.addEventListener(x, function(event) {
				$rootScope.$apply(fn.bind(null, event));
			});
		}
	}
	return EventSourceNg;
}
function refocusOn() {
	return {
		scope: {
			"refocusOn": "@"
		},
		link: function(scope, el, attrs) {
		}
	}
}