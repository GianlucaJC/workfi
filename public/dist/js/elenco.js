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
