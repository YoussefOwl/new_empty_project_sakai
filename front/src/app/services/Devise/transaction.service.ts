import { Injectable } from '@angular/core';
import { ApiService } from '../services';
import { environment } from 'src/environments/environment';
@Injectable({
  providedIn: 'root'
})
export class TransactionService {
  constructor(private apiService: ApiService) { }
  AfficherTransaction(body: any) {
      return this.apiService.post(environment.API_BASE_URL_DEVISE + environment.api.transaction.AfficherTransaction, body);
  }
  AjouterTransaction(body: any) {
    return this.apiService.post(environment.API_BASE_URL_DEVISE + environment.api.transaction.AjouterTransaction, body);
  }
  ModifierTransaction(body: any) {
    return this.apiService.post(environment.API_BASE_URL_DEVISE + environment.api.transaction.ModifierTransaction, body);
  }
  SuppressionTransaction(body: any) {
    return this.apiService.post(environment.API_BASE_URL_DEVISE + environment.api.transaction.SuppressionTransaction, body);
  }
}
