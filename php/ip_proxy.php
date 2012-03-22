<?php
/**
* There4 Development Geo location proxy
* Copyright There4, 2010
* craig@there4development.com
*
* This is a proxy script for the geo location services from
* http://ipinfodb.com 
*
* This script requires Snoopy from http://sourceforge.net/projects/snoopy/
*
* You must change the $allowed_hosts array to include your own domain
*/
require "Snoopy.class.php";

if (!function_exists('json_decode')){
  require('compat.json.php');
  function json_decode($input) {
    $json = new Services_JSON();
    return $json->decode($input);
  }
  function json_encode($input) {
    $json = new Services_JSON();
    return $json->encode($input);
  }
}

/** @var string host name from which to allow access */
$allowed_hosts = array(
    "there4development.com",
    $_SERVER["HTTP_HOST"]
    );

/** @var array urls of geo location service in order of failover from primary and secondary */
$geo_locations = array(
    "http://ipinfodb.com/ip_locator.php",
    "http://backup.ipinfodb.com/ip_locator.php"
    );

/** @var array query paramters to send the location service */
$vars = array(
    'output'   => 'json',
    'timezone' => $_REQUEST['timezone'],
    'ip'       => $_REQUEST['ip']
    );

// Only allow this to be used by ourselves
if (!in_array(parse_url($_SERVER['HTTP_REFERER'],PHP_URL_HOST), $allowed_hosts)) {
  exitWithError('This service is not allowed from your referring host.');
}

// Use snoopy to make the IP request
foreach ($geo_locations as $geo_location) {
  $snoopy = new Snoopy;
  if ($snoopy->fetch($geo_location . '?' . http_build_query($vars))) {
    break;
  }
}

if ('' == $snoopy->results) {
  exitWithError('IpInfoDB service is currently unavailable');
}


// Add an extra State two letter code to these results
$data = json_decode($snoopy->results);
$data->State = state_to_code($data->RegionName);

// remove some of the bits that we don't need and save some bytes.
$unneeded_keys = array("CountryCode","CountryName","RegionCode","Latitude","Longitude");
foreach ($unneeded_keys as $key) {
  unset($data->$key);
}

// Encode and return the data
header('Content-type: text/json');
print json_encode($data);
exit();


/**
* Return an error message
*
* @var string $message
*/
function exitWithError($message) {
  header('Content-type: text/json');
  print json_encode(array(
    'Status'  => 'Error',
    'Message' => $message
    ));
  exit();
}

/**
* Translate a state name to a two letter code
*
* @var string $name Name of a USA state
* @return string Two letter iso code for a USA state, or "--"
*/
function state_to_code($name) {
  if ($name == '') { return '--'; }
  $states = array_flip(array(
    'AL' => 'Alabama',
    'AK' => 'Alaska',
    'AS' => 'American Samoa',
    'AZ' => 'Arizona',
    'AR' => 'Arkansas',
    'CA' => 'California',
    'CO' => 'Colorado',
    'CT' => 'Connecticut',
    'DE' => 'Delaware',
    'DC' => 'District of Columbia',
    'EA' => 'Pearson Core Standards',
    'FM' => 'Federated States of Micronesia',
    'FL' => 'Florida',
    'GA' => 'Georgia',
    'GU' => 'Guam',
    'HI' => 'Hawaii',
    'ID' => 'Idaho',
    'IL' => 'Illinois',
    'IN' => 'Indiana',
    'IA' => 'Iowa',
    'KS' => 'Kansas',
    'KY' => 'Kentucky',
    'LA' => 'Louisiana',
    'ME' => 'Maine',
    'MH' => 'Marshall Islands',
    'MD' => 'Maryland',
    'MA' => 'Massachusetts',
    'MI' => 'Michigan',
    'MN' => 'Minnesota',
    'MS' => 'Mississippi',
    'MO' => 'Missouri',
    'MT' => 'Montana',
    'NE' => 'Nebraska',
    'NV' => 'Nevada',
    'NH' => 'New Hampshire',
    'NJ' => 'New Jersey',
    'NM' => 'New Mexico',
    'NY' => 'New York',
    'NC' => 'North Carolina',
    'ND' => 'North Dakota',
    'MP' => 'Northern Mariana Islands',
    'OH' => 'Ohio',
    'OK' => 'Oklahoma',
    'OR' => 'Oregon',
    'PW' => 'Palau',
    'PA' => 'Pennsylvania',
    'PR' => 'Puerto Rico',
    'RI' => 'Rhode Island',
    'SC' => 'South Carolina',
    'SD' => 'South Dakota',
    'TN' => 'Tennessee',
    'TX' => 'Texas',
    'UT' => 'Utah',
    'VT' => 'Vermont',
    'VI' => 'Virgin Islands',
    'VA' => 'Virginia',
    'WA' => 'Washington',
    'WV' => 'West Virginia',
    'WI' => 'Wisconsin',
    'WY' => 'Wyoming'
  ));
  if (isset($states[$name])) { return $states[$name]; }
  return '--';
}

?>