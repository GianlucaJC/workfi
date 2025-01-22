<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use DB;

class ApiController extends Controller
{
	/*
	public function __construct() {
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
	}
	*/

	public function register_push(Request $request) {
		$id_user=$request->input("id_user");
		$pushid=$request->input("pushid");
		if (strlen($pushid)>0) {
			user::where('id', $id_user)->update(['push_id' => $pushid]);
		}
		$resp=array();
		$resp['esito']="OK";
		return $resp;		
	}

	
	public function send_push_creator($id_appalto,$id_lav_ref,$sn) {
		$creator=appalti::select('appalti.id_creator')->where('appalti.id', "=",$id_appalto)->first();

		if (!$creator->id_creator) return false;
		$push=user::select('push_id')->where('id','=',$creator->id_creator)->get()->first();
		$push_id=null;
		if (isset($push->push_id)) $push_id=$push->push_id;
		if ($push_id==null || strlen($push_id)==0) return false;


		//$push_id="3863803b-eb7e-4ad4-aafd-958b85dff83f";
		$nome_lav= DB::table('candidatis as c')
		->where('c.id', "=",$id_lav_ref)
		->first()->nominativo;
		try {

		$params = []; 
		$url="ingfun/public/listapp";
		$params['url'] = $url;			
		$params['include_player_ids'] = [$push_id]; 
		$headings = array(
			"it" => 'MisAPP News',
			"en" => 'MisAPP News'
			);

		$yn="";
		if ($sn=="N") $yn="non";	

		$contents = [ 
			"it" => "Servizio $id_appalto $yn accettato da operatore $nome_lav", 
			"en" => "Servizio $id_appalto $yn accettato da operatore $nome_lav"
		]; 

		$params['priority'] = 10; 
		$params['contents'] = $contents; 
		$params['headings'] = $headings; 
		//$params['delayed_option'] = "timezone"; // Will deliver on user's timezone 
		//$params['delivery_time_of_day'] = "2:30PM"; // Delivery time

		$resp=OneSignal::sendNotificationCustom($params);		
						
		} catch (Throwable $e) {
			$status['status']="KO";
			$status['message']="Errore occorso durante l'invio! $e";
		}		
	}	
  

}
