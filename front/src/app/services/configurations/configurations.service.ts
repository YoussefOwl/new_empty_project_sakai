import { Injectable } from "@angular/core";
import { environment } from 'src/environments/environment';
import { ApiService } from '../api/api-service';
@Injectable({
  providedIn: "root"
})
export class ConfigurationsService {
  constructor(private apiService: ApiService) { }
  AfficherLogs(data: any) {
    return this.apiService.post(environment.API_BASE_URL_CONFIGURATIONS + environment.api.general.AfficherLogs, data);
  }
} // fin Class 