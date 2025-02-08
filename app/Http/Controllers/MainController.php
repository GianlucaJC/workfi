<?php
//test
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Route;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;



use DB;


class mainController extends Controller
{
	public function __construct(){
		
		//per login rapido prendere il login_laravel in online.db nella relativa tessera di accesso
		$this->redirect="https://www.filleaoffice.it/homeFO/enter/index.php?workfi=1";
		
		//$this->redirect="https://localhost/homeFO/enter/index.php?workfi=1";
	}	


    public function main($token=""){
		$request=Request();
	  	//$token = bin2hex(random_bytes(16)); 
	  	//echo $token;
		  
		if (strlen($token)==0) {
			if ($request->session()->has('token')) $token=$request->session()->get('token');
		} else {
			//in caso di token inviato e diverso dalla sessione in corso ->redirect!
			if ($request->session()->has('token')) {
				$token_s=$request->session()->get('token');
				if ($token_s!=$token) {
					return $this->redirect_url();
				}
			}
		}

		
		if (strlen($token)==0) return $this->redirect_url();
		

		$info=DB::table('online.db')
		->select("N_TESSERA","ATTIVA","PIN")
		->where('token_laravel','=',$token)
		->first();
		
		//BUG:in caso di token non valido ma con sessione ancora in corso si autentica!!
	  	
		if (!($request->session()->has('id'))) {
			if (isset($info->ATTIVA)) {
					if ($info->ATTIVA=="1") {
						$user=$info->N_TESSERA;
						$request->session()->put('id',$user);
						$request->session()->put('token',$token);
						return $this->elenco($token);
					} else {
						return $this->redirect_url();
					}
			} else {
				return $this->redirect_url();
			}
		} else return $this->elenco($token);
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

	public function elenco($token) {
		$request=Request();
		$count=DB::table('online.db')->select("is_admin_workfi")->where('token_laravel','=',$token)->count();
		if ($count==0) {
			return $this->redirect_url();
		}
		$info=DB::table('online.db')->select("is_admin_workfi")->where('token_laravel','=',$token)->first();
		$isadmin=$info->is_admin_workfi;

		$user=$request->session()->get('id');
	
		$tipo_view=$request->input('tipo_view');
		if (strlen($tipo_view)==0) $tipo_view="0";
		$op_az=$request->input('op_az');
		if (strlen($op_az)==0) $op_az="op";

		$arr_user=array();
		if ($isadmin!=1) {
			$arr_user=$this->elenco_assegnazioni($user);
		}

		if ($op_az=='az')  {
			$elenco=DB::table('anagrafe.t2_tosc_a')
			->select("*")
			->when($isadmin!=1, function ($elenco) use($arr_user){			
				return $elenco->whereIn('denom',$arr_user);
			})
			->whereNotNull('id_import')
			->groupBy('denom')
			->get();			
		}
		else {
			$elenco=DB::table('anagrafe.t2_tosc_a')
			->select("*")
			->when($isadmin!=1, function ($elenco) use($arr_user){			
				return $elenco->whereIn('denom',$arr_user);
			})
			->whereNotNull('id_import')
			->get();
		}
		

		$info_altrove=array();
		/*
		$altrove=DB::table('anagrafe.t2_tosc_a as t')
		->join('anagrafe_regioni.globale as g', function ($join) {
			$join->on('g.nome', '=', 't.nome')
				 ->on('g.datanasc', '=', 't.datanasc');
		})
		->select("g.PROVINCIA as altrove","t.id_anagr")
		->where('g.IDARC','<>','t2.tosc_a')
		->groupBy('g.IDARC')
		->get();
		$ind=0;
		
		
		foreach($altrove as $altro) {
			$id_ref=$altro->id_anagr;
			if (!isset($info_altrove[$id_ref])) $ind=0;
			else $ind++;
			$info_altrove[$id_ref][$ind]=$altro->altrove;
		}
		*/
		

		$note=$this->note();
		$funzionari=$this->funzionari();
		$elenco_assegnazioni=$this->elenco_assegnazioni('all');
		$stat_azi=$this->stat_azi();

		
		$solo_pref=1;
	

		return view('elenco',compact('elenco','isadmin','user','solo_pref','tipo_view','op_az','note','funzionari','elenco_assegnazioni','stat_azi','info_altrove'));

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

   public function elenco_assegnazioni($from) {
		$res=array();$sca=0;$old="?";

		$elenco=DB::table('bsfi.aziende_workfi')
		->select("id","denom as azienda",'id_funzionario','data_assegnazione')
		->when($from!='all', function ($elenco) use($from){			
			return $elenco->where('id_funzionario','=',$from);
		})
		->get();
		$res2=array();
		foreach($elenco as $risposta) {
			$azienda=$risposta->azienda;
			if ($old!=$azienda) {
				$sca=0;$old=$azienda;
			}
			$id_assegnazione=$risposta->id;
			$id_funzionario=$risposta->id_funzionario;
			$data_assegnazione=$risposta->data_assegnazione;
			$res2[]=$azienda;

			$azienda=str_replace("'","",$azienda);
			$azienda=str_replace('"',"",$azienda);
			$res[$azienda][$sca]['id_funzionario']=$id_funzionario;
			$res[$azienda][$sca]['id_assegnazione']=$id_assegnazione;
			$res[$azienda][$sca]['data_assegnazione']=$data_assegnazione;

			
			$sca++;
		}
		if ($from!="all") return $res2;

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
			
			$n_spec=DB::table('anagrafe.t2_tosc_a')
			->where('denom','=',$azienda)
			->where('sindacato','=',' ')
			->where('attivi','=',"S")
			->count();		
			
			if ($liberi>0) $res[$azienda_clean]['liberi']=$liberi;
			if ($filca>0) $res[$azienda_clean]['filca']=$filca;
			if ($feneal>0) $res[$azienda_clean]['feneal']=$feneal;
			if ($fillea>0) $res[$azienda_clean]['fillea']=$fillea;
			if ($n_spec>0) $res[$azienda_clean]['n_spec']=$n_spec;
			
		}
		return $res;

	}

	public function lav_from_azienda(Request $request){		
		$azienda=$request->input('azienda');
		$azienda=str_replace("[","'",$azienda);
		$azienda=str_replace("^","&",$azienda);
		$elenco=DB::table('anagrafe.t2_tosc_a')
		->select("*")
		->where("denom",'=',$azienda)
		->get();
		return json_encode($elenco);		

	}

	public function redirect_url() {
		$request=Request();
		$request->session()->forget('id');
		$request->session()->forget('token');

		return redirect()->away($this->redirect);		
	}

}
