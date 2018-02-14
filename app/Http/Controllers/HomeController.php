<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Googl;

class HomeController extends Controller
{
    public function index()
    {
        return view('login');
    }


    public function login(Request $request)
    {
        // Build the client object
        $cli = new Googl;
        $client = $cli->client();

        // Validate authorization code response:
        if ($request->has('code')) {

            //Exchange authorization code for refresh and access tokens
            $client->authenticate($request->input('code'));
            $token = $client->getAccessToken();

            
            // Build the service object
            $drive = new \Google_Service_Drive($client);

            // Calling an API
           
           /* $optParams = array(
                'pageSize' => 10,
                'fields' => 'nextPageToken, files(id, name)'
              );
            
            $results = $drive->files->listFiles($optParams);
            
            dd($results);
            */

            session([
                'user' => [
                    'token' => $token
                ]
            ]);
           
           return redirect('/dashboard');

        } else {
            //Generate a URL to request access from Google's OAuth 2.0 server
            $auth_url = $client->createAuthUrl();
            return redirect($auth_url);
        }
   }
}