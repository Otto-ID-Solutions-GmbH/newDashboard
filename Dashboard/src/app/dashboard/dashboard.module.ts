import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';

import {ItemsAtLocationComponent} from './items-at-location/items-at-location.component';
import {ItemKpiService} from "./services/item-kpi.service";
import {HttpClientModule} from "@angular/common/http";
import {FontAwesomeModule} from "@fortawesome/angular-fontawesome";
import {NgxChartsModule} from "@swimlane/ngx-charts";
import {BrowserAnimationsModule} from "@angular/platform-browser/animations";
import {CoreModule} from "../core/core.module";
import {DashboardComponent} from './dashboard/dashboard.component';
import {RouterModule} from "@angular/router";
import {ItemsLostDetailComponent} from './items-lost-detail/items-lost-detail.component';
import {ItemsLostDetailNowComponent} from './items-lost-detail/items-lost-detail-now/items-lost-detail-now.component';
import {ItemsLostDetailTimelineComponent} from './items-lost-detail/items-lost-detail-timeline/items-lost-detail-timeline.component';
import {AvgLifetimeDeltaComponent} from './avg-lifetime-delta/avg-lifetime-delta.component';
import {TargetReachDetailComponent} from './target-reach-detail/target-reach-detail.component';
import {AvgTurnaroundTimeDetailComponent} from './avg-turnaround-time-detail/avg-turnaround-time-detail.component';
import {ProductKpiService} from "./services/product-kpi.service";
import {StatisticsService} from "./services/statistics.service";
import {TargetKpiService} from "./services/target-kpi.service";
import {ProductAgeSummaryDetailComponent} from './product-age-summary-detail/product-age-summary-detail.component';
import {BsDropdownModule} from "ngx-bootstrap/dropdown";
import { ItemLostDetailCustomerComponent } from './items-lost-detail/item-lost-detail-customer/item-lost-detail-customer.component';
import { CustomerSummaryViewComponent } from './customer-summary-view/customer-summary-view.component';
import { ItemsLostAtCustomerTableComponent } from './items-lost-detail/item-lost-detail-customer/items-lost-at-customer-table/items-lost-at-customer-table.component';
import { AvgTurnaroundTimeTableComponent } from './avg-turnaround-time-detail/avg-turnaround-time-table/avg-turnaround-time-table.component';
import { ItemsAtSiteTableComponent } from './items-at-location/items-at-site-table/items-at-site-table.component';
import { DeliveredProductsDetailComponent } from './delivered-products-detail/delivered-products-detail.component';
import { ReturnedProductsDetailComponent } from './returned-products-detail/returned-products-detail.component';
import { IncomingOutgoingProductsDetailComponent } from './incoming-outgoing-products-detail/incoming-outgoing-products-detail.component';
import { BundleRatioDetailComponent } from './bundle-ratio-detail/bundle-ratio-detail.component';
import { ItemsAtSiteOverTimeComponent } from './items-at-location/items-at-site-over-time/items-at-site-over-time.component';

@NgModule({
  imports: [
    CommonModule,
    RouterModule,
    BrowserAnimationsModule,

    BsDropdownModule.forRoot(),
    NgxChartsModule,
    HttpClientModule,
    FontAwesomeModule,

    CoreModule
  ],
  declarations: [ItemsAtLocationComponent, DashboardComponent, ItemsLostDetailComponent, ItemsLostDetailNowComponent, ItemsLostDetailTimelineComponent, AvgLifetimeDeltaComponent, TargetReachDetailComponent, AvgTurnaroundTimeDetailComponent, ProductAgeSummaryDetailComponent, ItemLostDetailCustomerComponent, CustomerSummaryViewComponent, ItemsLostAtCustomerTableComponent, AvgTurnaroundTimeTableComponent, ItemsAtSiteTableComponent, DeliveredProductsDetailComponent, ReturnedProductsDetailComponent, IncomingOutgoingProductsDetailComponent, BundleRatioDetailComponent, ItemsAtSiteOverTimeComponent],
  providers: [ItemKpiService, ProductKpiService, StatisticsService, TargetKpiService]
})
export class DashboardModule {
}