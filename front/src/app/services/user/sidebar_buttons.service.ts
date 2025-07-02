import { Injectable } from '@angular/core';
import { environment } from 'src/environments/environment';
import { ApiService } from '../services';
@Injectable({
  providedIn: 'root'
})
export class SidebarButtonsService {
  constructor(private apiService: ApiService) { }
  AjouterSidebarButtons(body: any) {
    return this.apiService.post(environment.API_BASE_URL_USER + environment.api.app_parameters.AjouterSidebarButtons, body);
  }
  AfficherSidebarButtons(body: any) {
    return this.apiService.post(environment.API_BASE_URL_USER + environment.api.app_parameters.AfficherSidebarButtons, body);
  }
  ModifierSidebarButtons(body: any) {
    return this.apiService.post(environment.API_BASE_URL_USER + environment.api.app_parameters.ModifierSidebarButtons, body);
  }
  SupprimerSidebarButtons(body: any) {
    return this.apiService.post(environment.API_BASE_URL_USER + environment.api.app_parameters.SupprimerSidebarButtons, body);
  }
}