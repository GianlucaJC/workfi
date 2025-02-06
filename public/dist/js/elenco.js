$(document).ready( function () {
    if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
        // true for mobile device
       $("#btn_espandi").show()
      }else{
        // false for not mobile device
        $("#btn_espandi").hide()
     }
      
    tipo_view=$("#tipo_view").val()
    set_table(tipo_view)
} );

function set_table(tipo) {
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
    $('#tbl_articoli tfoot th').each(function () {
        var title = $(this).text();
		if (title.length!=0)
			$(this).html('<input type="text" placeholder="Cerca ' + title + '" />');
    });		    
    var table=$('#tbl_articoli').DataTable({
		dom: 'lBfrtip',
        responsive:responsive   ,
        scrollX: true,
        columnDefs: [

            { "width": "100px", "targets": [0] },
            { "width": "60px", "targets": [1] }
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


