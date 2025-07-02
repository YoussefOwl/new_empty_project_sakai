import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Router } from '@angular/router';
import Swal from 'sweetalert2';
@Injectable({
  providedIn: 'root'
})
export class RolesGuard {
  constructor(private router: Router) { }
  canActivate(route: ActivatedRouteSnapshot): boolean {
    try {
      let route_param: any = route.data["roles"];
      if (route_param) {
        let value_from_storage: any = localStorage.getItem(route_param);
        /* -------------------- Si le droit existe sur la session ------------------- */
        if (value_from_storage) {
          /* ------------------------- Si c'est un json valid ------------------------- */
          if (JSON.parse(value_from_storage)) {
            let expected_roles: any = JSON.parse(value_from_storage);
            if (localStorage.getItem('id_role_crypted_front')) {
              /* -------------------- Si l'utilisateur est autorisé ------------------- */
              if (Array.isArray(expected_roles)
                && expected_roles.length > 0
                && expected_roles.includes(atob(localStorage.getItem('id_role_crypted_front')))) {
                return true;
              }
              /* ----------------- Si l'utilisateur n'est pas autorisé ----------------- */
              else {
                this.error();
                return false;
              }
            }
            else {
              this.error();
              return false;
            }
          }
          else {
            this.error();
            return false;
          }
        }
        else {
          this.error();
          return false;
        }
      }
      else {
        this.error();
        return false;
      }
    }
    catch (e: any) {
      this.error();
      return false;
    }
  }
  error() {
    Swal.fire({
      icon: 'error',
      title: 'Accès interdit',
      showConfirmButton: false,
      timer: 2000
    });
    this.router.navigate(['/accueil']);
  }
}