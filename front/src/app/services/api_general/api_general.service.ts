import { Injectable } from "@angular/core";
import { environment } from 'src/environments/environment';
import { ApiService } from '../api/api-service';
@Injectable({
  providedIn: "root"
})
export class ApiGeneralService {
  constructor(private apiService: ApiService) { }
  TelechargerDocument(data: any) {
    return this.apiService.postDownload(environment.API_BASE_URL_PARAMETRAGE + environment.api.general.telecharger, data);
  }
  LoadParamsForList(data: any) {
    return this.apiService.post(environment.API_BASE_URL_PARAMETRAGE + environment.api.general.LoadParamsForList, data);
  }
} // fin Class 