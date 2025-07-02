import { Injectable } from '@angular/core';
import { ApiService } from '../services';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root'
})
export class GroupRoleSettingsService {

  constructor(private apiService: ApiService) { }

  AfficherGroupRole(body:any) {
    return this.apiService.post(environment.API_BASE_URL_USER+environment.api.app_parameters.AfficherGroupRole, body);
  }
  AjoutGroupRole(body:any) {
    return this.apiService.post(environment.API_BASE_URL_USER+environment.api.app_parameters.AjoutGroupRole, body);
  }
  ModifierGroupRole(body:any) {
    return this.apiService.post(environment.API_BASE_URL_USER+environment.api.app_parameters.ModifierGroupRole, body);
  }
}
