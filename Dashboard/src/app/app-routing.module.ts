import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';
import {ItemsAtLocationComponent} from "./dashboard/items-at-location/items-at-location.component";
import {DashboardComponent} from "./dashboard/dashboard/dashboard.component";
import {ItemsLostDetailComponent} from "./dashboard/items-lost-detail/items-lost-detail.component";
import {AvgLifetimeDeltaComponent} from "./dashboard/avg-lifetime-delta/avg-lifetime-delta.component";
import {TargetReachDetailComponent} from "./dashboard/target-reach-detail/target-reach-detail.component";
import {ScanActionsListComponent} from "./management/scan-actions-list/scan-actions-list.component";
import {AvgTurnaroundTimeDetailComponent} from "./dashboard/avg-turnaround-time-detail/avg-turnaround-time-detail.component";
import {ProductAgeSummaryDetailComponent} from "./dashboard/product-age-summary-detail/product-age-summary-detail.component";
import {CustomerSummaryViewComponent} from "./dashboard/customer-summary-view/customer-summary-view.component";
import {ItemDetailsComponent} from "./management/item-details/item-details.component";
import {DeliveredProductsDetailComponent} from './dashboard/delivered-products-detail/delivered-products-detail.component';
import {ReturnedProductsDetailComponent} from './dashboard/returned-products-detail/returned-products-detail.component';
import {IncomingOutgoingProductsDetailComponent} from './dashboard/incoming-outgoing-products-detail/incoming-outgoing-products-detail.component';
import {BundleRatioDetailComponent} from './dashboard/bundle-ratio-detail/bundle-ratio-detail.component';
import {ItemsAtSiteOverTimeComponent} from './dashboard/items-at-location/items-at-site-over-time/items-at-site-over-time.component';

const routes: Routes = [
  {
    path: 'dashboard',
    children: [
      {path: 'items-at-site', component: ItemsAtLocationComponent},
      {path: 'items-at-site-over-time', component: ItemsAtSiteOverTimeComponent},
      {path: 'items-lost', component: ItemsLostDetailComponent},

      {path: 'product-lifetime-delta', component: AvgLifetimeDeltaComponent},
      {path: 'avg-turnaround-time', component: AvgTurnaroundTimeDetailComponent},
      {path: 'age-summary', component: ProductAgeSummaryDetailComponent},

      {path: 'customer-summary', component: CustomerSummaryViewComponent},

      {path: 'container-target-reach', component: TargetReachDetailComponent},
      {path: 'bundle-ratio-outscans', component: BundleRatioDetailComponent},

      {path: 'incoming-outgoing-products', component: IncomingOutgoingProductsDetailComponent},
      {path: 'delivered-products', component: DeliveredProductsDetailComponent},
      {path: 'returned-products', component: ReturnedProductsDetailComponent},

      {path: '', pathMatch: 'full', component: DashboardComponent}
    ]
  },

  {
    path: 'management',
    children: [
      {path: 'scan-actions', component: ScanActionsListComponent},
      {path: 'items/:itemCuid', component: ItemDetailsComponent}
    ]
  },

  {path: '', pathMatch: 'full', redirectTo: 'dashboard'}
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {
}
