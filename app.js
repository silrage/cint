'use strict'

/**
 * =======================================================================
 * = Project TODO's:
 * =======================================================================
 * 1. When load app start mainLoad() core function
 * 2. LocalSettings save authorize keys
 * 3. When authorize put service name & token for fast authorize
 * 4. Actions cashed
 * 5. Pages cashed
 * 6. Responsive
 * =======================================================================
 */

function getUrlVars( param ){
  var vars = {}, hash;
  if(param == undefined) {
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++) {
      hash = hashes[i].split('=');
      vars.push(hash[0]);
      vars[hash[0]] = hash[1];
    }
  }else if(param === 'vk') {
    var code = 'code=';
    var href = window.location.href;
    vars.code = href.slice(href.indexOf(code) + code.length);
  }else{
    hash = 'access_token=';
    var href = window.location.href;
    vars.code = href.slice(href.indexOf(hash) + hash.length);
  }
  return vars;
}
function setCookie(name, value) {
  if(name != undefined) document.cookie = name+ "=" +value+ "; path=/;";
}
function getCookie(name) {
  if(name != undefined) {
    var matches = document.cookie.match(new RegExp(
      "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ))
    return matches ? decodeURIComponent(matches[1]) : undefined
  }else{
    return false;
  }
}

var sets = {},
    auth,
    authorized = {},
    App = angular.module('cint', [
      'ngRoute'
    ]),
    routes = function($httpProvider, $routeProvider, $locationProvider, $http) {
      $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
      $routeProvider
        .when('/', {
          templateUrl: '/main.tpl'
        })
        .when('/access_token=:token', {
          templateUrl: '/panel.tpl'
        })
        .when('/panel', {
          templateUrl: '/panel.tpl'
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

App.config(['$httpProvider', '$routeProvider', '$locationProvider', routes])
.run(['$http', function($http){
  //Core start
  $http({
    url: '/settings.json'
  }).success(function(file){
    sets = file[0].plugins;
  })

}])

.controller('getOBJ', ['$rootScope', '$scope', '$http', '$timeout', '$routeParams', function($rootScope, $scope, $http, $timeout, $routeParams){

  $scope.profile = {};

  //When load app need access to existing keys from cookie
  function mainLoad() {
    // console.log( plugin );
    // console.log( $location );
    // console.log( $get );
    // console.log( getUrlVars().code )
    auth = getCookie('authorize');
    if(auth) {
      if(getUrlVars(auth).code != undefined) {
        if(getCookie('authorize')) {
          // console.log($scope);
          $scope[auth].SetToken(getUrlVars(auth).code);
        }
      }
      if(auth === 'vk') {
        // $scope.vk.View(getCookie('token_vk'));
      }else{
        $scope.instagram.View(getCookie('token_insta'));
      }
    }

    // $scope.vk.View('3cdc11197be4051171095d52d690fa66e1e4b31e2592e2079dcdc4c362b691107831e4f2e099605e86ded');

    // authorized.instagram = {token: getCookie('token_insta')};
    // authorized.vk = {token: getCookie('token_vk')};
  }



  $scope.vk = {
    Authorize: function(){
      auth = 'vk';
      // console.log(sets)
      // var sets = $scope.$on('sets', function(e, p) {
      //   console.log(e)
      //   console.log(p)
        
      // })
      // var exist = getCookie('authorize');
      setCookie('authorize', auth);
      window.location = 'https://oauth.vk.com/authorize?client_id='+sets.vk.client_id+'&redirect_uri=http://cint.dev&scope=photos';
    },
    SetToken: function(code){
      $timeout(function(){
        console.log(code)
        var urlACT = 'https://oauth.vk.com/access_token?client_id='+sets.vk.client_id+'&client_secret='+sets.vk.client_secret+'&redirect_uri=http://cint.dev&code='+code;
        // var urlACT = 'https://oauth.vk.com/access_token';
        // window.location = 'https://oauth.vk.com/access_token?client_id='+sets.vk.client_id+'&client_secret='+sets.vk.client_secret+'&redirect_uri=http://cint.dev&code='+code;
        $http({
          method: 'POST',
          url: '/vk/obj.php',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          data: {
            url: urlACT
            // client_id: sets.vk.client_id,
            // client_secret: sets.vk.client_secret,
            // redirect_uri: 'http://cint.dev',
            // code: code
          },
          transformRequest: function(obj) {
            var str = [];
            for (var p in obj)
              str.push(encodeURIComponent(p) + '=' + encodeURIComponent(obj[p]));
            return str.join('&');
          },
        })
        .then(function(resp){
          if(resp.status === 200) {
            console.log(resp.data.access_token)
            setCookie('token_vk', resp.data.access_token);
            authorized.vk = {token: resp.data};
            $scope.vk.View(token);
          }
        })

        // $http.jsonp(urlACT)
        // .then(function(resp){
        //   console.log(resp);
        // })
      })
    },
    View: function(token) {
      // uid - Author id
      var uid = '5876929'; //My
      // var uid = '242341214'; //HJ
      // var uid = '2741589'; //OZ

      //Get albums by uid or gid (when use gid attach prefix '-')
      // var oid = '-59259151';
      // var vkURL = 'https://api.vk.com/method/photos.getAlbums?owner_id='+oid+'&access_token='+token+'';

      //Get photos by aid
      // var oid = '-59259151';
      // var aid = '180787831';
      // var vkURL = 'https://api.vk.com/method/photos.get?owner_id='+oid+'&album_id='+aid+'&access_token='+token+'';

      //Get all photos with max resolutions
      function getPhotosMaxRes(stack) {
        if(stack) {
          var obj = [];
          angular.forEach(stack, function(v, i) {
            if(v.src_xxbig) {
              obj[i] = v.src_xxbig;
            }else if(v.src_xbig){
              obj[i] = v.src_xbig;
            }else if(v.src_big){
              obj[i] = v.src_big;
            }else{
              obj[i] = v.src;
            }
          })
          return obj;
        }
      }

      //Get groups by uid
      var vkURL = 'https://api.vk.com/method/groups.get?user_id='+uid+'&access_token='+token+'';

      //Get group by id
      // var gid = '59259151';
      // var vkURL = 'https://api.vk.com/method/groups.getById?group_id='+gid+'&access_token='+token+'';

      //Create Album
      //Require scope photos
      // var vkURL = 'https://api.vk.com/method/photos.createAlbum?user_id='+uid+'&access_token='+token+'&title=Test+me&';

      $http({
        method: 'POST',
        url: '/vk/obj.php',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        data: {
          url: vkURL
        },
        transformRequest: function(obj) {
          var str = [];
          for (var p in obj)
            str.push(encodeURIComponent(p) + '=' + encodeURIComponent(obj[p]));
          return str.join('&');
        },
      })
      .then(function(resp){
        //When load albums load photos in albums
        var collection = resp.data.response;
        console.log(resp)

        //Get archive with images
        //Get max resolution photos
        // var getMaxPhotos = getPhotosMaxRes(collection);
        // $http({
        //   method: 'POST',
        //   url: '/vk/obj.php',
        //   headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        //   data: {
        //     url: vkURL,
        //     save: JSON.stringify(getMaxPhotos),
        //   },
        //   transformRequest: function(obj) {
        //     var str = [];
        //     for (var p in obj)
        //       str.push(encodeURIComponent(p) + '=' + encodeURIComponent(obj[p]));
        //     return str.join('&');
        //   },
        // })
        // .then(function(save){
        //   if(save.status === 200) {
        //     var anchor = '<a href="'+save.data.archive_link+'" target="_blank">Link</a>';
        //     angular.element(document.querySelectorAll('body'))
        //       .append( anchor )
        //   }
        // })


        // angular.forEach(collection, function(v, i) {
        //   if(v.aid) {
        //     var vkURLAlbom = 'https://api.vk.com/method/photos.get?owner_id='+uid+'&album_id='+v.aid+'&access_token='+token+'';
        //     $http({
        //       method: 'POST',
        //       url: '/vk/obj.php',
        //       headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        //       data: {
        //         url: vkURLAlbom
        //       },
        //       transformRequest: function(obj) {
        //         var str = [];
        //         for (var p in obj)
        //           str.push(encodeURIComponent(p) + '=' + encodeURIComponent(obj[p]));
        //         return str.join('&');
        //       },
        //     })
        //     .then(function(get_albom) {
        //       console.log(get_albom)
        //     })
        //   }
        // })
      });
    },
    Exit: function() {
      document.cookie = 'token_vk=false; path=/; expires=Sun, 22 Jun 1941 00:00:01 GMT;';
      window.location = '/panel';
      delete authorized.vk;
    }
  }


  $scope.instagram = {
    Authorize: function(){
      auth = 'instagram';
      //Set current authorize
      setCookie('authorize', auth);
      window.location = 'https://www.instagram.com/oauth/authorize/?client_id='+sets.instagram.client_id+'&redirect_uri=http://cint.dev&response_type=token&scope=basic+comments+public_content+follower_list+relationships+likes';
    },
    SetToken: function(token){
      if(token !== undefined) {
        setCookie('token_insta', token);
        document.cookie = 'authorize=false; path=/; expires=Sun, 22 Jun 1941 00:00:01 GMT;';
        window.location = '/panel';
        //View profile
        authorized.instagram = {token: token};
        $scope.instagram.View(token);
      }
    },
    View: function(token){
      var instaURL = 'https://api.instagram.com/v1/users/self/?access_token='+token+'&callback=JSON_CALLBACK';
      $http.jsonp(instaURL).success(function(resp) {
        $scope.profile.insta = resp.data
      })
      var endPoint = 'https://api.instagram.com/v1/users/self/media/liked?access_token='+token+'&callback=JSON_CALLBACK';
      $http.jsonp(endPoint)
      .success(function(resp){
        $scope.gallery = resp.data;
        $scope.gallery.size = 'low_resolution';
        $scope.gallery.countImages = 6;
      })
    },
    Action: function(task){
      console.log(task)
      var token = authorized.instagram.token;
      if(task == 'followed_by') {
        var endPoint = 'https://api.instagram.com/v1/users/self/followed-by?access_token='+token+'&callback=JSON_CALLBACK';
        $http.jsonp(endPoint).success(function(resp) {
          console.info(resp.data, 'followed_by');
          $scope.profile.insta.followed_by = resp.data;
        });
      }else if(task == 'follows'){
        var endPoint = 'https://api.instagram.com/v1/users/self/follows?access_token='+token+'&callback=JSON_CALLBACK';
        $http.jsonp(endPoint).success(function(resp) {
          console.info(resp.data, 'follows');
          $scope.profile.insta.follows = resp.data;
        });
      }else if(task == 'get_likes'){
        var mediaId = '1078185748760136941_1267338874';
        var endPoint = 'https://api.instagram.com/v1/media/'+mediaId+'/likes?access_token='+token+'&callback=JSON_CALLBACK';
        $http.jsonp(endPoint).success(function(resp) {
          console.info(resp.data, 'likes');
          $scope.profile.insta.likes = resp.data;
        });
      }else if(task == 'user_info'){
        var userId = '1390573092';
        var endPoint = 'https://api.instagram.com/v1/users/'+userId+'?access_token='+token+'&callback=JSON_CALLBACK';
        $http.jsonp(endPoint).success(function(resp) {
          console.info(resp.data, 'info about user');
          $scope.profile.insta.userGet = resp.data;
        });
      }else if(task == 'recent_posts'){
        var userId = '1390573092';
        var endPoint = 'https://api.instagram.com/v1/users/'+userId+'/media/recent?access_token='+token+'&callback=JSON_CALLBACK';
        $http.jsonp(endPoint).success(function(resp) {
          console.info(resp.data, 'recent media');
          $scope.profile.insta.userRecent = resp.data;
        });
      }else if(task == 'relationship'){
        // Function fetch user status private or not {target_user_is_private: boolean}
        var userId = '1390573092';
        var endPoint = 'https://api.instagram.com/v1/users/'+userId+'/relationship?access_token='+token+'&callback=JSON_CALLBACK';
        $http.jsonp(endPoint).success(function(resp) {
          console.info(resp.data, 'Relationship');
          $scope.profile.insta.userRelationship = resp.data;
        });
      }else if(task == 'i_follow'){
        // Function fetch user status private or not {target_user_is_private: boolean}
        var userId = '1390573092';
        var endPoint = 'https://api.instagram.com/v1/users/'+userId+'/relationship?access_token='+token+'&action=follow&callback=JSON_CALLBACK';
        var api = '/insta/obj.php';
        $http({
          url: api,
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          transformRequest: function(obj) {
            var str = [];
            for (var p in obj)
              str.push(encodeURIComponent(p) + '=' + encodeURIComponent(obj[p]));
            return str.join('&');
          },
          data: {
            url: endPoint
          }
        }).success(function(resp) {
          console.info(resp.data, 'Relationship');
          // $scope.profile.insta.userRelationship = resp.data;
        });      
      }else if(task == 'search'){
        var userName = 'j._helena';
        var endPoint = 'https://api.instagram.com/v1/users/search?q='+userName+'&access_token='+token+'&callback=JSON_CALLBACK';
        $http.jsonp(endPoint).success(function(resp) {
          console.info(resp.data, 'search');
        });
      }
    },
    Exit: function() {
      document.cookie = 'token_insta=false; path=/; expires=Sun, 22 Jun 1941 00:00:01 GMT;';
      window.location = '/panel';
      delete authorized.instagram;
    }
  }
  $scope.instaListActions = [
    'followed_by',
    'follows',
    'get_likes',
    'user_info',
    'recent_posts',
    'relationship',
    'search',
    'i_follow'
  ];

  mainLoad( );
  $scope.auth = authorized;
  // console.log($scope.auth)

}])

.directive('vkPanel', function(){
  return {
    restrict: 'E',
    templateUrl: 'vk.tpl'
  }
})
.directive('instaPanel', function(){
  return {
    restrict: 'E',
    templateUrl: 'insta.tpl'
  }
})



// .controller('objects', ['$rootScope', '$scope', '$http', function($rootScope, $scope, $http) {
//   var respToken = $rootScope.auth.token;
//   // console.log(respToken)
//   $scope.view = function(token) {
//     // var instaURL = 'https://api.instagram.com/v1/tags/nofilter/media/recent?access_token='+respToken;
//     var instaURL = 'https://api.instagram.com/v1/users/self/?access_token='+token+'&callback=JSON_CALLBACK';
//     $http.jsonp(instaURL).success(function(resp) {
      
//         insta: resp.data
//     })

//     //id_hj = 1390573092

//     //Get my popular media
//     var endPoint = 'https://api.instagram.com/v1/users/self/media/liked?access_token='+token+'&callback=JSON_CALLBACK';
//     $http.jsonp(endPoint)
//     .success(function(resp){
//       $scope.gallery = resp.data;
//       $scope.gallery.size = 'low_resolution';
//       $scope.gallery.countImages = 6;

//     })
//     .error(function(resp){
//       console.log(resp);
//     });
//   }

  // $scope.action = function(token){
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

  // }

// Good sample
// http://codepen.io/netsi1964/pen/drmkL?editors=1010


// }])
