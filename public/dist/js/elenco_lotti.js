$(document).ready( function () {
	
    var table=$('#tbl_articoli').DataTable({
		dom: 'lBfrtip',
		
        buttons: [
			''
		],		
        pagingType: 'full_numbers',
		pageLength: 10,
		lengthMenu: [10, 15, 20, 50, 100, 200, 500],

        language: {
            lengthMenu: "Visualizza _MENU_ libri per pagina",
            zeroRecords: 'Nessun libro trovato',
            info: 'Pagina _PAGE_ di _PAGES_',
            infoEmpty: 'Non sono presenti libri',
            infoFiltered: '(Filtrati da _MAX_ libri totali)',
        },

		
    });	

    
   
	

	
} );

function view_libro(id_libro) {
    $("#div_table").hide()
    $("#div_view_book").show(150)

    nome_libro=$("#id_ref"+id_libro).attr('data-nome_libro');
    descrizione_libro=$("#id_ref"+id_libro).attr('data-descrizione_libro');
    url_foto=$("#id_ref"+id_libro).attr('data-url_foto');
    prezzo=$("#id_ref"+id_libro).attr('data-prezzo');
    prezzo="<font color='blue'>"+prezzo+"</font>"
    $("#nome_libro").html(nome_libro)
    $("#prezzo").html(prezzo)
    $("#descrizione_libro").html(descrizione_libro)
    html="<img class='rounded img-fluid img-thumbnail' src='images/"+url_foto+"'>"

    $("#url_foto").html(html)
}


function make_call(indice) {
    new_provv = $("#new_provv_if_exist").is(":checked")
    n_p=0;
    if (new_provv==true) n_p=1
    const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
    const csrf = metaElements.length > 0 ? metaElements[0].content : "";
    codice=make_call.arr_info[indice]['codice']
    lotto=make_call.arr_info[indice]['lotto']
    fetch("crea_provv", {
        method: 'post',
        headers: {
          "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
          "X-CSRF-Token": csrf
        },
        body: "codice="+codice+"&lotto="+lotto+"&n_p="+n_p,
    })
    .then(response => {
        if (response.ok) {
           return response.json();
        }
    })
    .then(resp=>{
        console.log(resp)
        indice++ 
        perc=parseInt((100/make_call.arr_info.length)*indice)

        html=`
        <div class="progress mt-2" role="progressbar" aria-label="Avanzamento creazione provvisori" aria-valuenow="`+perc+`" aria-valuemin="`+perc+`" aria-valuemax="100">
            <div class="progress-bar bg-warning" style="width: `+perc+`%">`+perc+`%</div>
        </div><hr>`
        $("#div_progress").html(html);
        console.log("indice",indice,"arr_info.length",arr_info.length)
        if (indice<make_call.arr_info.length) make_call(indice)
        else {
            html=`<div class="alert alert-success mt-2" role="alert">
                Scansione lotti eseguita!<hr>

                <button type="button" onclick="$('#frm_lotti').submit()" class="btn btn-primary">Esegui il refresh della tabella per vedere l'esito dell'associazione master</button>
            </div>`
            $("#div_progress").html(html)
        }

    })
    .catch(status, err => {
        return console.log(status, err);
    })    

}
