'use strict'

var app = angular.module('cint', [
  'ngRoute'
]);

var routes = function($httpProvider, $routeProvider, $locationProvider, $http) {
  $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
  $routeProvider
    .when('/', {

    })
    //Error when not find page
    .otherwise({
      redirectTo: '/'
    })
  // Use HTML5 to History stack, without hashtag
  $locationProvider.html5Mode({
    enabled: true,
    requireBase: false
  });
};

app.controller('authorize', ['$rootScope', '$scope', '$location', '$http', function($rootScope, $scope, $location, $http) {
  var hash = window.location.hash,
      checkAcTo = hash.substr(2, 12),
      respToken;
  ( checkAcTo == 'access_token' ) ? respToken = hash.substr(15, hash.length) : respToken = false;
  if(respToken) {
    $rootScope.auth = {
      token: respToken,
      insta: true
    }
  }

  $scope.instaAuthorize = function() {
    // var instaAuthURL = 'https://api.instagram.com/oauth/authorize/?client_id=cb2e702fde06407da2bfeb9ffdb6618f&redirect_uri=http://cint.dev&response_type=token';
    // var fd = new FormData();
    // $http.post(instaAuthURL, fd, {
    //     transformRequest: angular.identity,
    //     headers: {'Content-Type': undefined}
    // })
    // .success(function(resp){
    //   console.log(resp);
    // })
    // .error(function(resp){
    //   console.log(resp);
    // });
  }
}])

app.controller('objects', ['$rootScope', '$scope', '$http', function($rootScope, $scope, $http) {
  var respToken = $rootScope.auth.token;
  $scope.view = function() {
    // var instaURL = 'https://api.instagram.com/v1/tags/nofilter/media/recent?access_token='+respToken;
    var instaURL = 'https://api.instagram.com/v1/users/self/?access_token='+respToken;
    $http({
      method: 'GET',
      url: '/insta/obj.php?url=https://api.instagram.com/v1/users/self/?access_token='+respToken//instaURL,//'insta/obj.php?url='+instaURL,
      // headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      // transformRequest: function(obj) {
      //   var str = [];
      //   for (var p in obj)
      //     str.push(encodeURIComponent(p) + '=' + encodeURIComponent(obj[p]));
      //   return str.join('&');
      // },
      // data: {
      //   access_token: respToken
      // }
    })
    .success(function(resp){
      // console.log(resp);
      $scope.profile = {
        insta: resp.data
      }
      $scope.action(respToken);

      //id_hj = 1390573092

      //Get my popular media
      // $http({
      //   method: 'GET',
      //   url: '/insta/obj.php?url=https://api.instagram.com/v1/users/self/media/liked?access_token='+respToken
      // }).success(function(resp){
      //   // console.log(resp);
      //   $scope.gallery = resp.data;
      //   $scope.gallery.size = 'low_resolution';
      //   $scope.gallery.countImages = 6;
      // })

    })
    .error(function(resp){
      console.log(resp);
    });
  }

  $scope.action = function(token){
    //Get my followed_by
    $http({
      method: 'GET',
      url: '/insta/obj.php?url=https://api.instagram.com/v1/users/self/follows?name=helenajewelry&name=helenajewelry&access_token='+token
    }).success(function(resp){
      console.info(resp, 'action');
      // $scope.followed_by = resp.data;
      // $scope.followed_by.countFollowers = 5;
    })
  }



  $scope.exit = function() {
    $scope.profile = false;
    $rootScope.auth = false;
    window.location = '/';
  }
  $scope.view();
}])