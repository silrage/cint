//Load API
console.log( 'init Insta lib: YES' );
// var respCode = window.location.search,
//     minusQueCode = respCode.substr( 6 ),
//     checkQuestion = minusQueCode.indexOf('&'),
//     code = '';
//
// (checkQuestion == '-1') ? code = minusQueCode : code = minusQueCode.substr(0, checkQuestion);
// console.log(code);

var hash = window.location.hash,
    checkAcTo = hash.substr(1, 12),
    respToken;

( checkAcTo == 'access_token' ) ? respToken = hash.substr(14, hash.length) : respToken = false;
// if(respToken) {
//   window.location = '/';
//   $.ajax({
//     url: 'https://api.instagram.com/v1/users/self/media/recent/',
//     data: {
//       access_token: respToken
//     }
//   })
//   .done(function(resp){
//     console.log( resp );
//   })
// }
