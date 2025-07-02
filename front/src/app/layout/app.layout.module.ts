import { NgModule } from '@angular/core';
import { provideHttpClient, withInterceptorsFromDi } from '@angular/common/http';
import { AppMenuComponent } from './app.menu.component';
import { AppTopBarComponent } from './app.topbar.component';
import { AppFooterComponent } from './app.footer.component';
import { AppSidebarComponent } from "./app.sidebar.component";
import { AppLayoutComponent } from "./app.layout.component";
import { BrowserModule } from '@angular/platform-browser';
import { RouterModule } from '@angular/router';
import { AppMenuitemComponent } from './app.menuitem.component';

@NgModule({ declarations: [
        AppMenuitemComponent,
        AppTopBarComponent,
        AppFooterComponent,
        AppMenuComponent,
        AppSidebarComponent,
        AppLayoutComponent,
    ],
    exports: [AppLayoutComponent], 
    imports: [
        BrowserModule,
        RouterModule
    ], 
    providers: [provideHttpClient(withInterceptorsFromDi())] })
export class AppLayoutModule { }
