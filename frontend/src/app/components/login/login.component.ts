import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from '../../services/user.service';
import { User } from '../../models/user';

@Component({
  selector: 'login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
  providers: [UserService]
})
export class LoginComponent implements OnInit {
	public page_title: string;
	public user: User;
	public status: string;
	public token;
	public identity;
	constructor(
		private _userService: UserService,
		private _router: Router,
		private _route: ActivatedRoute
	) { 
		this.page_title = 'Identificate';
		this.user = new User(1, '', '', 'ROLE_USER', '', '', '', '');
	}

	ngOnInit(): void {
		//Esta funcion se ejecuta siempre pero solo cierra sesión cuando le llega el parámetro sure por la url
		this.logout();
	}

	onSubmit(form) {
		this._userService.signup(this.user).subscribe(
			response => {
				//Aqui ha llegado el token
				if(response.status != 'error') {
					this.status = 'success';
					this.token = response;
					//Sacar objeto de usuario identificado
					this._userService.signup(this.user, true).subscribe(
						response => {
							this.identity = response;

							//CREAR SESSION DE USUARIO LOGEADO
							localStorage.setItem('token', this.token);
							localStorage.setItem('identity', JSON.stringify(this.identity));

							//Redireccion a inicio

							this._router.navigate(['home']);
						},
						error => {
							this.status = 'error';
							console.log(<any>error);
						}
					);
				} else {
					this.status = 'error';
				}
			},
			error => {
				this.status = 'error';
				console.log(<any>error);
			}
		);
	}

	logout() {
		this._route.params.subscribe(params => {
			let logout = +params['sure'];

			if(logout == 1){
				localStorage.removeItem('identity');
				localStorage.removeItem('token');

				this.identity = null;
				this.token = null;

				//Redireccion a inicio

				this._router.navigate(['home']);
			}
		})
	}
}
