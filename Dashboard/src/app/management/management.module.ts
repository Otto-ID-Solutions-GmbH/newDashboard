import {NgModule} from '@angular/core';
import {CommonModule, DatePipe} from '@angular/common';
import {ScanActionsListComponent} from './scan-actions-list/scan-actions-list.component';
import {CoreModule} from "../core/core.module";
import {BrowserModule} from "@angular/platform-browser";
import {HttpClientModule} from "@angular/common/http";
import {FontAwesomeModule} from "@fortawesome/angular-fontawesome";
import {NgxDatatableModule} from "@swimlane/ngx-datatable";
import {ScanActionsService} from "./services/scan-actions.service";
import { ItemDetailsComponent } from './item-details/item-details.component';
import { ItemStatusesComponent } from './item-statuses/item-statuses.component';

@NgModule({
  imports: [
    CommonModule,
    BrowserModule,
    HttpClientModule,

    FontAwesomeModule,
    NgxDatatableModule,

    CoreModule
  ],
  providers: [DatePipe, ScanActionsService],
  declarations: [ScanActionsListComponent, ItemDetailsComponent, ItemStatusesComponent]
})
export class ManagementModule {
}
