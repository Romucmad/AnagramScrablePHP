<?php

/**
 * Plugin Name: AnagramAPI
 * Plugin URI: https://wordenator.com/
 * Description: Anagral Scrable APi
 * Version: 1.0
 * Author: Your Name
 * Author URI: romuchmad.me
 */


function getArrayOfWord(){
    $arr=array();
    //Read the file with words
    $handle = fopen($_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/wp-api/master-list.txt", "r");


    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            array_push($arr, strtolower(trim($line)));
        }

        fclose($handle);
    }
    return $arr;
}



function isSubArray($wordTOCompare , $wordFromDatabase , $erross){
    $arrayFromWord = str_split($wordFromDatabase);

    foreach ($wordTOCompare as $char){
        for($i=0;$i<sizeof($arrayFromWord);$i++){
           if($char == $arrayFromWord[$i]){
                unset($arrayFromWord[$i]);
                $arrayFromWord = array_values($arrayFromWord);
                break;
            }
        }

    }
    return sizeof($arrayFromWord) <= $erross;
}


function getAnagram($word){
    $returnArray= array();
    $wordCHarArray = str_split($word);
    sort($wordCHarArray);
    $erros=0;

    substr_count($word, '-');
    foreach ($wordCHarArray as $char){
        if($char=="-"){
            $erros++;
        }
    }
    $wordsFromDatabse = getArrayOfWord();
    foreach ($wordsFromDatabse as $wordSmall) {
        if(isSubArray($wordCHarArray ,  $wordSmall, $erros)){
            array_push($returnArray,$wordSmall);
        }

    }

    $post_data = array( 'word'  => $returnArray);
    return $post_data;
}



function wl_posts() {
    return null;
}

function wl_post( $slug ) {
    $word = $slug['slug'];
    return   getAnagram($word);


}
add_action('rest_api_init', function() {
    register_rest_route('wl/v1/anagram', 'all', [
        'methods' => 'GET',
        'callback' => 'wl_posts',
    ]);
    register_rest_route( 'wl/v1/anagram', 'all/(?P<slug>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'wl_post',
    ) );
});
