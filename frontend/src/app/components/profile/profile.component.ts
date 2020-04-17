import { Component, OnInit } from '@angular/core';
import { Post } from '../../models/post';
import { User } from '../../models/user';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { global } from '../../services/global';
import { PostService } from '../../services/post.service';
import { UserService } from '../../services/user.service';


@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css'],
  providers: [PostService, UserService]
})
export class ProfileComponent implements OnInit {
	public posts: Array<Post>;
	public url;
	public user: User;
	public identity;
	public token;


  constructor(
  	private _route: ActivatedRoute,
  	private _router: Router,
  	private _postService: PostService,
  	private _userService: UserService
  ) { 
  	this.url = global.url;
  	this.identity = this._userService.getIdentity();
  	this.token = this._userService.getToken();
  }

  ngOnInit(): void {
  	this._route.params.subscribe(params => {
  		let userId = +params['id'];
  		this.getUser(userId);
  		this.getPosts(userId);
  	});
  }

  getUser(userId) {
  	this._userService.getUser(userId).subscribe(
  		response => {
  			if(response.status == 'success') {
  				this.user = response.user;
  			} 
  		},
  		error => {
  			console.log(<any>error);
  		}
  	);
  }

  getPosts(userId){
  	this._userService.getPosts(userId).subscribe(
  		response => {
  			if(response.status == 'success') {
  				this.posts = response.posts;
  			} 
  		},
  		error => {
  			console.log(<any>error);
  		}
  	);
  }

  deletePost(id) {
    this._postService.delete(this.token, id).subscribe(
      response => {
        this._route.params.subscribe(params => {
  		let userId = +params['id'];
  		this.getPosts(userId);
  	});
      },
      error => {
        console.log(error);
      }
    )
  }
}
