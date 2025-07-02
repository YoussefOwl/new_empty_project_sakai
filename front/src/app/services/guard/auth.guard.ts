import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { jwtDecode } from "jwt-decode";
import * as CryptoJS from 'crypto-js';
import { environment } from "src/environments/environment";
@Injectable({
  providedIn: 'root'
})
export class AuthGuard {
  constructor(private router: Router) { }
  canActivate(): boolean {
    /* -------------------- Si l'utilisateur est authentifié ------------------- */
    if (this.isLoggedIn()) {
      return true;
    }
    /* ----------------- Si l'utilisateur n'est pas authentifié ----------------- */
    else {
      this.router.navigate(['/login']);
      return false;
    }
  }
  /* ----------------- Vérifier si l'utilisateur est connécté ----------------- */
  isLoggedIn(): boolean {
    if (localStorage.getItem('jwt_generated_token')) {
      if (this.get_data_from_session_decrypted('jwt_generated_token') != null) {
        try // debut du try
        {
          let jwt_generated_token: any = jwtDecode(this.get_data_from_session_decrypted('jwt_generated_token'));
          if (jwt_generated_token['unique_claim'] == environment.unique_claim) {
            return true;
          }
          else {
            this.ClearSession();
            return false;
          }
        }
        catch (Error) {
          this.ClearSession();
          return false;
        }
      }
      else {
        this.ClearSession();
        return false;
      }
    }
    else {
      this.ClearSession();
      return false;
    }
  }
  /* ------------------ Si la variable existe sur la session ------------------ */
  get_data_from_session_decrypted(key: any): any {
    if (localStorage.getItem(key)) {
      let result: any = this.decryptUsingAES256(localStorage.getItem(key));
      if (result != null) {
        return result != null ? result : null;
      }
    }
    else {
      return null;
    }
  }
  decryptUsingAES256(data: any): any {
    let bytes = CryptoJS.AES.decrypt(data.toString(), environment.KEY_CRYPTO);
    if (bytes['sigBytes'] > 0) {
      let data = bytes.toString(CryptoJS.enc.Utf8);
      if (/^[\],:{}\s]*$/.test(data.replace(/\\["\\\/bfnrtu]/g, '@').
        replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
        replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
        return JSON.parse(bytes.toString(CryptoJS.enc.Utf8));
      }
      else {
        return null;
      }
    }
    else {
      return null;
    }
  }
  ClearSession() {
    localStorage.clear();
  }
}