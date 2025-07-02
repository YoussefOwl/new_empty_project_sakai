import { Injectable } from "@angular/core";
import { environment } from 'src/environments/environment';
import { ApiService } from '../api/api-service';

@Injectable({
  providedIn: "root"
})

export class UserService {
  constructor(private apiService: ApiService) { }
  LoginUtilisateur(data: any) {
    return this.apiService.postSansHeader(environment.API_BASE_URL_GENERAL + environment.api.user.login, data);
  }
  GetMyInfo(data: any) {
    return this.apiService.post(environment.API_BASE_URL_USER + environment.api.user.user_info, data);
  }
  ModifierUtilisateur(data: any) {
    return this.apiService.post(environment.API_BASE_URL_USER + environment.api.user.modifier, data);
  }
  ChangePassword(data: any) {
    return this.apiService.post(environment.API_BASE_URL_USER + environment.api.user.modifier_password, data);
  }
  AfficherMonImage(data: any) {
    return this.apiService.post(environment.API_BASE_URL_USER + environment.api.user.afficherimage, data);
  }
  SupprimerMonImage(data: any) {
    return this.apiService.post(environment.API_BASE_URL_USER + environment.api.user.supprimerimage, data);
  }
  ModifierMonImage(data: any) {
    return this.apiService.postUpload(environment.API_BASE_URL_USER + environment.api.user.modifierimage, data);
  }
  AfficherUtilisateur(data: any) {
    return this.apiService.post(environment.API_BASE_URL_USER + environment.api.user.afficher, data);
  }
  SupprimerUtilisateur(data: any) {
    return this.apiService.post(environment.API_BASE_URL_USER + environment.api.user.supprimer, data);
  }
  AjouterUtilisateur(data: any) {
    return this.apiService.post(environment.API_BASE_URL_USER + environment.api.user.ajouter, data);
  }
  ModifierParmsUtilisateur(data: any) {
    return this.apiService.post(environment.API_BASE_URL_USER + environment.api.user.modifier_params, data);
  }
} // fin Class UserService