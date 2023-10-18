import {BrowserModule} from '@angular/platform-browser';
import {NgModule} from '@angular/core';

import {AppRoutingModule} from './app-routing.module';
import {AppComponent} from './app.component';
import {DashboardModule} from "./dashboard/dashboard.module";
import {FontAwesomeModule} from "@fortawesome/angular-fontawesome";
import {ManagementModule} from "./management/management.module";

@NgModule({
  declarations: [
    AppComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    FontAwesomeModule,
    DashboardModule,
    ManagementModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule {
}
