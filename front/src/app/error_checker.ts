import { ErrorHandler, Injectable } from '@angular/core';
import Swal from 'sweetalert2';
@Injectable()
export class GlobalErrorHandler implements ErrorHandler {
  constructor() { }
  handleError(error: any) {
    let chunkFailedMessage = /loading chunk/i; // Case-insensitive pattern for "loading chunk"
    if (chunkFailedMessage.test(error?.message) || chunkFailedMessage.test(error?.stack?.toString())) {
      console.log("%cChunk load error detected", "color:green");
      window.location.reload();
    }
    else if ((error?.message).toString().includes("of null (reading 'includes')") || (error.toString()).includes("of null (reading 'includes')")) {
      localStorage.clear();
      console.log("%cSpecific error detected", "color:green");
      Swal.fire({
        icon: 'error',
        title: "Erreur de configuration !",
        html: `<pre style="height: 200px !important;text-align: left;">${error?.stack?.toString()}</pre>`,
        timer: 3000,
        showConfirmButton: false
      });
      setTimeout(() => {
        window.location.reload();
      }, 3100);
    }
    else {
      console.log("%cOther error detected", "color:green");
      console.log("error?.message : ",error?.message);
      console.log("error?.stack?.toString() : ",error?.stack?.toString());
    }
  }
}
