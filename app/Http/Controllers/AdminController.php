<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Googl;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    private $client;
    private $drive;
    private $token;

    public function __construct(Request $request)
    {
       

       
    }

    public function startConfig(){
        $cli = new Googl;
        $this->client = $cli->client();
        //dd(session('user.token'));
        $this->client->setAccessToken(session('user.token'));
        $this->drive = $cli->drive($this->client);
    }


    public function index()
    {   

        $this->startConfig();
        return view('admin.dashboard');
    }


    public function files()
    {
        $this->startConfig();
        $result = [];
        $pageToken = NULL;
        $files = [];

        // tres mese atras a partir de hoy en formato  formato RFC que por default es
        // timezone is UTC, e.g., 2012-06-04T12:00:00-08:00.
        // y es el formato nombrado en la documentacion de la api
        $three_months_ago = Carbon::now()->subMonths(3)->toRfc3339String();

        do {
            try {
                $parameters = [
                    'q' => "viewedByMeTime >= '$three_months_ago' or modifiedTime >= '$three_months_ago'",
                    'orderBy' => 'modifiedTime',
                    'fields' => 'nextPageToken, files(id, name, modifiedTime, iconLink, webViewLink, webContentLink)',
                ];

                // de existir el token para continuar una solicitud de lista previa en la página siguiente
                if ($pageToken) {
                    $parameters['pageToken'] = $pageToken;
                }

                $result = $this->drive->files->listFiles($parameters);
                $files = $result->files;
                //array_push($files,$result->files);

                // se obtiene la pagina siguiente 
                $pageToken = $result->getNextPageToken();

            } catch (Exception $e) {
                return redirect('/files')->with('message',
                    [
                        'type' => 'error',
                        'text' => 'Something went wrong while trying to list the files'
                    ]
                );
              $pageToken = NULL;
            }
            // debe seguir enviando la consulta hasta que no exista una pagina mas
        } while ($pageToken);

        $page_data = [
            'files' => $files
        ];

        return view('admin.files', $page_data);
   }


    public function search(Request $request)
    {   
        $this->startConfig();
        $query = '';
        $files = [];

        if ($request->has('query')) {
            $query = $request->input('query');

            $parameters = [
                'q' => "name contains '$query'",
                'fields' => 'files(id, name, modifiedTime, iconLink, webViewLink, webContentLink)',
            ];

            $result = $this->drive->files->listFiles($parameters);
            if($result){
                $files = $result->files;
            }
        }

        $page_data = [
            'query' => $query,
            'files' => $files
        ];

        return view('admin.search', $page_data);
   }


    public function delete($id)
    {   
        $this->startConfig();
        try {
            $this->drive->files->delete($id);
        } catch (Exception $e) {
            $message = [
                'type' => 'success',
                'text' => 'No se pudo eliminar el archivo'];
            return view('admin.dashboard',$message);
          
        }

        $message = [
            'type' => 'success',
            'text' => 'Archivo eliminado'];
        return view('admin.dashboard', $message);
          
    }


    public function upload()
    {   
        $this->startConfig();
        return view('admin.upload');
    }


    public function doUpload(Request $request)
    {   
        $this->startConfig();
        if ($request->hasFile('file')) {

            $file = $request->file('file');

            $mime_type = $file->getMimeType();
            $title = $file->getClientOriginalName();
            $description = $request->input('description');

            $name = $file->getClientOriginalName();
                        
            
            $request->file('file')->move('documentos/', $name);
            $contents = File::get(public_path('documentos\\'.$name));


            $file_metadata = new \Google_Service_Drive_DriveFile();
            $file_metadata->setName($title);
            $file_metadata->setDescription($description);
            //$file_metadata->setMimeType($mime_type);

            try {
                // permite subir varios archivos a la vez (multipart)
                $createdFile = $this->drive->files->create($file_metadata, [
                    'data' => $contents,
                    'mimeType' => $mime_type,
                    'uploadType' => 'multipart'
                ]);

                $file_id = $createdFile->getId();

                File::delete(public_path('documentos\\'.$name));

                $message = [
                    'type' => 'success',
                    'text' => 'Archivo almacenado con el id: '. $file_id];
                return view('admin.upload', $message);

                

            } catch (Exception $e) {

                $message = [
                    'type' => 'error',
                    'text' => 'Error al almcenar el archivo'];
                return view('admin.upload', $message);
               
            }
        }

    }


    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect('/')->with('message', ['type' => 'success', 'text' => 'You are now logged out']);
    }

}