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
    id_utente=$("#info_user").val()
    
    nome_libro=$("#id_ref"+id_libro).attr('data-nome_libro');
    descrizione_libro=$("#id_ref"+id_libro).attr('data-descrizione_libro');
    url_foto=$("#id_ref"+id_libro).attr('data-url_foto');
    prezzo=$("#id_ref"+id_libro).attr('data-prezzo');
    prefer=$("#id_ref"+id_libro).attr('data-prefer');

    prezzo="<font color='blue'>"+prezzo+"</font>"
    $("#nome_libro").html(nome_libro)
    $("#prezzo").html(prezzo)
    $("#descrizione_libro").html(descrizione_libro)
    html="<img class='rounded img-fluid img-thumbnail' src='"+url_foto+"'>"
    $("#url_foto").html(html)
    
    if (id_utente.length>0) {
        st="far fa-star fa-lg";testo="Aggiungi alla lista dei tuoi libri preferiti"
        pr=0    
        if (prefer.length!=0) {
            pr=1;
            st="fa-solid fa-star";
            testo="Rimuovi dalla lista dei tuoi preferiti"
        }
        
        
        current=`<span id ='current_star'><i class="`+st+`" style="color: #0471ca;"></i></span>`

        html=`
            <span id='div_spin' style='display:none'><i class="fa-solid fa-spinner fa-spin-pulse"></i></span>
            <a href="javascript:void(0)" onclick="change_prefer(`+pr+`,`+id_libro+`,`+id_utente+`)">
               `+current+`
            </a>
            <span id='testo' class='ml-2'>`+testo+`</span>`
        
        $("#star").html(html)    
        
       
    }

}




function change_prefer(stato_prefer,id_libro,id_utente) {
    $("#div_spin").show();
    const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
    const csrf = metaElements.length > 0 ? metaElements[0].content : "";
    fetch("change_prefer", {
        method: 'post',
        headers: {
          "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
          "X-CSRF-Token": csrf
        },
        body: "stato_prefer="+stato_prefer+"&id_libro="+id_libro+"&id_utente="+id_utente,
    })
    .then(response => {
        if (response.ok) {
           return response.json();
        }
    })
    .then(resp=>{
        if (stato_prefer=="1") {
            $("#id_ref"+id_libro).attr('data-prefer', '');
            current=`<i class="far fa-star fa-lg" style="color: #0471ca;">`
            testo="Aggiungi alla lista dei tuoi libri preferiti"
            solo_pref=$("#solo_pref").val()
            if (solo_pref=="1") $("#tr"+id_libro).remove()
            
        } else {
            $("#id_ref"+id_libro).attr('data-prefer', '1');
            current=`<i class="fa-solid fa-star" style="color: #0471ca;">`
            testo="Rimuovi dalla lista dei tuoi preferiti"
        }
        $("#current_star").html(current)
        $("#testo").html(testo)
        $("#div_spin").hide();

        $("#div_view_book").show().hide(1000)
        $("#div_table").hide().show(1000)
        
        
            
        console.log(resp)

    })
    .catch(status, err => {
        return console.log(status, err);
    })    

}
