import { Injectable } from '@angular/core';
import * as CryptoJS from 'crypto-js';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root'
})

export class CrypteService {

  Key: any = environment.KEY_CRYPTO;

  constructor() {
  }

  /* ------------------- Fonction pour crypter une variable ------------------- */

  encryptUsingAES256(data: any) {
    return CryptoJS.AES.encrypt(JSON.stringify(data), this.Key.toString());
  }

  /* ------------------- Fonction pour décrypter une variable ------------------- */

  decryptUsingAES256(data: any): any {
    let bytes = CryptoJS.AES.decrypt(data.toString(), this.Key);
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
}