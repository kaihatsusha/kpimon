'use strict';
var serviceBase = 'http://localhost/KpiMon/web/api/v1/'
// Declare app level module which depends on views, and components
var spaApp = angular.module('spaApp', [
  'ngRoute',
  'spaApp.site',
  'ngAnimate'
]);
var spaApp_site = angular.module('spaApp.site', ['ngRoute']);

spaApp.config(['$routeProvider', function($routeProvider) {
  $routeProvider.otherwise({redirectTo: '/site/index'});
}]);