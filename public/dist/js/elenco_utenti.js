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
            lengthMenu: "Visualizza _MENU_ utenti per pagina",
            zeroRecords: 'Nessun utente trovato',
            info: 'Pagina _PAGE_ di _PAGES_',
            infoEmpty: 'Non sono presenti utenti',
            infoFiltered: '(Filtrati da _MAX_ utenti totali)',
        },

		
    });	

    
   
	

	
} );

function preferiti(id_utente) {
    $("#cont_prefer").empty();
    $("#modal_prefer").modal();
    
    const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
    const csrf = metaElements.length > 0 ? metaElements[0].content : "";
    fetch("load_prefer", {
        method: 'post',
        headers: {
          "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
          "X-CSRF-Token": csrf
        },
        body: "id_utente="+id_utente,
    })
    .then(response => {
        if (response.ok) {
           return response.json();
        }
    })
    .then(resp=>{
        console.log(resp)
        libri=resp.info
        html=`
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nome libro</th>
                    <th scope="col">Descrizione</th>
                </tr>
            </thead>
            <tbody>`
            for (sca=0;sca<libri.length;sca++) {
                nome_libro=libri[sca].nome_libro
                descrizione_libro=libri[sca].descrizione_libro
                html+="<tr>"
                    html+="<th scope='row'>"+(sca+1)+"</th>"
                    html+="<td><b><font color='blue'>"+nome_libro+"</font></b></td>"
                    html+="<td>"+descrizione_libro+"</td>"
                html+="</tr>";
            } 
            html+="</tbody></table>"
        
        $("#cont_prefer").html(html)

    })
    .catch(status, err => {
        return console.log(status, err);
    })        
}

function elimina(id_utente) {
    if (!confirm("Sicuri di eliminare l'utente?")) return false;
    
    const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
    const csrf = metaElements.length > 0 ? metaElements[0].content : "";
    fetch("dele_user", {
        method: 'post',
        headers: {
          "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
          "X-CSRF-Token": csrf
        },
        body: "id_utente="+id_utente,
    })
    .then(response => {
        if (response.ok) {
           return response.json();
        }
    })
    .then(resp=>{
        if (resp.header=="OK")
            $("#tr"+id_utente).remove();
        else alert("Problema occorso durante la cancellazione")
        console.log(resp)

    })
    .catch(status, err => {
        return console.log(status, err);
    })       
}


