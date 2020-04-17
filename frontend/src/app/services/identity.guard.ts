import { Injectable } from '@angular/core';
import { Router, CanActivate } from '@angular/router';
import { UserService } from './user.service';

@Injectable()
export class IdentityGuard implements CanActivate{
	
	constructor(
		private _router: Router,
		private _userService: UserService
	) {}

	canActivate() {
		let identity = this._userService.getIdentity();

		if(identity) {
			return true;
		} else {
			this._router.navigate(['/home']);
			return false;
		}
	}
}