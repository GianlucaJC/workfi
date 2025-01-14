
var app = Vue.component('App',{
	template:
		`
        <form autocomplete="off">
		 <div class='container mt-5' v-if="edit_new!=null">
            <span class='mb-4'>Definizione Nota</span>
            <p v-if="resp==null">
                Caricamento dati utente in corso <i class="fas fa-spinner fa-spin"></i>
            </p>



            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Testo libero*</span>
                </div>
                <textarea required placeholder='Descrizione' class="form-control" aria-label="Testo libero" v-model='testo_nota' aria-describedby="Descrizione del libro" rows="5"></textarea>
            </div>


            <div>
                <small>I dai contrassegnati con * sono obbligatori</small>
            </div>

            <div class='mt-3'>
                <p v-if='savewait==true'>
                    <i class="fas fa-spinner fa-spin"></i>
                </p>
                <button type="button" v-if="isnew==false" class="btn btn-success" @click="save_book()">Salva</button>

                <button type="button" class="ml-2 btn btn-secondary" @click='close_edit()'>Torna all'elenco</button>
            </div>
 

		</div>
        </form>	
			
	`,
	data() {
        let flagsave=0;
        let isnew=false;
        let savewait=false
        let id_libro=0;
        let edit_new=null;
        let resp=null

		let testo_nota="";
        
        


        
        let ref_ente="";

		return {
            flagsave,
            isnew,
            savewait,
            id_libro,
            edit_new,
            resp,
			testo_nota,
            


			ref_ente,

		};
	},
	watch:{        
		ref_ente(newval,oldval) {
			this.ref_event=""
			if (newval!=oldval) {
				this.ref_percorso=null
				this.check_response=null
				this.check_response_noiscr=null
				this.is_vis=false
				this.percorsi=null
			}	
		}	

	 },
    mounted: function () {
        
        window.work=this;
        
		//this.events(this.periodo_ref)
    },	
	methods: {
        reset_form() {
            this.testo_nota="";
            

        },
        close_edit(){
            if (this.flagsave==0) {
                this.edit_new=null;            
                $("#div_table").show(150)
            } else {
                window.location.href = 'libri';
            }

        },
        active(from) {
            $("#div_table").hide()
            this.edit_new=1
            setTimeout(function() {	
                work.resp=1
			}, 600);
        },
		check_ins() {
			let testo_nota=this.testo_nota
            

            if (testo_nota.length==0) return false
		},  

          
		save_book() {
            
            check=this.check_ins()
            if (check==false) {
                alert("Attenzione! Compilare tutti i campi contrassegnati con * e la correttezza dei dati")
                return false
            }
			var self = this;

            this.savewait=true    

			setTimeout(function() {
				//<meta name="csrf-token" content="{{{ csrf_token() }}}"> //da inserire in html
				const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
				const csrf = metaElements.length > 0 ? metaElements[0].content : "";			
				fetch("save_book", {
					method: 'post',
					headers: {
						"Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
						"X-CSRF-Token": csrf
					},
					body: "id_libro="+self.id_libro+"&testo_nota="+self.testo_nota
				})
				.then(response => {
					if (response.ok) {
						return response.json();
					}
				})
				.then(response=>{
                    self.savewait=false
					esito=response.header
                    if (esito=="OK") {
                        self.flagsave=1
                        if (self.id_libro==0) self.isnew=true 
                        else self.isnew=false
                        alert("Dati salvati con successo!")

                    } 
                    else alert("Attenzione! Problema occorso durante il salvataggio");
				})
				.catch(status, err => {
					return console.log(status, err);
				})		
			}, 600);					

		},


		load_info(id_libro) {
            $("#div_table").hide()
            this.edit_new=1
			var self = this;
			this.check_response="wait";
			this.check_response_noiscr=null;

			setTimeout(function() {
				//<meta name="csrf-token" content="{{{ csrf_token() }}}"> //da inserire in html
				const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
				const csrf = metaElements.length > 0 ? metaElements[0].content : "";			
				fetch("load_book", {
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
				.then(info_book=>{
                    self.resp=1
                    
                    self.testo_nota=info_book.info[0].testo_nota
                    
                    
				})
				.catch(status, err => {
					return console.log(status, err);
				})		
			}, 600);		
						
			
		},
	
		percs(id_evento){
			this.percorsi=null
			var self = this;
			setTimeout(function() {
				
				//<meta name="csrf-token" content="{{{ csrf_token() }}}"> //da inserire in html
				const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
				const csrf = metaElements.length > 0 ? metaElements[0].content : "";			
				fetch("verifica.php", {
					method: 'post',
					headers: {
					  "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
					  "X-CSRF-Token": csrf
					},
					body: "operazione=percorsi&id_evento="+id_evento,
				})
				.then(response => {
					if (response.ok) {
					   return response.json();
					}
				})
				.then(percorsi=>{
					self.percorsi=percorsi
				})
				.catch(status, err => {
					return console.log(status, err);
				})			
			}, 100);			
			
		},
	}	
});


ev=new Vue ({
	el:"#app"
});	

function view(from) {
    work.flagsave=0
    work.isnew=false
    work.resp=null
    work.reset_form();
    if (from && from!=="0") {
        work.load_info(from)
        work.id_libro=from
    } else {
    	//window.work.active(from); 
    }    
}


function add_nota() {
    work.flagsave=0
    work.isnew=false
    work.resp=null
    work.reset_form();
    work.id_libro=0
    window.work.active(0); 
}