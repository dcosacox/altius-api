<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConnectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function download(Request $request){
        $token = $request->post('token');
        $apiUrl = $request->post('apiUrl');
        
        header('Content-Type: application/json'); // Specify the type of data
        $ch = curl_init($apiUrl); // Initialise cURL
        // $post = json_encode($post); // Encode the data array into a JSON string
        $authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); // Inject the token into the header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1); // Specify the request method as POST
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Set the posted fields
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects
        $result = curl_exec($ch); // Execute the cURL statement
        curl_close($ch); // Close the cURL connection
        return json_decode($result);



    }
    
    public function connect(Request $request)
    {

       $email = $request->post('name'); 
       $site = $request->post('siteUrl'); 
       $pass = $request->post('password'); 

        // $email = 'fo1_test_user@whatever.com';
        // $site = 'fo1.altius.finance';
        // $pass = 'Test123!';


        // get the first page: 

        //init curl
        $ch = curl_init();  

        //Set the URL to work with
        curl_setopt($ch, CURLOPT_URL, $site);

        // ENABLE HTTP POST
        curl_setopt($ch, CURLOPT_POST, false);

        // curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        //Handle cookies for the login
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');

        //Setting CURLOPT_RETURNTRANSFER variable to 1 will force cURL
        //not to print out the results of its query.
        //Instead, it will return the results as a string return value
        //from curl_exec() instead of the usual true/false.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, true);


        //execute the request (retrieve the main login page)
        $basic_page = curl_exec($ch);
        curl_close($ch);

        // if the login page is based on javascript
        if(strpos($basic_page, 'You need to enable JavaScript to run this app')){
            preg_match_all('/< *script[^>]*src *= *["\']?([^"\']*)/i', $basic_page, $matches);
            $js_file_path = $matches[1][0];
            $js_file_path = 'https://' . $site . $js_file_path;
        }

        // Get the file name and extension using basename()
        $fileName = basename( $js_file_path );

        // Specify file save path
        $savePath =  $fileName;

        // Use file_get_contents to get the file, use @ to hide possible errors
        $js_file_content = @file_get_contents( $js_file_path );

        $offset = 0;
        $pos_arr = [];
        while (($pos = strpos($js_file_content, 'api/', $offset)) !== FALSE) {
            $offset = $pos + 1;
            $first_quote = min(strpos($js_file_content,'"', $offset-1), strpos($js_file_content,'?', $offset-1));
            $endpoint = trim (substr($js_file_content,$offset-1,$first_quote-$offset+1), '/'); 
            if(!isset($pos_arr[$endpoint]) && trim($endpoint) != '' ) {
                $pos_arr[] = trim($endpoint);
            };
        }

        // contains all available endpoints of the site
        $endpoints_arr = array_values(array_unique(array_values($pos_arr)));

        foreach ($endpoints_arr as $endpoint) {
            if(str_ends_with($endpoint, '/login')){
                $api_url = $endpoint;
            }
        } 

        // login on site file

        $site = str_replace('altius','api.altius',$site);
        $api_url = 'https://'. $site .'/'.  $api_url;

        //init curl
        $ch = curl_init();  

        //Set the URL to work with
        curl_setopt($ch, CURLOPT_URL, $api_url);

        // ENABLE HTTP POST
        curl_setopt($ch, CURLOPT_POST, true);

        //Set the post parameters
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'email='.$email.'&password='.$pass);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);


        //Handle cookies for the login
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');

        //Setting CURLOPT_RETURNTRANSFER variable to 1 will force cURL
        //not to print out the results of its query.
        //Instead, it will return the results as a string return value
        //from curl_exec() instead of the usual true/false.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, true);


        // execute the login request

        $login_content = curl_exec($ch);
        curl_close($ch);
        return $login_content;
    }
}
