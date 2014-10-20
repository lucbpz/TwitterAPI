<meta charset="utf-8">
<?php
class Twitter{
 
    function getTweets($user,$since_id){
        ini_set('display_errors', 1);
        require_once('TwitterAPIExchange.php');
 
        $settings = array(
            'oauth_access_token' => "174750936-DzNSD9DphIEAejkJDHfYrzuK2hZsgBRGu5Cpx2h9",
            'oauth_access_token_secret' => "AbNjGzKHLHxyeMfs3ZIezzRUeewxaAMcseJktqCZbWegX",
            'consumer_key' => "ZEMWp9Jj4nvLX4ZcFJA12Lu9Z",
            'consumer_secret' => "Cw72rVoHD3lhYoBUCDR0pgQtWg3lKe6EKf6N5WJlYt5zdT9hlp"
        );
 
        $url = 'https://api.twitter.com/1.1/search/tweets.json';
        $getfield = '?q='.$user.'&count=100&since_id='.$since_id;        
        $requestMethod = 'GET';
        $twitter = new TwitterAPIExchange($settings);
        $json =  $twitter->setGetfield($getfield)
                     ->buildOauth($url, $requestMethod)
                     ->performRequest();
        return $json;
 
    }
	
	 	function getSinceID($jsonraw){
 		// $rawdata = "";
        $json = json_decode($jsonraw);
		$user = $json->statuses;
 		$user = $user[1];
		
		$sinceid = $user->id;
		
		return $sinceid;
 	}
 
    function getArrayTweets($jsonraw){
        $rawdata = "";
        $json = json_decode($jsonraw);
        $num_items = count($json->statuses);
        for($i=0; $i<$num_items; $i++){
 
            $user = $json->statuses;
 			$user = $user[$i];
            $fecha = $user->created_at;
            $url_imagen = $user->user->profile_image_url;
            $screen_name = $user->user->screen_name;
            $tweet = $user->text;
 
            $imagen = "<a href='https://twitter.com/".$screen_name."' target=_blank><img src=".$url_imagen."></img></a>";
            $name = "<a href='https://twitter.com/".$screen_name."' target=_blank>@".$screen_name."</a>";
 
 			$coincidencia = (preg_match('/RT /', $tweet) || preg_match('/RT@/', $tweet));
 
 
 			if(!$coincidencia){
	            $rawdata[$i][0]=$fecha;
	            $rawdata[$i]["FECHA"]=$fecha;
	            $rawdata[$i][1]=$imagen;
	            $rawdata[$i]["imagen"]=$imagen;
	            $rawdata[$i][2]=$name;
	            $rawdata[$i]["screen_name"]=$name;
	            $rawdata[$i][3]=$tweet;
	            $rawdata[$i]["tweet"]=$tweet;
			}
        }
        return $rawdata;
    }
 
    function displayTable($rawdata){
 
        //DIBUJAMOS LA TABLA
        echo '<table border=1>';
        $columnas = count($rawdata[0])/2;
        //echo $columnas;
        $filas = count($rawdata);
        //echo "<br>".$filas."<br>";
        //A�adimos los titulos
 
        for($i=1;$i<count($rawdata[0]);$i=$i+2){
            next($rawdata[0]);
            echo "<th><b>".key($rawdata[0])."</b></th>";
            next($rawdata[0]);
        }
        for($i=0;$i<$filas;$i++){
            echo "<tr>";
            for($j=0;$j<$columnas;$j++){
                echo "<td>".$rawdata[$i][$j]."</td>";
 
            }
            echo "</tr>";
        }       
        echo '</table>';
    }
}

//inicializacion variables
 $stringpelicula= Array ("pelicula%20personaje%20" , "pelicula%20personajes%20" , "pelicula%20direccion%20", "pelicula%20bso%20" , 
 "pelicula%20actor%20" , "pelicula%20actriz%20" , "pelicula%20actores%20" , "pelicula%20actrices%20" , "pelicula%20fotografia%20" , 
 "pelicula%20genero%20" , "pelicula%20recomendable%20" , "pelicula%20recomiendo%20" , "pelicula%20mejores%20" , 
 "pelicula%20peores%20" , "pelicula%20interprete%20" , "pelicula%20interpretes%20" , "pelicula%20artista%20" , "pelicula%20artistas%20" , 
 "pelicula%20proyeccion%20" , "pelicula%20guion%20" , "pelicula%20escena%20" , "pelicula%20escenas%20" , "pelicula%20protagonista%20" , 
 "pelicula%20protagonistas%20" , "pelicula%20director%20" , "pelicula%20buena%20" , "pelicula%20mala%20", "pelicula%20genial%20" , "pelicula%20vaya%20" ,
 "pelicula%20me%20" , "pelicula%20gran%20");
 //$stringpelicula= Array ("pelicula%20");
 
$since_id=0;
$maxSince_id=0;
 
 //bucle de busquedas
 for ($i=0; $i < count($stringpelicula); $i++) { 
 
	$twitterObject = new Twitter();
	$jsonraw =  $twitterObject->getTweets($stringpelicula[$i].$pelicula,$since_id);
	
	//echo empty($jsonraw);
	$rawdata =  $twitterObject->getArrayTweets($jsonraw);
	     if ($since_id < $twitterObject->getSinceID($jsonraw)) {
     	//solo se actualiza maxSince_id si el since_id del primer tweet es mayor.
         $maxSince_id = $twitterObject->getSinceID($jsonraw);
     	}
	
	if (!empty($rawdata)) {
		$twitterObject->displayTable($rawdata);
		
	}
	else {
		echo "no hay resultados";
	}

 }
 
 //una vez que termina el bucle de búsquedas actualizamos el campo since_id para futuras búsquedas
 $since_id=$maxSince_id;














?>
