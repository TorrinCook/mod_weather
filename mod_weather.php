<?php
//print_r($params->get('location'));die;
/**
* Weather module designed to pull in weather information from
* an xml api from http://www.worldweatheronline.com
**/
defined('_JEXEC') or die;
echo '<style type="text/css">
table, td, th
{
border:0px;
}
td
{
vertical-align:top;
padding:10px;
}
</style>';
if($_GET["q"])
$area=str_replace(" ", "+", $_GET["q"]);
else $area=str_replace(" ", "+", $params->get('location'));
$key='?key=1f1a74a94c191313123105';
$days='&num_of_days='.$params->get('days');
$Url='http://free.worldweatheronline.com/feed/weather.ashx'.$key.''.$days.'&q='.$area;
function curl_download($Url){

    // is cURL installed yet?
    if (!function_exists('curl_init')){
        die('Sorry cURL is not installed!');
    }

    // OK cool - then let's create a new cURL resource handle
    $ch = curl_init();

    // Now set some options (most are optional)

    // Set URL to download
    curl_setopt($ch, CURLOPT_URL, $Url);

    // Should cURL return or print out the data? (true = return, false = print)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Timeout in seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    // Download the given URL, and return output
    $output = curl_exec($ch);

    // Close the cURL resource, and free system resources
    curl_close($ch);

    return $output;
}

$data = curl_download($Url);
$xml = JFactory::getXML( $data, false );
// Checks if it's a real location confirmed by the weather API
if($xml->error->msg)
    echo $xml->error->msg;
else
{
//print_r($xml);die;
echo '<table>';
echo 'Information for the '.$xml->request->type.' of '.$xml->request->query;

echo '<tr><td><p>Todays Weather</br>';

echo '<img border=0 src="'.$xml->current_condition->weatherIconUrl.'" alt="'.$xml->current_condition->weatherDesc.'" /></p></td>';

echo '<td>Time Recorded: </br>'.$xml->current_condition->observation_time.'</br></br>Humidity : '.$xml->current_condition->humidity.'</td>';

echo '<td>Tempature: </br>'.$xml->current_condition->temp_F.' F</br></br>Visibility : '.$xml->current_condition->visibility.'</td>';

echo '<td>Wind Speed :</br>'.$xml->current_condition->windspeedMiles.' mph '.$xml->current_condition->winddir16Point.'</td></tr><tr>';

for($day=1;$day<$params->get('days');$day++)
{
    if ($day==3)
        echo '</tr><tr>';
    echo '<td><p>';
    echo 'Date:'.$xml->weather[$day]->date.'</br><img border=0 src="'.$xml->weather[$day]->weatherIconUrl.'" alt="'.$xml->weather[$day]->weatherDesc.'" /></td>';

    echo '<td>Tempature (F): </br>';
    echo 'Low of '.$xml->weather[$day]->tempMinF;
    echo '</br>High of '.$xml->weather[$day]->tempMaxF;

    echo '</br>Wind Speed : </br>';
    echo $xml->weather[$day]->windspeedMiles.' mph '.$xml->weather[$day]->winddirection;
    echo '</p></td>';
}
echo '</tr></table>';
}
if ($params->get('searchable')==1)
echo '<form action=# method="get">
    Location (zip code or city name):</br>
    <input type="text" name="q" /></br>
    </br><input type="submit"/>
    </form>';