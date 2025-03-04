ismobile=false
$(document).ready( function () {
    if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
        ismobile=true
        // true for mobile device
       $("#btn_espandi").show()
      }else{
        ismobile=false
        // false for not mobile device
        $("#btn_espandi").hide()
     }
      
    tipo_view=$("#tipo_view").val()
    set_table(tipo_view,'tbl_articoli')
} );

// Example starter JavaScript for disabling form submissions if there are invalid fields
(function () {
    'use strict'
  
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')
  
    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
      .forEach(function (form) {
        form.addEventListener('submit', function (event) {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          } else {
              var cf=$("#codfisc_frt").val()
              var valida=validaCodiceFiscale(cf);
              if (valida==false) {
                $("#codfisc_frt").removeClass('is-valid').addClass('is-invalid');
                event.preventDefault()
                event.stopPropagation()
                alert("Codice fiscale non valido!")
              } else {
                  $("#codfisc").removeClass('is-invalid').addClass('is-valid');			
                  save_frt()
              }
              
          }	
          form.classList.add('was-validated')
        }, false)
      })
  })()


function validaCodiceFiscale(cf){
    var validi, i, s, set1, set2, setpari, setdisp;
    if( cf == '' )  return '';
    cf = cf.toUpperCase();
    if( cf.length != 16 )
        return false;
    validi = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    for( i = 0; i < 16; i++ ){
        if( validi.indexOf( cf.charAt(i) ) == -1 )
            return false;
    }
    set1 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    set2 = "ABCDEFGHIJABCDEFGHIJKLMNOPQRSTUVWXYZ";
    setpari = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    setdisp = "BAKPLCQDREVOSFTGUHMINJWZYX";
    s = 0;
    for( i = 1; i <= 13; i += 2 )
        s += setpari.indexOf( set2.charAt( set1.indexOf( cf.charAt(i) )));
    for( i = 0; i <= 14; i += 2 )
        s += setdisp.indexOf( set2.charAt( set1.indexOf( cf.charAt(i) )));
    if( s%26 != cf.charCodeAt(15)-'A'.charCodeAt(0) )
        return false;
    return true;
}
function set_table(tipo,id_tb) {
    responsive=true
    if (tipo==1) {
        responsive= {
            details: {
                display: DataTable.Responsive.display.childRowImmediate,
                target: '',
                type: 'none'
            }            
        }
    }
    $("#"+id_tb+" tfoot th").each(function () {
        var title = $(this).text();
		if (title.length!=0)
			$(this).html('<input type="text" placeholder="Cerca ' + title + '" />');
    });		    
    w="25rem"
    if (ismobile==true) w="11rem"
    
    var table=$('#'+id_tb).DataTable({
		dom: 'lBfrtip',
        responsive:responsive   ,
        scrollX: true,
        columnDefs: [

            { "width": w, "targets": [0] },

           // { "width": "1rem", "targets": [1] }
        ],          
        buttons: [
			''
		],		
        initComplete: function () {
            // Apply the search
            this.api()
                .columns()
                .every(function () {
                    var that = this;
 
                    $('input', this.footer()).on('keyup change clear', function () {
                        if (that.search() !== this.value) {
                            that.search(this.value).draw();
                        }
                    });
                });
        },	        
        pagingType: 'full_numbers',
		pageLength: 10,
		lengthMenu: [10, 15, 20, 50, 100, 200, 500],

        language: {
            lengthMenu: "Visualizza _MENU_ lavoratori per pagina",
            zeroRecords: 'Nessun lavoratore trovato',
            info: 'Pagina _PAGE_ di _PAGES_',
            infoEmpty: 'Non sono presenti lavoratori',
            infoFiltered: '(Filtrati da _MAX_ lavoratori totali)',
        },

		
    });	    
}



function elimina(id_libro) {
    if (!confirm("Sicuri di eliminare il libro?")) return false;
    
    const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
    const csrf = metaElements.length > 0 ? metaElements[0].content : "";
    fetch("dele_book", {
        method: 'post',
        headers: {
          "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
          "X-CSRF-Token": csrf
        },
        body: "id_libro="+id_libro,
    })
    .then(response => {
        if (response.ok) {
           return response.json();
        }
    })
    .then(resp=>{
        if (resp.header=="OK")
            $("#tr"+id_libro).remove();
        else alert("Problema occorso durante la cancellazione")
        console.log(resp)

    })
    .catch(status, err => {
        return console.log(status, err);
    })       
}

