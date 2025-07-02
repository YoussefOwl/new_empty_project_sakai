import { Injectable } from "@angular/core";
import { environment } from 'src/environments/environment';
import { ApiService } from '../api/api-service';
@Injectable({
  providedIn: "root"
})
export class RoleService {
  constructor(private apiService: ApiService) { }
  AfficherRole(data: any) {
    return this.apiService.post(environment.API_BASE_URL_CONFIGURATIONS + environment.api.role.afficher, data);
  }
  AjouterRole(data: any) {
    return this.apiService.post(environment.API_BASE_URL_CONFIGURATIONS + environment.api.role.ajouter, data);
  }
  ModifierRole(data: any) {
    return this.apiService.post(environment.API_BASE_URL_CONFIGURATIONS + environment.api.role.modfier, data);
  }
}