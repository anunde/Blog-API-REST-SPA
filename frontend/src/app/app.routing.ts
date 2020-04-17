//IMPORTS NECESARIOS PARA LA FUNCIONALIDAD DE ROUTING
import { ModuleWithProviders } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

//IMPORTS DE LOS COMPONENTES
import { LoginComponent } from './components/login/login.component';
import { RegisterComponent } from './components/register/register.component';
import { HomeComponent } from './components/home/home.component';
import { ErrorComponent } from './components/error/error.component';
import { UserEditComponent } from './components/user-edit/user-edit.component';
import { CategoryNewComponent } from './components/category-new/category-new.component';
import { PostNewComponent } from './components/post-new/post-new.component';
import { PostEditComponent } from './components/post-edit/post-edit.component';
import { PostDetailComponent } from './components/post-detail/post-detail.component';
import { CategoryDetailComponent } from './components/category-detail/category-detail.component';
import { ProfileComponent } from './components/profile/profile.component';

import { IdentityGuard } from './services/identity.guard';

//DEFINIR RUTAS
const appRoutes: Routes = [
	{path: '', component: HomeComponent},
	{path: 'home', component: HomeComponent},
	{path: 'login', component: LoginComponent},
	{path: 'logout/:sure', component: LoginComponent},
	{path: 'register', component: RegisterComponent, canActivate: [IdentityGuard]},
	{path: 'settings', component: UserEditComponent, canActivate: [IdentityGuard]},
	{path: 'new-category', component: CategoryNewComponent, canActivate: [IdentityGuard]},
	{path: 'new-post', component: PostNewComponent, canActivate: [IdentityGuard]},
	{path: 'post/:id', component: PostDetailComponent},
	{path: 'edit/post/:id', component: PostEditComponent, canActivate: [IdentityGuard]},
	{path: 'profile/:id', component: ProfileComponent, canActivate: [IdentityGuard]},
	{path: 'category/:id', component: CategoryDetailComponent},
	{path: '**', component: ErrorComponent}
];

//EXPORTAR CONFIGURACION
export const appRoutingProviders: any[] = [];
export const routing: ModuleWithProviders = RouterModule.forRoot(appRoutes);