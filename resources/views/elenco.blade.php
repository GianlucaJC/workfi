<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Google Font: Source Sans Pro -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        

        <link href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css" rel="stylesheet">


        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>

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


    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
           
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 p-2">
                      <center><b>Gestione Organizzativa</b></center>
                    </div>
                </header>    

         <!-- Page Content -->
        <main>
              

              @if ($isadmin==1)     

                <div class="container-fluid mt-3">
                <!-- handle from Vue !-->
                <div id="app">
                  <App></App>
                </div>    

                <form method='post' action="{{ route('main') }}" id='frm_main' name='frm_main' autocomplete="off">
                    <input type="hidden" value="{{url('/')}}" id="url" name="url">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}" id='token_csrf'>	  
                    <input type='hidden' name='tipo_view' id='tipo_view' value='{{$tipo_view}}'>

                    <div id="div_table">
                      <div style="text-align: right;display:none" id='btn_espandi'> 
                        <?php 
                          $out="";$txt="Espandi tutto";$value_e=1;
                          
                          if ($tipo_view=="1") {$out="outline-";$txt="Riduci";$value_e=0;}
                        ?>
                        <button type="button" class="btn btn-{{$out}}primary btn-sm" onclick="$('#tipo_view').val({{$value_e}});$('#frm_main').submit();">{{$txt}}</button>
                      </div>

                      <table id='tbl_articoli' class="display nowrap">
                        <thead>
                          <tr>
                            <th>Nominativo</th>
                            <th>Stat</th>
                            <th>Azioni</th>
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
                        </thead>
                        <tbody>
                          @foreach($elenco as $info)
                            <?php
                              $azienda=$info->DENOM;
                              $azienda_clean=str_replace("'","",$azienda);
                              $azienda_clean=str_replace('"',"",$azienda_clean);                          
                            ?>

                              <tr id='tr{{$info->ID_anagr}}'>
                                  <td>
                                    <?php
                                      if (isset($note[$info->posizione])) echo "<b>".$info->NOME."</b>";
                                      else echo $info->NOME;
                                    ?> 
                                  </td>

                                  <td>
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

                                  <td>
                                    <?php
                                      if (isset($info->posizione) && strlen($info->posizione)>0) {?>
                                        <button type="button" onclick="add_nota('{{$info->posizione}}','{{$user}}')" class="btn btn-primary btn-sm">Note</button>
                                    <?php } ?>    
                                      <button type="button" class="btn btn-secondary btn-sm">FRT</button>

                                  </td> 

                                  <td>
                                    {{$info->data_scarico}}
                                    <span id='id_ref{{$info->ID_anagr}}' 
                                        data-nominativo='{{$info->NOME}}'
                                    >
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
                                          $id_funz=$elenco_assegnazioni[$azienda_clean][$i]['id_funzionario'];
                              
                                          if (array_key_exists($id_funz,$funzionari)) {
                                            echo "<b>$id_funz</b>: ";
                                            echo $funzionari[$id_funz];
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
                                      {{$info->DENOM}}
                                  </td>
                                  <td>Altrove</td>
                                  <td>
                                    <a id="phone1" href="tel:{{$info->C1}}">{{$info->C1}}</a>
                                  </td>
                                  <td>
                                    <a id="phone2" href="tel:{{$info->tel_ce}}">{{$info->tel_ce}}</a>
                                  </td>
                                  <td>
                                    <a id="phone3" href="tel:{{$info->tel_gps}}">{{$info->tel_gps}}</a>
                                  </td>
                                  <td>
                                    <a id="phone4" href="tel:{{$info->tel_sin}}">{{$info->tel_sin}}</a>
                                  </td>
                                  <td>
                                    <a id="phone5" href="tel:{{$info->tel_altro}}">{{$info->tel_altro}}</a>
                                  </td>
                                  
                                  <td>FRT</td>
                                  <td>
                                    <?php
                                      if (isset($note[$info->posizione])) {
                                        $view='<table class="table">
                                          <thead>
                                            <tr>
                                              <th scope="col">Utente</th>
                                              <th scope="col">Data</th>
                                              <th scope="col">Nota</th>
                                              <th scope="col">Stato</th>
                                            </tr>
                                          </thead>
                                          <tbody>';
                                            for ($sca=0;$sca<count($note[$info->posizione]);$sca++) {
                                              $view.="<tr>";
                                                $view.="<td>";
                                                  $view.=$note[$info->posizione][$sca]->id_user;
                                                $view.="</td>";
                                                $view.="<td>";
                                                  $view.=$note[$info->posizione][$sca]->created_at;
                                                $view.="</td>";                                                
                                                $view.="<td>";
                                                  $view.="<i>".$note[$info->posizione][$sca]->note."</i>";
                                                $view.="</td>";
                                                $view.="<td style='text-align:center'>";
                                                  if  ($note[$info->posizione][$sca]->stato_nota=="1")
                                                    $view.="<i class='fas fa-circle fa-lg mt-3' style='color: #ff0000;'></i>";
                                                  if  ($note[$info->posizione][$sca]->stato_nota=="2")
                                                    $view.="<i class='fas fa-circle fa-lg mt-3' style='color: #FFD43B;'></i>";
                                                  if  ($note[$info->posizione][$sca]->stato_nota=="3")
                                                    $view.="<i class='fas fa-circle fa-lg mt-3' style='color: #00ca00;'></i>";
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

                        </tfoot>					
                      </table>
                                           
                    </div>  
                </form>    

              @endif 
              
              @if($isadmin==0) 
                  <div class="jumbotron mt-3">
                    <h1 class="display-4">Attenzione</h1>
                    <p class="lead">Non hai accesso a questa pagina con il tuo livello di utenza</p>
                    <hr class="my-4">
                    <p class="lead mt-2">
                      <a class="btn btn-primary btn-lg" href="{{ route('elenco_libri') }}" role="button">Home page</a>
                    </p>
                  </div>
              @endif
          </div>
       

        <!-- Modal for libri preferiti-->
         
        <div class="modal fade" id="modal_prefer" tabindex="-1" role="dialog" aria-labelledby="Libri preferiti" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Libri preferiti dall'utente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body" id="cont_prefer">
                
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
              </div>
            </div>
          </div>
        </div>          
        </main> 



       </div>
  
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
