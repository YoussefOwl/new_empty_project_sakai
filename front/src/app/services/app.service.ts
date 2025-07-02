import { Injectable } from '@angular/core';
@Injectable({
  providedIn: 'root'
})
export class AppService {
  isSidebarPinned:boolean = false;
  isSidebarToggeled:boolean = false;
  constructor() { }
  toggleSidebar() {
    this.isSidebarToggeled = !this.isSidebarToggeled;
  }
  toggleSidebarPin() {
    this.isSidebarPinned = !this.isSidebarPinned;
  }
  getSidebarStat() {
    return {
      isSidebarPinned: this.isSidebarPinned,
      isSidebarToggeled: this.isSidebarToggeled
    }
  }
}