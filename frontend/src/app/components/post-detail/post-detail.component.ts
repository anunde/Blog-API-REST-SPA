import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { Post } from '../../models/post';
import { PostService } from '../../services/post.service';
import { UserService } from '../../services/user.service';

@Component({
  selector: 'app-post-detail',
  templateUrl: './post-detail.component.html',
  styleUrls: ['./post-detail.component.css'],
  providers: [PostService, UserService]
})
export class PostDetailComponent implements OnInit {
	public post: Post;
  public identity;
  constructor(
  	private _postService: PostService,
  	private _route: ActivatedRoute,
  	private _router: Router,
    private _userService: UserService
  ) { 
    this.identity = this._userService.getIdentity();
  }

  ngOnInit(): void {
  	this.getPost();
  }

  getPost(){
  	this._route.params.subscribe(params => {
  		let id = +params['id'];

  		this._postService.getPost(id).subscribe(
  			response => {
  				if(response.status == 'success') {
  					this.post = response.post;
  					console.log(this.post);
  				} else {  					
  					this._router.navigate(['/home']);
  				}
  			},
  			error => {
  				console.log(error);
  				this._router.navigate(['/home']);
  			}
  		);
  	});
  }
}
