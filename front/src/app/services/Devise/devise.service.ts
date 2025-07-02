import { Injectable } from '@angular/core';
import { ApiService } from '../services';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root'
})
export class DeviseService {
  
  constructor(private apiService: ApiService) { }
  
  AfficherDeviseParameter(body: any) {
    return this.apiService.post(environment.API_BASE_URL_DEVISE + environment.api.devise.AfficherDeviseParameter, body);
  }
  AjouterDeviseParameter(body: any) {
    return this.apiService.post(environment.API_BASE_URL_DEVISE + environment.api.devise.AjouterDeviseParameter, body);
  }
  ModifierDeviseParameter(body: any) {
    return this.apiService.post(environment.API_BASE_URL_DEVISE + environment.api.devise.ModifierDeviseParameter, body);
  }
  SuppressionDeviseParameter(body: any) {
    return this.apiService.post(environment.API_BASE_URL_DEVISE + environment.api.devise.SuppressionDeviseParameter, body);
  }
}
