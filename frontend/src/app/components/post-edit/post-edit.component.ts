import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from '../../services/user.service';
import { PostService } from '../../services/post.service';
import { CategoryService } from '../../services/category.service';
import '@ckeditor/ckeditor5-build-classic/build/translations/es';
import * as ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import { Post } from '../../models/post';
import { global } from '../../services/global';

@Component({
  selector: 'app-post-edit',
  templateUrl: '../post-new/post-new.component.html',
  providers: [UserService, CategoryService, PostService]
})
export class PostEditComponent implements OnInit {
	public Editor = ClassicEditor;
	public page_title: string;
	public token;
	public identity;
	public post: Post;
	public categories;
  public url: string;
	public status;
	public is_edit: boolean;
	public afuConfig = {
    multiple: false,
    formatsAllowed: ".jpg,.png,.gif,.jpeg",
    maxSize: "50",
    uploadAPI:  {
      url: global.url+'post/upload',
      headers: {
        "Authorization" : this._userService.getToken()
      }
    },
    theme: "attachPin",
    hideProgressBar: false,
    hideResetBtn: true,
    hideSelectBtn: false,
    attachPinText: 'Sube una imagen'
  	};

  constructor(
  	private _route: ActivatedRoute,
  	private _router: Router,
  	private _userService: UserService,
  	private _categoryService: CategoryService,
  	private _postService: PostService
  ) { 
  	this.page_title = 'Editar entrada';
  	this.identity = this._userService.getIdentity();
  	this.token = this._userService.getToken();
  	this.is_edit = true;
    this.url = global.url;
  }

  ngOnInit(): void {
  	this.getCategories();
  	this.post = new Post(1, this.identity.sub, 1, '', '', null, null, null, null);
  	this.getPost();
  }

  getCategories() {
  	this._categoryService.getCategories().subscribe(
  		response => {
  			if(response.status == "success") {
  				this.categories = response.categories;
  			}
  		},
  		error => {
  			console.log(<any>error);
  		}
  	)
  }

  imageUpload(data) {
    let upload = JSON.parse(data.response);
    this.post.image = upload.image;
  }

  onSubmit(form) {
  	this._postService.update(this.token, this.post, this.post.id).subscribe(
  		response => {
  			if(response.status == 'success') {
  				this.status = 'success';
  				this.post = response.post;
  				this._router.navigate(['/post/'+this.post.id]);

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

  getPost(){
  	this._route.params.subscribe(params => {
  		let id = +params['id'];

  		this._postService.getPost(id).subscribe(
  			response => {
  				if(response.status == 'success') {
  					this.post = response.post;

            if(this.post.user_id != this.identity.sub) {
              this._router.navigate(['/home']);
            }
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
