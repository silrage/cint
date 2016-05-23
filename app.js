'use strict'

var app = angular.module('cint', [
  'ngRoute'
]);
var sets;
var routes = function($httpProvider, $routeProvider, $locationProvider, $http) {
  $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
  $routeProvider
    .when('/', {
      templateUrl: '/main.php'
    })
    .when('/panel', {
      templateUrl: '/panel.html'
    })
    // .when('/?code=:code', {
    //   templateUrl: 'index.php'
    // })
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

app.controller('authorize', ['$rootScope', '$scope', '$location', '$routeParams', '$http', '$timeout', function($rootScope, $scope, $location, $routeParams, $http, $timeout) {
  var hash = window.location.hash,
      checkAcTo = hash.substr(2, 12),
      respToken;
  ( checkAcTo == 'access_token' ) ? respToken = hash.substr(15, hash.length) : respToken = false;

  $scope.settings = {
    vk: {}
  };
  
  function getUrlVars(){
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++) {
      hash = hashes[i].split('=');
      vars.push(hash[0]);
      vars[hash[0]] = hash[1];
    }
    return vars;
  }


  //Core start
  //Get settings
  $http({
    url: '/settings.json'
  }).success(function(file){
    sets = file[0].plugins.vk;
    // mainLoad(  );
  })

  function mainLoad() {
    console.log( $location );
    console.log( $get );
    console.log( getUrlVars().code )
    if(getUrlVars().code != undefined) {
      sets.code = getUrlVars().code;
      var urlACT = 'https://oauth.vk.com/access_token?client_id='+sets.client_id+'&client_secret='+sets.client_secret+'&redirect_uri=http://cint.dev';
      // $http.jsonp(urlACT).success(function(resp){
      //   console.log(resp);
      // })
    }
    // console.log(sets)
  }
  $scope.vk = {
    Authorize: function(){
      // console.log(sets)
      // var sets = $scope.settings.vk;
      window.location = 'https://oauth.vk.com/authorize?client_id='+sets.client_id+'&redirect_uri=http://cint.dev';
    }
  }

  // if(respToken) {
  //   $rootScope.auth = {
  //     token: respToken,
  //     insta: true
  //   }
  // }

  // $scope.instaAuthorize = function() {
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
  // }
}])

app.controller('objects', ['$rootScope', '$scope', '$http', function($rootScope, $scope, $http) {
  var respToken = $rootScope.auth.token;
  // console.log(respToken)
  $scope.view = function(token) {
    // var instaURL = 'https://api.instagram.com/v1/tags/nofilter/media/recent?access_token='+respToken;
    var instaURL = 'https://api.instagram.com/v1/users/self/?access_token='+token+'&callback=JSON_CALLBACK';
    $http.jsonp(instaURL).success(function(resp) {
      $scope.profile = {
        insta: resp.data
      }
    })
      
    //id_hj = 1390573092

    //Get my popular media
    var endPoint = 'https://api.instagram.com/v1/users/self/media/liked?access_token='+token+'&callback=JSON_CALLBACK'; 
    $http.jsonp(endPoint)
    .success(function(resp){
      $scope.gallery = resp.data;
      $scope.gallery.size = 'low_resolution';
      $scope.gallery.countImages = 6;

    })
    .error(function(resp){
      console.log(resp);
    });
  }

  $scope.action = function(token){
    //Get my followed_by
    // var endPoint = 'https://api.instagram.com/v1/users/self/followed-by?access_token='+token+'&callback=JSON_CALLBACK'; 
    // $http.jsonp(endPoint).success(function(resp) {
    //   console.info(resp.data, 'followed_by');
    //   $scope.profile.insta.followed_by = resp.data;
    // });

    //Get my follows
    // var endPoint = 'https://api.instagram.com/v1/users/self/follows?access_token='+token+'&callback=JSON_CALLBACK'; 
    // $http.jsonp(endPoint).success(function(resp) {
    //   console.info(resp.data, 'follows');
    //   $scope.profile.insta.follows = resp.data;
    // });

    //Get likes from media
    // var mediaId = '1078185748760136941_1267338874';
    // var endPoint = 'https://api.instagram.com/v1/media/'+mediaId+'/likes?access_token='+token+'&callback=JSON_CALLBACK'; 
    // $http.jsonp(endPoint).success(function(resp) {
    //   console.info(resp.data, 'likes');
    //   $scope.profile.insta.likes = resp.data;
    // });

    //Get info about user
    // var userId = '1390573092';
    // var endPoint = 'https://api.instagram.com/v1/users/'+userId+'?access_token='+token+'&callback=JSON_CALLBACK'; 
    // $http.jsonp(endPoint).success(function(resp) {
    //   console.info(resp.data, 'info about user');
    //   $scope.profile.insta.userGet = resp.data;
    // });

    //Get most recent media by userId
    // var userId = '1390573092';
    // var endPoint = 'https://api.instagram.com/v1/users/'+userId+'/media/recent?access_token='+token+'&callback=JSON_CALLBACK'; 
    // $http.jsonp(endPoint).success(function(resp) {
    //   console.info(resp.data, 'recent media');
    //   $scope.profile.insta.userRecent = resp.data;
    // });

    //Relationship by userId
    // Function fetch user status private or not {target_user_is_private: boolean}
    // var userId = '1390573092';
    // var endPoint = 'https://api.instagram.com/v1/users/'+userId+'/relationship?access_token='+token+'&callback=JSON_CALLBACK'; 
    // $http.jsonp(endPoint).success(function(resp) {
    //   console.info(resp.data, 'Relationship');
    //   $scope.profile.insta.userRelationship = resp.data;
    // });

    //Search users by name
    // var userName = 'j._helena';
    // var endPoint = 'https://api.instagram.com/v1/users/search?q='+userName+'&access_token='+token+'&callback=JSON_CALLBACK';
    // $http.jsonp(endPoint).success(function(resp) {
    //   console.info(resp.data, 'search');
    // });

    // var endPoint = 'https://www.facebook.com/tr/?id=1267338874&ev=PageView&dl=https%3A%2F%2Fwww.instagram.com%2Fsilrage%2Ffollowers%2F&rl=https%3A%2F%2Fwww.instagram.com%2Fsilrage%2F&if=false&ts=1463577938976&ud[external_id]=da1c75d22dc3c1e844f0d528585281aa37824ee8bd1a99ad32da40a5b52d5d1a&v=2.5.0&pv=visible';
    // $http.jsonp(endPoint).success(function(resp) {
    //   console.info(resp.data, '?');
    // });

  }

// Good sample
// http://codepen.io/netsi1964/pen/drmkL?editors=1010


  $scope.exit = function() {
    $scope.profile = false;
    $rootScope.auth = false;
    window.location = '/';
  }
  $scope.view(respToken);
}])
