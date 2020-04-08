import { Component, OnInit } from '@angular/core';
import { User } from '../../models/user';
import { UserService } from '../../services/user.service';
import { faBold} from '@fortawesome/free-solid-svg-icons';
import { faItalic} from '@fortawesome/free-solid-svg-icons';



@Component({
  selector: 'app-user-edit',
  templateUrl: './user-edit.component.html',
  styleUrls: ['./user-edit.component.css'],
  providers: [UserService]
})
export class UserEditComponent implements OnInit {
	public page_title: string;
  public faItalic = faItalic;
  public faBold = faBold;
	public user: User;
	public identity;
	public token;
	public status;
  public selected: string;

  constructor(
  	private _userService: UserService
  ) { 
  	this.page_title = 'Configuración';
  	this.user = new User(1, '', '', 'ROLE_USER', '', '', '', '');
  	this.identity = this._userService.getIdentity();
  	this.token = this._userService.getToken();
  	
  	this.user = new User(
  		this.identity.sub,
  		this.identity.name,
  		this.identity.surname,
  		this.identity.role,
  		this.identity.email,
  		'',
  		this.identity.description,
  		this.identity.image
  		);
  }

  ngOnInit(): void {
  }

  onSubmit(form) {
  	this._userService.update(this.token, this.user).subscribe(
  		response => {
  			if(response) {
  				console.log(response && response.status);
  				this.status = 'success';
          console.log(response);
  				//Actualizar usuario en sesión
  				if(response.user.name){
  					this.user.name = response.user.name;
  				}

  				if(response.user.surname){
  					this.user.surname = response.user.surname;
  				}

  				if(response.user.email){
  					this.user.email = response.user.email;
  				}

  				if(response.user.description){
  					this.user.description = response.user.description;
  				}

  				if(response.user.image){
  					this.user.image = response.user.image;
  				}

  				this.identity = response.user;
  				localStorage.setItem('identity', JSON.stringify(this.identity));
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

  mouseUp() {
    this.selected = window.getSelection().toString();
  }

  doBold() {
    document.execCommand('bold', false);
  }

  doItalic() {
    document.execCommand('italic', false);
  }

}
