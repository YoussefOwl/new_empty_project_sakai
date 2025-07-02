import { Injectable } from "@angular/core";
import Swal from 'sweetalert2';
import { CrypteService } from '../crypte/crypte.service';
import { Router } from "@angular/router";
import * as FileSaver from 'file-saver';
import * as ExcelJS from 'exceljs';
import { jwtDecode } from "jwt-decode";
import { environment } from "src/environments/environment";
import { FormGroup, FormArray, FormControl } from "@angular/forms";
@Injectable({
  providedIn: "root"
})
/* ------------------- jwt_generated_token est le jwt jwt_generated_token ------------------- */
export class GeneralService {
  public CURRDATE = new Date().toISOString().substring(0, 10);
  public if_load: boolean = true;
  public is_loading: boolean = false;
  public currentMenu: any;
  public innerWidth: any = window.innerWidth; // StoreService.innerWidth>=992 (web) // StoreService.innerWidth<992 (mobile)
  public menu_toggel: boolean = true;
  constructor(private CryprtService: CrypteService, private router: Router) { }
  getInfosFile(fileExtension: any, mediaTypes: any[], wanted_key: any) {
    for (const mediaType of mediaTypes) {
      const allowedExtensions = mediaType.allowed.split(',');
      if (allowedExtensions.includes(fileExtension)) {
        return mediaType?.[wanted_key] ?? null;
      }
    }
    // Si l'extension n'est pas trouvée
    return null;
  }
  getBinarySize(base64String: any): number {
    if (base64String) {
      // Remove header from data URL if present
      const base64WithoutHeader = base64String.split(',')[1] || base64String;
      // The length of a Base64-encoded string is 4/3 times the size of the source data.
      // Since Base64 encoding expands data by 1/3, and '=' characters are padding,
      // they are not included in the actual data, so we need to subtract them.
      const padding = (base64WithoutHeader.match(/=+$/) || []).length;
      let binaryLength = (base64WithoutHeader.length * (3 / 4)) - padding;
      binaryLength = Number(binaryLength);
      let number = binaryLength / 1024;
      return parseFloat(number.toFixed(2)); // Size in bytes
    }
    else {
      return 0;
    }
  }
  LoadYears() {
    let start_year: any = environment.start_year;
    const currentYear = new Date().getFullYear(); // Obtient l'année actuelle
    return Array.from({ length: currentYear - start_year + 1 }, (v, i) => ({
      value: start_year + i,
      label: start_year + i
    }));
  }
  hasDuplicates(array: any[], key: any) {
    const seen = new Map(); // Use a Map to track occurrences of each key.
    const duplicates = new Set(); // Use a Set to keep unique duplicates.
    for (const item of array) {
      if (item.hasOwnProperty(key)) {
        if (seen.has(item[key])) {
          duplicates.add(item); // Add to duplicates if already seen.
        }
        seen.set(item[key], (seen.get(item[key]) || 0) + 1);
      }
    }
    return {
      hasDuplicates: duplicates.size > 0,
      arrayOfDuplicates: Array.from(duplicates)
    };
  }
  /* -------------------------------------------------------------------------- */
  /*                     Fonction de l'exportation en excel                     */
  /* -------------------------------------------------------------------------- */
  exportAsExcelFile(jsonData: any[], excelFileName: string, taille: any = 40): void {
    const EXCEL_TYPE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8';
    // Create a new Excel workbook
    const workbook = new ExcelJS.Workbook();
    // Add a worksheet
    const worksheet = workbook.addWorksheet('Feuille 1');
    // Define the header row
    const header = Object.keys(jsonData[0]);
    let headerRow = worksheet.addRow(header);
    headerRow.eachCell((cell: any) => {
      cell.fill = {
        type: 'pattern',
        pattern: 'solid',
        fgColor: { argb: 'FFCCFFE5' },
        bgColor: { argb: 'FFFFD700' }
      }
      cell.border = { top: { style: 'thin' }, left: { style: 'thin' }, bottom: { style: 'thin' }, right: { style: 'thin' } }
    });
    // Add data rows
    jsonData.forEach((data) => {
      const row = Object.values(data);
      worksheet.addRow(row);
    });
    // Assuming 'worksheet' is already defined and 'headers' contains your column headers
    header.forEach((_, index) => {
      const columnIndex = index + 1; // ExcelJS columns are 1-based
      worksheet.getColumn(columnIndex).width = taille;
    });
    // Generate and save the Excel file
    workbook.xlsx.writeBuffer().then((buffer) => {
      const blob = new Blob([buffer], { type: EXCEL_TYPE });
      FileSaver.saveAs(blob, excelFileName + '.xlsx');
    });
  }
  /* -------------------------------------------------------------------------- */
  /*                      Fonction de l'exportation en csv                      */
  /* -------------------------------------------------------------------------- */
  exportAsCsvFile(items: any[], file_name: any, separateur: any = ",") {
    const header = Object.keys(items[0]);
    const headerString = header.join(separateur);
    const replacer = (key: any, value: any) => value ?? '';
    const rowItems = items.map((row) =>
      header
        .map((fieldName) => JSON.stringify(row[fieldName], replacer))
        .join(separateur)
    );
    const csv = [headerString, ...rowItems].join('\r\n');
    const data: Blob = new Blob(["\uFEFF" + csv], { type: "text/csv;charset=utf-8" });
    FileSaver.saveAs(data, file_name + '.csv');
  }
  Speak(message: any) {
    if (this.get_data_from_session_decrypted('if_has_sound') == "true") {
      let synth = window.speechSynthesis;
      let utterance = new SpeechSynthesisUtterance();
      utterance.text = message;
      utterance.lang = "fr-FR";
      utterance.voice = synth.getVoices().filter(function (voice) { return voice.name == 'Google français'; })[0];
      synth.speak(utterance);
    }
  }
  isJson(str: any) {
    try {
      JSON.parse(str);
    }
    catch (e) {
      return false;
    }
    return true;
  }
  If_Dictionnary_has_Same_Structure(arr: any[]) {
    if (arr.length > 0) {
      // Get the keys of the first object in the array
      let keys: any[] = ['icon', 'title', 'routerLink'];
      // Check if the other objects in the array have the same keys and types
      for (var i = 1; i < arr.length; i++) {
        if (keys.length !== Object.keys(arr[i]).length) {
          return false; // The objects have different number of properties
        }
        for (var j = 0; j < keys.length; j++) {
          var key = keys[j];
          if (typeof arr[i][key] !== typeof arr[0][key]) {
            return false; // The property has a different data type
          }
        }
      }
      return true; // All objects have the same structure
    }
    else {
      return false;
    }
  }
  /* -------------------------------------------------------------------------- */
  /*                 Extract images base64 from random json file                */
  /* -------------------------------------------------------------------------- */
  extractImagesFromJson(json: any): any[] {
    let imageArray: any[] = [];
    for (let key in json) {
      if (json.hasOwnProperty(key)) {
        let value = json[key];
        if (value && typeof value === "string" && value.startsWith("data:image")) {
          imageArray.push(value);
        }
        else if (typeof value === "object") {
          imageArray = imageArray.concat(this.extractImagesFromJson(value));
        }
      }
    }
    return imageArray;
  }
  syntaxHighlight(value: any) {
    return JSON.stringify(value, null, 4)
      .replace(/ /g, '&nbsp;') // note the usage of `/ /g` instead of `' '` in order to replace all occurences
      .replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
        var cls = 'number';
        if (/^"/.test(match)) {
          if (/:$/.test(match)) {
            cls = 'key';
          } else {
            cls = 'string';
          }
        } else if (/true|false/.test(match)) {
          cls = 'boolean';
        } else if (/null/.test(match)) {
          cls = 'null';
        }
        return '<span class="' + cls + '">' + match + '</span>';
      })
      .replace(/\n/g, '<br/>');
  }
  /* ---- Fonction pour Vérifier si un intervalle de deux dates est valide ---- */
  CheckIfDateValid(debut: any, fin: any) {
    let start = new Date(debut);
    let end = new Date(fin);
    let diff = end.valueOf() - start.valueOf();
    // la methode valueOf appliquée à une date retourne une valeur en millisecondes écoulées depuis le 1 janvier 1970 00h00 
    if ((diff / 3600000) > 0 || (diff / 3600000) == 0) // La durée converti des millisecondes en Heures
    {
      return true;
    }
    else // durée invalide
    {
      return false;
    }// fin else la durée est invalide
  }
  GET_DATE_DIFF(debut: any, fin: any) {
    let start = new Date(debut);
    let end = new Date(fin);
    let diff = end.valueOf() - start.valueOf();
    return ((diff / 3600000) / 24) + 1; // en JOURS
  }
  GetFormatedDateNow(jour: any = new Date(), formated_by: any = '-') {
    let d = jour,
      month = '' + (d.getMonth() + 1),
      day = '' + d.getDate(),
      year = d.getFullYear();
    if (month.length < 2) { month = '0' + month; }
    if (day.length < 2) { day = '0' + day; }
    return [year, month, day].join(formated_by);
  }
  GetCurentYear() {
    let d = new Date(), year = d.getFullYear();
    return year;
  }
  GetCurentMonth() {
    let d = new Date(), month = '' + (d.getMonth() + 1);
    if (month.length < 2) { month = '0' + month; }
    return month;
  }
  GetFormatedDateNext() {
    const today = new Date();
    const tomorrow = new Date();
    tomorrow.setDate(today.getDate() + 1);
    let month = '' + (tomorrow.getMonth() + 1),
      day = '' + tomorrow.getDate(),
      year = tomorrow.getFullYear();
    if (month.length < 2) { month = '0' + month; }
    if (day.length < 2) { day = '0' + day; }
    return [year, month, day].join('-');
  }
  GetFormatedTimeNow() {
    let d = new Date(),
      hours = '' + d.getHours(),
      minutes = '' + d.getMinutes();
    if (hours.length < 2) { hours = '0' + hours; }
    if (minutes.length < 2) { minutes = '0' + minutes; }
    return hours + ":" + minutes;
  }
  /* --------------- Récupération d'une valeur depuis une liste --------------- */
  Get_libelle(comparatif_value: any = null, liste_source: any[] = [], wanted_key: any = 'label', comparatif_key: any = "value") {
    if (comparatif_value && liste_source.length > 0) {
      return liste_source.find((element: any) => element?.[comparatif_key] == comparatif_value)?.[wanted_key];
    }
    else {
      return null;
    }
  }
  /* -------------------------------------------------------------------------- */
  /*                           Gestion du localStorage                          */
  /* -------------------------------------------------------------------------- */
  get_array_access_from_storage(key_name: any): any[] {
    let value: any = localStorage.getItem(key_name);
    if (value && this.isJson(value)) {
      let value_parsed: any = JSON.parse(value);
      if (Array.isArray(value_parsed)) {
        return value_parsed;
      }
      else {
        return [];
      }
    }
    else {
      return [];
    }
  }
  set_data_to_session_crypted(body: any) {
    body.forEach((element: any) => {
      if (
        element?.key
        && element?.key != null
        && element?.key != ""
        && element?.value != ""
        && element?.value != null
        && element?.value
      ) {
        localStorage.setItem(
          element?.key,
          this.CryprtService.encryptUsingAES256(element?.value).toString()
        );
      }
    });
    return true;
  }
  get_data_from_session_decrypted(key: any): any {
    /* ------------------ Si la variable existe sur la session ------------------ */
    if (localStorage.getItem(key)) {
      let result: any = this.CryprtService.decryptUsingAES256(localStorage.getItem(key));
      if (result != null) {
        return result != null ? result : null;
      }
    }
    else {
      if (key == 'id_user') {
        localStorage.clear();
        setTimeout(() => {
          this.router.navigate(['/login'])
        }, 100);
      }
      return null;
    }
  }
  GetCurrentRole() {
    return atob(localStorage.getItem('id_role_crypted_front'));
  }
  function_if_can_manage_roles(): boolean {
    let id_role: any = this.GetCurrentRole();
    return this.get_array_access_from_storage("if_can_manage_roles").includes(id_role);
  }
  function_if_can_access(libelle: any): boolean {
    let id_role: any = this.GetCurrentRole();
    return this.get_array_access_from_storage(libelle).includes(id_role);
  }
  /* ----------------------- Fonction pour décode le jwt ---------------------- */
  Get_claim(claim: any) {
    let result: any = jwtDecode(this.get_data_from_session_decrypted('jwt_generated_token'));
    return result?.[claim];
  }
  DecodeJwt(jwt_generated_token: any, claim: any) {
    let result: any = jwtDecode(jwt_generated_token);
    return result?.[claim];
  }
  /* ------------------------- La gestion des erreurs ------------------------- */
  errorSwal(message: any, duration: any = 2000, icon: any = "error", text: any = null, showConfirmButton: boolean = false) {
    Swal.fire({
      icon: icon,
      title: message,
      text: text,
      showConfirmButton: showConfirmButton,
      timer: duration
    });
  }
  /* -------------------------- optimized sweetAlert -------------------------- */
  sweetAlert(title: string = null, message: string = null, icon = null) {
    return Swal.fire({
      title: title,
      text: message !== null ? message : "",
      icon: icon,
      showCancelButton: true,
      reverseButtons: true,
      cancelButtonText: "Annuler",
      confirmButtonColor: "#258662",
      cancelButtonColor: "#f50707",
      confirmButtonText: "Valider"
    });
  }
  /* ------------------------ Les fonctions de calcule ------------------------ */
  sum_one_prop(liste: any[], prop: any, round: number = 2): any {
    if (liste.length > 0 && prop) {
      let result: any = liste.reduce((a, b) => a + (b[prop] || 0), 0);
      return Number(result.toFixed(round));
    }
    else {
      return 0;
    }
  }
  sum_two_elements_multipliyed(liste: any[], prop_one: any, prop_two: any, round: any = 2): any {
    if (liste.length > 0 && prop_one && prop_one) {
      let result: number = liste.reduce((a, b) => a + (b[prop_one] * b[prop_two] || 0), 0);
      return Number((result).toFixed(round));
    }
    else {
      return 0;
    }
  }
  numberWithCommas(number: number) {
    let test: any = number.toFixed(2);
    let parts: any = test.toString().split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    return parts.join(",");
  }
  GotoTop() {
    setTimeout(() => {
      let element: any = document.getElementById("top_bar_html");
      element.scrollIntoView({ behavior: "smooth", block: "end", inline: "end" });
    }, 100);
  }
  // -------------------------//
  /* -------------------------- optimized sweetAlert -------------------------- */
  /* --- set timer on 1 | null for 2500 or '2' => 3500 or '3' => 5000 --- */
  timer(timerDefault: any, icon: any) {
    if (timerDefault == 'noTime') {
      return 0;
    }
    if ((timerDefault == null || timerDefault == 1) && icon != 'error') {
      return 2500;
    }
    if (timerDefault == 2 || icon == 'error') {
      return 3500;
    }
    if (timerDefault == 3) {
      return 5000;
    }
  }
  sweetAlert2(
    title?: any,
    message?: any,
    type?: any,
    icon?: any,
    timerDefault?: any,
    speak?: number
  ) {
    // set timer on (1 | null) for 2500 or '2' => 3500 or '3' => 5000 
    const timer = this.timer(timerDefault, icon);
    let result = null;
    if (type == "confirm") {
      result = Swal.fire({
        title: title,
        text: message !== null ? message : "",
        icon: icon,
        showCancelButton: true,
        reverseButtons: true,
        cancelButtonText: "Annuler",
        confirmButtonText: "Valider",
        confirmButtonColor: "#258662",
        cancelButtonColor: "#f50707"
      });
    }
    if (type == "simple") {
      result = Swal.fire({
        title: title,
        text: message !== null ? message : "",
        icon: icon,
        timer: timer,
        showConfirmButton: !timer,
        confirmButtonColor: "#258662",
        confirmButtonText: "Ok"
      });
    }
    if (type == "none-dissmis") {
      result = Swal.fire({
        title: title,
        text: message !== null ? message : "",
        icon: icon,
        showConfirmButton: false,
        showCancelButton: false,  // Hide the cancel button
        allowOutsideClick: false,  // Disable clicks outside the modal
        allowEscapeKey: false,  // Disable the escape key
        allowEnterKey: false,  // Disable the enter key
        timer: timer,
      });
    }
    if (speak && Number(this.get_data_from_session_decrypted("speech_synthesis"))) { this.Speak(message); }
    return result;
  }
  initFormArray(data: any, FormulaireModification: FormGroup, FormArrayName: string, validation: any[] = [], commandes: any[] = []) {
    Promise.resolve()
      .then(() => (<FormArray>FormulaireModification.get(FormArrayName)).clear())
      .then(() => {
        /* ----------------------------- refill the list ---------------------------- */
        data.map((res: any) => {
          const keys = Object.keys(res); // set keys
          const formGroup = new FormGroup({}); // init formGrop
          /* ----------------------- append object to formGroup ----------------------- */
          keys.forEach((key) => {
            let validators = validation.find((res) => res?.key == key)?.validator ?? null; // get Validator
            let disabled = commandes.find((res) => res?.key == key)?.disabled ?? false; // if disabled
            formGroup.addControl(key, new FormControl({ value: res?.[key], disabled: disabled }, validators));
          });
          /* ---- append formGroup to the formArray inside the parent form group ---- */
          (<FormArray>FormulaireModification.get(FormArrayName)).push(formGroup);
        });
      })
  }
  is_admin(): boolean {
    return this.get_data_from_session_decrypted('is_admin') === 'true';
  }
  /* ---------------------- Pour Migrer une fonction RXJS --------------------- */
  // .subscribe({
  //   next: (res: any) => {
  //   },
  //   error: () => {}
  // });
  // .subscribe({
  //   next: (r: any) => {
  //   },
  //   error: () => {}
  // });
}