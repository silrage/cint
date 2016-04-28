<?php
 /**
  * Main instagram framework
   */
  //  print parse_url( $_SERVER['REQUEST_URI'] )['query'];
   $url = 'https://api.instagram.com/oauth/access_token';
   $client_id = "cb2e702fde06407da2bfeb9ffdb6618f";
   $client_secret = "96704d4432434dab981bbfb6d740c9b1";
   //SSL Enought
  //  $code = "f9918b9e7db24a01b79819ef44e2d577";
  //  $ch = curl_init();
  //  if(!$ch) die("Couldn't initialize a cURL handle");
   //
  //  $ret = curl_setopt( $ch, CURLOPT_URL, $url );
  //  $ret = curl_setopt( $ch, CURLOPT_HEADER, 1 );
  //  $ret = curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
  //  $ret = curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 0 );
  //  $ret = curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );
  //  $ret = curl_setopt( $ch, CURLOPT_POSTFIELDS, "client_id=".$client_id );
  //  $ret = curl_setopt( $ch, CURLOPT_POSTFIELDS, "client_secret=".$client_secret );
  //  $ret = curl_setopt( $ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code" );
  //  $ret = curl_setopt( $ch, CURLOPT_POSTFIELDS, "redirect_uri=http://cint.dev" );
  //  $ret = curl_setopt( $ch, CURLOPT_POSTFIELDS, "code=".$code );
  //  $ret = curl_setopt( $ch, CURLINFO_HEADER_OUT, true );
   //
  //  //execute
  //  $ret =  curl_exec($ch);
  //  if (empty($ret)) {
  //     // some kind of an error happened
  //     die(curl_error($ch));
  //     curl_close($ch); // close cURL handler
  //   } else {
  //       $info = curl_getinfo($ch);
  //       curl_close($ch); // close cURL handler
  //       if (empty($info['http_code'])) {
  //               die("No HTTP code was returned");
  //       } else {
  //           // load the HTTP codes
  //           $http_codes = parse_ini_file("../../userdata/config/");
  //           // echo results
  //           echo "The server responded: <br />";
  //           echo $info['http_code'] . " " . $http_codes[$info['http_code']];
  //       }
  //   }
  //  var_dump( curl_getinfo($ch) );
  //  var_dump( curl_getinfo($ch,CURLINFO_HEADER_OUT) );
  //  curl_close($ch);

    //  -F 'client_id=cb2e702fde06407da2bfeb9ffdb6618f' \
    //  -F 'client_secret=96704d4432434dab981bbfb6d740c9b1' \
    //  -F 'grant_type=authorization_code' \
    //  -F 'redirect_uri=http://cint.dev' \
    //  -F 'code=CODE' \
    //  https://api.instagram.com/oauth/access_token

 ?>
