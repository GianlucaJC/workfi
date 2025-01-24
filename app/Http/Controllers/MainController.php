<?php
//test
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Route;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Session;


use DB;


class mainController extends Controller
{
	public function __construct(){
		/*
		$this->middleware(function ($request, $next){
			$current_route=Route::current()->getName();
			return $next($request);
			if ($current_route!="main") {
				$user = session('id');
				if (!$user || strlen($user)==0) $this->login=false;
				return $next($request);
			}
		});
		*/
	}	


    public function main($token=""){
	  //$token = bin2hex(random_bytes(16)); 
	  //echo $token;
	    $user=Session::get( 'id');
	  	if (!isset($user) || strlen($user)==0) {
			$info=DB::table('online.db')
			->select("N_TESSERA","ATTIVA","PIN",)
			->where('token_laravel','=',$token)
			->first();

			if (isset($info->ATTIVA)) {
					if ($info->ATTIVA=="1") {
						$user=$info->N_TESSERA;
						Session::put( 'id', $user);
						return $this->elenco();
					} else {return redirect()->away('https://www.filleaoffice.it');}
			} else {return redirect()->away('https://www.filleaoffice.it');}
		} else return $this->elenco();
    }	

	public function note() {
		$elenco=DB::table('bsfi.note')
		->select("*")->orderBy('created_at','desc')
		->get();
		$info=array();
		foreach ($elenco as $nota) {
			$codlav=$nota->codlav;
			$info[$codlav][]=$nota;
		}
		return $info;

	}

	public function elenco() {
		$request=Request();
		$tipo_view=$request->input('tipo_view');
		if (strlen($tipo_view)==0) $tipo_view="0";
		$elenco=DB::table('anagrafe.t2_tosc_a')
		->select("*")
		->whereNotNull('id_import')
		->get();
		
		$note=$this->note();
		$funzionari=$this->funzionari();
		$elenco_assegnazioni=$this->elenco_assegnazioni();
		$stat_azi=$this->stat_azi();
		$isadmin=1;
		$user = session('id');
		
		$solo_pref=1;
	

		return view('elenco',compact('elenco','isadmin','user','solo_pref','tipo_view','note','funzionari','elenco_assegnazioni','stat_azi'));

   }	

   public function funzionari() {
		$elenco=DB::table('online.db')
		->select("N_TESSERA","UTENTEFILLEA")
		->get();
		$res=array();
		foreach($elenco as $row) {
			$res[$row->N_TESSERA]=$row->UTENTEFILLEA;
		}
		return $res;	
   }

   public function elenco_assegnazioni() {
		$res=array();$sca=0;$old="?";

		$elenco=DB::table('bsfi.aziende_workfi')
		->select("id","denom as azienda",'id_funzionario')
		->get();

		foreach($elenco as $risposta) {
			$azienda=$risposta->azienda;
			if ($old!=$azienda) {
				$sca=0;$old=$azienda;
			}
			$id_assegnazione=$risposta->id;
			$id_funzionario=$risposta->id_funzionario;
			$azienda=str_replace("'","",$azienda);
			$azienda=str_replace('"',"",$azienda);
			$res[$azienda][$sca]['id_funzionario']=$id_funzionario;
			$res[$azienda][$sca]['id_assegnazione']=$id_assegnazione;
			$sca++;
		}

		return $res;	
	
	}

	function stat_azi() {
		$elenco=DB::table('anagrafe.t2_tosc_a')
		->select("denom")
		->whereNotNull('id_import')
		->groupby('denom')
		->get();
		$res=array();
		
		foreach($elenco as $info) {
			$azienda=$info->denom;
			$azienda_clean=str_replace("'","",$azienda);
			$azienda_clean=str_replace('"',"",$azienda_clean);

			$liberi=DB::table('anagrafe.t2_tosc_a')
			->where('denom','=',$azienda)
			->where('sindacato','=',0)
			->where('attivi','=',"S")
			->count();

			$filca=DB::table('anagrafe.t2_tosc_a')
			->where('denom','=',$azienda)
			->where('sindacato','=',2)
			->where('attivi','=',"S")
			->count();			

			$feneal=DB::table('anagrafe.t2_tosc_a')
			->where('denom','=',$azienda)
			->where('sindacato','=',3)
			->where('attivi','=',"S")
			->count();			

			$fillea=DB::table('anagrafe.t2_tosc_a')
			->where('denom','=',$azienda)
			->where('sindacato','=',1)
			->where('attivi','=',"S")
			->count();			
			if ($liberi>0) $res[$azienda_clean]['liberi']=$liberi;
			if ($filca>0) $res[$azienda_clean]['filca']=$filca;
			if ($feneal>0) $res[$azienda_clean]['feneal']=$feneal;
			if ($fillea>0) $res[$azienda_clean]['fillea']=$fillea;
			
		}
		return $res;

	}

}
