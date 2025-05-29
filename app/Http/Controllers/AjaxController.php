<?php
//test
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Route;

use App\Models\note;
use App\Models\generale;
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
        $from=$request->input('from');


        $risp=array();
        $azienda=null;
        DB::table('anagrafe.t2_tosc_a')
         ->where('posizione','=',$codlav)
         ->update(['presenza_note' => 1]);
        if ($from=="2") {
          $info = DB::table('anagrafe.t2_tosc_a')
          ->select('denom')
          ->where('posizione','=',$codlav)
          ->first();
          
          if($info) {
             $azienda=$info->denom;
             DB::table('anagrafe.t2_tosc_a')
               ->where('denom','=',$azienda)
               ->update(['presenza_note' => 1]);
          }
        }
        $note=new note;
        $note->id_user=$user;
        
        if ($from=='2') {
          $note->azienda=$azienda;
          $note->codlav="NotaA";
        } else $note->codlav=$codlav;
        $note->note=$testo_nota;
        $note->save();	

         $risp['esito']="OK";
        
        return json_encode($risp);	
   }

   public function ins_frt(Request $request) {
          $id_anagr=$request->input('id_anagr');
          $operatore=$request->input('user_ref');
          $nome_frt=$request->input('nome_frt');
          $natoil_frt=$request->input('natoil_frt');
          $codfisc_frt=$request->input('codfisc_frt');
          $tel_frt=$request->input('tel_frt');
          $sesso_frt=$request->input('sesso_frt');
          $sind_frt=$request->input('sind_frt');
          
          $ente_frt="workfi";




          $today=date("Y-m-d");
          
          $up_green=DB::table('anagrafe.t2_tosc_a')
          ->where('ID_anagr', '=',$id_anagr)
          ->update(['stato_lav' => 3]);

          $info = DB::table('online.db')
          ->select('id')
          ->where('N_TESSERA','=',$operatore)
          ->first();
          
          $id_operatore=0;
          if($info) $id_operatore=$info->id;
          
          
          $frt=new generale;
          $frt->data_update=$today;
          $frt->utente=$operatore;
          $frt->id_oper_user=$id_operatore;
          $frt->id_oper_oper=$id_operatore;
          $frt->nome=$nome_frt;
          $frt->natoil=$natoil_frt;
          $frt->codfisc=$codfisc_frt;
          $frt->telefono=$tel_frt;
          $frt->sesso=$sesso_frt;
          $frt->sindacato=$sind_frt;
          $frt->tb_fo="t2_tosc_a";
          $frt->tb_user="t2_tosc_a";
          $frt->semestre=0;
          $frt->dati_grezzi="Delega FRT da Workfi";
          $frt->ente_origine=$ente_frt;
          $frt->save();

          
          $risp=array();
          $risp['esito']="OK";
          return json_encode($risp);		
     }

   public function save_stato(Request $request) {
      
      $codlav=$request->input('codlav');
      $stato_nota=$request->input('stato_nota');
	
      $risp=array();

      DB::table('anagrafe.t2_tosc_a')
       ->where('posizione','=',$codlav)
       ->update(['stato_lav' => $stato_nota]);
      
       $risp['esito']="OK";
      
      return json_encode($risp);	
      
 }   

}
