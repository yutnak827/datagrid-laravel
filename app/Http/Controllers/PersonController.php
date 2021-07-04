<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DB;
use Schema;

class PersonController extends Controller
{
    public function index()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://data.gov.il/api/3/action/datastore_search?resource_id=2156937e-524a-4511-907d-5470a6a5264f');

        $response->getStatusCode(); // 200
        $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'
        $result = json_decode($response->getBody()); // '{"id": 1420053, "name": "guzzle", ...}'
        
        $datas = $result->result->records;
        $indexes = [];

        if (count($datas) > 0)  {
            $json = $datas[0];
            $resArr = json_decode( json_encode($json), true);
            $indexes = array_keys($resArr);
        }

        return view("datatable", compact("datas", "indexes"));
    }

    public function datasets($datasets)
    {
        $url = "";
        $table_name = "";
        $db_table = "person_" . $datasets;
        $tab_name = "אדם";

        $records = [];
        $fields = [];
        
        switch ($datasets) {
            case 'pr2018':
                $url = "https://data.gov.il/api/3/action/datastore_search?resource_id=2156937e-524a-4511-907d-5470a6a5264f&limit=1000";
                $link = "https://data.gov.il/dataset/pr2018/resource/2156937e-524a-4511-907d-5470a6a5264f";
                $table_name = "הכונס הרשמי  פרטיים";
                break;
            case 'notary':
                $url = "https://data.gov.il/api/3/action/datastore_search?resource_id=3ead5fae-3513-46f8-a458-959ea3e035ae&limit=1000";
                $link = "https://data.gov.il/dataset/notary/resource/3ead5fae-3513-46f8-a458-959ea3e035ae";
                $table_name = "רשימת הנוטריונים";
                break;
            case 'yerusha':
                $url = "https://data.gov.il/api/3/action/datastore_search?resource_id=7691b4a2-fe1d-44ec-9f1b-9f2f0a15381b&limit=1000";
                $link = "https://data.gov.il/dataset/yerusha/resource/7691b4a2-fe1d-44ec-9f1b-9f2f0a15381b";
                $table_name = "בקשות לרשם הירושות";
                break;    
            case 'pinkashakablanim':
                $url = "https://data.gov.il/api/3/action/datastore_search?resource_id=4eb61bd6-18cf-4e7c-9f9c-e166dfa0a2d8&limit=1000";
                $link = "https://data.gov.il/dataset/pinkashakablanim/resource/4eb61bd6-18cf-4e7c-9f9c-e166dfa0a2d8";
                $table_name = "קבלנים רשומים";
                break; 
            default:
                $url = "https://data.gov.il/api/3/action/datastore_search?resource_id=2156937e-524a-4511-907d-5470a6a5264f&limit=1000";
                $link = "https://data.gov.il/dataset/pr2018/resource/2156937e-524a-4511-907d-5470a6a5264f";
                $table_name = "הכונס הרשמי  פרטיים";
                break;
        }

        if (Schema::hasTable($db_table)) {
            // $records = DB::table($db_table)->get();
            $fields = Schema::getColumnListing($db_table);
        }
        else {
            $fields=["default"];
        }

        $tab_en = "person";

        return view("datatable", compact("fields", "table_name", "link", "tab_name", "datasets", "tab_en"));
    }

    public function reload($datasets)
    {
        $db_table = "person_" . $datasets;
        Schema::dropIfExists($db_table);
        
        switch ($datasets) {
            case 'pr2018':
                $url = "https://data.gov.il/api/3/action/datastore_search?resource_id=2156937e-524a-4511-907d-5470a6a5264f&limit=1000";
                $table_name = "הכונס הרשמי  פרטיים";
                break;
            case 'notary':
                $url = "https://data.gov.il/api/3/action/datastore_search?resource_id=3ead5fae-3513-46f8-a458-959ea3e035ae&limit=1000";
                $table_name = "רשימת הנוטריונים";
                break;
            case 'yerusha':
                $url = "https://data.gov.il/api/3/action/datastore_search?resource_id=7691b4a2-fe1d-44ec-9f1b-9f2f0a15381b&limit=1000";
                $table_name = "בקשות לרשם הירושות";
                break;    
            case 'pinkashakablanim':
                $url = "https://data.gov.il/api/3/action/datastore_search?resource_id=4eb61bd6-18cf-4e7c-9f9c-e166dfa0a2d8&limit=1000";
                $table_name = "קבלנים רשומים";
                break; 
            default:
                $url = "https://data.gov.il/api/3/action/datastore_search?resource_id=2156937e-524a-4511-907d-5470a6a5264f&limit=1000";
                $table_name = "הכונס הרשמי  פרטיים";
                break;
        }
        
        do {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $url);

            $response->getStatusCode(); // 200
            $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'
            $result = json_decode($response->getBody()); // '{"id": 1420053, "name": "guzzle", ...}'
            
            
            if ($result->result) {
                $fields = $result->result->fields;
                $records = $result->result->records;

                if (!Schema::hasTable($db_table)) {
                    Schema::create($db_table, function($table) use($fields)
                    {
                        foreach ($fields as $key => $field) {
                            $value = str_replace(".", "-" , $field->id);
                            switch ($field->type) {
                                case 'int':
                                    $table->integer($value)->nullable();
                                    break;
                                case 'text':
                                    $table->text($value)->nullable();
                                    break;
                                default:
                                    $table->text($value)->nullable();
                                    break;
                            }
                        }
                    });
                }

                foreach ($records as $key => $record) {
                    $decoded = str_replace(".", "-" , json_encode($record));
                    $resArr = json_decode($decoded, true);
                    DB::table($db_table)->insert($resArr);
                }
                $url = 'https://data.gov.il' . $result->result->_links->next;
            }
        // } while (false);
        } while (count($records) > 0);

        return response()->json(['success'=>'Data is successfully added']);
    }

    public function getDatasets(Request $request, $datasets)
    {
        $db_table = "corporation_" . $datasets;
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value
        
        if (Schema::hasTable($db_table)) {
            $fields = Schema::getColumnListing($db_table);
            
            // Total records
            $totalRecords = DB::table($db_table)->select('count(*) as allcount')->count();
            $totalRecordswithFilter = DB::table($db_table)
                ->select('count(*) as allcount')
                ->where(function ($query) use($searchValue, $fields) {
                    for ($i = 0; $i < count($fields); $i++){
                    $query->orwhere($fields[$i], 'like',  '%' . $searchValue .'%');
                    }      
                })
                ->count();

            // Fetch records
            $records = DB::table($db_table)->orderBy($columnName,$columnSortOrder)
                ->where(function ($query) use($searchValue, $fields) {
                    for ($i = 0; $i < count($fields); $i++){
                    $query->orwhere($fields[$i], 'like',  '%' . $searchValue .'%');
                    }      
                })
                ->select('*')
                ->skip($start)
                ->take($rowperpage)
                ->get();

            $sno = $start+1;

            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordswithFilter,
                "aaData" => $records
            ); 

            echo json_encode($response);
        }
        else {
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => []
            ); 
            echo json_encode($response);
        }
        exit;
    }
}