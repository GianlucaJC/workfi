<?php
//test
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Route;

use App\Models\note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Session;
use DB;


class AjaxController extends Controller
{
	public function __construct(){
	}	

   public function save_nota(Request $request) {
        $user=$request->input('user');
        $codlav=$request->input('codlav');
        $testo_nota=$request->input('testo_nota');
        $stato_nota=$request->input('stato_nota');

        $note=new note;
        $note->id_user=$user;
        $note->codlav=$codlav;
        $note->note=$testo_nota;
        $note->stato_nota=$stato_nota;
        $note->save();	
        $risp=array();
        $risp['esito']="OK";
        return json_encode($risp);	
   }


}
