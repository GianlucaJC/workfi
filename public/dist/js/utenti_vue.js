
var app = Vue.component('App',{
	template:
		`
        <form autocomplete="off">
		 <div class='container mt-5' v-if="edit_new!=null">
            <span class='mb-4'>Definizione dati utenza</span>
            <p v-if="resp==null">
                Caricamento dati utente in corso <i class="fas fa-spinner fa-spin"></i>
            </p>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Nome*</span>
                </div>
                <input type="text" required class="form-control" placeholder="Nome utente o alias" aria-label="Name" aria-describedby="Name" v-model="name">
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">@*</span>
                </div>
                <input type="text" required placeholder='Email' class="form-control" v-model='email' aria-describedby="Email">
            </div>


            <div v-if='c_pw==true'>
                <small>
                    Criteri richiesti: Almeno un carattere maiuscolo, almeno un carattere minuscolo, almeno un numero, almeno un carattere speciale
                </small>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Cambia Password*</span>
                    </div>
                    <input type="password" required class="form-control" aria-label="Password" v-model='password'>
                </div>
            
        
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Ripeti Password*</span>
                    </div>
                    <input type="password" required class="form-control" aria-label="Ripeti Password" v-model='password1'>
                </div>
            </div>

            
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="tipo_utenza">Tipo utenza*</label>
                </div>
                <select class="custom-select" v-model="tipo_utenza" :value='tipo_utenza'>
                    <option value="1">Admin</option>
                    <option value="0">User</option>
                </select>
            </div>     
            <div>
                <small>I dai contrassegnati con * sono obbligatori</small>
            </div>

            <div class='mt-3'>
                <p v-if='savewait==true'>
                    <i class="fas fa-spinner fa-spin"></i>
                </p>
                <button type="button" v-if="isnew==false" class="btn btn-success" @click="save_user()">Salva</button>

                <button type="button" class="ml-2 btn btn-secondary" @click='close_edit()'>Torna all'elenco</button>
            </div>
 

		</div>
        </form>	
			
	`,
	data() {
        let c_pw=true;
        let flagsave=0;
        let isnew=false;
        let savewait=false
        let id_user=0;
        let edit_new=null;
        let resp=null
		let name=""
		let email=null;
		let password=null
		let password1= null; 
        let tipo_utenza=""
        
        let ref_ente="";

		return {
            c_pw,
            flagsave,
            isnew,
            savewait,
            id_user,
            edit_new,
            resp,
			name,
			email,
			password,
			password1,
            tipo_utenza,


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
        
        window.users=this;
        
		//this.events(this.periodo_ref)
    },	
	methods: {
        reset_form() {
            this.name=""
            this.email=null;
            this.password=null
            this.password1= null; 
            this.tipo_utenza=""            
        },
        close_edit(){
            if (this.flagsave==0) {
                this.edit_new=null;            
                $("#div_table").show(150)
            } else {
                window.location.href = 'elenco_utenti';
            }

        },
        active(from) {
            $("#div_table").hide()
            this.edit_new=1
            setTimeout(function() {	
                users.resp=1
			}, 600);
        },
		check_ins() {
			let name=this.name
			let email=this.email
            let password=this.password
            let password1=this.password1
            let tipo_utenza=this.tipo_utenza
           
            if (this.c_pw==true) {
			    if (name.length==0 || email.length==0 || password.length==0 || password1.length==0 || tipo_utenza.length==0) return false
            }
            else {
                if (name.length==0 || email.length==0 || tipo_utenza.length==0) return false
            }

		},  

        checkPwd(pwd) {
            let regex = 
            /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@.#$!%*?&])[A-Za-z\d@.#$!%*?&]{8,15}$/;
            return regex.test(pwd);
        },
          
        validateEmail(email) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailPattern.test(email);
        },         
		save_user() {
            
            check=this.check_ins()
            if (check==false) {
                alert("Attenzione! Compilare tutti i campi contrassegnati con *")
                return false
            }
            check_mail=this.validateEmail(this.email)
            if (!check_mail) {
                alert("Attenzione! La mail non risulta formalmente valida")
                return false
            }
            
            if (this.c_pw==true) {
                check_pw=this.checkPwd(this.password)
                if (check_pw==false)   {
                    alert("Attenzione! La password non soddisfa i criteri richiesti:\n\nAlmeno un carattere maiuscolo\nAlmeno un carattere minuscolo\nAlmeno un numero\Almeno un carattere speciale")
                    return false
                }
                if (this.password!=this.password1) {
                    alert("Attenzione! Le due password non coincidono")
                    return false;
                }
            } else this.password=""
			var self = this;

            this.savewait=true    

			setTimeout(function() {
				//<meta name="csrf-token" content="{{{ csrf_token() }}}"> //da inserire in html
				const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
				const csrf = metaElements.length > 0 ? metaElements[0].content : "";			
				fetch("save_user", {
					method: 'post',
					headers: {
						"Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
						"X-CSRF-Token": csrf
					},
					body: "id_user="+self.id_user+"&name="+self.name+"&email="+self.email+"&password="+self.password+"&isadmin="+self.tipo_utenza,
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
                        if (self.id_user==0) self.isnew=true 
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


		load_info(id_utente) {
            $("#div_table").hide()
            this.edit_new=1
			var self = this;
			this.check_response="wait";
			this.check_response_noiscr=null;

			setTimeout(function() {
				//<meta name="csrf-token" content="{{{ csrf_token() }}}"> //da inserire in html
				const metaElements = document.querySelectorAll('meta[name="csrf-token"]');
				const csrf = metaElements.length > 0 ? metaElements[0].content : "";			
				fetch("load_info", {
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
				.then(info_user=>{
                    self.resp=1
                    self.name=info_user.info[0].name
                    self.email=info_user.info[0].email
                    self.tipo_utenza=info_user.info[0].isadmin
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
    users.flagsave=0
    users.isnew=false
    users.resp=null
    users.reset_form();
    if (from && from!=="0") {
        users.load_info(from)
        users.c_pw=false
        users.id_user=from
    } else {
    	//window.users.active(from); 
    }    
}


function add_user() {
    users.c_pw=true
    users.flagsave=0
    users.isnew=false
    users.resp=null
    users.reset_form();
    users.id_user=0
    window.users.active(0); 
}