function set_opaz() {
    current=$("#op_az").val()
    if (current=="op") $("#op_az").val('az')
    else $("#op_az").val('op')
    $('#frm_main').submit();
}


function insert_frt(id_anagr) {
	$("#confirm_frt").prop('checked', false)
	$("#btn_save_frt").html('Inserisci in FRT'); 
	$("#btn_save_frt").prop('disabled', false);
	
	ref=$("#id_ref"+id_anagr)
	$("#ref_edit_frt").val(id_anagr)
	nome=ref.data('nome')
	datanasc=ref.data('datanasc').substr(0,10)
	telefoni=ref.data('telefoni')
	
	//precompilazione delega in funzione della scelta
	$("#nome_frt").val(nome)
	$("#natoil_frt").val(datanasc)

	$("#tel_frt").val(telefoni)
	
	$('#modal_frt').modal('toggle')
	$("#title_modal_frt").html("Inserisci <b>"+nome+"</b> in FilleaRealTime<b>")
}	

function save_frt() {
	event.preventDefault()
	if (!($('#confirm_frt').is(':checked'))) {
		alert("Confermare la richiesta di iscrizione in FRT")
		return false;
	}
	user_ref=$("#user_ref").val()	
	ref_edit_frt=$("#ref_edit_frt").val()
	nome_frt=$("#nome_frt").val()
	natoil_frt=$("#natoil_frt").val()
	codfisc_frt=$("#codfisc_frt").val()
	sesso_frt=$("#sesso_frt").val()
	sind_frt=$("#sind_frt").val()
	ente_frt=$("#ente_frt").val()
	tel_frt=$("#tel_frt").val()
	id_azienda=$("#id_azienda").val()

	$("#btn_save_frt").html('Attendere...'); 
	$("#btn_save_frt").prop('disabled', true);
	
	var timer,delay = 800;	

	clearTimeout(timer);
	timer = setTimeout(function() {	
		base_path = $("#url").val();
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		let CSRF_TOKEN = $("#token_csrf").val();
		$.ajax({
			type: 'POST',
			url: base_path+"/ins_frt",
			data: {_token: CSRF_TOKEN,user_ref:user_ref,nome_frt:nome_frt,natoil_frt:natoil_frt,codfisc_frt:codfisc_frt,sesso_frt:sesso_frt,sind_frt:sind_frt,ente_frt:ente_frt,tel_frt:tel_frt},
			success: function (data) {
				html="";
				html+=`<a href='#' onclick="$('#frm_main').submit()">
						Refresh pagina dopo inserimento FRT
					</a>`	
				$("#frt_"+ref_edit_frt).html(html)
				$('#modal_frt').modal('toggle')			
			}
		})	
	}, delay)	
}
//function disabilitata a favore della nuova consultazione anagrafe (passo i parametri in $_get...valutare $_post per problemi escape)
function view_lav(azienda) {
    $("#div_lav").empty();
    const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
    const csrf = metaElements.length > 0 ? metaElements[0].content : "";
    fetch("lav_from_azienda", {
        method: 'post',
        headers: {
          "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
          "X-CSRF-Token": csrf
        },
        body: "azienda="+azienda,
    })
    .then(response => {
        if (response.ok) {
           return response.json();
        }
    })
    .then(resp=>{
        $("#div_aziende").hide(100);
        html=""
        html+=`
            <button type="button" class="btn btn-primary" onclick="$('#div_lav').empty();$('#div_aziende').show(100)">Torna alle aziende</button><hr>
            <table id='tbl_lav_az' class="display nowrap">
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
                <tr>
                <td>Nominativo</td>
                <td>Data Ass</td>
                <td>Stat</td>
                <td>Azioni</td>
                <td>Posizione</td>
                <td>Funzionari assegnati</td>
                <td>Nato a</td>
                <td>Nato il</td>
                <td>Azienda</td>
                <td>Altrove</td>
                <td>Tel FO</td>
                <td>Tel CE</td>
                <td>Tel GPS</td>
                <td>Tel SIN</td>
                <td>Tel altro</td>
                <td>FRT</td>
                <td>Note</td>
                </tr>                  
            </tbody>

            <tfoot>
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
            </tfoot>

            </table>

        `
        $("#div_lav").html(html)
        
        tipo_view=$("#tipo_view").val()
        set_table(tipo_view,'tbl_lav_az')        
        $("#div_lav").show(100);
        console.log(resp)

    })
    .catch(status, err => {
        return console.log(status, err);
    })     
}


function set_filtro_stato(value) {
    $("#filtro_colore").val(value)
    $("#frm_main").submit()
}