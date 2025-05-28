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
		
		$this->redirect="https://www.filleaoffice.it/homeFO/enter/index.php";
		$this->user=null;
		$this->isadmin=0;
	}	


	
    public function main($token="",$dataass=""){	
		
		$request=Request();

		//vedi token_worfi.php (desktop) oppure genera un url valido da filleaoffice online
		if (strlen($token)==0) 
			return $this->redirect_url();
		else {
			$decode=base64_decode($token);
			$info_token=explode("|",$decode);
			if (!isset($info_token[1])) return $this->redirect_url();
			else {
				if (time()>$info_token[1])  return $this->redirect_url(); //token expired!
				else {
					$user=$info_token[0];
					$this->log_w($user,$token);
					$isadmin=$info_token[2];
					$this->user=$user;
					$this->isadmin=$isadmin;
					return $this->elenco($token,$dataass);
				}
			}

		}
		
		 
    }	

	public function log_w($user,$token) {
		$check=DB::table('bsfi.accessi_workfi')
		->select("id")
		->where('token','=',$token)
		->count();
		if ($check==0) {
			DB::insert('insert into bsfi.accessi_workfi (id_funzionario, token) values (?, ?)', [$user, $token]);
		}
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

	public function elenco($token,$dataass) {

		$request=Request();
		$d=date("Y-m-d");
		$isadmin=$this->isadmin;
		$user=$this->user;
		$dele_green=$request->input('dele_green');
		if (strlen($dele_green)>0) {
			$elenco=DB::table('anagrafe.t2_tosc_a')
			->where('stato_lav', '=',3)
			->update(['dele_workfi' => 1,'data_elimina' => $d]);
		}
		$anomali=$request->input('anomali');
		if (strlen($anomali)==0) $anomali=0;
		$filtro_note=$request->input('filtro_note');
		
		$tipo_view=$request->input('tipo_view');
		if (strlen($tipo_view)==0) $tipo_view="0";
		$op_az=$request->input('op_az');
		if (strlen($op_az)==0) $op_az="op";

		$arr_user=array();
		if ($isadmin!=1) {
			$arr_user=$this->elenco_assegnazioni($user);
		}
		$data_sca="";
		if (strlen($dataass)==8) 
			$data_sca=substr($dataass,0,4)."-".substr($dataass,4,2)."-".substr($dataass,6,2);

		
		$filtro_colore=$request->input('filtro_colore');

		if ($op_az=='az')  {
			$elenco=DB::table('anagrafe.t2_tosc_a')
			->select("*")
			->when($isadmin!=1, function ($elenco) use($arr_user){			
				return $elenco->whereIn('denom',$arr_user);
			})
			->when(strlen($data_sca)==10, function ($elenco) use($data_sca){			
				return $elenco->where('data_scarico',$data_sca);
			})	
			//->whereNotNull('id_import')
			->groupBy('denom')
			->get();			
		}
		else {
			$elenco=DB::table('anagrafe.t2_tosc_a as t')
			->select("*")
			->where('t.dele_workfi','=',0)
			->when($anomali==1, function ($elenco) {			
				return $elenco->where('t.is_anomal','=',1);
			})
			->when(strlen($filtro_colore)!=0, function($elenco) use ($filtro_colore) {
				return $elenco->where('t.stato_lav',"=",$filtro_colore);
			})			
			->when($isadmin!=1, function ($elenco) use($arr_user){			
				return $elenco->whereIn('t.denom',$arr_user);
			})
			->when(strlen($data_sca)==10, function ($elenco) use($data_sca){			
				return $elenco->where('t.data_scarico',$data_sca);
			})
			->when(strlen($filtro_note)!=0, function ($elenco) use($filtro_note){			
				return $elenco->where('t.presenza_note',"=",$filtro_note);
			})
			->whereNotNull('t.id_import')
			->groupBy('t.posizione')
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
		$cantieri=$this->cantieri();
		$elenco_assegnazioni=$this->elenco_assegnazioni('all');
		$elenco_frt=$this->elenco_frt();
		
		$stat_azi=$this->stat_azi();
	

		return view('elenco',compact('token','dataass','elenco','isadmin','user','tipo_view','op_az','note','funzionari','elenco_assegnazioni','elenco_frt','stat_azi','info_altrove','filtro_note','filtro_colore','anomali','cantieri'));

   }	

   public function cantieri() {
		$info = DB::table('bsfi.cantieri')
		->select("id","p_iva","denom","indirizzo","civico","comune","inizio_lav","fine_lav","id_import","data_forzata")
		->get();
		$cant=array();

		foreach($info as $cantiere) {
			$cant[$cantiere->p_iva]=$cantiere;
		}
		return $cant;	
   }

   public function funzionari() {
		$info = DB::table('online.db')
			->select('db.n_tessera','db.id_prov_associate','db.utentefillea','p.provincia','p.sigla_pr')
			->join('bdf.province as p','db.id_prov_associate','p.id')
			->get();
		$user=array();

		foreach($info as $utente) {
			$tess=strtoupper($utente->n_tessera);
			$user[$tess]['utentefillea']=$utente->utentefillea;
			$user[$tess]['id_prov_associate']=$utente->id_prov_associate;
			$user[$tess]['provincia']=$utente->provincia;
			$user[$tess]['sigla_pr']=$utente->sigla_pr;
		}
		return $user;
	}   



   public function elenco_assegnazioni($from) {
		$res=array();$sca=0;$old="?";

		$elenco=DB::table('bsfi.aziende_workfi')
		->select("id","denom as azienda",'id_funzionario','stat_azi_before','data_assegnazione')
		->when($from!='all', function ($elenco) use($from){			
			return $elenco->where('id_funzionario','=',$from);
		})
		->get();
		$res2=array();
		foreach($elenco as $risposta) {
			$azienda=$risposta->azienda;
			if ($old!=$azienda) {
				$sca=0;
			}
			$old=$azienda;
			$id_assegnazione=$risposta->id;
			$id_funzionario=strtoupper($risposta->id_funzionario);
			$data_assegnazione=$risposta->data_assegnazione;
			$stat_azi_before=$risposta->stat_azi_before;
			$res2[]=$azienda;

			$azienda=str_replace("'","",$azienda);
			$azienda=str_replace('"',"",$azienda);
			$res[$azienda][$sca]['id_funzionario']=$id_funzionario;
			$res[$azienda][$sca]['id_assegnazione']=$id_assegnazione;
			$res[$azienda][$sca]['stat_azi_before']=$stat_azi_before;
			$res[$azienda][$sca]['data_assegnazione']=$data_assegnazione;

			
			$sca++;
		}
		if ($from!="all") return $res2;

		return $res;	
	
	}

	function elenco_frt() {
		$elenco=DB::table('anagrafe.t2_tosc_a as t')
		->join('frt.generale as frt', function($join) {
			$join->on('frt.nome','=','t.nome');
			$join->on('frt.natoil','=','t.datanasc');
		})		
		->select("t.posizione","frt.utente","data_update")
		->orderby('data_update','desc')
		->get();
		$resp=array();
		$indice=0;
		foreach($elenco as $frt) {
			$posizione=$frt->posizione;
			$utente=strtoupper($frt->utente);
			$data_update=$frt->data_update;
			if (array_key_exists($posizione,$resp)) $indice++;
			else $indice=0;
			$resp[$posizione][$indice]['utente']=$utente;
			$resp[$posizione][$indice]['data_update']=$data_update;
		}
		return $resp;
	}

	function stat_azi() {
		$elenco=DB::table('anagrafe.t2_tosc_a')
		->select("denom")
		->whereRaw('LENGTH(denom) > ?', [0])
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
			->whereNull("id_import")
			->count();

			$filca=DB::table('anagrafe.t2_tosc_a')
			->where('denom','=',$azienda)
			->where('sindacato','=',2)
			->where('attivi','=',"S")
			->whereNull("id_import")
			->count();			

			$feneal=DB::table('anagrafe.t2_tosc_a')
			->where('denom','=',$azienda)
			->where('sindacato','=',3)
			->where('attivi','=',"S")
			->whereNull("id_import")
			->count();			

			$fillea=DB::table('anagrafe.t2_tosc_a')
			->where('denom','=',$azienda)
			->where('sindacato','=',1)
			->where('attivi','=',"S")
			->whereNull("id_import")
			->count();		
			
			$n_spec=DB::table('anagrafe.t2_tosc_a')
			->where('denom','=',$azienda)
			->where('sindacato','=',' ')
			->where('attivi','=',"S")
			->whereNull("id_import")
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
		return redirect()->away($this->redirect);		
	}

}
