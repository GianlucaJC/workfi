
var app = Vue.component('App',{
	template:
		`
        <form autocomplete="off">
		 <div class='container mt-5' v-if="edit_new!=null">
            <span class='mb-4'>Definizione Nota</span>
            <p v-if="resp==null">
                Caricamento dati in corso <i class="fas fa-spinner fa-spin"></i>
            </p>

            <div class="input-group mb-3 mt-2">
				<textarea required placeholder='Descrizione' class="form-control" aria-label="Testo libero" v-model='testo_nota' aria-describedby="Descrizione della nota" rows="5">
				</textarea>
            </div>

			<div class='mt-2'>
				Impostazione stato: 
				<a href='javascript:void(0)'>
				<i class="far fa-circle fa-lg mt-3 semaforo" style="color: #ff0000;" id="sem1" @click='set_stato(1)'></i></a>
				<a href='javascript:void(0)'>
				<i class="far fa-circle fa-lg mt-3 semaforo" style="color: #FFD43B;" id="sem2" @click='set_stato(2)'></i></a>
				<a href='javascript:void(0)'>
				<i class="far fa-circle fa-lg mt-3 semaforo" style="color: #00ca00;" id="sem3" @click='set_stato(3)'></i></a>
				<a href='javascript:void(0)'>
				<i class="fas fa-ban fa-lg mt-3" style="color:rgba(0, 0, 0, 0.94);" id="sem4" @click='set_stato(0)'></i></a>				
			</div>


            <div class='mt-3'>
                <p v-if='savewait==true'>
                    <i class="fas fa-spinner fa-spin"></i>
                </p>
                <button type="button" v-if="isnew==false" class="btn btn-success" @click="save_nota(1)">Salva</button>
                <button type="button" v-if="isnew==false" class="btn btn-success" @click="save_nota(2)">Salva nota per tutta l'azienda</button>				
				
                <button type="button" class="ml-2 btn btn-secondary" @click='close_edit()'>Torna all'elenco</button>
            </div>
 		</div>
        </form>	
	`
		//<button type="button" v-if="flagsave==1" class="ml-2 btn btn-outline-primary" @click="refr()">Refresh tabella (se vuoi vedere subito la nota)</button>
	,
	data() {
        let codlav=0;
		let user="";
        let flagsave=0;
        let isnew=false;
        let savewait=false
		let stato_nota=0;
        let edit_new=null;
        let resp=null
		let testo_nota="";
        let ref_ente="";

		return {
			codlav,
			user,
            flagsave,
            isnew,
            savewait,
			stato_nota,
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
		set_stato(from) {			
			$(".semaforo").removeClass("fas fa-circle")
			$(".semaforo").removeClass("far fa-circle")
			$(".semaforo").addClass("far fa-circle")
			$("#sem"+from).addClass("fas fa-circle")
			work.stato_nota=from
			this.save_stato();
		},
        reset_form() {
            this.testo_nota="";
        },
		refr() {
			window.location.href = 'main';
		},
        close_edit(){
			this.flagsave=0
			this.edit_new=null;            
			$("#div_table").show(150)
           
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

		save_stato() {
            base_path = $("#url").val();
			var self = this;
            this.savewait=true    

			setTimeout(function() {
				//<meta name="csrf-token" content="{{{ csrf_token() }}}"> //da inserire in html
				const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
				const csrf = metaElements.length > 0 ? metaElements[0].content : "";			
				fetch(base_path+"/save_stato", {
					method: 'post',
					headers: {
						"Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
						"X-CSRF-Token": csrf
					},
					body: "codlav="+self.codlav+"&stato_nota="+self.stato_nota
				})
				.then(response => {
					if (response.ok) {
						return response.json();
					}
				})
				.then(response=>{
                    self.savewait=false
					esito=response.esito
                    if (esito=="OK") {
                        self.flagsave=1
                        alert("Dati salvati con successo!")
						self.close_edit()
						html=""
						if (self.stato_nota=="1")
							html='<i class="fas fa-circle fa-lg mt-3" style="color: #ff0000;"></i>'
						if (self.stato_nota=="2")
						  	html='<i class="fas fa-circle fa-lg mt-3" style="color: #FFD43B;"></i>'
						if (self.stato_nota=="3")
						  	html='<i class="fas fa-circle fa-lg mt-3" style="color: #00ca00;"></i>'
						
						$("#status_lav"+self.codlav).html(html)

                    } 
                    else alert("Attenzione! Problema occorso durante il salvataggio");
				})
				.catch(status, err => {
					return console.log(status, err);
				})		
			}, 600);					

		},
		
		save_nota(from) {
            base_path = $("#url").val();
            check=this.check_ins()
            if (check==false) {
                alert("Attenzione! Compilare tutti i campi contrassegnati con * e la correttezza dei dati")
                return false
            }
			if (from=="2") {
				if (!confirm("Sicuri di creare la nota per tutta l'azienda?")) return false
			}
			var self = this;

            this.savewait=true    

			setTimeout(function() {
				//<meta name="csrf-token" content="{{{ csrf_token() }}}"> //da inserire in html
				const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
				const csrf = metaElements.length > 0 ? metaElements[0].content : "";			
				fetch(base_path+"/save_nota", {
					method: 'post',
					headers: {
						"Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
						"X-CSRF-Token": csrf
					},
					body: "user="+self.user+"&codlav="+self.codlav+"&testo_nota="+self.testo_nota+'&from='+from
				})
				.then(response => {
					if (response.ok) {
						return response.json();
					}
				})
				.then(response=>{
                    self.savewait=false
					esito=response.esito
                    if (esito=="OK") {
                        self.flagsave=1
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
    } else {
    	//window.work.active(from); 
    }    
}


function add_nota(codlav,user) {
	work.codlav=codlav
	work.user=user
    work.flagsave=0
    work.isnew=false
    work.resp=null
    work.reset_form();
    window.work.active(0); 
}