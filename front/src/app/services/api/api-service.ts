import { Injectable } from "@angular/core";
import { HttpClient, HttpHeaders, HttpErrorResponse } from "@angular/common/http";
import { catchError } from "rxjs/operators";
import { throwError,Observable } from "rxjs";
import { Router } from "@angular/router";
import { GeneralService } from '../general/general.service';
import { MessageService } from "primeng/api";
/* -------------------------------------------------------------------------- */
/*              EndPoint pour les différentes requêtes vers l'api             */
/* -------------------------------------------------------------------------- */
@Injectable({
  providedIn: "root"
})
export class ApiService {
  jwt_generated_token: string = ""; // l'access token
  constructor(
    private http: HttpClient,
    private router: Router,
    private messageService: MessageService,
    public generalService: GeneralService) {
  }
  /* ----------------------- Pour créer les headers autorisation ---------------------- */
  createAuthorizationHeader() {
    this.jwt_generated_token = "Bearer " + this.generalService.get_data_from_session_decrypted("jwt_generated_token");
    let headers = new HttpHeaders();
    if (this.jwt_generated_token) {
      headers = headers.append("Authorization", this.jwt_generated_token);
      headers = headers.append("Content-Type", "application/json");
    }
    return headers;
  }
  createAuthorizationHeaderUpload() {
    this.jwt_generated_token = "Bearer " + this.generalService.get_data_from_session_decrypted("jwt_generated_token");
    let headers = new HttpHeaders();
    if (this.jwt_generated_token) {
      headers = headers.append("Authorization", this.jwt_generated_token);
    }
    return headers;
  }
  errormessage(code: any) { // gestion des erreurs http
    switch (code) {
      case 403: {
        localStorage.clear();
        this.messageService.add({ severity: 'error', detail: 'Accès interdit !' });
        this.generalService.Speak('Accès interdit !');
        setTimeout(() => {
          setTimeout(() => {
            console.clear();
          }, 3000);
          this.router.navigate(["/login"]);
        }, 1500);
        break;
      }
      case 404: {
        this.messageService.add({ severity: 'error', detail: 'Méthode introuvable ...' });
        this.generalService.Speak('Méthode introuvable');
        break;
      }
      case 402: {
        this.messageService.add({ severity: 'error', detail: 'Fichier introuvable ...' });
        this.generalService.Speak('Fichier introuvable');
        break;
      }
      case 401: {
        localStorage.clear();
        this.messageService.add({ severity: 'warn', detail: 'Session expirée ...' });
        setTimeout(() => { window.location.reload();}, 1000); // en cas de mise à jour ça aide
        break;
      }
      case 500:
      case 405:
        {
          this.messageService.add({ severity: 'error', detail: 'Oups quelque chose a mal tourné au niveau du serveur !' });
          this.generalService.Speak('Oups quelque chose a mal tourné au niveau du serveur !');
          break;
        }
      case 0: {
        this.messageService.add({ severity: 'error', detail: 'Serveur inaccessible !' });
        this.generalService.Speak('Serveur inaccessible !');
        setTimeout(() => {
          console.clear();
        }, 3000);
        break;
      }
      default: {
        this.messageService.add({ severity: 'error', detail: 'Oups quelque chose a mal tourné au niveau du serveur !' });
        this.generalService.Speak('Oups quelque chose a mal tourné au niveau du serveur !');
        break;
      }
    } // fin switch erreur
  }
  /* ---------------- Endpoint pour les requetes de types post ---------------- */
  post(endpoint: string, body?: any): Observable<any> {
    return this.http.post(endpoint, body, {
      headers: this.createAuthorizationHeader()
    }).pipe(
      catchError((err: HttpErrorResponse) => {
        this.errormessage(err?.status);
        return throwError(()=>new Error('Http error : ' + err?.status));
      })
    );
  }
  get(endpoint: string): Observable<any> {
    return this.http.get(endpoint, {
      headers: this.createAuthorizationHeader()
    }).pipe(
      catchError((err: HttpErrorResponse) => {
        this.errormessage(err?.status);
        return throwError(()=>new Error('Http error : ' + err?.status));
      })
    );
  }
  postDownload(endpoint: string, body: any): Observable<any> {
    return this.http.post(endpoint, body, {
      headers: this.createAuthorizationHeader(),
      responseType: "blob"
    }).pipe(
      catchError((err: HttpErrorResponse) => {
        this.errormessage(err?.status);
        return throwError(()=>new Error('Http error : ' + err?.status));
      })
    );
  }
  postUpload(endpoint: string, body: any): Observable<any> {
    return this.http.post(endpoint, body, {
      headers: this.createAuthorizationHeaderUpload()
    }).pipe(
      catchError((err: HttpErrorResponse) => {
        this.errormessage(err?.status);
        return throwError(()=>new Error('Http error : ' + err?.status));
      })
    );
  }
  postSansHeader(endpoint: string, body: any): Observable<any> {
    return this.http.post(endpoint, body).pipe(
      catchError((err: HttpErrorResponse) => {
        this.errormessage(err?.status);
        return throwError(()=>new Error('Http error : ' + err?.status));
      })
    );
  }
  getSansHeader(endpoint: string): Observable<any> {
    return this.http.get(endpoint).pipe(
      catchError((err: HttpErrorResponse) => {
        this.errormessage(err?.status);
        return throwError(()=>new Error('Http error : ' + err?.status));
      })
    );
  }
}