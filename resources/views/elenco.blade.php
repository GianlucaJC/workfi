<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>


        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Google Font: Source Sans Pro -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        

        <link href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css" rel="stylesheet">


        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />


        <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>

        <style>
          table{
            margin: 0 auto;
            width: 100%;
            clear: both;
            border-collapse: collapse;
            table-layout: fixed; // ***********add this
            word-wrap:break-word; // ***********and this
          }



        </style>

        <script>

          window.OneSignalDeferred = window.OneSignalDeferred || [];
          OneSignalDeferred.push(function (OneSignal) {
            OneSignal.init({
              appId: "1ecaf775-b78f-45cd-95d4-acb2f04e8047",
            });
            OneSignal.User.PushSubscription.addEventListener("change", function (event) {
              console.log("event");
              console.log(event);
              if (event.current.id) {
                  register_push(event.current.id)
              }


            });
          });		
          
          //register_push("test") //per test in locale
          function register_push(pushid) {
            id_user="<?php echo $user;?>"
            base_path = $("#url").val();
            let CSRF_TOKEN = $("#token_csrf").val();

            const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
            const csrf = metaElements.length > 0 ? metaElements[0].content : "";			
            fetch(base_path+"/register_push", {
              method: 'post',
              headers: {
              "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
              "X-CSRF-Token": csrf
              },
              body: "pushid="+pushid+"&id_user="+id_user
            })
            .then(response => {
              if (response.ok) {
                return response.json();
              }
            })
            .then(resp=>{

            })
            .catch(status, err => {
              return console.log(status, err);
            })		
          }			
          
        </script>	


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <style>
      .filtraggio{
        display: flex;
        justify-content: start;
        flex-direction: row;
        margin-bottom: 10px;
      }

      .filtri{
        display: flex;
        justify-content: end;
        align-items: center;
        flex-direction: column;
        margin-right: 15px;

        div{
          display: flex;
          flex-direction: row;
          gap: 5px;
        }
      }
      .dt-search {
        margin-top:7px;
      }

      @media all and (max-width:768px){
        body {
          font-size:0.64rem;
        }
      }

    </style>

    <body class="font-sans antialiased">

        <div class="min-h-screen bg-gray-100">
           
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 p-2">
                      <center><b>Gestione Organizzativa</b></center>
                    </div>
                </header>    

         <!-- Page Content -->
        <main>

              @if ($isadmin==1 || 1==1)     
                <div class="container-fluid mt-3">
                <?php
                  $txt="Operai";
                  if ($op_az=="az") $txt="Aziende";
                ?>
                <button type="button" class="btn btn-primary btn-sm" onclick="set_opaz()">{{$txt}}</button>
                <span class='ml-2'>Notifiche</span>
                <a href="https://wa.me/+14155238886?text=join stone-month">
                  <button type="button" class="btn btn-success btn-sm ml-2"><i class="fab fa-whatsapp"></i> Attiva</button>
                </a>
                <a href="https://wa.me/+14155238886?text=stop">
                  <button type="button" class="btn btn-warning btn-sm ml-2"><i class="fab fa-whatsapp"></i> Disabilita</button>
                </a>
             

                <hr>
                <!-- handle from Vue !-->
                <div id="app">
                  <App></App>
                </div>    

                <form method='POST' action="{{ route('main', [$token,$dataass]) }}" id='frm_main' name='frm_main' autocomplete="off">
               


                    <input type="hidden" value="{{url('/')}}" id="url" name="url">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}" id='token_csrf'>	  
                    <input type='hidden' name='tipo_view' id='tipo_view' value='{{$tipo_view}}'>
                    <input type='hidden' name='op_az' id='op_az' value='{{$op_az}}'>
                    <input type='hidden' name='filtro_colore' id='filtro_colore' value='{{$filtro_colore}}'>
                    <?php
                      $vis="";
                      if ($op_az!='op') $vis="display:none";
                    ?>
                    <div id="div_table">
                      <div class="filtraggio" id='div_filtri' style="{{$vis}}">
                        <div class="filtri" class='mb-3'>
                          <label for="filtro_note">Filtro note</label> 
                          <select class="form-select" style='background-color:white;border:1px solid;padding:4px;border-radius:5px;border-color:gray' aria-label="Default select example" name="filtro_note" id="filtro_note" onchange="$('#frm_main').submit()">
                            <option value=""
                            @if ($filtro_note=='') selected @endif
                            >Tutti</option>
                            <option value="1"
                            @if ($filtro_note=='1') selected @endif
                            >Solo con note</option>
                            <option value="0"
                            @if ($filtro_note=='0') selected @endif
                            >Senza note</option>
                          </select>
                        </div>
                          <div class='filtri'>
                          <?php
                            $cl1="far";$cl2="far";$cl3="far";
                            if ($filtro_colore=="1") $cl1="fas";
                            if ($filtro_colore=="2") $cl2="fas";
                            if ($filtro_colore=="3") $cl3="fas";
                          ?>  

                            <div>
                            <a href='javascript:void(0)' onclick='set_filtro_stato(1)'>
                            <i class="{{$cl1}} fa-circle fa-lg mt-3 semaforo" style="color: #ff0000;" id="sem1"></i>
                            </a>
                            <a href='javascript:void(0)' onclick='set_filtro_stato(2)'>
                            <i class="{{$cl2}} fa-circle fa-lg mt-3 semaforo" style="color: #FFD43B;" id="sem2"></i>
                            </a>
                            <a href='javascript:void(0)' onclick='set_filtro_stato(3)'>
                            <i class="{{$cl3}} fa-circle fa-lg mt-3 semaforo" style="color: #00ca00;" id="sem3"></i>
                            </a>
                            @if (strlen($filtro_colore)!=0) 
                            <a href='javascript:void(0)' onclick='set_filtro_stato()'>
                            <i class="ml-2 fa-solid fa-xmark fa-lg mt-3" style="color: #ff0000;"></i>
                            </a>
                            @endif

                          </div>
                          </div>                          
                        </div>
                      
                      <div style="text-align: right;display:none" id='btn_espandi'> 
                        <?php 
                          $out="";$txt="Espandi tutto";$value_e=1;
                          
                          if ($tipo_view=="1") {$out="outline-";$txt="Riduci";$value_e=0;}
                          
                        ?>
                        <button type="button" class="btn btn-{{$out}}primary btn-sm" onclick="$('#tipo_view').val({{$value_e}});$('#frm_main').submit();">{{$txt}}</button>
                      </div>

                      @if ($op_az=='op')
                      <table id='tbl_articoli' class="display nowrap">
                        <thead>
                          <tr>
                            <th>Nominativo</th>
                            <th>Data Ass</th>
                            <th>Stat</th>
                            <th>Azioni</th>
                            <th>Posizione</th>
                            <th>Funzionari assegnati</th>
                            <th>Nato a</th>
                            <th>Nato il</th>
                            <th>Azienda</th>
                            <th>Altrove</th>
                            <th>Tel FO</th>
                            <th>Tel CE</th>
                            <th>Tel GPS</th>
                            <th>Tel SIN</th>
                            <th>Tel altro</th>
                            <th>FRT</th>
                            <th>Note</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($elenco as $info)
                            <?php
                              $p_iva=$info->C2;
                              $azienda=$info->DENOM;
                              $azienda_clean=str_replace("'","",$azienda);
                              $azienda_clean=str_replace('"',"",$azienda_clean);                          
                            ?>

                              <tr id='tr{{$info->ID_anagr}}'>
                                  <td>
                                    <?php
                                      $nome_orig=$info->NOME;
                                      $nominativo=$nome_orig;
                                      if (strlen($nome_orig)>18) $nominativo=substr($nome_orig,0,18)."<br>".substr($nome_orig,19);
                                      if (isset($note[$info->posizione])) echo "<b>".$nominativo."</b>";
                                      else  {
                                        echo "<span title='$nome_orig'>";
                                        
                                        echo "$nominativo</span>";
                                      }
                                      
                                    ?>

                                    <span class='ml-2' id='status_lav{{$info->posizione}}' style="float: right">
                                        @if ($info->stato_lav=="1")
                                          <i class="fas fa-circle fa-lg mt-3" style="color: #ff0000;" id="sem1" @click='set_stato(1)'></i>
                                        @endif
                                        @if ($info->stato_lav=="2")
                                        <i class="fas fa-circle fa-lg mt-3" style="color: #FFD43B;" id="sem2" @click='set_stato(2)'></i>
                                        @endif
                                        @if ($info->stato_lav=="3")
                                        <i class="fas fa-circle fa-lg mt-3" style="color: #00ca00;" id="sem3" @click='set_stato(3)'></i>
                                        @endif
                                    </span>    

                                  </td>

                                  <td>
                                    {{$info->data_scarico}}
                                  </td>                                  

                                  <td>
                                    <?php

                                    
                                      if (array_key_exists($azienda_clean,$stat_azi)){
                                        if (isset($stat_azi[$azienda_clean]['liberi']))
                                          echo "<i class='fas fa-square fa-sm' style='color: #FFD43B;'> <small>".$stat_azi[$azienda_clean]['liberi']."</small></i> ";
                                        if (isset($stat_azi[$azienda_clean]['fillea']))
                                          echo "<i class='fas fa-square fa-sm' style='color: #FF0000;'> <small>".$stat_azi[$azienda_clean]['fillea']."</small></i> ";                                        
                                        if (isset($stat_azi[$azienda_clean]['filca']))
                                          echo "<i class='fas fa-square fa-sm' style='color: #63E6BE;'> <small>".$stat_azi[$azienda_clean]['filca']."</small></i> ";
                                        if (isset($stat_azi[$azienda_clean]['feneal']))
                                          echo "<i class='fas fa-square fa-sm' style='color: #74C0FC;'> <small>".$stat_azi[$azienda_clean]['feneal']."</small></i> ";
                                        if (isset($stat_azi[$azienda_clean]['n_spec']))
                                          echo "<i class='fas fa-square fa-sm' style='color: #ccccd1;'> <small>".$stat_azi[$azienda_clean]['n_spec']."</small></i> ";
                                      
                                        if (array_key_exists($azienda_clean,$elenco_assegnazioni)) {
                                          $stat_azi_before=$elenco_assegnazioni[$azienda_clean][0]['stat_azi_before'];
                                          
                                          $info_before=explode(";",$stat_azi_before);
                                          if (strlen($stat_azi_before)!=0) {
                                            echo "<hr>";
                                            if ($info_before[0]!="0")
                                                echo "<i class='fas fa-square fa-sm' style='color: #FFD43B;'> <small>".$info_before[0]."</small></i> ";
                                            
                                            if ($info_before[1]!="0")
                                                echo "<i class='fas fa-square fa-sm' style='color: #FF0000;'> <small>".$info_before[1]."</small></i> ";

                                            if ($info_before[2]!="0")
                                                echo "<i class='fas fa-square fa-sm' style='color: #63E6BE;'> <small>".$info_before[2]."</small></i> ";

                                            if ($info_before[3]!="0")
                                                echo "<i class='fas fa-square fa-sm' style='color: #74C0FC;'> <small>".$info_before[3]."</small></i> ";

                                            if ($info_before[4]!="0")
                                                echo "<i class='fas fa-square fa-sm' style='color: #ccccd1;'> <small>".$info_before[4]."</small></i> ";                                        

                                          }   
                                        }
                                     }                                   
                                    ?> 
                                  </td>

                                  <td id='frt_{{$info->ID_anagr}}'>
                                    <?php
                                      $btn_col="secondary";
                                      if (isset($note[$info->posizione])) $btn_col="danger";
                                      if (isset($info->posizione) && strlen($info->posizione)>0) {?>
                                        <button type="button" onclick="add_nota('{{$info->posizione}}','{{$user}}')" class="btn btn-{{$btn_col}} btn-sm">Note</button>
                                    <?php } 
                                    
                                    $btn_col="secondary";
                                    if (isset($elenco_frt[$info->posizione])) $btn_col="danger";
                                    ?>    


                                      <button type="button" class="btn btn-{{$btn_col}} btn-sm"  onclick="insert_frt({{$info->ID_anagr}})">
                                        
                                        FRT
                                      </button>

                                  </td> 


                                  <td>
                                     {{$info->posizione}}
                                  </td>
                                  <td>
                                    <?php
                                      $entr=false;
                                      if (array_key_exists($azienda_clean,$elenco_assegnazioni)){	
                                        $entr=true;
                                        for ($i=0;$i<count($elenco_assegnazioni[$azienda_clean]);$i++) {
                                          if ($i>0) echo "<hr>";
                                          $id_assegnazione=$elenco_assegnazioni[$azienda_clean][$i]['id_assegnazione'];
                                          $id_funz=strtoupper($elenco_assegnazioni[$azienda_clean][$i]['id_funzionario']);
                                          $data_assegnazione=$elenco_assegnazioni[$azienda_clean][$i]['data_assegnazione'];
                                          if (array_key_exists($id_funz,$funzionari)) {
                                            echo "<b>$id_funz</b>: ";
                                            echo $funzionari[$id_funz]['utentefillea'];
                                          }
                                        }
                                      }                                     
                                    ?>
                                  </td>
                                  <td>
                                     {{$info->COMUNENASC}}
                                  </td>
                                  <td>
                                     {{date('d-m-Y', strtotime($info->DATANASC));}}
                                  </td>                                  
                                  <td>
                                      
                                      <?php
                                      $pref="https://www.filleaoffice.it";
                                      if ($_SERVER["HTTP_HOST"]=="localhost:8012")
                                          $pref="http://localhost/";

                                      if (strlen($p_iva)!=0)
                                        echo "<a href='".$pref."/anagrafe/pages/consultazioni/consultazioni.php?token=".$token."&tb_fo=t2_tosc_a&p_iva=".$p_iva."' target='_blank'>$azienda</a>";      
                                      else
                                        echo "<a href='".$pref."/anagrafe/pages/consultazioni/consultazioni.php?token=".$token."&tb_fo=t2_tosc_a&azienda=".$azienda."' target='_blank'>$azienda</a>";   
                                      ?>                                         
                                  </td>
                                  <td>
                                    <?php
                                      if (isset($info_altrove[$info->ID_anagr])) {
                                          $refarr=$info_altrove[$info->ID_anagr];
                                          for ($xx=0;$xx<count($refarr);$xx++) {
                                              if ($xx>0) echo "<hr>";
                                              echo $refarr[$xx];
                                          }
                                      }
                                    ?>
                                  </td>

                                  <?php
                                    $t1=$info->C1;
                                    $t1=adegua_tel($t1);
                                    $t2=$info->tel_ce;
                                    $t2=adegua_tel($t2);
                                    $t3=$info->tel_gps;
                                    $t3=adegua_tel($t3);
                                    $t4=$info->tel_sin;
                                    $t4=adegua_tel($t4);
                                    $t5=$info->tel_altro;
                                    $t5=adegua_tel($t5);
                                    $tel_all="$t1 $t2 $t3 $t4 $t5";
                                  ?>
                                  <td>
                                    <a id="phone1" href="tel:{{$t1}}">{{$t1}}</a>
                                  </td>
                                  <td>
                                    <a id="phone2" href="tel:{{$t2}}">{{$t2}}</a>
                                  </td>
                                  <td>
                                    <a id="phone3" href="tel:{{$t3}}">{{$t3}}</a>
                                  </td>
                                  <td>
                                    <a id="phone4" href="tel:{{$t4}}">{{$t4}}</a>
                                  </td>
                                  <td>
                                    <a id="phone5" href="tel:{{$t5}}">{{$t5}}</a>
                                  </td>
                                  
                                  <td>
                                    <?php
                                        $render_frt=render_frt($elenco_frt,$info->posizione,$funzionari);
                                        echo $render_frt;
                                        $view="";
                                        $view.="
                                         <span id='id_ref".$info->ID_anagr."'
                                            data-nominativo='".$info->NOME."'
                                            data-nome='".$info->NOME."'
                                            data-datanasc='".$info->DATANASC."'
                                            data-telefoni='".$tel_all."'>
                                        </span>";                                       
                                        echo $view;                                          
                                      ?>  

                                    
                                  </td>
                                  <td>
                                    <?php
                                      if (isset($note[$info->posizione])) {
                                        $view='<table class="table">
                                          <thead>
                                            <tr>
                                              <th scope="col" style="width:30px"></th>
                                              <th scope="col">Utente</th>
                                              <th scope="col">Nota</th>
                                              <th scope="col">Data</th>
                                            </tr>
                                          </thead>
                                          <tbody>';
                                            for ($sca=0;$sca<count($note[$info->posizione]);$sca++) {
                                              $view.="<tr>";
                                                  $view.="<td style='width:30px'>";
                                                  if  ($note[$info->posizione][$sca]->stato_nota=="1")
                                                  $view.="<i class='fas fa-circle fa-lg mt-3' style='color: #ff0000;'></i>";
                                                if  ($note[$info->posizione][$sca]->stato_nota=="2")
                                                  $view.="<i class='fas fa-circle fa-lg mt-3' style='color: #FFD43B;'></i>";
                                                if  ($note[$info->posizione][$sca]->stato_nota=="3")
                                                  $view.="<i class='fas fa-circle fa-lg mt-3' style='color: #00ca00;'></i>";
                                                $view.="</td>";                                              
                                                $view.="<td>";
                                                  $id_funz=strtoupper($note[$info->posizione][$sca]->id_user);
                                                  //$view.=$id_funz;

                                                    if (array_key_exists($id_funz,$funzionari)) {
                                                      $view.="<b>$id_funz</b>: ";
                                                      $view.=$funzionari[$id_funz]['utentefillea'];
                                                    }                                                     
                                                                   
                                                $view.="</td>";
                                                $view.="<td style='white-space:wrap'>";
                                                  $view.="<i>".$note[$info->posizione][$sca]->note."</i>";
                                                $view.="</td>";
                                                $view.="<td>";
                                                  $view.=$note[$info->posizione][$sca]->created_at;
                                                $view.="</td>";                                                

                                              $view.="</tr>";  
                                            }
                                          $view.='
                                          </tbody>  
                                        </table>';
                                        echo $view;
                                      }
                                    ?>

                                  </td>                                  

                                </tr>  
                          @endforeach
                        </tbody>
                        <tfoot>
                      
                          <tr>
                            <th>Nominativo</th>
                            <th>Stat</th>
                            <th></th>
                            <th>Scarico del</th>
                            <th>Posizione</th>
                            <th>Funzionari assegnati</th>
                            <th>Nato a</th>
                            <th>Nato il</th>
                            <th>Azienda</th>
                            <th>Altrove</th>
                            <th>Tel FO</th>
                            <th>Tel CE</th>
                            <th>Tel GPS</th>
                            <th>Tel SIN</th>
                            <th>Tel altro</th>
                            <th>FRT</th>
                            <th>Note</th>
                          </tr>
                      
                        </tfoot>					
                      </table>
                      @endif

                      @if ($op_az=='az')

                      <div id='div_aziende'>
                      <table id='tbl_articoli' class="display nowrap">
                        <thead>
                          <tr>
                            <th>Azienda</th>
                            <th>Data Ass</th>
                            <th>Funzionari assegnati</th>
                            <th>Stat</th>
                          </tr>
                        </thead>  
                        <tbody>
                          @foreach($elenco as $info)                          
                          <?php
                              $p_iva=$info->C2;
                              $azienda=$info->DENOM;
                              $azienda_clean=str_replace("'","",$azienda);
                              $azienda_clean=str_replace('"',"",$azienda_clean);                          
                          ?>    
                                                        
                          <tr>
                              <td>
                                <?php
                                    $azienda_view=$azienda;
                                    $azienda_js=str_replace("'","[",$azienda);
                                    $azienda_js=str_replace("&","^",$azienda_js);
                                    if (strlen($azienda)>20) $azienda_view=substr($azienda,0,18)."<br>".substr($azienda,19);
                                    $timestamp = time() + 60*60; // now + 1 hour
                                    $str=strtoupper($user)."|$timestamp";
                                    $token=base64_encode($str);
                                    


                                    $pref="https://www.filleaoffice.it";
                                    if ($_SERVER["HTTP_HOST"]=="localhost:8012")
                                        $pref="http://localhost/";

                                    if (strlen($p_iva)!=0)
                                      echo "<a href='$pref/anagrafe/pages/consultazioni/consultazioni.php?token=".$token."&tb_fo=t2_tosc_a&p_iva=".$p_iva."' target='_blank'>$azienda</a>";      
                                    else
                                       echo "<a href='$pref/anagrafe/pages/consultazioni/consultazioni.php?token=".$token."&tb_fo=t2_tosc_a&azienda=".$azienda."' target='_blank'>$azienda</a>";      
                                    /*
                                    echo "<a href='javascript:void(0)' onclick=\"view_lav('$azienda_js')\">
                                           <span title='$azienda_view'>$azienda_view</span>
                                          </a>";
                                    */      
                                   
                                ?>
                                

                              </td>
                              <td>
                                  {{$info->data_scarico}}
                                  <span id='id_ref{{$info->ID_anagr}}' 
                                      data-nominativo='{{$info->NOME}}'
                                  >
                              </td>
                              <td>
                                <?php
                                  $entr=false;
                                  if (array_key_exists($azienda_clean,$elenco_assegnazioni)){	
                                    $entr=true;
                                    for ($i=0;$i<count($elenco_assegnazioni[$azienda_clean]);$i++) {
                                      if ($i>0) echo "<hr>";
                                      $id_assegnazione=$elenco_assegnazioni[$azienda_clean][$i]['id_assegnazione'];
                                      $id_funz=strtoupper($elenco_assegnazioni[$azienda_clean][$i]['id_funzionario']);
                                      $data_assegnazione=$elenco_assegnazioni[$azienda_clean][$i]['data_assegnazione'];
                                      if (array_key_exists($id_funz,$funzionari)) {
                                        echo "<b>$id_funz</b>: ";
                                        echo $funzionari[$id_funz]['utentefillea'];
                                      }
                                    }
                                  }                                     
                                ?>
                              </td>                                                            

                                  <td style='text-align:left'>
                                    <?php
                                    
                                      if (array_key_exists($azienda_clean,$stat_azi)){
                                        if (isset($stat_azi[$azienda_clean]['liberi']))
                                          echo "<i class='fas fa-square fa-sm' style='color: #FFD43B;'> <small>".$stat_azi[$azienda_clean]['liberi']."</small></i> ";
                                        if (isset($stat_azi[$azienda_clean]['filca']))
                                          echo "<i class='fas fa-square fa-sm' style='color: #63E6BE;'> <small>".$stat_azi[$azienda_clean]['filca']."</small></i> ";
                                        if (isset($stat_azi[$azienda_clean]['feneal']))
                                          echo "<i class='fas fa-square fa-sm' style='color: #74C0FC;'> <small>".$stat_azi[$azienda_clean]['feneal']."</small></i> ";
                                        if (isset($stat_azi[$azienda_clean]['fillea']))
                                          echo "<i class='fas fa-square fa-sm' style='color: #FF0000;'> <small>".$stat_azi[$azienda_clean]['fillea']."</small></i> ";
                                        if (isset($stat_azi[$azienda_clean]['n_spec']))
                                          echo "<i class='fas fa-square fa-sm' style='color: #ccccd1;'> <small>".$stat_azi[$azienda_clean]['n_spec']."</small></i> ";
                                      }
                                    ?> 
                                  </td>                     

                          </tr>
                          @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Nominativo</th>
                            <th>Data Ass</th>
                            <th>Funzionari assegnati</th>
                            <th>Stat</th>
                          </tr>                          
                        </tfoot>
                      </table> 
                      </div>

                      <div id='div_lav' style='display:none'>
                      </div>
                      @endif            
                    </div>  
                </form>    

              @endif 

          </div>
       
          <!--MODAL FRT !-->
          <form method='post' action="" id='frm_tab2' name='frm_tab2' autocomplete="off"  class="needs-validation form-floating" novalidate>
            <input name="_token" type="hidden" value="{{ csrf_token() }}" id='token_csrf'>
            <!-- Modal for edit note/contatti -->
            <div class="modal fade bd-example-modal-lg" id="modal_frt" tabindex="-1" role="dialog" aria-labelledby="info" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
              <input type='hidden' name='ref_edit_frt' id='ref_edit_frt'>
              <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="title_modal_frt">Inserisci nominativo in FilleaRealTime</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                </div>
        
              <div class="modal-body" id='body_modal_edit_frt'>
                <div class='row g-2 mb-2'>
                  
                  <div class="col-sm-12 col-md-8">
                    <div class="form-floating">
                      <input class="form-control" id="nome_frt" name='nome_frt' type="text" placeholder="Nominativo" maxlength=100 required />
                      <label for="nome_frt">Nominativo*</label>
                    </div>
                  </div>

                  <div class="col-sm-12 col-md-4">
                    <div class="form-floating">
                      <input class="form-control" id="natoil_frt" name='natoil_frt' type="date" required />
                      <label for="natoil_frt">Nato il*</label>
                    </div>
                  </div>
                  
                </div>	
                
                <div class='row g-2 mb-2'>
                  <div class="col-md-4">
                    <div class="form-floating">
                      <input class="form-control" id="codfisc_frt" name='codfisc_frt' type="text" placeholder="CF" maxlength=16 required />
                      <label for="codfisc_frt">Codice Fiscale*</label>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-floating">
                      <input class="form-control" id="tel_frt" name='tel_frt' type="text" placeholder="Telefono" maxlength=50 required />
                      <label for="tel_frt">Telefono*</label>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-floating mb-3 mb-md-0">
                    <select class="form-select" id="sesso_frt" aria-label="Sesso" name='sesso_frt' required>
                      <option value=''>Select...</option>
                      <option value='M'
                      >Maschile</option>
                      <option value='F' 
                      >Femminile</option>
                    </select>
                    <label for="sesso_frt">Sesso*</label>
                    </div>
                  </div>
                  <input type='hidden' name='sind_frt' id='sind_frt' value='0'>
                  <input type='hidden' name='ente_frt' id='ente_frt' value='C'>

                </div>
                <div class='row g-2 mb-2 ml-3'>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="confirm_frt" required>
                    <label class="form-check-label for="confirm_frt">Conferma operazione di iscrizione</label>
                  </div>						
                </div>
              
                
                </div>
                
                <div class="modal-footer">
                
                <button type="submit" class="btn btn-primary" id='btn_save_frt'>Inserisci in FRT</button>
                
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                
                </div>
              </div>
              </div>
            </div>
          </form>    
        
        
        </main> 



       </div>


       <?php
        function render_frt($info_frt,$posizione,$user_frt) {
          $view=null;
          if (!isset($info_frt[$posizione])) return false;
          $view.="<div style='display: flex;justify-content: left;width:60%'>";
            $view.="<table class='table table-bordered mt-2'>";
              $view.="<thead>";
                $view.="<tr>";
                  $view.="<th>Utente</th>";
                  $view.="<th>Data</th>";
                $view.="</tr>";
              $view.="</thead>";
              


              foreach($info_frt[$posizione] as $frt_dati) {
                $view.="<tr>";
                  $view.="<td>";
                    $view.=$frt_dati['utente'];
                    if (isset($user_frt[$frt_dati['utente']])) {
                      $view.="<br><small><i>".$user_frt[$frt_dati['utente']]['utentefillea']."</i></small>";
                      
                      $view.="(".$user_frt[$frt_dati['utente']]['sigla_pr'];
                      /*
                      $view.="<a href='javascript:void(0)' onclick=\"info_stru(".$user_frt[$frt_dati['utente']]['id_prov_associate'].")\">";
                        $view.= "<i class='fas fa-sign-in-alt fa-xs ml-2'></i>";
                      $view.= "</a>";	
                      */

                      $view.=")";							
                      
              
                    }
                  $view.="</td>";
                  
                  
                  $view.="<td>";
                    $view.=$frt_dati['data_update'];
                  $view.="</td>";	
                              
                $view.="</tr>";
              }
            $view.="</table>";
          $view.="</div>";
          

          
          return $view;
        }
        function adegua_tel($t1) {
          if (strlen($t1)==0) return $t1;
          if (substr($t1,0,3)!="+39") {
            if (substr($t1,0,2)!="39") $t1="+39$t1";
            elseif (substr($t1,0,2)=="39" && strlen($t1)>10) $t1="+$t1";
            elseif (substr($t1,0,2)=="39" && strlen($t1)<=10) $t1="+39$t1";
          }
          return $t1;
        }
      ?>       
  
      <!-- dipendenze DataTables !-->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css"/>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowreorder/1.5.0/css/rowReorder.dataTables.css"/>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.dataTables.css"/>

        <script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/rowreorder/1.5.0/js/dataTables.rowReorder.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/rowreorder/1.5.0/js/rowReorder.dataTables.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/responsive/3.0.3/js/responsive.dataTables.js"></script>


      <!-- fine DataTables !-->
      
      <script src="https://cdn.jsdelivr.net/npm/vue@2.5.17/dist/vue.js"></script>
      
    
       <script src="{{ URL::asset('/') }}dist/js/elenco.js?ver=<?= time() ?>"></script>
       <script src="{{ URL::asset('/') }}dist/js/elenco_vue.js?ver=<?= time() ?>"></script>
       
        

        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        
    </body>
</html>


